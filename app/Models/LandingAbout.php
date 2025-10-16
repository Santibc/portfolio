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
        'main_image_path',
        'purpose_title',
        'purpose_content',
        'mission_title',
        'mission_content',
        'vision_title',
        'vision_content',
        'years_experience',
        'happy_clients',
        'client_satisfaction',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
