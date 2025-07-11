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
        //$this->call(RolesAndPermissionsSeeder::class); // <-- Añade esta línea
        $user = User::where('id',1)->first();

        $user->assignRole('admin'); // Asigna el rol 'admin'


    }
}
