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
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('repartidor_id')->nullable()->after('estado')->constrained('repartidores')->nullOnDelete();
            $table->timestamp('asignado_repartidor_at')->nullable()->after('repartidor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['repartidor_id']);
            $table->dropColumn(['repartidor_id', 'asignado_repartidor_at']);
        });
    }
};
