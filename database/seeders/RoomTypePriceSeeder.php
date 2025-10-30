<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomTypePrice;

class RoomTypePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roomTypes = [
            ['room_type' => 'bathroom', 'price' => 50.00, 'order' => 1],
            ['room_type' => 'bedroom', 'price' => 60.00, 'order' => 2],
            ['room_type' => 'kitchen', 'price' => 70.00, 'order' => 3],
            ['room_type' => 'other', 'price' => 40.00, 'order' => 4],
        ];

        foreach ($roomTypes as $roomType) {
            RoomTypePrice::create($roomType);
        }
    }
}
