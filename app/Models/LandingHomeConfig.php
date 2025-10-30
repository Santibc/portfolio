<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHomeConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_description',
        'hero_image_path',
        'hero_services_button_url',
        'hero_estimate_button_url',
        'about_title',
        'about_lead',
        'about_description',
        'about_image_path',
        'about_years_experience',
        'about_happy_clients',
        'about_client_satisfaction',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'about_years_experience' => 'integer',
        'about_happy_clients' => 'integer',
        'about_client_satisfaction' => 'integer',
    ];
}
