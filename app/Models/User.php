<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

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
