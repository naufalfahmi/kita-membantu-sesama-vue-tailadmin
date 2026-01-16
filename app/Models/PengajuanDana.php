<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanDana extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pengajuan_danas';

    protected $fillable = [
        'fundraiser_id',
        'program_id',
        'submission_type',
        'amount',
        'used_at',
        'purpose',
        'kantor_cabang_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'used_at' => 'date:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function fundraiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fundraiser_id');
    }

    public function kantorCabang(): BelongsTo
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function disbursements()
    {
        return $this->hasMany(PengajuanDanaDisbursement::class, 'pengajuan_dana_id');
    }

    public function penyalurans(): HasMany
    {
        return $this->hasMany(Penyaluran::class, 'pengajuan_dana_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PengajuanDanaApproval::class, 'pengajuan_dana_id');
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(PengajuanDanaApproval::class, 'pengajuan_dana_id')->latestOfMany();
    }
}
