<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'tipe_absensi_id',
        'kantor_cabang_id',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'jarak_masuk',
        'jam_keluar',
        'latitude_keluar',
        'longitude_keluar',
        'jarak_keluar',
        'total_jam_kerja',
        'status',
        'catatan',
        'alasan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
        'jarak_masuk' => 'decimal:2',
        'jarak_keluar' => 'decimal:2',
        'total_jam_kerja' => 'decimal:2',
    ];

    /**
     * Get the user that owns this attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the attendance type.
     */
    public function tipeAbsensi(): BelongsTo
    {
        return $this->belongsTo(TipeAbsensi::class, 'tipe_absensi_id');
    }

    /**
     * Get the branch office.
     */
    public function kantorCabang(): BelongsTo
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    /**
     * Get the user that created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user that deleted the record.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in meters
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if location is within allowed radius.
     */
    public static function isWithinRadius(float $userLat, float $userLon, float $officeLat, float $officeLon, int $allowedRadius): bool
    {
        $distance = self::calculateDistance($userLat, $userLon, $officeLat, $officeLon);
        return $distance <= $allowedRadius;
    }

    /**
     * Calculate work hours between clock in and clock out.
     */
    public function calculateWorkHours(): ?float
    {
        if (!$this->jam_masuk || !$this->jam_keluar) {
            return null;
        }

        $diff = $this->jam_keluar->diffInMinutes($this->jam_masuk);
        return round($diff / 60, 2);
    }

    /**
     * Determine attendance status based on clock in time and type settings.
     */
    public function determineStatus(): string
    {
        if (!$this->jam_masuk) {
            return 'tidak_hadir';
        }

        // Get tipe absensi settings
        $tipeAbsensi = $this->tipeAbsensi;
        if (!$tipeAbsensi || !$tipeAbsensi->jam_masuk) {
            return 'hadir';
        }

        $jamMasukSeharusnya = $tipeAbsensi->jam_masuk;
        $jamMasukActual = $this->jam_masuk->format('H:i:s');

        // Compare times
        if ($jamMasukActual > $jamMasukSeharusnya) {
            return 'terlambat';
        }

        // Check for early leave
        if ($this->jam_keluar && $tipeAbsensi->jam_keluar) {
            $jamKeluarSeharusnya = $tipeAbsensi->jam_keluar;
            $jamKeluarActual = $this->jam_keluar->format('H:i:s');

            if ($jamKeluarActual < $jamKeluarSeharusnya) {
                return 'pulang_awal';
            }
        }

        return 'hadir';
    }

    /**
     * Get today's attendance for a user.
     */
    public static function getTodayAttendance(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->whereDate('jam_masuk', today())
            ->first();
    }

    /**
     * Check if user already clocked in today.
     */
    public static function hasClockInToday(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereDate('jam_masuk', today())
            ->exists();
    }

    /**
     * Check if user already clocked out today.
     */
    public static function hasClockOutToday(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereDate('jam_masuk', today())
            ->whereNotNull('jam_keluar')
            ->exists();
    }
}
