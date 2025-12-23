<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('nombre_empresa', 255);
            $table->string('persona_contacto', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->enum('origen', ['contacto_directo', 'recomendacion', 'licitacion', 'web', 'otro']);
            $table->text('descripcion')->nullable();
            $table->decimal('importe_estimado', 12, 2)->nullable();
            $table->integer('probabilidad')->default(50)->comment('Porcentaje 0-100');
            $table->enum('temperatura', ['frio', 'tibio', 'caliente'])->default('tibio');
            $table->enum('capacidad_economica_percibida', ['baja', 'media', 'alta'])->nullable();
            $table->date('fecha_estimada_cierre')->nullable();
            $table->enum('estado', ['nuevo', 'contactado', 'propuesta_enviada', 'negociacion', 'ganado', 'perdido'])->default('nuevo');
            $table->text('motivo_perdida')->nullable();
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
