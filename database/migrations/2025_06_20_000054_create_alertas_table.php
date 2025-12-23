<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 100);
            $table->string('titulo', 255);
            $table->text('mensaje');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');

            // Referencia al registro que genera la alerta (polimórfico)
            $table->string('alertable_type', 255)->comment('Modelo: Trabajador, Vehiculo, etc.');
            $table->unsignedBigInteger('alertable_id');

            // Destinatarios
            $table->json('para_roles')->nullable()->comment('["admin", "rrhh"]');
            $table->foreignId('para_usuario_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->date('fecha_vencimiento')->nullable()->comment('Fecha del vencimiento que genera la alerta');

            $table->boolean('leida')->default(false);
            $table->datetime('fecha_lectura')->nullable();
            $table->boolean('resuelta')->default(false);
            $table->datetime('fecha_resolucion')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['alertable_type', 'alertable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
