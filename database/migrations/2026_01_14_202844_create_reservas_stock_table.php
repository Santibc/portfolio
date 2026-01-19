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
        Schema::create('reservas_stock', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('solicitud_cotizacion_id')
                  ->constrained('solicitudes_cotizacion')
                  ->onDelete('cascade');

            $table->foreignId('item_solicitud_id')
                  ->constrained('items_solicitud_cotizacion')
                  ->onDelete('cascade');

            $table->foreignId('stock_producto_id')
                  ->constrained('stock_productos')
                  ->onDelete('cascade');

            // Datos de la reserva
            $table->integer('cantidad_reservada');
            $table->timestamp('expira_en');
            $table->timestamp('liberada_en')->nullable();

            // Estado de la reserva
            $table->enum('estado', ['activa', 'aplicada', 'expirada', 'liberada_manual'])
                  ->default('activa');

            // Información de liberación
            $table->string('motivo_liberacion')->nullable();
            $table->foreignId('liberada_por')
                  ->nullable()
                  ->constrained('users');

            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['estado', 'expira_en']);
            $table->index('solicitud_cotizacion_id');
            $table->index('stock_producto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservas_stock');
    }
};
