<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos_solicitud', function (Blueprint $table) {
            $table->text('comprobante')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pagos_solicitud', function (Blueprint $table) {
            $table->string('comprobante')->nullable()->change();
        });
    }
};
