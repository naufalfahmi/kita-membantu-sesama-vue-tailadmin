<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tanggal',
        'keterangan',
        'is_cuti',
        'tahun',
        'source',
        'raw_payload',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_cuti' => 'boolean',
        'raw_payload' => 'array',
    ];

    public static function isHoliday(string $date): bool
    {
        return self::whereDate('tanggal', $date)->exists();
    }
}
