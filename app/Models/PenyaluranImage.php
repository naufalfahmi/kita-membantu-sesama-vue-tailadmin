<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PenyaluranImage extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['penyaluran_id', 'path', 'caption', 'created_by'];

    public function penyaluran()
    {
        return $this->belongsTo(Penyaluran::class, 'penyaluran_id');
    }
}
