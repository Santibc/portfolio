<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_solicitudes_cotizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_cotizacion_id')->constrained('solicitudes_cotizacion')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('accion', 50);
            $table->json('detalle')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['solicitud_cotizacion_id', 'accion'], 'log_sol_cot_accion_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_solicitudes_cotizacion');
    }
};
