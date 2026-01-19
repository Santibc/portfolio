<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migración para agregar campos de envío a solicitudes_cotizacion
     * Fase 7: Portal de Cliente
     */
    public function up(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Estado de envío
            $table->enum('estado_envio', [
                'pendiente',
                'preparando',
                'despachado',
                'en_transito',
                'entregado'
            ])->default('pendiente')->after('estado_pago');

            // Información de envío
            $table->string('numero_guia', 100)->nullable()->after('estado_envio');
            $table->string('transportadora', 100)->nullable()->after('numero_guia');
            $table->string('archivo_guia')->nullable()->after('transportadora');

            // Timestamps de envío
            $table->timestamp('despachado_en')->nullable()->after('archivo_guia');
            $table->foreignId('despachado_por')
                ->nullable()
                ->after('despachado_en')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('entregado_en')->nullable()->after('despachado_por');

            // Índices para búsquedas
            $table->index('estado_envio');
            $table->index('numero_guia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['estado_envio']);
            $table->dropIndex(['numero_guia']);

            // Eliminar foreign key
            $table->dropForeign(['despachado_por']);

            // Eliminar columnas
            $table->dropColumn([
                'estado_envio',
                'numero_guia',
                'transportadora',
                'archivo_guia',
                'despachado_en',
                'despachado_por',
                'entregado_en'
            ]);
        });
    }
};
