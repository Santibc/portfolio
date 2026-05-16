<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE movimientos_stock MODIFY origen ENUM('compra','venta','devolucion','ajuste_inventario','cotizacion','traslado','novedad','otro','garantia') NOT NULL");
    }

    public function down(): void
    {
        $existen = DB::table('movimientos_stock')->where('origen', 'garantia')->exists();
        if ($existen) {
            throw new \RuntimeException('Existen registros con origen "garantia"; no se puede revertir el enum.');
        }

        DB::statement("ALTER TABLE movimientos_stock MODIFY origen ENUM('compra','venta','devolucion','ajuste_inventario','cotizacion','traslado','novedad','otro') NOT NULL");
    }
};
