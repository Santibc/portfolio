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

        // Crear permisos
        $permisos = [
            // Usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',

            // Categorías
            'ver_categorias',
            'crear_categorias',
            'editar_categorias',
            'eliminar_categorias',

            // Cursos
            'ver_cursos',
            'crear_cursos',
            'editar_cursos',
            'eliminar_cursos',

            // Videos
            'ver_videos',
            'crear_videos',
            'editar_videos',
            'eliminar_videos',

            // Notas
            'ver_notas',
            'crear_notas',
            'editar_notas',
            'eliminar_notas',

            // Reportes
            'ver_reportes',
            'exportar_reportes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $estudianteRole = Role::firstOrCreate(['name' => 'Estudiante']);

        // Asignar todos los permisos al Administrador
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos limitados al Estudiante
        $estudianteRole->givePermissionTo([
            'ver_categorias',
            'ver_cursos',
            'ver_videos',
            'ver_notas',
            'crear_notas',
            'editar_notas',
            'eliminar_notas',
        ]);

        // Crear usuario administrador por defecto
        $admin = User::firstOrCreate(
            ['email' => 'admin@gva.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        // Asignar rol de Administrador
        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        // Eliminar el rol "Usuario" anterior si existe (opcional, para limpieza)
        $usuarioRole = Role::where('name', 'Usuario')->first();
        if ($usuarioRole) {
            // Migrar usuarios con rol "Usuario" a "Estudiante"
            $usuarios = User::role('Usuario')->get();
            foreach ($usuarios as $usuario) {
                $usuario->removeRole('Usuario');
                $usuario->assignRole('Estudiante');
            }
        }
    }
}
