<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Desactivar verificación de foreign keys
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Eliminar todas las tablas excepto las de usuarios y permisos
        Schema::dropIfExists('actualizaciones_precios');
        Schema::dropIfExists('items_compra');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('transacciones_pago');
        Schema::dropIfExists('comisiones');
        Schema::dropIfExists('configuracion_pasarelas');
        Schema::dropIfExists('envios');
        Schema::dropIfExists('pagos_empresas');
        Schema::dropIfExists('log_transacciones');
        Schema::dropIfExists('carritos');
        Schema::dropIfExists('carrusel_empresas');
        Schema::dropIfExists('movimientos_stock');
        Schema::dropIfExists('stock_productos');
        Schema::dropIfExists('precios_variantes');
        Schema::dropIfExists('variantes_producto');
        Schema::dropIfExists('precios_productos');
        Schema::dropIfExists('imagenes_producto');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('listas_precios');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('items_solicitud_cotizacion');
        Schema::dropIfExists('solicitudes_cotizacion');
        Schema::dropIfExists('enlaces_acceso');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('logs');
        Schema::dropIfExists('parametros');

        // Reactivar verificación de foreign keys
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No se puede revertir esta migración automáticamente
        // Se deben restaurar las migraciones originales si es necesario
    }
};
