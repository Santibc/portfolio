<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modificar el ENUM del campo tipo para agregar 'horas'
        DB::statement("ALTER TABLE trabajador_bonos MODIFY COLUMN tipo
            ENUM('prima_produccion', 'bono_especial', 'plus_nocturnidad', 'horas', 'otro')");

        // Agregar columna horas
        Schema::table('trabajador_bonos', function (Blueprint $table) {
            $table->decimal('horas', 5, 2)->nullable()->after('importe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar columna horas
        Schema::table('trabajador_bonos', function (Blueprint $table) {
            $table->dropColumn('horas');
        });

        // Revertir el ENUM a los valores originales
        DB::statement("ALTER TABLE trabajador_bonos MODIFY COLUMN tipo
            ENUM('prima_produccion', 'bono_especial', 'plus_nocturnidad', 'otro')");
    }
};
