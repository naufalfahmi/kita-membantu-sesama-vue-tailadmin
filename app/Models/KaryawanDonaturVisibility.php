<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaryawanDonaturVisibility extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'karyawan_donatur_visibilities';

    protected $fillable = [
        'karyawan_id',
        'visible_karyawan_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function visibleKaryawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visible_karyawan_id');
    }
}
