<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\MenuService;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\LandingKegiatan;

class SearchController extends Controller
{
    /**
     * Global search across all accessible menus and data with relationships
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min((int) $request->get('limit', 10), 50); // Max 50 results
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [
                    'menus' => [],
                    'data' => [],
                    'relationships' => [],
                ],
            ]);
        }

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            
            $results = [
                'menus' => [],
                'data' => [],
                'relationships' => [], // Keterkaitan antar menu
            ];

            try {
                // Get all accessible menu items
                $menuConfig = MenuService::getMenuConfig();
                $accessibleMenus = $this->getAccessibleMenus($menuConfig, $user);
                
                // Search in menu items
                $matchedMenus = $this->searchMenus($accessibleMenus, $query, $limit);
                $results['menus'] = $matchedMenus;
            } catch (\Exception $e) {
                \Log::error('Error searching menus: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                $results['menus'] = [];
            }

            try {
                // Search in data based on permissions - expanded search
                $dataResults = $this->searchAllData($user, $query, $limit);
                $results['data'] = $dataResults;
            } catch (\Exception $e) {
                \Log::error('Error searching data: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                $results['data'] = [];
            }

            try {
                // Find relationships/connections between search results
                $relationships = $this->findRelationships($user, $query, $results['data'] ?? []);
                $results['relationships'] = $relationships;
            } catch (\Exception $e) {
                \Log::error('Error finding relationships: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                $results['relationships'] = [];
            }
            
            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan pencarian',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get autocomplete suggestions including data
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min((int) $request->get('limit', 5), 10); // Max 10 for autocomplete
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            
            $suggestions = [];

            try {
                // Get menu suggestions
                $menuConfig = MenuService::getMenuConfig();
                $accessibleMenus = $this->getAccessibleMenus($menuConfig, $user);
                $menuSuggestions = $this->searchMenus($accessibleMenus, $query, 3);
                
                foreach ($menuSuggestions as $menu) {
                    $suggestions[] = [
                        'type' => 'menu',
                        'title' => $menu['name'],
                        'path' => $menu['path'],
                        'category' => $menu['category'] ?? 'Menu',
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error getting menu suggestions: ' . $e->getMessage());
            }

            try {
                // Get data suggestions (limited)
                $dataResults = $this->searchAllData($user, $query, 5);
                foreach ($dataResults as $result) {
                    $suggestions[] = [
                        'type' => $result['type'],
                        'title' => $result['title'],
                        'path' => $result['path'],
                        'category' => $result['category'] ?? 'Data',
                        'menu_name' => $result['menu_name'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error getting data suggestions: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => array_slice($suggestions, 0, $limit),
            ]);
        } catch (\Exception $e) {
            \Log::error('Autocomplete error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan autocomplete',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get all accessible menu items for user
     */
    private function getAccessibleMenus(array $menuConfig, $user): array
    {
        $accessibleMenus = [];
        
        foreach ($menuConfig as $group) {
            foreach ($group['items'] as $item) {
                // Check if user has permission for this item
                $hasPermission = !isset($item['permission']) || $user->can($item['permission']);
                
                if (!$hasPermission) {
                    continue;
                }

                // Add item if it doesn't have subItems
                if (!isset($item['subItems'])) {
                    $accessibleMenus[] = [
                        'name' => $item['name'],
                        'path' => $item['path'] ?? '/',
                        'category' => $group['title'] ?? 'Other',
                        'icon' => $item['icon'] ?? null,
                    ];
                } else {
                    // Add subItems
                    foreach ($item['subItems'] as $subItem) {
                        $hasSubPermission = !isset($subItem['permission']) || $user->can($subItem['permission']);
                        
                        if ($hasSubPermission) {
                            $accessibleMenus[] = [
                                'name' => $subItem['name'],
                                'path' => $subItem['path'] ?? '/',
                                'category' => $item['name'] ?? $group['title'] ?? 'Other',
                                'icon' => $item['icon'] ?? null,
                            ];
                        }
                    }
                }
            }
        }
        
        return $accessibleMenus;
    }

    /**
     * Search in menu items
     */
    private function searchMenus(array $menus, string $query, int $limit): array
    {
        $matches = [];
        $queryLower = mb_strtolower($query);
        
        foreach ($menus as $menu) {
            $nameLower = mb_strtolower($menu['name']);
            
            // Exact match gets higher priority
            if ($nameLower === $queryLower) {
                array_unshift($matches, $menu);
            } elseif (str_contains($nameLower, $queryLower)) {
                $matches[] = $menu;
            }
        }
        
        return array_slice($matches, 0, $limit);
    }

