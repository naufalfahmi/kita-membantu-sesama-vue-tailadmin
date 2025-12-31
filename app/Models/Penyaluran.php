<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penyaluran extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pengajuan_dana_id', 'program_name', 'pic', 'village', 'district', 'city', 'province', 'postal_code', 'address', 'report', 'amount', 'kantor_cabang_id', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanDana::class, 'pengajuan_dana_id');
    }

    public function images()
    {
        return $this->hasMany(PenyaluranImage::class, 'penyaluran_id');
    }

    public function kantorCabang()
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }
}
