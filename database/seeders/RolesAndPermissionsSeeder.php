<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos de gestión de usuarios únicamente
        $permisos = [
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear solo rol Administrador
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // Asignar todos los permisos al Administrador
        $adminRole->givePermissionTo(Permission::all());

        // Crear usuario administrador por defecto
        $admin = User::firstOrCreate(
            ['email' => 'admin@manzer.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        // Asignar rol de Administrador
        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }
    }
}
