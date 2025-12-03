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
        'persentase_hak_program',
        'persentase_hak_program_operasional',
        'persentase_hak_championship',
        'tipe_pembagian_marketing',
        'persentase_hak_marketing',
        'persentase_hak_operasional_1',
        'persentase_hak_iklan',
        'persentase_hak_operasional_2',
        'persentase_hak_operasional_3',
        'jumlah_persentase',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'persentase_hak_program' => 'decimal:2',
        'persentase_hak_program_operasional' => 'decimal:2',
        'persentase_hak_championship' => 'decimal:2',
        'persentase_hak_marketing' => 'decimal:2',
        'persentase_hak_operasional_1' => 'decimal:2',
        'persentase_hak_iklan' => 'decimal:2',
        'persentase_hak_operasional_2' => 'decimal:2',
        'persentase_hak_operasional_3' => 'decimal:2',
        'jumlah_persentase' => 'decimal:2',
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
}
