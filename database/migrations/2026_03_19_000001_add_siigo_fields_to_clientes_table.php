<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('tipo_documento', 10)->nullable()->after('tipo_cliente')
                ->comment('Código SIIGO tipo doc: 13=CC, 31=NIT, 22=CE, 41=Pasaporte');
            $table->string('siigo_id')->nullable()->after('activo')
                ->comment('GUID del cliente en SIIGO');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento', 'siigo_id']);
        });
    }
};
