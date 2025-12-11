<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingProfile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'landing_profiles';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'email',
        'phone_number',
        'address',
        'bank_account_1',
        'bank_account_2',
        'about',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'address' => 'array',
        'bank_account_1' => 'array',
    ];
}
