<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Remunerasi extends Model
{
    use HasFactory;

    protected $table = 'remunerasis';

    // UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'bulan_remunerasi',
        'tahun_remunerasi',
        'gaji_pokok',
        'take_home_pay',
        'tanggal',
        'karyawan_id',
        'kantor_cabang_id',
    ];

    protected $casts = [
        'bulan_remunerasi' => 'integer',
        'tahun_remunerasi' => 'integer',
        'gaji_pokok' => 'integer',
        'take_home_pay' => 'integer',
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function kantorCabang()
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id', 'id');
    }

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id', 'id');
    }
}
