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
        Schema::table('items_solicitud_cotizacion', function (Blueprint $table) {
            $table->boolean('precio_editado_manualmente')->default(false)->after('precio_total');
            $table->decimal('precio_original', 10, 2)->nullable()->after('precio_editado_manualmente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_solicitud_cotizacion', function (Blueprint $table) {
            $table->dropColumn(['precio_editado_manualmente', 'precio_original']);
        });
    }
};
