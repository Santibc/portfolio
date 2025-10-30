<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceExtra;

class ServiceExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $extras = [
            ['name' => 'Clean Oven', 'icon_class' => 'bi bi-thermometer-half', 'price' => 50.00, 'order' => 1],
            ['name' => 'Clean Inside Fridge', 'icon_class' => 'bi bi-snow', 'price' => 40.00, 'order' => 2],
            ['name' => 'Clean Dirty Dishes In Kitchen', 'icon_class' => 'bi bi-droplet', 'price' => 30.00, 'order' => 3],
            ['name' => 'Small Balcony', 'icon_class' => 'bi bi-building', 'price' => 25.00, 'order' => 4],
            ['name' => 'Large Balcony', 'icon_class' => 'bi bi-building', 'price' => 45.00, 'order' => 5],
            ['name' => 'Inside Cupboards', 'icon_class' => 'bi bi-box', 'price' => 60.00, 'order' => 6],
            ['name' => 'Spot Clean Walls 1-3 Bedrooms', 'icon_class' => 'bi bi-palette', 'price' => 35.00, 'order' => 7],
            ['name' => 'Wash All Walls 1-3 Bedrooms', 'icon_class' => 'bi bi-palette', 'price' => 80.00, 'order' => 8],
            ['name' => 'Laundry - 1 Load', 'icon_class' => 'bi bi-recycle', 'price' => 20.00, 'order' => 9],
            ['name' => 'Inside windows for 1-3 bedrooms', 'icon_class' => 'bi bi-window', 'price' => 45.00, 'order' => 10],
            ['name' => 'Wet Wipes Blinds per fixture', 'icon_class' => 'bi bi-menu-button-wide', 'price' => 15.00, 'order' => 11],
            ['name' => 'Move in/out Clean', 'icon_class' => 'bi bi-box-arrow-right', 'price' => 100.00, 'order' => 12],
            ['name' => 'Inside windows for 4 bed and +', 'icon_class' => 'bi bi-window', 'price' => 70.00, 'order' => 13],
            ['name' => 'Clean balcony', 'icon_class' => 'bi bi-building', 'price' => 35.00, 'order' => 14],
            ['name' => 'Garage sweep and tidy', 'icon_class' => 'bi bi-house-door', 'price' => 40.00, 'order' => 15],
            ['name' => 'Change Bed Linen', 'icon_class' => 'bi bi-lamp', 'price' => 25.00, 'order' => 16],
            ['name' => 'Change Bedsheets(I)', 'icon_class' => 'bi bi-lamp', 'price' => 15.00, 'order' => 17],
        ];

        foreach ($extras as $extra) {
            ServiceExtra::create($extra);
        }
    }
}
