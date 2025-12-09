<?php

namespace Database\Seeders;

use App\Models\LandingConfiguracion;
use Illuminate\Database\Seeder;

class LandingConfiguracionSeeder extends Seeder
{
    public function run()
    {
        LandingConfiguracion::create([
            'logo' => 'images/logo1.png',
            'favicon' => null,
            'footer_texto' => 'Potenciando al talento creativo de Colombia.',
            'terminos_url' => '#',
            'whatsapp_numero' => '573001234567',
            'whatsapp_texto' => 'Contáctanos vía Whatsapp',
            'facebook_url' => '#',
            'tiktok_url' => '#',
            'instagram_url' => '#',
            'linkedin_url' => '#',
            'contacto_email' => 'contacto@betogether.com.co',
        ]);
    }
}
