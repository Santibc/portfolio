<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingContactInfo;

class LandingContactInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LandingContactInfo::create([
            'address' => 'Wisconsin, USA',
            'phone' => '+1 (555) 000-0000',
            'email' => 'info@cleanme.com',
            'description' => 'Contact us today for a free estimate. We are here to help you with all your cleaning needs.',
            'receive_messages_email' => 'admin@cleanme.com',
            'google_maps_embed' => null,
            'is_active' => true,
        ]);
    }
}
