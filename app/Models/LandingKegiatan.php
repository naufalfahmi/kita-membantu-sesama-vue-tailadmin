<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LandingKegiatan extends Model
{
    use HasFactory;

    protected $table = 'landing_kegiatan';

    protected $fillable = [
        'title',
        'slug',
        'number_of_recipients',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'address',
        'activity_date',
        'status',
        'description',
        'images',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'number_of_recipients' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->title)) {
                $base = Str::slug($model->title);
                $slug = $base ?: ('kegiatan-' . time());
                $i = 1;
                while (\DB::table($model->getTable())->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });

        static::updating(function ($model) {
            // if title changed and slug is empty or equal to old slugified title, regenerate
            $dirty = $model->getDirty();
            if (array_key_exists('title', $dirty)) {
                $currentSlug = $model->slug ?? '';
                $slugFromTitle = Str::slug($model->title);
                if (empty($currentSlug) || $currentSlug === Str::slug($model->getOriginal('title'))) {
                    $base = $slugFromTitle ?: ('kegiatan-' . $model->id);
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
