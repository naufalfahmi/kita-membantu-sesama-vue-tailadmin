<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
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
        'nama',
        'password',
        'email',
        'no_handphone',
        'nama_bank',
        'no_rekening',
        'tanggal_lahir',
        'pendidikan',
        'jabatan_id',
        'user_id',
        'kantor_cabang_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Hidden attributes for arrays / JSON
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the kantor cabang associated with the mitra.
     */
    public function kantorCabang(): BelongsTo
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'jabatan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(\App\Models\MitraPayroll::class, 'mitra_id');
    }

    /**
     * Get the user who created the mitra.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the mitra.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the mitra.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
