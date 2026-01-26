<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingBulletin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'landing_bulletins';

    protected $fillable = [
        'name',
        'slug',
        'date',
        'file',
        'file_name',
        'file_size',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Use non-incrementing string (UUID) primary key
     */
    public $incrementing = false;

    /**
     * Primary key type is string
     */
    protected $keyType = 'string';
}
