<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'transaksis';

    protected $fillable = [
        'kode',
        'kantor_cabang_id',
        'donatur_id',
        'program_id',
        'mitra_id',
        'fundraiser_id',
        'nominal',
        'tanggal_transaksi',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_transaksi' => 'date',
        ];
    }

    /**
     * Get the kantor cabang that owns the transaksi.
     */
    public function kantorCabang()
    {
        return $this->belongsTo(KantorCabang::class, 'kantor_cabang_id');
    }

    /**
     * Get the donatur that owns the transaksi.
     */
    public function donatur()
    {
        return $this->belongsTo(Donatur::class, 'donatur_id');
    }

    /**
     * Get the program that owns the transaksi.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Get the mitra that owns the transaksi.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Get the fundraiser (user) that owns the transaksi.
     */
    public function fundraiser()
    {
        return $this->belongsTo(User::class, 'fundraiser_id');
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this record.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
