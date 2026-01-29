<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Models\KaryawanDonaturVisibility;
use App\Models\KaryawanTransaksiVisibility;
use App\Models\KaryawanMitraTransaksiVisibility;
use App\Models\KaryawanMitraDonaturVisibility;
use App\Models\PushSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'uuid',
        'name',
        'email',
        'password',
        'avatar',
        'no_induk',
        'posisi',
        'pangkat_id',
        'tipe_absensi_id',
        'no_handphone',
        'nama_bank',
        'no_rekening',
        'tanggal_lahir',
        'pendidikan',
        'tanggal_masuk',
        'kantor_cabang_id',
        'leader_id',
        'tipe_user',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot model events to ensure uuid is set on create.
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the pangkat that belongs to the user.
     */
    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_id');
    }

    /**
     * Get the tipe absensi that belongs to the user.
     */
    public function tipeAbsensi()
    {
        return $this->belongsTo(TipeAbsensi::class, 'tipe_absensi_id');
    }

    /**
     * Get the kantor cabang that belongs to the user.
     */
    public function kantorCabang()
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    /**
     * Many-to-many kantor cabang assignments for a user.
     */
    public function kantorCabangs()
    {
        return $this->belongsToMany(KantorCabang::class, 'kantor_cabang_user', 'user_id', 'kantor_cabang_id');
    }

    /**
     * Leader (another user) for this karyawan.
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Subordinates for which this user is leader.
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'leader_id');
    }

    /**
     * Explicit visibility list for transaksi (who this user may see).
     */
    public function transaksiVisibilityEntries()
    {
        return $this->hasMany(KaryawanTransaksiVisibility::class, 'karyawan_id')
            ->whereNull('deleted_at');
    }

    /**
     * Explicit visibility list for donatur (who this user may see).
     */
    public function donaturVisibilityEntries()
    {
        return $this->hasMany(KaryawanDonaturVisibility::class, 'karyawan_id')
            ->whereNull('deleted_at');
    }

    /**
     * Explicit visibility list for mitra in transaksi context.
     */
    public function mitraTransaksiVisibilityEntries()
    {
        return $this->hasMany(KaryawanMitraTransaksiVisibility::class, 'karyawan_id')
            ->whereNull('deleted_at');
    }

    /**
     * Explicit visibility list for mitra in donatur context.
     */
    public function mitraDonaturVisibilityEntries()
    {
        return $this->hasMany(KaryawanMitraDonaturVisibility::class, 'karyawan_id')
            ->whereNull('deleted_at');
    }

    /**
     * Explicit visibility list for absensi (who this user may see).
     */
    public function absensiVisibilityEntries()
    {
        return $this->hasMany(KaryawanAbsensiVisibility::class, 'karyawan_id')
            ->whereNull('deleted_at');
    }

    /**
     * Push subscription entries for this user.
     */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class, 'user_id');
    }

    /**
     * Helper: return IDs this user may see in transaksi context.
     */
    public function visibleTransaksiKaryawanIds(): array
    {
        $ids = $this->transaksiVisibilityEntries()->pluck('visible_karyawan_id')->toArray();

        if (! empty($ids)) {
            $ids[] = $this->id;
            return $this->normalizeVisibilityIds($ids);
        }

        return User::descendantIdsOf($this->id);
    }

    /**
     * Helper: return IDs this user may see in donatur context.
     */
    public function visibleDonaturKaryawanIds(): array
    {
        $ids = $this->donaturVisibilityEntries()->pluck('visible_karyawan_id')->toArray();

        if (! empty($ids)) {
            $ids[] = $this->id;
            return $this->normalizeVisibilityIds($ids);
        }

        return User::descendantIdsOf($this->id);
    }

    /**
     * Helper: return IDs this user may see in absensi context.
     */
    public function visibleAbsensiKaryawanIds(): array
    {
        $ids = $this->absensiVisibilityEntries()->pluck('visible_karyawan_id')->toArray();

        if (! empty($ids)) {
            $ids[] = $this->id;
            return $this->normalizeVisibilityIds($ids);
        }

        return User::descendantIdsOf($this->id);
    }

    /**
     * Helper: return mitra IDs this user may see in transaksi context.
     */
    public function visibleMitraTransaksiIds(): array
    {
        $ids = $this->mitraTransaksiVisibilityEntries()->pluck('visible_mitra_id')->toArray();

        if (! empty($ids)) {
            return $this->normalizeVisibilityIds($ids);
        }

        return [];
    }

    /**
     * Helper: return mitra IDs this user may see in donatur context.
     */
    public function visibleMitraDonaturIds(): array
    {
        $ids = $this->mitraDonaturVisibilityEntries()->pluck('visible_mitra_id')->toArray();

        if (! empty($ids)) {
            return $this->normalizeVisibilityIds($ids);
        }

        return [];
    }

    /**
     * Linked mitra profile when this user represents a mitra account.
     */
    public function mitra()
    {
        return $this->hasOne(Mitra::class, 'user_id');
    }

    /**
     * Return an array of user ids consisting of the given user's id
     * plus all descendant subordinate ids (recursive, multi-level).
     * Suitable for small-to-medium org trees.
     *
     * Usage: $allowed = User::descendantIdsOf($user->id);
     *
     * @param  int|string  $userId
     * @return array<string>
     */
    public static function descendantIdsOf($userId): array
    {
        $collected = [];
        $queue = [$userId];

        while (! empty($queue)) {
            $level = User::whereIn('leader_id', $queue)->pluck('id')->toArray();
            // remove any ids we've already collected
            $level = array_values(array_diff($level, $collected));
            if (empty($level)) {
                break;
            }
            $collected = array_merge($collected, $level);
            $queue = $level;
        }

        // ensure caller is included first
        array_unshift($collected, $userId);
        // unique string ids (support UUIDs)
        $collected = array_values(array_unique(array_map('strval', $collected)));
        return $collected;
    }

    /**
     * Normalize visibility IDs to a unique string array (supports UUIDs).
     */
    protected function normalizeVisibilityIds(array $ids): array
    {
        $normalized = array_map(static fn ($id) => (string) $id, $ids);
        return array_values(array_unique($normalized));
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include karyawan.
     */
    public function scopeKaryawan($query)
    {
        return $query->where('tipe_user', 'karyawan');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
