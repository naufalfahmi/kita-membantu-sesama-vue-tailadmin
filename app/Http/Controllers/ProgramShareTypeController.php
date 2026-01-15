<?php

namespace App\Http\Controllers;

use App\Models\ProgramShareType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramShareTypeController extends Controller
{
    public function index(Request $request)
    {
        $items = ProgramShareType::orderBy('orders', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Get distinct submission types (aliases) for pengajuan dana form
     * Returns only share types with non-null alias
     */
    public function submissionTypes()
    {
        try {
            // Get distinct aliases with their corresponding share keys and names
            $aliases = ProgramShareType::whereNotNull('alias')
                ->orderBy('alias')
                ->get()
                ->groupBy('alias')
                ->map(function ($items, $alias) {
                    $first = $items->first();
                    
                    return [
                        'value' => $alias,
                        'label' => $alias,  // Frontend will format this
                        'share_key' => $first->key,
                        'name' => $first->name,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $aliases,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat tipe pengajuan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Map alias to share_key
     * Helper method to get share_key from alias
     */
    public static function getShareKeyFromAlias(?string $alias): ?string
    {
        if (!$alias) return null;

        $shareType = ProgramShareType::where('alias', $alias)
            ->whereNotNull('alias')
            ->first();

        return $shareType ? $shareType->key : null;
    }

    /**
     * Map share_key to alias (reverse lookup)
     */
    public static function getAliasFromShareKey(?string $shareKey): ?string
    {
        if (!$shareKey) return null;

        $shareType = ProgramShareType::where('key', $shareKey)
            ->whereNotNull('alias')
            ->first();

        return $shareType ? $shareType->alias : null;
    }
}
