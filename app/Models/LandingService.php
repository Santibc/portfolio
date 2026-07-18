<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingService extends Model
{
    use HasFactory;

    protected $table = 'landing_services';

    protected $fillable = [
        'slug',
        'icon_class',
        'title',
        'subtitle',
        'short_description',
        'hero_image_path',
        'description',
        'content_html',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'canonical_url',
        'robots',
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
        'order',
        'is_active',
        'is_published',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'schema_data' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function (self $service) {
            if (empty($service->slug)) {
                $service->slug = self::uniqueSlug($service->title, $service->id);
            }
        });
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'service-' . uniqid();
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true)->where('is_published', true);
    }

    public function getUrlAttribute(): string
    {
        return $this->slug ? route('services.show', $this->slug) : route('servicios');
    }
}
