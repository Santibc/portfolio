<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CleanerHourPrice;

class CleanerHourPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prices = [];
        $order = 1;

        // Generar precios para 1-5 limpiadores y 1-8 horas
        for ($cleaners = 1; $cleaners <= 5; $cleaners++) {
            for ($hours = 1; $hours <= 8; $hours++) {
                // Precio base: $30 por hora por limpiador
                $basePrice = 30.00;
                $price = $basePrice * $cleaners * $hours;

                // Descuento por más limpiadores
                if ($cleaners >= 3) {
                    $price *= 0.95; // 5% descuento
                }
                if ($cleaners >= 4) {
                    $price *= 0.93; // 7% descuento adicional
                }

                $prices[] = [
                    'num_cleaners' => $cleaners,
                    'num_hours' => $hours,
                    'price' => round($price, 2),
                    'order' => $order++,
                    'is_active' => true,
                ];
            }
        }

        foreach ($prices as $price) {
            CleanerHourPrice::create($price);
        }
    }
}
