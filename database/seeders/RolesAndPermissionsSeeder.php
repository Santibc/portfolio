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

        // Crear todos los permisos del sistema
        $permisos = [
            // Dashboard
            'ver_dashboard',
            // Usuarios
            'gestionar_usuarios',
            // Configuracion
            'gestionar_configuracion',
            // Tabla precios
            'gestionar_tabla_precios',
            // Clientes
            'ver_clientes', 'crear_clientes', 'editar_clientes',
            // Catalogo items
            'ver_catalogo_items', 'crear_catalogo_items', 'editar_catalogo_items',
            // Bosquejos
            'ver_bosquejos_matriz', 'gestionar_bosquejos_matriz',
            // Ordenes
            'ver_ordenes', 'crear_ordenes', 'editar_ordenes', 'anular_ordenes', 'generar_ordenes',
            // Entregas
            'gestionar_entregas',
            // Pagos
            'ver_pagos', 'crear_pagos', 'aprobar_pagos',
            // Actividades
            'ver_actividades_globales', 'ver_actividades_propias',
            // Operario
            'trabajar_piezas', 'transferir_piezas', 'complementar_ordenes',
            // Consultar precios
            'consultar_precios',
            // Garantias
            'gestionar_garantias',
            // Notificaciones
            'ver_notificaciones',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // === Crear roles con permisos asignados ===

        // Administrador: todos los permisos
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        // Recepcion
        $recepcion = Role::firstOrCreate(['name' => 'Recepcion']);
        $recepcion->syncPermissions([
            'ver_dashboard',
            'ver_clientes', 'crear_clientes', 'editar_clientes',
            'ver_catalogo_items',
            'ver_bosquejos_matriz',
            'ver_ordenes', 'crear_ordenes', 'editar_ordenes', 'anular_ordenes', 'generar_ordenes',
            'gestionar_entregas',
            'ver_pagos', 'crear_pagos',
            'ver_actividades_globales', 'ver_actividades_propias',
            'consultar_precios',
            'gestionar_garantias',
            'ver_notificaciones',
        ]);

        // Contabilidad
        $contabilidad = Role::firstOrCreate(['name' => 'Contabilidad']);
        $contabilidad->syncPermissions([
            'ver_dashboard',
            'ver_catalogo_items',
            'ver_bosquejos_matriz',
            'ver_ordenes',
            'ver_pagos', 'crear_pagos', 'aprobar_pagos',
            'ver_actividades_propias',
            'ver_notificaciones',
        ]);

        // Operario
        $operario = Role::firstOrCreate(['name' => 'Operario']);
        $operario->syncPermissions([
            'ver_dashboard',
            'ver_bosquejos_matriz',
            'ver_ordenes',
            'ver_actividades_propias',
            'trabajar_piezas', 'transferir_piezas', 'complementar_ordenes',
            'ver_notificaciones',
        ]);

        // === Crear usuarios de prueba ===

        $user = User::firstOrCreate(
            ['email' => 'admin@sinden.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );
        if (!$user->hasRole('Administrador')) {
            $user->assignRole('Administrador');
        }

        $user = User::firstOrCreate(
            ['email' => 'recepcion@sinden.com'],
            [
                'name' => 'Usuario Recepcion',
                'password' => Hash::make('password'),
            ]
        );
        if (!$user->hasRole('Recepcion')) {
            $user->assignRole('Recepcion');
        }

        $user = User::firstOrCreate(
            ['email' => 'contabilidad@sinden.com'],
            [
                'name' => 'Usuario Contabilidad',
                'password' => Hash::make('password'),
            ]
        );
        if (!$user->hasRole('Contabilidad')) {
            $user->assignRole('Contabilidad');
        }

        $user = User::firstOrCreate(
            ['email' => 'operario@sinden.com'],
            [
                'name' => 'Usuario Operario',
                'password' => Hash::make('password'),
            ]
        );
        if (!$user->hasRole('Operario')) {
            $user->assignRole('Operario');
        }
    }
}
