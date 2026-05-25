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
        'google_search_console_verification',
        'default_og_image_path',
        'google_maps_api_key',
        'google_maps_country',
        'admin_notification_email',
        'customer_email_subject',
        'customer_email_intro',
        'customer_email_next_title',
        'customer_email_next_text',
        'customer_email_footer_text',
        'customer_email_signature',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
