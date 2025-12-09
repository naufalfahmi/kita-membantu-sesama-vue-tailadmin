<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class MenuService
{
    /**
     * Get menu configuration with permission mapping
     * 
     * @return array
     */
    public static function getMenuConfig(): array
    {
        return [
            [
                'title' => 'Admin',
                'items' => [
                    [
                        'icon' => 'HomeIcon',
                        'name' => 'Dashboard',
                        'path' => '/',
                        'permission' => null, // Dashboard accessible to all authenticated users
                        'pro' => false,
                    ],
                    [
                        'icon' => 'PageIcon',
                        'name' => 'Company',
                        'permission' => 'view landing profile', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Landing Profile',
                                'path' => '/company/landing-profile',
                                'permission' => 'view landing profile',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Landing Kegiatan',
                                'path' => '/company/landing-kegiatan',
                                'permission' => 'view landing kegiatan',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Landing Program',
                                'path' => '/company/landing-program',
                                'permission' => 'view landing program',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Landing Proposal',
                                'path' => '/company/landing-proposal',
                                'permission' => 'view landing proposal',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Landing Bulletin',
                                'path' => '/company/landing-bulletin',
                                'permission' => 'view landing bulletin',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'FolderIcon',
                        'name' => 'Administrasi',
                        'permission' => 'view kantor cabang', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Kantor Cabang',
                                'path' => '/administrasi/kantor-cabang',
                                'permission' => 'view kantor cabang',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Program',
                                'path' => '/administrasi/program',
                                'permission' => 'view program',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Jabatan',
                                'path' => '/administrasi/jabatan',
                                'permission' => 'view jabatan',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Pangkat',
                                'path' => '/administrasi/pangkat',
                                'permission' => 'view pangkat',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Tipe Absensi',
                                'path' => '/administrasi/tipe-absensi',
                                'permission' => 'view tipe absensi',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Gaji',
                                'path' => '/administrasi/gaji',
                                'permission' => 'view gaji',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Tipe Donatur',
                                'path' => '/administrasi/tipe-donatur',
                                'permission' => 'view tipe donatur',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Form Pesan',
                                'path' => '/administrasi/form-pesan',
                                'permission' => 'view form pesan',
                                'pro' => false,
                            ],
                            [
                                'name' => 'SOP',
                                'path' => '/administrasi/sop',
                                'permission' => 'view sop',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Aturan Kepegawaian',
                                'path' => '/administrasi/aturan-kepegawaian',
                                'permission' => 'view aturan kepegawaian',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'ListIcon',
                        'name' => 'Konten & Publikasi',
                        'permission' => 'view program kami', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Program Kami',
                                'path' => '/konten/program-kami',
                                'permission' => 'view program kami',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Profile Kami',
                                'path' => '/konten/profile-kami',
                                'permission' => 'view profile kami',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Proposal Data',
                                'path' => '/konten/proposal-data',
                                'permission' => 'view proposal data',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Bulletin Data',
                                'path' => '/konten/bulletin-data',
                                'permission' => 'view bulletin data',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'UserCircleIcon',
                        'name' => 'User & Kepegawaian',
                        'permission' => 'view karyawan', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Karyawan',
                                'path' => '/user-kepegawaian/karyawan',
                                'permission' => 'view karyawan',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Mitra',
                                'path' => '/user-kepegawaian/mitra',
                                'permission' => 'view mitra',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Donatur',
                                'path' => '/user-kepegawaian/donatur',
                                'permission' => 'view donatur',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'CalenderIcon',
                        'name' => 'Operasional',
                        'permission' => 'view absensi', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Absensi',
                                'path' => '/operasional/absensi',
                                'permission' => 'view absensi',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Remunerasi',
                                'path' => '/operasional/remunerasi',
                                'permission' => 'view remunerasi',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'PieChartIcon',
                        'name' => 'Keuangan',
                        'permission' => 'view keuangan', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Keuangan',
                                'path' => '/keuangan/keuangan',
                                'permission' => 'view keuangan',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Penyaluran',
                                'path' => '/keuangan/penyaluran',
                                'permission' => 'view penyaluran',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Pengajuan Dana',
                                'path' => '/keuangan/pengajuan-dana',
                                'permission' => 'view pengajuan dana',
                                'pro' => false,
                            ],
                        ],
                    ],
                    [
                        'icon' => 'PieChartIcon',
                        'name' => 'Transaksi',
                        'path' => '/keuangan/transaksi',
                        'permission' => 'view transaksi',
                        'pro' => false,
                    ],
                    [
                        'icon' => 'BarChartIcon',
                        'name' => 'Laporan',
                        'permission' => 'view laporan transaksi', // Use first permission as parent permission
                        'subItems' => [
                            [
                                'name' => 'Laporan Transaksi',
                                'path' => '/laporan/laporan-transaksi',
                                'permission' => 'view laporan transaksi',
                                'pro' => false,
                            ],
                            [
                                'name' => 'Laporan Keuangan',
                                'path' => '/laporan/laporan-keuangan',
                                'permission' => 'view laporan keuangan',
                                'pro' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get filtered menu based on user permissions
     * 
     * @return array
     */
    public static function getFilteredMenu(): array
    {
        $user = Auth::user();
        $menuConfig = self::getMenuConfig();

        // If no user is authenticated, return empty menu
        if (!$user) {
            return [];
        }

        // Clear permission cache to ensure fresh permissions are used
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        
        // Reload user's permissions
        $user->load('permissions', 'roles.permissions');

        // Filter menu based on permissions
        $filteredMenu = [];
        
        foreach ($menuConfig as $group) {
            $filteredItems = [];
            
            foreach ($group['items'] as $item) {
                // Filter subItems first if they exist
                if (isset($item['subItems'])) {
                    $filteredSubItems = [];
                    
                    foreach ($item['subItems'] as $subItem) {
                        $hasSubPermission = !isset($subItem['permission']) || $user->can($subItem['permission']);
                        
                        if ($hasSubPermission) {
                            // Remove permission from response (not needed in frontend)
                            unset($subItem['permission']);
                            $filteredSubItems[] = $subItem;
                        }
                    }
                    
                    // Only add parent item if it has at least one accessible subItem
                    // (This way, parent menu shows if user has permission to ANY child)
                    if (!empty($filteredSubItems)) {
                        $item['subItems'] = $filteredSubItems;
                        unset($item['permission']);
                        $filteredItems[] = $item;
                    }
                } else {
                    // For items without subItems, check the item's own permission
                    $hasPermission = !isset($item['permission']) || $user->can($item['permission']);
                    
                    if ($hasPermission) {
                        // Remove permission from response
                        unset($item['permission']);
                        $filteredItems[] = $item;
                    }
                }
            }
            
            // Only add group if it has at least one item
            if (!empty($filteredItems)) {
                $filteredMenu[] = [
                    'title' => $group['title'],
                    'items' => $filteredItems,
                ];
            }
        }

        return $filteredMenu;
    }
}

