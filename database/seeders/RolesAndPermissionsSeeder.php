<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles para AGROMARKET
        Role::firstOrCreate(['name' => 'Inversionista']);
        Role::firstOrCreate(['name' => 'Agricultor']);
        Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Supervisor']);
        Role::firstOrCreate(['name' => 'Vendedor']);

        // Crear permisos básicos (se pueden expandir según necesidades)
        $permisos = [
            // Proyectos
            'ver_proyectos',
            'crear_proyectos',
            'editar_proyectos',
            'aprobar_proyectos',
            'eliminar_proyectos',

            // Inversiones
            'ver_inversiones',
            'crear_inversiones',
            'gestionar_inversiones',

            // Usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',

            // KYC
            'revisar_kyc',
            'aprobar_kyc',

            // Transacciones
            'ver_transacciones',
            'aprobar_depositos',
            'aprobar_retiros',
            'procesar_pagos',

            // Dividendos
            'gestionar_dividendos',
            'pagar_dividendos',

            // CRM
            'gestionar_prospectos',
            'ver_reportes',
            'generar_reportes',

            // Configuración
            'gestionar_configuracion',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Asignar permisos a roles
        $adminRole = Role::findByName('Administrador');
        $adminRole->givePermissionTo(Permission::all());

        $supervisorRole = Role::findByName('Supervisor');
        $supervisorRole->givePermissionTo([
            'ver_proyectos', 'aprobar_proyectos',
            'ver_inversiones', 'gestionar_inversiones',
            'revisar_kyc', 'aprobar_kyc',
            'ver_transacciones', 'aprobar_depositos', 'aprobar_retiros',
            'gestionar_dividendos', 'pagar_dividendos',
            'ver_reportes', 'generar_reportes'
        ]);

        $agricultorRole = Role::findByName('Agricultor');
        $agricultorRole->givePermissionTo([
            'ver_proyectos', 'crear_proyectos', 'editar_proyectos'
        ]);

        $inversionistaRole = Role::findByName('Inversionista');
        $inversionistaRole->givePermissionTo([
            'ver_proyectos', 'ver_inversiones', 'crear_inversiones'
        ]);

        $vendedorRole = Role::findByName('Vendedor');
        $vendedorRole->givePermissionTo([
            'gestionar_prospectos', 'ver_usuarios', 'crear_usuarios'
        ]);
    }
}
