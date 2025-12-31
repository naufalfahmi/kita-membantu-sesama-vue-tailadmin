<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaApproval extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pengajuan_dana_approvals';

    protected $fillable = [
        'id', 'pengajuan_dana_id', 'approver_id', 'decision', 'comment',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanDana::class, 'pengajuan_dana_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
