<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta tabla relaciona productos con complementarios específicos.
     * Ejemplo: Un "Ramo de Rosas" puede tener como complementarios específicos:
     * - Florero
     * - Preservante para flores
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos_complementarios_sugeridos', function (Blueprint $table) {
            $table->id();

            // El producto principal (ej: Ramo de Rosas)
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade')
                ->comment('Producto principal al que se sugiere el complementario');

            // El complementario específico (ej: Florero)
            $table->foreignId('producto_adicional_id')
                ->constrained('productos_adicionales')
                ->onDelete('cascade')
                ->comment('Producto complementario sugerido');

            // Orden de aparición
            $table->integer('orden')->default(0);

            // Activo/Inactivo
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Índices para mejor performance
            $table->index(['producto_id', 'activo']);
            $table->unique(['producto_id', 'producto_adicional_id'], 'unique_producto_complementario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_complementarios_sugeridos');
    }
};
