<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fechas_especiales_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('fechas_especiales_cliente', 'ultimo_recordatorio')) {
                $table->timestamp('ultimo_recordatorio')->nullable()->after('activo');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fechas_especiales_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('fechas_especiales_cliente', 'ultimo_recordatorio')) {
                $table->dropColumn('ultimo_recordatorio');
            }
        });
    }
};
