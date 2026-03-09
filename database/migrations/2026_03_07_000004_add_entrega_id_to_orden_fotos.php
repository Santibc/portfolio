<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orden_fotos', function (Blueprint $table) {
            $table->unsignedBigInteger('entrega_id')->nullable()->after('orden_pieza_id');
            $table->foreign('entrega_id')->references('id')->on('entregas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orden_fotos', function (Blueprint $table) {
            $table->dropForeign(['entrega_id']);
            $table->dropColumn('entrega_id');
        });
    }
};
