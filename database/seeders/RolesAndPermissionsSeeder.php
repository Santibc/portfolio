<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de roles y permisos.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // DEFINICIÓN DE PERMISOS
        // ============================================

        $permisos = [
            // Módulo Dashboard
            'dashboard.ver',
            'dashboard.metricas',

            // Módulo Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Módulo Clientes
            'clientes.ver',
            'clientes.crear',
            'clientes.editar',
            'clientes.eliminar',
            'clientes.documentos',

            // Módulo Categorías
            'categorias.ver',
            'categorias.crear',
            'categorias.editar',
            'categorias.eliminar',

            // Módulo Productos
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',
            'productos.importar',
            'productos.exportar',

            // Módulo Listas de Precios
            'precios.ver',
            'precios.crear',
            'precios.editar',
            'precios.eliminar',
            'precios.importar',

            // Módulo Cotizaciones
            'cotizaciones.ver',
            'cotizaciones.crear',
            'cotizaciones.editar',
            'cotizaciones.aprobar',
            'cotizaciones.rechazar',
            'cotizaciones.eliminar',
            'cotizaciones.exportar',

            // Módulo Catálogo
            'catalogo.ver',
            'catalogo.crear_enlace',

            // Módulo Stock/Inventario
            'stock.ver',
            'stock.entrada',
            'stock.salida',
            'stock.ajuste',
            'stock.traslado',
            'stock.importar',
            'stock.exportar',
            'stock.novedades',

            // Módulo Punto de Venta
            'pdv.ver',
            'pdv.vender',
            'pdv.anular',
            'pdv.reportes',

            // Módulo Facturación
            'facturacion.ver',
            'facturacion.crear',
            'facturacion.anular',

            // Módulo Pagos
            'pagos.ver',
            'pagos.confirmar',
            'pagos.rechazar',

            // Portal Cliente
            'portal.ver',
            'portal.historial',
            'portal.seguimiento',
            'portal.descargar_guia',
            'portal.descargar_factura',

            // Módulo Traslados
            'traslados.ver',
            'traslados.aprobar',
            'traslados.rechazar',

            // Reportes
            'reportes.ver',
            'reportes.exportar',

            // Configuración
            'configuracion.ver',
            'configuracion.editar',

            // Módulo Garantías
            'garantias.ver',
            'garantias.crear',
            'garantias.liberar',
        ];

        // Crear todos los permisos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // ============================================
        // DEFINICIÓN DE ROLES Y SUS PERMISOS
        // ============================================

        $roles = [
            // Administrador - Acceso total
            'admin' => $permisos,

            // Vendedor - Gestión de cotizaciones y clientes (solo ver)
            'vendedor' => [
                'dashboard.ver',
                'clientes.ver',
                'productos.ver',
                'precios.ver',
                'cotizaciones.ver',
                'cotizaciones.crear',
                'cotizaciones.editar',
                'cotizaciones.exportar',
                'catalogo.ver',
                'catalogo.crear_enlace',
                'stock.ver',
            ],

            // Inventarios - Gestión de stock + Cotizaciones completo
            'inventarios' => [
                'dashboard.ver',
                'productos.ver',
                'stock.ver',
                'stock.entrada',
                'stock.salida',
                'stock.ajuste',
                'stock.traslado',
                'stock.importar',
                'stock.exportar',
                'stock.novedades',
                'reportes.ver',
                'reportes.exportar',
                'cotizaciones.ver',
                'cotizaciones.crear',
                'cotizaciones.editar',
                'cotizaciones.aprobar',
                'cotizaciones.rechazar',
                'cotizaciones.eliminar',
                'cotizaciones.exportar',
                'pagos.ver',
                'pagos.confirmar',
                'pagos.rechazar',
                'facturacion.ver',
                'facturacion.crear',
            ],

            // Facturación - Gestión de pagos y facturas
            'facturacion' => [
                'dashboard.ver',
                'cotizaciones.ver',
                'cotizaciones.aprobar',
                'pagos.ver',
                'pagos.confirmar',
                'pagos.rechazar',
                'facturacion.ver',
                'facturacion.crear',
                'facturacion.anular',
                'reportes.ver',
                'reportes.exportar',
            ],

            // Punto de Venta
            'punto_venta' => [
                'dashboard.ver',
                'productos.ver',
                'stock.ver',
                'pdv.ver',
                'pdv.vender',
                'pdv.anular',
                'pdv.reportes',
            ],

            // Cliente - Acceso limitado al portal
            'cliente' => [
                'portal.ver',
                'portal.historial',
                'portal.seguimiento',
                'portal.descargar_guia',
                'portal.descargar_factura',
            ],

            // Técnico - Para módulo de servicio técnico (si se reactiva)
            'tecnico' => [
                'dashboard.ver',
            ],

            // Auxiliar de Inventario - Stock (solo lectura + historial) y cotizaciones pagadas
            'auxiliar_inventario' => [
                'dashboard.ver',
                'stock.ver',
                'cotizaciones.ver',
                'cotizaciones.exportar',
            ],

            // Centro de Experiencia - Solo aprobar/rechazar traslados
            'centro_experiencia' => [
                'dashboard.ver',
                'traslados.ver',
                'traslados.aprobar',
                'traslados.rechazar',
            ],

            // Auxiliar Administrativo - Todo lo del admin MENOS métricas y reportes
            'auxiliar_administrativo' => array_values(array_diff($permisos, [
                'dashboard.metricas',
                'reportes.ver',
                'reportes.exportar',
            ])),

            // Garantías - Solo módulo de garantías + lectura de cotizaciones
            'garantias' => [
                'dashboard.ver',
                'garantias.ver',
                'garantias.crear',
                'garantias.liberar',
                'cotizaciones.ver',
                'cotizaciones.exportar',
            ],
        ];

        // Crear roles y asignar permisos
        foreach ($roles as $roleName => $rolePermisos) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermisos);
        }

        $this->command->info('Roles y permisos creados correctamente.');
        $this->command->table(
            ['Rol', 'Permisos'],
            collect($roles)->map(fn($perms, $role) => [$role, count($perms)])->toArray()
        );
    }
}
