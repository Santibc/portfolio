<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feria_promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feria_id')->constrained('ferias')->cascadeOnDelete();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('variante_producto_id')->nullable();
            $table->decimal('precio', 12, 2);            // precio promo (absoluto) que cobra el POS en la ventana
            $table->decimal('precio_referencia', 12, 2)->nullable(); // precio normal de la feria al crear (informativo)
            $table->dateTime('inicia_en');
            $table->dateTime('termina_en');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['feria_id', 'producto_id', 'variante_producto_id'], 'fp_feria_prod_var_idx');
            $table->index(['inicia_en', 'termina_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feria_promociones');
    }
};
