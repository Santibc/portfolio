<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;
    
    protected $table = 'seo';
    
    protected $fillable = [
        'page_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'focus_keyword',
        'og_title',
        'og_description',
        'og_image_path',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image_path',
        'schema_type',
        'schema_data',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'schema_data' => 'array',
    ];
    
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
