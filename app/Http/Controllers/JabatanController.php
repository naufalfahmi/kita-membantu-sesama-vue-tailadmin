<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name', 'asc')
                      ->paginate($request->get('per_page', 20));

        // Load permissions for each role
        $roles->getCollection()->transform(function ($role) {
            $role->permissions = $role->permissions->pluck('name');
            return $role;
        });

        return response()->json([
            'success' => true,
            'data' => $roles->items(),
            'pagination' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            // Assign permissions if provided
            if ($request->has('permissions') && is_array($request->permissions)) {
                $permissionModels = [];
                foreach ($request->permissions as $permissionName) {
                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionName, 'guard_name' => 'web'],
                        ['name' => $permissionName, 'guard_name' => 'web']
                    );
                    $permissionModels[] = $permission;
                }
                $role->syncPermissions($permissionModels);
            }

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil ditambahkan',
                'data' => $role->load('permissions'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jabatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role->update([
                'name' => $request->name,
            ]);

            // Sync permissions
            if ($request->has('permissions') && is_array($request->permissions)) {
                $permissionModels = [];
                foreach ($request->permissions as $permissionName) {
                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionName, 'guard_name' => 'web'],
                        ['name' => $permissionName, 'guard_name' => 'web']
                    );
                    $permissionModels[] = $permission;
                }
                $role->syncPermissions($permissionModels);
            } else {
                // If permissions array is empty, remove all permissions
                $role->syncPermissions([]);
            }

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil diupdate',
                'data' => $role->load('permissions'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate jabatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan',
            ], 404);
        }

        try {
            // Prevent deleting admin roles (e.g., "Admin", "Admin Cabang")
            if (preg_match('/^admin$/i', $role->name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan admin tidak dapat dihapus',
                ], 422);
            }

            // Check if role is assigned to any user
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan tidak dapat dihapus karena masih digunakan oleh pengguna',
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jabatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Get all available permissions grouped by module
     */
    public function getPermissions()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        
        // Group permissions by module
        $grouped = [];
        foreach ($permissions as $permission) {
            // Extract module from permission name (e.g., "view branch_office" -> "branch_office")
            $parts = explode(' ', $permission->name);
            if (count($parts) >= 2) {
                $action = $parts[0]; // view, create, update, show, delete
                $module = implode(' ', array_slice($parts, 1)); // module name
                
                if (!isset($grouped[$module])) {
                    $grouped[$module] = [];
                }
                
                $grouped[$module][$action] = $permission->name;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    /**
     * Clone role with permissions
     */
    public function clone(Request $request, string $id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $newRole = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            // Copy permissions from original role
            $newRole->syncPermissions($role->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil diduplikasi',
                'data' => $newRole->load('permissions'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menduplikasi jabatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}

