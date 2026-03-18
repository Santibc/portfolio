<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingService extends Model
{
    use HasFactory;

    protected $table = 'landing_services';

    protected $fillable = [
        'icon_class', 'title', 'description', 'slug',
        'short_description', 'long_description', 'image_path',
        'gallery_images', 'featured_image_alt', 'page_id',
        'order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gallery_images' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function seo()
    {
        return $this->hasOneThrough(Seo::class, Page::class, 'id', 'page_id', 'page_id', 'id');
    }
}
