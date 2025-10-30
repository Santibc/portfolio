<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingHeroValue;

class LandingHeroValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            [
                'icon_class' => 'bi bi-shield-check',
                'title' => 'Trusted & Insured',
                'order' => 1,
            ],
            [
                'icon_class' => 'bi bi-lightning-charge',
                'title' => 'Fast Service',
                'order' => 2,
            ],
            [
                'icon_class' => 'bi bi-award',
                'title' => 'Professional',
                'order' => 3,
            ],
            [
                'icon_class' => 'bi bi-tree',
                'title' => 'Eco-Friendly',
                'order' => 4,
            ],
            [
                'icon_class' => 'bi bi-patch-check',
                'title' => 'Guaranteed',
                'order' => 5,
            ],
            [
                'icon_class' => 'bi bi-clock',
                'title' => 'Flexible Hours',
                'order' => 6,
            ],
            [
                'icon_class' => 'bi bi-people',
                'title' => 'Experienced Team',
                'order' => 7,
            ],
            [
                'icon_class' => 'bi bi-star',
                'title' => 'Quality Work',
                'order' => 8,
            ],
        ];

        foreach ($values as $value) {
            LandingHeroValue::create($value);
        }
    }
}
