<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingHomeConfig;

class LandingHomeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LandingHomeConfig::create([
            'hero_title' => 'CLEAN ME',
            'hero_subtitle' => 'Top Quality Guaranteed',
            'hero_description' => 'At Clean Me, we believe that putting in a lot of hard work ensures the best and fastest service.',
            'hero_image_path' => 'images/mujer.png',
            'hero_services_button_url' => '/servicios',
            'hero_estimate_button_url' => '#contact',
            'about_title' => 'WE ARE CLEAN ME',
            'about_lead' => 'Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.',
            'about_description' => 'We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep.',
            'about_image_path' => 'images/paginaanterior/imagenluegodeltitulo.avif',
            'about_years_experience' => 16,
            'about_happy_clients' => 500,
            'about_client_satisfaction' => 100,
            'facebook_url' => 'https://www.facebook.com/cleanme',
            'instagram_url' => 'https://www.instagram.com/cleanme',
            'linkedin_url' => 'https://www.linkedin.com/company/cleanme',
            'youtube_url' => 'https://www.youtube.com/@cleanme',
            'is_active' => true,
        ]);
    }
}
