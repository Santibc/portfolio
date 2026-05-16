<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantia_productos_liberacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garantia_id')->constrained('garantias')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->onDelete('restrict');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->onDelete('restrict');
            $table->unsignedInteger('cantidad');
            $table->foreignId('movimiento_stock_id')->nullable()->constrained('movimientos_stock')->onDelete('set null');
            $table->timestamps();

            $table->index('garantia_id', 'gpl_garantia_idx');
            $table->index(['producto_id', 'variante_producto_id'], 'gpl_prod_var_idx');
            $table->index('ubicacion_id', 'gpl_ubicacion_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_productos_liberacion');
    }
};
