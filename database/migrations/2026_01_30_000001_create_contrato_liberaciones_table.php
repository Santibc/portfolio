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
        Schema::create('contrato_liberaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->unsignedTinyInteger('porcentaje_liberado'); // 1-100 (solo enteros)
            $table->decimal('importe_liberado', 12, 2); // Calculado automáticamente
            $table->date('fecha_liberacion'); // Fecha efectiva (permite futuras)
            $table->text('notas')->nullable(); // Motivo/comentario (opcional)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Quién realizó la liberación
            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['contrato_id', 'fecha_liberacion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contrato_liberaciones');
    }
};
