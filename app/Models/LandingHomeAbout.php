<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHomeAbout extends Model
{
    use HasFactory;

    protected $table = 'landing_home_about';

    protected $fillable = [
        'image_path',
        'title',
        'lead_text',
        'description',
        'years_experience',
        'happy_clients',
        'client_satisfaction',
        'cta_button_text',
        'cta_button_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
