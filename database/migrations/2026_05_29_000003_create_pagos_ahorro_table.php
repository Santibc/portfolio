<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_ahorro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_turno_id')
                ->constrained('trabajadores_turno')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->unsignedInteger('monto');
            $table->text('observacion')->nullable();
            $table->dateTime('pagado_en');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['trabajador_turno_id', 'pagado_en']);
            $table->index('pagado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_ahorro');
    }
};
