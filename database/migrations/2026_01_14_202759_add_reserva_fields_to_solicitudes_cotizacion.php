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
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Campos para sistema de reserva de stock
            $table->boolean('tiene_reserva_stock')->default(false)->after('monto_total');
            $table->timestamp('reserva_expira_en')->nullable()->after('tiene_reserva_stock');
            $table->timestamp('reserva_liberada_en')->nullable()->after('reserva_expira_en');

            // Observaciones del vendedor (obligatorias al aprobar)
            $table->text('observaciones_vendedor')->nullable()->after('observaciones_admin');

            // Campos adicionales para cotización
            $table->decimal('valor_flete', 10, 2)->nullable()->default(0)->after('monto_total');
            $table->decimal('descuento_total', 10, 2)->nullable()->default(0)->after('valor_flete');

            // Auditoría de edición
            $table->timestamp('editada_en')->nullable()->after('rechazada_por');
            $table->foreignId('editada_por')->nullable()->constrained('users')->after('editada_en');

            // Soft deletes para eliminación lógica
            $table->softDeletes();

            // Índices para búsquedas
            $table->index('reserva_expira_en');
            $table->index(['estado', 'tiene_reserva_stock']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['reserva_expira_en']);
            $table->dropIndex(['estado', 'tiene_reserva_stock']);

            // Eliminar foreign key
            $table->dropForeign(['editada_por']);

            // Eliminar columnas
            $table->dropColumn([
                'tiene_reserva_stock',
                'reserva_expira_en',
                'reserva_liberada_en',
                'observaciones_vendedor',
                'valor_flete',
                'descuento_total',
                'editada_en',
                'editada_por',
                'deleted_at'
            ]);
        });
    }
};
