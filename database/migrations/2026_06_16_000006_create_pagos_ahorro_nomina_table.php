<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_ahorro_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->unsignedInteger('monto');
            $table->text('observacion')->nullable();
            $table->date('pagado_en');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empleado_id', 'pagado_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_ahorro_nomina');
    }
};
