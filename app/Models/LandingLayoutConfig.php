<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingLayoutConfig extends Model
{
    use HasFactory;
    
    protected $table = 'landing_layout_config';
    
    protected $fillable = [
        'site_title',
        'topbar_email',
        'topbar_phone',
        'twitter_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'footer_address',
        'footer_city',
        'footer_phone',
        'footer_email',
        'copyright_company',
        'footer_description',
        'footer_logo_path',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