    /**
     * Comprehensive search across all data tables based on user permissions
     */
    private function searchAllData($user, string $query, int $limit): array
    {
        $results = [];

        // Search Jabatan (Roles)
        if ($user->can('view jabatan')) {
            try {
                $jabatans = Role::where('name', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();
                
                foreach ($jabatans as $jabatan) {
                    $results[] = [
                        'type' => 'jabatan',
                        'id' => $jabatan->id,
                        'title' => $jabatan->name,
                        'path' => "/administrasi/jabatan/{$jabatan->id}/edit",
                        'menu_path' => '/administrasi/jabatan',
                        'menu_name' => 'Jabatan',
                        'category' => 'Administrasi',
                        'description' => 'Jabatan',
                        'related_key' => 'jabatan_id',
                    ];
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search Users/Karyawan
        if ($user->can('view karyawan')) {
            try {
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();
                
                foreach ($users as $userItem) {
                    $results[] = [
                        'type' => 'karyawan',
                        'id' => $userItem->id,
                        'title' => $userItem->name,
                        'path' => "/user-kepegawaian/karyawan/{$userItem->id}/edit",
                        'menu_path' => '/user-kepegawaian/karyawan',
                        'menu_name' => 'Karyawan',
                        'category' => 'User & Kepegawaian',
                        'description' => $userItem->email,
                        'related_key' => 'user_id',
                    ];
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search Landing Kegiatan
        if ($user->can('view landing kegiatan')) {
            try {
                $kegiatans = LandingKegiatan::where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('village', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();
                
                foreach ($kegiatans as $kegiatan) {
                    $results[] = [
                        'type' => 'landing_kegiatan',
                        'id' => $kegiatan->id,
                        'title' => $kegiatan->title,
                        'path' => "/company/landing-kegiatan/{$kegiatan->id}/edit",
                        'menu_path' => '/company/landing-kegiatan',
                        'menu_name' => 'Landing Kegiatan',
                        'category' => 'Company',
                        'description' => substr($kegiatan->description ?? '', 0, 100),
                        'related_key' => 'kegiatan_id',
                    ];
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search in other tables using DB facade (dynamic search)
        $searchableTables = $this->getSearchableTables($user, $query);
        foreach ($searchableTables as $tableResult) {
            $results[] = $tableResult;
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * Get searchable tables based on user permissions
     */
    private function getSearchableTables($user, string $query): array
    {
        $results = [];
        $queryEscaped = addslashes($query);

        // Search in Kantor Cabang
        if ($user->can('view kantor cabang')) {
            try {
                if (DB::getSchemaBuilder()->hasTable('kantor_cabangs')) {
                    $items = DB::table('kantor_cabangs')
                        ->where(function($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                              ->orWhere('address', 'like', "%{$query}%")
                              ->orWhere('city', 'like', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($items as $item) {
                        $results[] = [
                            'type' => 'kantor_cabang',
                            'id' => $item->id,
                            'title' => $item->name ?? 'Kantor Cabang #' . $item->id,
                            'path' => "/administrasi/kantor-cabang/{$item->id}/edit",
                            'menu_path' => '/administrasi/kantor-cabang',
                            'menu_name' => 'Kantor Cabang',
                            'category' => 'Administrasi',
                            'description' => ($item->address ?? '') . ', ' . ($item->city ?? ''),
                            'related_key' => 'kantor_cabang_id',
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search in Absensi
        if ($user->can('view absensi')) {
            try {
                if (DB::getSchemaBuilder()->hasTable('absensis')) {
                    // Search by user name through users table
                    $userIds = User::where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->pluck('id');
                    
                    if ($userIds->isNotEmpty()) {
                        $items = DB::table('absensis')
                            ->whereIn('user_id', $userIds)
                            ->limit(5)
                            ->get();
                        
                        foreach ($items as $item) {
                            $userModel = User::find($item->user_id);
                            $userName = $userModel ? $userModel->name : 'Unknown';
                            $results[] = [
                                'type' => 'absensi',
                                'id' => $item->id,
                                'title' => "Absensi - {$userName}",
                                'path' => "/operasional/absensi",
                                'menu_path' => '/operasional/absensi',
                                'menu_name' => 'Absensi',
                                'category' => 'Operasional',
                                'description' => "Absensi untuk {$userName}",
                                'related_key' => 'user_id',
                                'related_id' => $item->user_id,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search in Remunerasi
        if ($user->can('view remunerasi')) {
            try {
                if (DB::getSchemaBuilder()->hasTable('remunerasis')) {
                    $userIds = User::where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->pluck('id');
                    
                    if ($userIds->isNotEmpty()) {
                        $items = DB::table('remunerasis')
                            ->whereIn('user_id', $userIds)
                            ->limit(5)
                            ->get();
                        
                        foreach ($items as $item) {
                            $userModel = User::find($item->user_id);
                            $userName = $userModel ? $userModel->name : 'Unknown';
                            $results[] = [
                                'type' => 'remunerasi',
                                'id' => $item->id,
                                'title' => "Remunerasi - {$userName}",
                                'path' => "/operasional/remunerasi/{$item->id}/edit",
                                'menu_path' => '/operasional/remunerasi',
                                'menu_name' => 'Remunerasi',
                                'category' => 'Operasional',
                                'description' => "Remunerasi untuk {$userName}",
                                'related_key' => 'user_id',
                                'related_id' => $item->user_id,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        // Search in Transaksi
        if ($user->can('view transaksi')) {
            try {
                if (DB::getSchemaBuilder()->hasTable('transaksis')) {
                    // Search by description or related user
                    $items = DB::table('transaksis')
                        ->where(function($q) use ($query) {
                            $q->where('description', 'like', "%{$query}%")
                              ->orWhere('notes', 'like', "%{$query}%");
                        })
                        ->limit(5)
                        ->get();
                    
                    foreach ($items as $item) {
                        $results[] = [
                            'type' => 'transaksi',
                            'id' => $item->id,
                            'title' => $item->description ?? 'Transaksi #' . $item->id,
                            'path' => "/keuangan/transaksi/{$item->id}/edit",
                            'menu_path' => '/keuangan/transaksi',
                            'menu_name' => 'Transaksi',
                            'category' => 'Keuangan',
                            'description' => substr($item->notes ?? '', 0, 100),
                            'related_key' => null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $results;
    }

    /**
     * Find relationships between search results
     * Example: If searching for a user, find their absensi, remunerasi, etc.
     */
    private function findRelationships($user, string $query, array $dataResults): array
    {
        $relationships = [];
        
        // Group results by type
        $groupedResults = [];
        foreach ($dataResults as $result) {
            $type = $result['type'];
            if (!isset($groupedResults[$type])) {
                $groupedResults[$type] = [];
            }
            $groupedResults[$type][] = $result;
        }

        // Find relationships for each result
        foreach ($dataResults as $result) {
            $related = [];
            
            // If result is a user/karyawan, find all related data
            if ($result['type'] === 'karyawan') {
                $userId = $result['id'];
                
                // Find jabatan (roles) of this user
                try {
                    $userModel = User::find($userId);
                    if ($userModel) {
                        $roles = $userModel->getRoleNames();
                        foreach ($roles as $roleName) {
                            $role = Role::where('name', $roleName)->first();
                            if ($role && $user->can('view jabatan')) {
                                $related[] = [
                                    'type' => 'jabatan',
                                    'title' => $role->name,
                                    'menu_name' => 'Jabatan',
                                    'menu_path' => '/administrasi/jabatan',
                                    'path' => "/administrasi/jabatan/{$role->id}/edit",
                                    'relationship' => 'Memiliki Jabatan',
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore
                }

                // Find absensi for this user
                if ($user->can('view absensi') && DB::getSchemaBuilder()->hasTable('absensis')) {
                    try {
                        $absensiCount = DB::table('absensis')
                            ->where('user_id', $userId)
                            ->count();
                        
                        if ($absensiCount > 0) {
                            $related[] = [
                                'type' => 'absensi',
                                'title' => "{$absensiCount} Data Absensi",
                                'menu_name' => 'Absensi',
                                'menu_path' => '/operasional/absensi',
                                'path' => '/operasional/absensi?user_id=' . $userId,
                                'relationship' => 'Memiliki Absensi',
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }
                }

                // Find remunerasi for this user
                if ($user->can('view remunerasi') && DB::getSchemaBuilder()->hasTable('remunerasis')) {
                    try {
                        $remunerasiCount = DB::table('remunerasis')
                            ->where('user_id', $userId)
                            ->count();
                        
                        if ($remunerasiCount > 0) {
                            $related[] = [
                                'type' => 'remunerasi',
                                'title' => "{$remunerasiCount} Data Remunerasi",
                                'menu_name' => 'Remunerasi',
                                'menu_path' => '/operasional/remunerasi',
                                'path' => '/operasional/remunerasi?user_id=' . $userId,
                                'relationship' => 'Memiliki Remunerasi',
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }
                }
            }

            // If result is a jabatan, find users with this role
            if ($result['type'] === 'jabatan') {
                $roleId = $result['id'];
                try {
                    $role = Role::find($roleId);
                    if ($role && $user->can('view karyawan')) {
                        $usersWithRole = $role->users;
                        if ($usersWithRole->count() > 0) {
                            $related[] = [
                                'type' => 'karyawan',
                                'title' => $usersWithRole->count() . ' Karyawan',
                                'menu_name' => 'Karyawan',
                                'menu_path' => '/user-kepegawaian/karyawan',
                                'path' => '/user-kepegawaian/karyawan?role_id=' . $roleId,
                                'relationship' => 'Memiliki Karyawan',
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore
                }
            }

            // Add relationships if found
            if (!empty($related)) {
                $relationships[] = [
                    'source' => [
                        'type' => $result['type'],
                        'id' => $result['id'],
                        'title' => $result['title'],
                        'menu_name' => $result['menu_name'] ?? '',
                    ],
                    'related' => $related,
                ];
            }
        }

        return $relationships;
    }
}

