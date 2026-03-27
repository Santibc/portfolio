<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historial_estados_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_cotizacion_id')
                  ->constrained('solicitudes_cotizacion')
                  ->cascadeOnDelete();
            $table->string('tipo_cambio', 30); // estado, envio, pago
            $table->string('estado_anterior', 30)->nullable();
            $table->string('estado_nuevo', 30);
            $table->text('observaciones')->nullable();
            $table->json('datos_adicionales')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['solicitud_cotizacion_id', 'tipo_cambio'], 'hist_estado_sol_tipo_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_estados_solicitud');
    }
};
