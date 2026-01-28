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

        // Vision & Mission fields
        'vision_title',
        'vision_description',
        'mission_description',
        'vision_mission_image',
        'features',

        // CTA fields
        'cta_title',
        'cta_description',
        'cta_button_active',
        'cta_button_link',

        // Hero fields
        'hero_title',
        'hero_description',
        'hero_button_active',
        'hero_button_link',
        'hero_whatsapp_active',
        'hero_whatsapp_number',
        'hero_image',

        // Transparency fields
        'transparency_title',
        'transparency_description',
        'transparency_kantor_cabang_text',
        'transparency_kantor_cabang_total',
        'transparency_donatur_text',
        'transparency_donatur_total',
        'transparency_fundraiser_text',
        'transparency_fundraiser_total',
        'transparency_penggalangan_dana_text',
        'transparency_penggalangan_dana_total',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'address' => 'array',
        'bank_account_1' => 'array',
        'features' => 'array',
        'cta_button_active' => 'boolean',

        // Hero flags
        'hero_button_active' => 'boolean',
        'hero_whatsapp_active' => 'boolean',

        // Transparency totals
        'transparency_kantor_cabang_total' => 'integer',
        'transparency_donatur_total' => 'integer',
        'transparency_fundraiser_total' => 'integer',
        'transparency_penggalangan_dana_total' => 'integer',
    ];
}
