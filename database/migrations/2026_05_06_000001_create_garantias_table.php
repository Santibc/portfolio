<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->onDelete('restrict');
            $table->enum('tipo', ['cambio_producto', 'descuento', 'nota_credito', 'otro']);
            $table->text('tipo_otro_descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'liberado'])->default('pendiente');
            $table->text('observacion_liberacion')->nullable();
            $table->foreignId('solicitud_cotizacion_id')->nullable()->constrained('solicitudes_cotizacion')->onDelete('set null');
            $table->foreignId('usuario_creador_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('usuario_liberador_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('liberado_en')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'estado']);
            $table->index('estado');
            $table->index('solicitud_cotizacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
