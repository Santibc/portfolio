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
        Schema::create('puntos_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('puntos')->comment('Cantidad de puntos (negativo si es canje)');
            $table->enum('tipo', ['ganados', 'canjeados', 'referido', 'registro', 'primera_compra', 'bonus', 'ajuste'])
                  ->default('ganados')
                  ->comment('Tipo de movimiento de puntos');
            $table->text('concepto')->comment('Descripción del movimiento');
            $table->foreignId('compra_id')->nullable()->constrained('compras')->onDelete('set null');
            $table->date('fecha_expiracion')->nullable()->comment('Los puntos ganados expiran después de 1 año');
            $table->timestamps();

            // Índices para mejor performance
            $table->index(['user_id', 'tipo']);
            $table->index(['user_id', 'fecha_expiracion']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('puntos_cliente');
    }
};
