<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions
        $permissions = [
            'pdv.cajas.configurar',
            'pdv.cajas.abrir',
            'pdv.cajas.cerrar',
            'pdv.prefacturas.crear',
            'pdv.prefacturas.aceptar',
            'pdv.prefacturas.anular',
            'pdv.vender',
            'pdv.anular',
            'pdv.descuento_linea',
            'pdv.descuento_global',
            'pdv.cambiar_precio',
            'pdv.cambiar_lista_precio',
            'pdv.vales.crear',
            'pdv.vales.anular',
            'pdv.vales.redimir',
            'pdv.novedades.registrar',
            'pdv.traslados.solicitar',
            'pdv.reportes.propios',
            'pdv.reportes.todos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create new roles
        $cajeroPrincipal = Role::firstOrCreate(['name' => 'cajero_principal', 'guard_name' => 'web']);
        $auxiliarVenta = Role::firstOrCreate(['name' => 'auxiliar_venta', 'guard_name' => 'web']);

        // Assign permissions to cajero_principal
        $cajeroPrincipal->syncPermissions([
            'dashboard.ver',
            'productos.ver',
            'stock.ver',
            'pdv.cajas.abrir',
            'pdv.cajas.cerrar',
            'pdv.prefacturas.aceptar',
            'pdv.prefacturas.anular',
            'pdv.vender',
            'pdv.descuento_linea',
            'pdv.cambiar_lista_precio',
            'pdv.vales.crear',
            'pdv.vales.anular',
            'pdv.vales.redimir',
            'pdv.novedades.registrar',
            'pdv.traslados.solicitar',
            'pdv.reportes.propios',
        ]);

        // Assign permissions to auxiliar_venta
        $auxiliarVenta->syncPermissions([
            'dashboard.ver',
            'productos.ver',
            'stock.ver',
            'pdv.prefacturas.crear',
        ]);

        // Add pdv.prefacturas.crear to vendedor role if it exists
        $vendedor = Role::where('name', 'vendedor')->first();
        if ($vendedor) {
            $vendedor->givePermissionTo('pdv.prefacturas.crear');
        }

        // Add all pdv permissions to admin role
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }
    }

    public function down()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove roles
        Role::where('name', 'cajero_principal')->delete();
        Role::where('name', 'auxiliar_venta')->delete();

        // Remove permissions
        Permission::where('name', 'like', 'pdv.%')->delete();
    }
};
