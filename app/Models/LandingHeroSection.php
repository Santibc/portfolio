<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHeroSection extends Model
{
    use HasFactory;

    protected $table = 'landing_hero_section';

    protected $fillable = [
        'subtitle',
        'hero_image_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
