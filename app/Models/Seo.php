<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';

    protected $fillable = [
        'page_id', 'meta_title', 'meta_description', 'meta_keywords',
        'canonical_url', 'robots', 'focus_keyword', 'is_active',
        'og_title', 'og_description', 'og_image', 'og_type',
        'schema_type', 'schema_data'
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
