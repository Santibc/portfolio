<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingCarouselImage extends Model
{
    use HasFactory;
    
    protected $table = 'landing_carousel_images';
    
    protected $fillable = [
        'image_path',
        'alt_text',
        'order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
