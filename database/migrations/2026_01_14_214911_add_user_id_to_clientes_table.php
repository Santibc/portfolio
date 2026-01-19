<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar user_id para vincular cliente con su cuenta de usuario (portal cliente)
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('vendedor_id')
                ->constrained('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
