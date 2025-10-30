<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingService;

class LandingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'icon_class' => 'bi bi-fire',
                'title' => 'Hood Cleaning',
                'description' => 'Our professional team specializes in thorough hood cleaning for commercial kitchens. We ensure that your kitchen exhaust systems are free from grease buildup and fire hazards, keeping your workspace safe and compliant with regulations.',
                'order' => 1,
            ],
            [
                'icon_class' => 'bi bi-shield-check',
                'title' => 'Sanitation Services',
                'description' => 'We provide comprehensive sanitation services to maintain a clean and hygienic environment in your commercial space. Our experts use industry-standard disinfectants to eliminate bacteria, viruses, and germs, ensuring the well-being of your staff and customers.',
                'order' => 2,
            ],
            [
                'icon_class' => 'bi bi-thermometer-half',
                'title' => 'Stove and Grill Cleaning',
                'description' => 'We deep clean stoves and grills to remove grease, carbon buildup, and food residues. This not only enhances the longevity of your equipment but also ensures that your food preparation areas meet the highest hygiene standards.',
                'order' => 3,
            ],
            [
                'icon_class' => 'bi bi-snow',
                'title' => 'Refrigerator Cleaning',
                'description' => 'We offer professional cleaning of commercial refrigerators, ensuring a clean and safe storage environment for your perishable goods. Our services help maintain food quality and reduce the risk of contamination.',
                'order' => 4,
            ],
            [
                'icon_class' => 'bi bi-house-door',
                'title' => 'Basic Cleaning',
                'description' => 'Our basic residential cleaning service covers essential tasks like dusting, vacuuming, mopping, and sanitizing common living areas, ensuring a clean and tidy home for your everyday comfort.',
                'order' => 5,
            ],
            [
                'icon_class' => 'bi bi-stars',
                'title' => 'Deep Cleaning',
                'description' => 'For a more thorough and comprehensive clean, our deep cleaning service goes beyond the basics. We pay attention to every nook and cranny, tackling accumulated grime, dirt, and dust. Ideal for periodic deep cleans or when moving in/out.',
                'order' => 6,
            ],
            [
                'icon_class' => 'bi bi-balloon',
                'title' => 'Special Occasions',
                'description' => 'Whether you\'re hosting a party, celebrating a special event, or having guests over, our special occasion cleaning service ensures your home is spotless and ready to impress. We\'ll take care of the cleaning so you can focus on the celebration.',
                'order' => 7,
            ],
            [
                'icon_class' => 'bi bi-tools',
                'title' => 'Construction Cleaning',
                'description' => 'After a construction or renovation project, our construction cleaning service helps you rid your home of construction debris, dust, and dirt. We\'ll leave your space clean, safe, and ready for you to enjoy.',
                'order' => 8,
            ],
        ];

        foreach ($services as $service) {
            LandingService::create($service);
        }
    }
}
