<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingConfiguracion extends Model
{
    use HasFactory;

    protected $table = 'landing_configuracion';

    protected $fillable = [
        'logo',
        'favicon',
        'footer_texto',
        'terminos_url',
        'whatsapp_numero',
        'whatsapp_texto',
        'facebook_url',
        'tiktok_url',
        'instagram_url',
        'linkedin_url',
        'contacto_email',
    ];

    public static function obtener()
    {
        return self::first() ?? new self();
    }
}
