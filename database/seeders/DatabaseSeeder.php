<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PaisSeeder::class,
            DepartamentoSeeder::class,
            CiudadSeeder::class,
            DistrictsSeeder::class,
            LandingLayoutConfigSeeder::class,
            LandingHomeConfigSeeder::class,
            LandingAboutSeeder::class,
            LandingServiceSeeder::class,
            LandingContactInfoSeeder::class,
            LandingHeroValueSeeder::class,
            LandingTestimonialSeeder::class,
            ServiceExtraSeeder::class,
            RoomTypePriceSeeder::class,
            CleanerHourPriceSeeder::class,
        ]);

    }
}
