<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingPricingConfig;
use App\Models\LandingPricingRange;
use Illuminate\Support\Facades\DB;

class LandingPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar tablas
        DB::table('landing_pricing_ranges')->truncate();
        DB::table('landing_pricing_config')->truncate();

        // Crear configuración principal
        LandingPricingConfig::create([
            'whatsapp_number' => '573202230467',
            'extra_heavy_duty' => 150.00,
            'inside_fridge_ea' => 50.00,
            'inside_oven_ea' => 50.00,
            'post_construction_government' => 0.90,
            'post_construction_private' => 0.60,
            'window_clean_interior' => 8.00,
            'window_clean_exterior' => 10.00,
            'recurring_weekly_discount' => 20,
            'recurring_biweekly_discount' => 15,
        ]);

        // Rangos de precios basados en la imagen
        $pricingRanges = [
            [
                'sq_ft_min' => 0,
                'sq_ft_max' => 1000,
                'initial_clean' => 280.00,
                'weekly' => 144.00,
                'biweekly' => 153.00,
                'monthly' => 180.00,
                'deep_clean' => 400.00,
                'move_out_clean' => 500.00,
                'order' => 1,
            ],
            [
                'sq_ft_min' => 1001,
                'sq_ft_max' => 1500,
                'initial_clean' => 300.00,
                'weekly' => 160.00,
                'biweekly' => 170.00,
                'monthly' => 200.00,
                'deep_clean' => 450.00,
                'move_out_clean' => 550.00,
                'order' => 2,
            ],
            [
                'sq_ft_min' => 1501,
                'sq_ft_max' => 2000,
                'initial_clean' => 340.00,
                'weekly' => 192.00,
                'biweekly' => 204.00,
                'monthly' => 240.00,
                'deep_clean' => 500.00,
                'move_out_clean' => 600.00,
                'order' => 3,
            ],
            [
                'sq_ft_min' => 2001,
                'sq_ft_max' => 2500,
                'initial_clean' => 400.00,
                'weekly' => 240.00,
                'biweekly' => 255.00,
                'monthly' => 300.00,
                'deep_clean' => 600.00,
                'move_out_clean' => 750.00,
                'order' => 4,
            ],
            [
                'sq_ft_min' => 2501,
                'sq_ft_max' => 3000,
                'initial_clean' => 460.00,
                'weekly' => 288.00,
                'biweekly' => 306.00,
                'monthly' => 360.00,
                'deep_clean' => 720.00,
                'move_out_clean' => 900.00,
                'order' => 5,
            ],
            [
                'sq_ft_min' => 3001,
                'sq_ft_max' => 3500,
                'initial_clean' => 520.00,
                'weekly' => 336.00,
                'biweekly' => 357.00,
                'monthly' => 420.00,
                'deep_clean' => 840.00,
                'move_out_clean' => 1050.00,
                'order' => 6,
            ],
            [
                'sq_ft_min' => 3501,
                'sq_ft_max' => 4000,
                'initial_clean' => 580.00,
                'weekly' => 384.00,
                'biweekly' => 408.00,
                'monthly' => 480.00,
                'deep_clean' => 960.00,
                'move_out_clean' => 1200.00,
                'order' => 7,
            ],
            [
                'sq_ft_min' => 4001,
                'sq_ft_max' => 4500,
                'initial_clean' => 640.00,
                'weekly' => 432.00,
                'biweekly' => 459.00,
                'monthly' => 540.00,
                'deep_clean' => 1080.00,
                'move_out_clean' => 1350.00,
                'order' => 8,
            ],
            [
                'sq_ft_min' => 4501,
                'sq_ft_max' => 5000,
                'initial_clean' => 700.00,
                'weekly' => 480.00,
                'biweekly' => 510.00,
                'monthly' => 600.00,
                'deep_clean' => 1200.00,
                'move_out_clean' => 1500.00,
                'order' => 9,
            ],
        ];

        foreach ($pricingRanges as $range) {
            LandingPricingRange::create($range);
        }
    }
}
