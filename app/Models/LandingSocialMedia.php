<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSocialMedia extends Model
{
    use HasFactory;

    protected $table = 'landing_social_media';

    protected $fillable = [
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
