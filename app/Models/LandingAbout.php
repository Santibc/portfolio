<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingAbout extends Model
{
    use HasFactory;
    
    protected $table = 'landing_about';
    
    protected $fillable = [
        'page_title',
        'page_subtitle',
        'main_image_path',
        'purpose_title',
        'purpose_content',
        'mission_title',
        'mission_content',
        'vision_title',
        'vision_content',
        'stats_years_experience',
        'stats_happy_clients',
        'stats_client_satisfaction',
        'value1_icon',
        'value1_title',
        'value1_description',
        'value2_icon',
        'value2_title',
        'value2_description',
        'value3_icon',
        'value3_title',
        'value3_description',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
