<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        // Load relationships
        $user->load(['pangkat', 'tipeAbsensi', 'kantorCabang', 'roles']);

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
                'role' => $user->roles->first(),
                'kantor_cabang' => $user->kantorCabang,
                'jabatan' => $user->posisi,
                'pangkat' => $user->pangkat,
                'tipe_absensi' => $user->tipeAbsensi,
                'facebook' => $user->facebook,
                'twitter' => $user->twitter,
                'linkedin' => $user->linkedin,
                'instagram' => $user->instagram,
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

