<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingKegiatan extends Model
{
    use HasFactory;

    protected $table = 'landing_kegiatan';

    protected $fillable = [
        'title',
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
}
