<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar ubicacion_id a stock_productos
        Schema::table('stock_productos', function (Blueprint $table) {
            $table->foreignId('ubicacion_id')->nullable()->after('variante_producto_id');
        });

        // Asignar ubicación principal a registros existentes
        $ubicacionPrincipal = DB::table('ubicaciones')->where('es_principal', true)->first();
        if ($ubicacionPrincipal) {
            DB::table('stock_productos')
                ->whereNull('ubicacion_id')
                ->update(['ubicacion_id' => $ubicacionPrincipal->id]);
        }

        // Agregar constraint después de migrar datos
        Schema::table('stock_productos', function (Blueprint $table) {
            $table->foreign('ubicacion_id')
                  ->references('id')
                  ->on('ubicaciones')
                  ->onDelete('restrict');
        });

        // Agregar ubicacion_id a movimientos_stock
        Schema::table('movimientos_stock', function (Blueprint $table) {
            $table->foreignId('ubicacion_id')->nullable()->after('variante_producto_id');
            $table->enum('tipo_operacion', ['contado', 'credito', 'general'])->default('general')->after('origen');
        });

        // Asignar ubicación principal a movimientos existentes
        if ($ubicacionPrincipal) {
            DB::table('movimientos_stock')
                ->whereNull('ubicacion_id')
                ->update(['ubicacion_id' => $ubicacionPrincipal->id]);
        }

        // Agregar constraint
        Schema::table('movimientos_stock', function (Blueprint $table) {
            $table->foreign('ubicacion_id')
                  ->references('id')
                  ->on('ubicaciones')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_stock', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_id']);
            $table->dropColumn(['ubicacion_id', 'tipo_operacion']);
        });

        Schema::table('stock_productos', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_id']);
            $table->dropColumn('ubicacion_id');
        });
    }
};
