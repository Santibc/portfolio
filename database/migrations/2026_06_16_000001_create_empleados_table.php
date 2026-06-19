<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metodo_pago_id')
                ->nullable()
                ->constrained('metodos_pago')
                ->nullOnDelete();
            $table->string('nombre', 120);
            $table->string('documento', 30)->unique();
            $table->string('cargo', 80)->nullable();
            $table->unsignedInteger('salario_base');
            $table->unsignedInteger('auxilio_transporte')->default(0);
            $table->boolean('tiene_auxilio')->default(true);
            $table->unsignedInteger('bono_default')->default(0);
            $table->unsignedSmallInteger('porcentaje_salud')->default(4);
            $table->unsignedSmallInteger('porcentaje_pension')->default(4);
            $table->string('eps', 80)->nullable();
            $table->string('fondo_pension', 80)->nullable();
            $table->string('fondo_cesantias', 80)->nullable();
            $table->date('fecha_ingreso');
            $table->string('banco', 80)->nullable();
            $table->string('numero_cuenta', 40)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
