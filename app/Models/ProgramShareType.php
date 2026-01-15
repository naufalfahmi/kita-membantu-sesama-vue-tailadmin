<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramShareType extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'program_share_types';

    protected $fillable = ['name', 'key', 'alias', 'default_type', 'orders', 'program_id', 'created_by', 'updated_by', 'deleted_by'];

    protected $casts = [
        'orders' => 'integer',
        'program_id' => 'string',
    ];

    public function shares(): HasMany
    {
        return $this->hasMany(ProgramShare::class, 'program_share_type_id');
    }
}
