<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La columna productos.moneda_id no se usa en ninguna parte: el modelo Producto
 * no la referencia, el formulario no la expone y la facturación maneja su propia
 * moneda en la tabla facturas. Se elimina junto con su llave foránea.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'moneda_id')) {
                $table->dropForeign('productos_moneda_id_foreign');
                $table->dropColumn('moneda_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'moneda_id')) {
                $table->foreignId('moneda_id')->nullable()->after('precio_unitario')->constrained('monedas')->restrictOnDelete();
            }
        });
    }
};
