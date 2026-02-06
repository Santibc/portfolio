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
        Schema::create('pagos_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_cotizacion_id')->constrained('solicitudes_cotizacion')->onDelete('cascade');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago', 50);
            $table->string('comprobante')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_solicitud');
    }
};
