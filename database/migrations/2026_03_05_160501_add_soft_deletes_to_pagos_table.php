<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('rechazado_por')->nullable()->after('aprobado_por');
            $table->string('motivo_rechazo', 500)->nullable()->after('rechazado_por');

            $table->foreign('rechazado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['rechazado_por']);
            $table->dropColumn(['deleted_at', 'rechazado_por', 'motivo_rechazo']);
        });
    }
};
