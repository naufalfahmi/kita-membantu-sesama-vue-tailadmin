<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingProgram extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'landing_programs';

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'is_active',
        'is_highlight',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_highlight' => 'boolean',
    ];
}
