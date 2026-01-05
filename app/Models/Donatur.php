<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donatur extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'kode',
        'nama',
        'jenis_donatur',
        'pic',
        'alamat',
        'provinsi',
        'kota_kab',
        'kecamatan',
        'kelurahan',
        'no_handphone',
        'email',
        'tanggal_lahir',
        'status',
        'kantor_cabang_id',
        'mitra_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jenis_donatur' => 'array',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the kantor cabang associated with the donatur.
     */
    public function kantorCabang(): BelongsTo
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    /**
     * Get the user who created the donatur.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the donatur.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the donatur.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the PIC user (reference to users table).
     */
    public function picUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic');
    }

    /**
     * Get the mitra associated with the donatur.
     */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }
}
