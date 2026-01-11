<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            // Log request for debugging
            \Log::info('Login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->validate([
                // `email` field accepts either an email address or `no_induk` value
                'email' => 'required|string',
                'password' => 'required|string',
                'remember' => 'boolean',
            ]);

            $identifier = $request->input('email');
            $password = $request->input('password');
            $remember = $request->boolean('remember', false);

            \Log::info('Attempting login', ['identifier' => $identifier]);

            $authenticated = false;

            // If identifier looks like an email, use the normal Auth::attempt
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $credentials = ['email' => $identifier, 'password' => $password];
                if (Auth::attempt($credentials, $remember)) {
                    $authenticated = true;
                }
            } else {
                // Otherwise treat identifier as no_induk (employee number)
                $userModel = config('auth.providers.users.model');
                $user = (new $userModel)::where('no_induk', $identifier)->first();
                if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                    Auth::login($user, $remember);
                    $authenticated = true;
                }
            }

            // Fallback: authenticate via mitra table when user record is missing
            if (! $authenticated && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $mitra = Mitra::where('email', $identifier)->first();

                if ($mitra && ! empty($mitra->password) && Hash::check($password, $mitra->password)) {
                    $user = null;

                    if ($mitra->user_id) {
                        $user = User::find($mitra->user_id);
                    }

                    if (! $user) {
                        $user = User::where('email', $identifier)->first();
                    }

                    $createdUser = false;

                    if (! $user) {
                        $user = User::create([
                            'name' => $mitra->nama ?? $identifier,
                            'email' => $identifier,
                            'password' => Hash::make($password),
                            'tipe_user' => 'mitra',
                            'is_active' => true,
                            'created_by' => null,
                        ]);
                        $createdUser = true;
                    } elseif (! Hash::check($password, $user->password)) {
                        $user->password = Hash::make($password);
                        $user->save();
                    }

                    if ($user) {
                        // Ensure mitra record links back to the user we are authenticating
                        if (! $mitra->user_id || $mitra->user_id !== $user->id) {
                            $mitra->user_id = $user->id;
                            $mitra->save();
                        }

                        if ($createdUser) {
                            $role = null;
                            if (! empty($mitra->jabatan_id)) {
                                $role = \Spatie\Permission\Models\Role::find($mitra->jabatan_id);
                            }
                            if (! $role) {
                                $role = \Spatie\Permission\Models\Role::where('name', 'mitra')->first();
                            }
                            if ($role) {
                                $user->assignRole($role->name);
                            }
                        }

                        Auth::login($user, $remember);
                        $authenticated = true;

                        \Log::info('Login successful via mitra fallback', [
                            'user_id' => $user->id,
                            'mitra_id' => $mitra->id,
                        ]);
                    }
                }
            }

            if ($authenticated) {
                $request->session()->regenerate();

                $user = Auth::user();
                
                // Check if this is first login (last_login_at is null)
                $isFirstLogin = is_null($user->last_login_at);
                
                // Update last_login_at
                $user->last_login_at = now();
                $user->save();

                \Log::info('Login successful', ['user_id' => $user->id, 'email' => $user->email, 'is_first_login' => $isFirstLogin]);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'is_first_login' => $isFirstLogin,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);
            }

            \Log::warning('Login failed', ['identifier' => $identifier]);

            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => ['The provided credentials do not match our records.'],
                ],
            ], 422);
        } catch (ValidationException $e) {
            \Log::error('Login validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated',
            ], 401);
        }

        // Load relationships (include pivot list for determining primary)
        $user->load([
            'pangkat',
            'tipeAbsensi',
            'kantorCabangs',
            'roles',
            'mitra.kantorCabang',
            'mitra.jabatan',
        ]);

        // For backward compatibility, pick a primary kantor cabang (first pivot or legacy single column)
        $primaryKantor = $user->kantorCabangs->first() ?? $user->kantorCabang;

        $mitraProfile = null;
        if ($user->mitra) {
            $mitra = $user->mitra;
            $mitraProfile = [
                'id' => $mitra->id,
                'nama' => $mitra->nama,
                'email' => $mitra->email,
                'no_handphone' => $mitra->no_handphone,
                'nama_bank' => $mitra->nama_bank,
                'no_rekening' => $mitra->no_rekening,
                'tanggal_lahir' => optional($mitra->tanggal_lahir)->toDateString(),
                'pendidikan' => $mitra->pendidikan,
                'tanggal_dibuat' => optional($mitra->created_at)->toDateString(),
                'kantor_cabang' => $mitra->kantorCabang ? [
                    'id' => $mitra->kantorCabang->id,
                    'nama' => $mitra->kantorCabang->nama,
                ] : null,
                'jabatan' => $mitra->jabatan ? [
                    'id' => $mitra->jabatan->id,
                    'name' => $mitra->jabatan->name,
                ] : null,
            ];
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'no_induk' => $user->no_induk,
                'no_handphone' => $user->no_handphone,
                'pendidikan' => $user->pendidikan,
                'nama_bank' => $user->nama_bank,
                'no_rekening' => $user->no_rekening,
                'tanggal_lahir' => $user->tanggal_lahir,
                'tanggal_masuk' => $user->tanggal_masuk,
                'tipe_user' => $user->tipe_user,
                'role' => $user->roles->first(),
                'kantor_cabang' => $primaryKantor,
                // include full list of kantor cabangs for frontend that supports multiple branches
                'kantor_cabangs' => $user->kantorCabangs->map(function($k) {
                    return [
                        'id' => $k->id,
                        'nama' => $k->nama,
                        'latitude' => $k->latitude ?? null,
                        'longitude' => $k->longitude ?? null,
                    ];
                })->toArray(),
                // backward-compatible alias some frontends expect
                'kantor_cabangs_raw' => $user->kantorCabangs->toArray(),
                'jabatan' => $user->posisi,
                'pangkat' => $user->pangkat,
                'tipe_absensi' => $user->tipeAbsensi,
                'facebook' => $user->facebook,
                'twitter' => $user->twitter,
                'linkedin' => $user->linkedin,
                'instagram' => $user->instagram,
                // Include whether the user is an admin (role named 'admin')
                'is_admin' => $user->hasRole('admin'),
                // Include permission names for client-side checks
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                // Include visibility lists so frontend filters can mirror backend access rules
                'visible_donatur_ids' => method_exists($user, 'visibleDonaturKaryawanIds') ? $user->visibleDonaturKaryawanIds() : [],
                'visible_transaksi_ids' => method_exists($user, 'visibleTransaksiKaryawanIds') ? $user->visibleTransaksiKaryawanIds() : [],
                'visible_mitra_transaksi_ids' => method_exists($user, 'visibleMitraTransaksiIds') ? $user->visibleMitraTransaksiIds() : [],
                'visible_mitra_donatur_ids' => method_exists($user, 'visibleMitraDonaturIds') ? $user->visibleMitraDonaturIds() : [],
                'is_mitra' => (bool) $mitraProfile,
                'mitra_profile' => $mitraProfile,
            ],
        ]);
    }

    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            $user->avatar = '/storage/' . $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar' => $user->avatar,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded',
        ], 400);
    }

    public function updateSocial(Request $request)
    {
        $request->validate([
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update($request->only(['facebook', 'twitter', 'linkedin', 'instagram']));

        return response()->json([
            'success' => true,
            'message' => 'Social media information updated successfully',
            'user' => [
                'facebook' => $user->facebook,
                'twitter' => $user->twitter,
                'linkedin' => $user->linkedin,
                'instagram' => $user->instagram,
            ],
        ]);
    }

    /**
     * Update authenticated user's personal information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'no_induk' => 'nullable|string|max:50',
            'pangkat_id' => 'nullable|exists:pangkats,id',
            'posisi' => 'nullable|string|max:255',
            'no_handphone' => 'nullable|string|max:30',
            'nama_bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tipe_absensi_id' => 'nullable|exists:tipe_absensis,id',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $user = Auth::user();

        $data = $request->only([
            'no_induk', 'pangkat_id', 'posisi', 'no_handphone', 'nama_bank', 'no_rekening',
            'tanggal_lahir', 'pendidikan', 'tanggal_masuk', 'tipe_absensi_id',
            'first_name', 'last_name', 'email'
        ]);

        $user->update($data);

        $user->load(['pangkat', 'tipeAbsensi', 'kantorCabang', 'roles']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_induk' => $user->no_induk,
                'no_handphone' => $user->no_handphone,
                'pendidikan' => $user->pendidikan,
                'nama_bank' => $user->nama_bank,
                'no_rekening' => $user->no_rekening,
                'tanggal_lahir' => $user->tanggal_lahir,
                'tanggal_masuk' => $user->tanggal_masuk,
                'pangkat' => $user->pangkat,
                'tipe_absensi' => $user->tipeAbsensi,
            ],
        ]);
    }

    /**
     * Change authenticated user's password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'errors' => ['current_password' => ['Current password is incorrect']],
            ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }
}

