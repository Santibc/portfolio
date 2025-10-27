<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            // New South Wales (Sydney)
            ['name' => 'Sydney CBD', 'state' => 'NSW', 'postcode' => '2000', 'order' => 1],
            ['name' => 'Bondi', 'state' => 'NSW', 'postcode' => '2026', 'order' => 2],
            ['name' => 'Parramatta', 'state' => 'NSW', 'postcode' => '2150', 'order' => 3],
            ['name' => 'Manly', 'state' => 'NSW', 'postcode' => '2095', 'order' => 4],
            ['name' => 'Chatswood', 'state' => 'NSW', 'postcode' => '2067', 'order' => 5],
            ['name' => 'Cronulla', 'state' => 'NSW', 'postcode' => '2230', 'order' => 6],
            ['name' => 'Newtown', 'state' => 'NSW', 'postcode' => '2042', 'order' => 7],
            ['name' => 'Surry Hills', 'state' => 'NSW', 'postcode' => '2010', 'order' => 8],
            ['name' => 'Paddington', 'state' => 'NSW', 'postcode' => '2021', 'order' => 9],
            ['name' => 'Bankstown', 'state' => 'NSW', 'postcode' => '2200', 'order' => 10],

            // Victoria (Melbourne)
            ['name' => 'Melbourne CBD', 'state' => 'VIC', 'postcode' => '3000', 'order' => 11],
            ['name' => 'St Kilda', 'state' => 'VIC', 'postcode' => '3182', 'order' => 12],
            ['name' => 'Richmond', 'state' => 'VIC', 'postcode' => '3121', 'order' => 13],
            ['name' => 'Carlton', 'state' => 'VIC', 'postcode' => '3053', 'order' => 14],
            ['name' => 'Fitzroy', 'state' => 'VIC', 'postcode' => '3065', 'order' => 15],
            ['name' => 'Brighton', 'state' => 'VIC', 'postcode' => '3186', 'order' => 16],
            ['name' => 'South Yarra', 'state' => 'VIC', 'postcode' => '3141', 'order' => 17],
            ['name' => 'Brunswick', 'state' => 'VIC', 'postcode' => '3056', 'order' => 18],
            ['name' => 'Collingwood', 'state' => 'VIC', 'postcode' => '3066', 'order' => 19],
            ['name' => 'Hawthorn', 'state' => 'VIC', 'postcode' => '3122', 'order' => 20],

            // Queensland (Brisbane)
            ['name' => 'Brisbane CBD', 'state' => 'QLD', 'postcode' => '4000', 'order' => 21],
            ['name' => 'Fortitude Valley', 'state' => 'QLD', 'postcode' => '4006', 'order' => 22],
            ['name' => 'South Bank', 'state' => 'QLD', 'postcode' => '4101', 'order' => 23],
            ['name' => 'New Farm', 'state' => 'QLD', 'postcode' => '4005', 'order' => 24],
            ['name' => 'West End', 'state' => 'QLD', 'postcode' => '4101', 'order' => 25],
            ['name' => 'Kangaroo Point', 'state' => 'QLD', 'postcode' => '4169', 'order' => 26],
            ['name' => 'Indooroopilly', 'state' => 'QLD', 'postcode' => '4068', 'order' => 27],
            ['name' => 'Paddington', 'state' => 'QLD', 'postcode' => '4064', 'order' => 28],
            ['name' => 'Chermside', 'state' => 'QLD', 'postcode' => '4032', 'order' => 29],
            ['name' => 'Sunnybank', 'state' => 'QLD', 'postcode' => '4109', 'order' => 30],

            // Western Australia (Perth)
            ['name' => 'Perth CBD', 'state' => 'WA', 'postcode' => '6000', 'order' => 31],
            ['name' => 'Fremantle', 'state' => 'WA', 'postcode' => '6160', 'order' => 32],
            ['name' => 'Subiaco', 'state' => 'WA', 'postcode' => '6008', 'order' => 33],
            ['name' => 'Northbridge', 'state' => 'WA', 'postcode' => '6003', 'order' => 34],
            ['name' => 'Cottesloe', 'state' => 'WA', 'postcode' => '6011', 'order' => 35],
            ['name' => 'Scarborough', 'state' => 'WA', 'postcode' => '6019', 'order' => 36],
            ['name' => 'Joondalup', 'state' => 'WA', 'postcode' => '6027', 'order' => 37],
            ['name' => 'Morley', 'state' => 'WA', 'postcode' => '6062', 'order' => 38],
            ['name' => 'Victoria Park', 'state' => 'WA', 'postcode' => '6100', 'order' => 39],
            ['name' => 'Leederville', 'state' => 'WA', 'postcode' => '6007', 'order' => 40],

            // South Australia (Adelaide)
            ['name' => 'Adelaide CBD', 'state' => 'SA', 'postcode' => '5000', 'order' => 41],
            ['name' => 'North Adelaide', 'state' => 'SA', 'postcode' => '5006', 'order' => 42],
            ['name' => 'Glenelg', 'state' => 'SA', 'postcode' => '5045', 'order' => 43],
            ['name' => 'Norwood', 'state' => 'SA', 'postcode' => '5067', 'order' => 44],
            ['name' => 'Unley', 'state' => 'SA', 'postcode' => '5061', 'order' => 45],
            ['name' => 'Prospect', 'state' => 'SA', 'postcode' => '5082', 'order' => 46],
            ['name' => 'Burnside', 'state' => 'SA', 'postcode' => '5066', 'order' => 47],
            ['name' => 'Hahndorf', 'state' => 'SA', 'postcode' => '5245', 'order' => 48],
            ['name' => 'Port Adelaide', 'state' => 'SA', 'postcode' => '5015', 'order' => 49],
            ['name' => 'Glenelg North', 'state' => 'SA', 'postcode' => '5045', 'order' => 50],

            // Australian Capital Territory (Canberra)
            ['name' => 'Canberra City', 'state' => 'ACT', 'postcode' => '2601', 'order' => 51],
            ['name' => 'Belconnen', 'state' => 'ACT', 'postcode' => '2617', 'order' => 52],
            ['name' => 'Woden', 'state' => 'ACT', 'postcode' => '2606', 'order' => 53],
            ['name' => 'Tuggeranong', 'state' => 'ACT', 'postcode' => '2900', 'order' => 54],
            ['name' => 'Gungahlin', 'state' => 'ACT', 'postcode' => '2912', 'order' => 55],

            // Tasmania (Hobart)
            ['name' => 'Hobart CBD', 'state' => 'TAS', 'postcode' => '7000', 'order' => 56],
            ['name' => 'Sandy Bay', 'state' => 'TAS', 'postcode' => '7005', 'order' => 57],
            ['name' => 'North Hobart', 'state' => 'TAS', 'postcode' => '7000', 'order' => 58],
            ['name' => 'Battery Point', 'state' => 'TAS', 'postcode' => '7004', 'order' => 59],
            ['name' => 'Launceston', 'state' => 'TAS', 'postcode' => '7250', 'order' => 60],

            // Northern Territory (Darwin)
            ['name' => 'Darwin City', 'state' => 'NT', 'postcode' => '0800', 'order' => 61],
            ['name' => 'Casuarina', 'state' => 'NT', 'postcode' => '0810', 'order' => 62],
            ['name' => 'Parap', 'state' => 'NT', 'postcode' => '0820', 'order' => 63],
            ['name' => 'Fannie Bay', 'state' => 'NT', 'postcode' => '0820', 'order' => 64],
            ['name' => 'Alice Springs', 'state' => 'NT', 'postcode' => '0870', 'order' => 65],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}
