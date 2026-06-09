<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas_factura', function (Blueprint $table) {
            // Define si las facturas que usen esta plantilla son nacionales o de exportación.
            // La factura hereda el tipo de la plantilla seleccionada → determina el payload Siigo.
            $table->enum('tipo', ['nacional', 'internacional'])
                ->default('nacional')
                ->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('plantillas_factura', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
