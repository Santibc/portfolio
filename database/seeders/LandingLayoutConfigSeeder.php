<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingLayoutConfig;

class LandingLayoutConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LandingLayoutConfig::create([
            'site_title' => 'Clean Me',
            'topbar_email' => 'info@cleanme.com',
            'topbar_phone' => '+1 5589 55488 55',
            'twitter_url' => 'https://twitter.com/cleanme',
            'facebook_url' => 'https://www.facebook.com/cleanme',
            'instagram_url' => 'https://www.instagram.com/cleanme',
            'linkedin_url' => 'https://linkedin.com/company/cleanme',
            'footer_address' => 'A108 Adam Street',
            'footer_city' => 'New York, NY 535022',
            'footer_phone' => '+1 5589 55488 55',
            'footer_email' => 'info@cleanme.com',
            'copyright_company' => 'Clean Me',
            'footer_description' => 'Excellence and professionalism in residential and commercial cleaning services.',
            'footer_logo_path' => 'images/logo.png',
            'is_active' => true,
        ]);
    }
}
