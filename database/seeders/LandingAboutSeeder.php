<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingAbout;

class LandingAboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LandingAbout::create([
            'page_title' => 'About Us',
            'page_subtitle' => 'Learn more about Clean Me',
            'main_image_path' => 'images/limpieza.png',
            'purpose_title' => 'Our Purpose',
            'purpose_content' => 'Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.',
            'mission_title' => 'Our Mission',
            'mission_content' => 'Since 2009, our goal has remained the same—to provide reliable services and make sure our clients know we are professionals they can trust. We focus on delivering top-quality cleaning solutions that exceed expectations.',
            'vision_title' => 'Our Vision',
            'vision_content' => 'We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep. We aim to be the most trusted cleaning company in Wisconsin.',
            'stats_years_experience' => 16,
            'stats_happy_clients' => 500,
            'stats_client_satisfaction' => 100,
            'value1_icon' => 'bi bi-award',
            'value1_title' => 'Quality Assurance',
            'value1_description' => 'We use eco-friendly cleaning products and employ highly trained professionals to deliver exceptional results every time.',
            'value2_icon' => 'bi bi-people',
            'value2_title' => 'Customer Focus',
            'value2_description' => 'Your satisfaction is our priority. We tailor our services to meet your specific needs and exceed your expectations.',
            'value3_icon' => 'bi bi-clock-history',
            'value3_title' => 'Reliability',
            'value3_description' => 'Since 2009, we\'ve built our reputation on consistent, dependable service that you can count on.',
            'is_active' => true,
        ]);
    }
}
