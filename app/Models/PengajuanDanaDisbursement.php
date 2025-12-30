<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDanaDisbursement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pengajuan_dana_disbursements';

    protected $fillable = [
        'pengajuan_dana_id',
        'transaksi_id',
        'program_id',
        'amount',
        'tanggal_disburse',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'tanggal_disburse' => 'date:Y-m-d',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanDana::class, 'pengajuan_dana_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
