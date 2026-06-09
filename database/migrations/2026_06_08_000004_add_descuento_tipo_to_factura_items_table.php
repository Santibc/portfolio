<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factura_items', function (Blueprint $table) {
            // Tipo de descuento por línea: 'valor' (monto fijo en la moneda de la
            // factura) o 'porcentaje' (% sobre cantidad × precio unitario).
            // La columna `descuento` guarda el valor crudo capturado; el monto
            // efectivo se calcula en FacturaItem::descuentoValor().
            $table->enum('descuento_tipo', ['valor', 'porcentaje'])
                ->default('valor')
                ->after('descuento');
        });
    }

    public function down(): void
    {
        Schema::table('factura_items', function (Blueprint $table) {
            $table->dropColumn('descuento_tipo');
        });
    }
};
