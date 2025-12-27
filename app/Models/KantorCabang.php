<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KantorCabang extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kantor_cabang';

    protected $fillable = [
        'kode',
        'nama',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'alamat',
        'latitude',
        'longitude',
        'radius',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
    ];

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
     * Many-to-many users assigned to this kantor cabang.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kantor_cabang_user', 'kantor_cabang_id', 'user_id');
    }
}
