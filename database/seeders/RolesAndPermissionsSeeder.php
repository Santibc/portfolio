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

        // Crear permisos del sistema
        $permisos = [
            // Usuarios
            'ver_usuarios', 'crear_usuarios', 'editar_usuarios', 'eliminar_usuarios',
            // Trabajadores
            'ver_trabajadores', 'crear_trabajadores', 'editar_trabajadores', 'eliminar_trabajadores',
            // Cuadrillas
            'ver_cuadrillas', 'crear_cuadrillas', 'editar_cuadrillas', 'eliminar_cuadrillas',
            // Clientes
            'ver_clientes', 'crear_clientes', 'editar_clientes', 'eliminar_clientes',
            // Leads
            'ver_leads', 'crear_leads', 'editar_leads', 'eliminar_leads',
            // Obras
            'ver_obras', 'crear_obras', 'editar_obras', 'eliminar_obras', 'ver_rentabilidad_obras',
            // Fichajes
            'ver_fichajes', 'crear_fichajes', 'editar_fichajes', 'eliminar_fichajes', 'validar_fichajes',
            // Partes diarios
            'ver_partes', 'crear_partes', 'editar_partes', 'eliminar_partes', 'validar_partes',
            // Maquinaria
            'ver_maquinaria', 'crear_maquinaria', 'editar_maquinaria', 'eliminar_maquinaria',
            // Vehículos
            'ver_vehiculos', 'crear_vehiculos', 'editar_vehiculos', 'eliminar_vehiculos',
            // Subcontratas
            'ver_subcontratas', 'crear_subcontratas', 'editar_subcontratas', 'eliminar_subcontratas',
            // Contratos
            'ver_contratos', 'crear_contratos', 'editar_contratos', 'eliminar_contratos',
            // Facturación
            'ver_facturas', 'crear_facturas', 'editar_facturas', 'eliminar_facturas',
            // Ingresos/Gastos
            'ver_finanzas', 'crear_finanzas', 'editar_finanzas', 'eliminar_finanzas',
            // EPIs
            'ver_epis', 'crear_epis', 'editar_epis', 'eliminar_epis',
            // Formaciones
            'ver_formaciones', 'crear_formaciones', 'editar_formaciones', 'eliminar_formaciones',
            // Primas
            'ver_primas', 'crear_primas', 'editar_primas',
            // Alertas
            'ver_alertas', 'gestionar_alertas',
            // Dashboard
            'ver_dashboard_admin', 'ver_dashboard_encargado', 'ver_dashboard_trabajador',
            // Auditoría
            'ver_auditoria',
            // Configuración
            'gestionar_configuracion',
            // Documentos
            'subir_documentos_maquinaria',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $roles = [
            'Administrador' => Permission::all()->pluck('name')->toArray(),
            'Contabilidad' => [
                'ver_usuarios', 'ver_trabajadores', 'ver_obras', 'ver_clientes', 'ver_leads',
                'crear_clientes', 'editar_clientes', 'crear_leads', 'editar_leads',
                'ver_fichajes', 'ver_partes', 'ver_maquinaria', 'ver_vehiculos',
                'ver_subcontratas', 'crear_subcontratas', 'editar_subcontratas',
                'ver_contratos', 'crear_contratos', 'editar_contratos',
                'ver_facturas', 'crear_facturas', 'editar_facturas',
                'ver_finanzas', 'crear_finanzas', 'editar_finanzas',
                'ver_epis', 'ver_formaciones', 'ver_primas',
                'ver_alertas', 'ver_auditoria',
            ],
            'Encargado' => [
                'ver_trabajadores', 'ver_cuadrillas', 'editar_cuadrillas',
                'ver_obras',
                'ver_fichajes', 'crear_fichajes', 'editar_fichajes',
                'ver_partes', 'crear_partes', 'editar_partes',
                'ver_maquinaria', 'ver_vehiculos',
                'ver_epis', 'crear_epis', 'editar_epis', 'eliminar_epis', // Acceso completo a EPIs
                'ver_formaciones',
                'ver_contratos',
                'ver_finanzas', 'crear_finanzas', 'editar_finanzas', 'eliminar_finanzas', // Acceso completo a Gastos
                'ver_alertas', 'ver_dashboard_encargado',
                'subir_documentos_maquinaria', // Puede subir documentos de maquinaria
            ],
            'RRHH' => [
                'ver_usuarios', 'ver_trabajadores', 'crear_trabajadores', 'editar_trabajadores', 'eliminar_trabajadores',
                'ver_cuadrillas', 'crear_cuadrillas', 'editar_cuadrillas', 'eliminar_cuadrillas',
                'ver_fichajes', 'ver_partes',
                'ver_maquinaria', 'ver_vehiculos',
                'ver_epis', 'crear_epis', 'editar_epis',
                'ver_formaciones', 'crear_formaciones', 'editar_formaciones',
                'ver_subcontratas', 'ver_contratos',
                'ver_alertas', 'gestionar_alertas',
                'ver_auditoria',
            ],
            'Auditor' => [
                'ver_usuarios', 'ver_trabajadores', 'ver_cuadrillas', 'ver_obras', 'ver_rentabilidad_obras',
                'ver_clientes', 'ver_leads', 'ver_fichajes', 'ver_partes',
                'ver_maquinaria', 'ver_vehiculos', 'ver_subcontratas', 'ver_contratos',
                'ver_facturas', 'ver_finanzas', 'ver_epis', 'ver_formaciones', 'ver_primas',
                'ver_alertas', 'ver_auditoria', 'ver_dashboard_admin',
            ],
            'Trabajador' => [
                'ver_fichajes', 'crear_fichajes', // Solo los propios
                'ver_epis', 'ver_formaciones', 'ver_primas',
                'ver_alertas', 'ver_dashboard_trabajador',
            ],
        ];

        foreach ($roles as $roleName => $permisos) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permisos);
        }

        // Crear usuarios de ejemplo
        $usuarios = [
            [
                'name' => 'Administrador',
                'email' => 'admin@manzer.com',
                'password' => 'password',
                'role' => 'Administrador',
            ],
            [
                'name' => 'María García (Contabilidad)',
                'email' => 'contabilidad@manzer.com',
                'password' => 'password',
                'role' => 'Contabilidad',
            ],
            [
                'name' => 'Juan Martínez (Encargado)',
                'email' => 'encargado@manzer.com',
                'password' => 'password',
                'role' => 'Encargado',
            ],
            [
                'name' => 'Ana López (RRHH)',
                'email' => 'rrhh@manzer.com',
                'password' => 'password',
                'role' => 'RRHH',
            ],
        ];

        foreach ($usuarios as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
