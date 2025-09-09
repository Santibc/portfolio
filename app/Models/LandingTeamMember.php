<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingTeamMember extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'position',
        'description',
        'image_path',
        'twitter_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
