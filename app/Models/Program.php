<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'program';

    protected $fillable = [
        'nama_program',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        // dynamic shares stored in `program_shares` relation
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

    public function shares(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProgramShare::class, 'program_id');
    }

    /**
     * Get all transaksis for this program.
     */
    public function transaksis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaksi::class, 'program_id');
    }

    /**
     * Get all pengajuan danas for this program.
     */
    public function pengajuanDanas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PengajuanDana::class, 'program_id');
    }
}
