<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LandingProgram extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'landing_programs';

    protected $fillable = [
        'name',
        'slug',
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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $base = Str::slug($model->name);
                $slug = $base ?: ('program-' . time());
                $i = 1;
                while (\DB::table($model->getTable())->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });

        static::updating(function ($model) {
            $dirty = $model->getDirty();
            if (array_key_exists('name', $dirty)) {
                $currentSlug = $model->slug ?? '';
                if (empty($currentSlug) || $currentSlug === Str::slug($model->getOriginal('name'))) {
                    $base = Str::slug($model->name) ?: ('program-' . $model->id);
                    $slug = $base;
                    $i = 1;
                    while (\DB::table($model->getTable())->where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                        $slug = $base . '-' . $i++;
                    }
                    $model->slug = $slug;
                }
            }
        });
    }
}
