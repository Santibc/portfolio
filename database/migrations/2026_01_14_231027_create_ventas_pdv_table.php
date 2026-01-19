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
        Schema::create('ventas_pdv', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->string('nombre_cliente')->nullable(); // Para ventas sin cliente registrado
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->default('efectivo');
            $table->decimal('monto_efectivo', 12, 2)->nullable();
            $table->decimal('monto_tarjeta', 12, 2)->nullable();
            $table->decimal('monto_transferencia', 12, 2)->nullable();
            $table->enum('estado', ['completada', 'anulada'])->default('completada');
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('anulada_por')->nullable()->constrained('users');
            $table->timestamp('anulada_en')->nullable();
            $table->text('motivo_anulacion')->nullable();
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
        Schema::dropIfExists('ventas_pdv');
    }
};
