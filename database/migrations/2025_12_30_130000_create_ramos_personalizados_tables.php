<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tipos de flores disponibles para armar ramos
        Schema::create('flores_disponibles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color')->nullable();
            $table->decimal('precio_unidad', 10, 2);
            $table->integer('cantidad_minima')->default(1);
            $table->integer('cantidad_maxima')->default(50);
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->integer('stock')->default(0);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Tamaños de ramo predefinidos
        Schema::create('tamanos_ramo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Pequeño, Mediano, Grande
            $table->integer('cantidad_flores_min');
            $table->integer('cantidad_flores_max');
            $table->decimal('precio_base', 10, 2); // Precio base del envoltura/preparación
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Productos adicionales (upsells) - chocolates, peluches, globos
        Schema::create('productos_adicionales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('categoria'); // chocolate, peluche, globo, otro
            $table->decimal('precio', 10, 2);
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->integer('stock')->default(0);
            $table->boolean('mostrar_en_checkout')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Ramos personalizados guardados (antes de agregar al carrito)
        Schema::create('ramos_personalizados', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->foreignId('tamano_ramo_id')->constrained('tamanos_ramo')->onDelete('cascade');
            $table->json('flores_seleccionadas'); // [{flor_id, cantidad, precio_unitario}]
            $table->json('adicionales')->nullable(); // [{adicional_id, cantidad, precio}]
            $table->decimal('subtotal_flores', 10, 2)->default(0);
            $table->decimal('subtotal_adicionales', 10, 2)->default(0);
            $table->decimal('precio_base', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('mensaje_tarjeta', 250)->nullable();
            $table->timestamps();
        });

        // Adicionales en la compra
        Schema::create('adicionales_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->foreignId('producto_adicional_id')->nullable()->constrained('productos_adicionales')->onDelete('set null');
            $table->string('nombre_adicional');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('precio_total', 10, 2);
            $table->timestamps();
        });

        // Agregar campo para identificar si un item es ramo personalizado
        Schema::table('items_compra', function (Blueprint $table) {
            $table->boolean('es_ramo_personalizado')->default(false)->after('info_variante');
            $table->json('detalle_ramo')->nullable()->after('es_ramo_personalizado'); // Flores y cantidades
        });
    }

    public function down()
    {
        Schema::table('items_compra', function (Blueprint $table) {
            $table->dropColumn(['es_ramo_personalizado', 'detalle_ramo']);
        });

        Schema::dropIfExists('adicionales_compra');
        Schema::dropIfExists('ramos_personalizados');
        Schema::dropIfExists('productos_adicionales');
        Schema::dropIfExists('tamanos_ramo');
        Schema::dropIfExists('flores_disponibles');
    }
};
