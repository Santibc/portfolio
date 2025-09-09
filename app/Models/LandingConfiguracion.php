<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingConfiguracion extends Model
{
    use HasFactory;
    
    protected $table = 'landing_configuracion';
    
    protected $fillable = [
        'company_name',
        'company_description',
        'contact_email',
        'contact_phone',
        'contact_address',
        'google_maps_embed',
        'services_button_url',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
