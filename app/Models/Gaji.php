<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\Permission\Models\Role as Jabatan;
use App\Models\Pangkat;

class Gaji extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'gajis';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'nominal',
        'tipe',
        'tanggal_efektif',
        'jabatan_id',
        'pangkat_id',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'tanggal_efektif',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_id');
    }
}
