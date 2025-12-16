<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Foto de perfil
            $table->string('foto_perfil', 500)->nullable()->after('direccion');

            // Campos para usuarios creados por admin (agricultores)
            $table->boolean('creado_por_admin')->default(false)->after('deleted_at');
            $table->unsignedBigInteger('admin_creador_id')->nullable()->after('creado_por_admin');

            // Foreign key
            $table->foreign('admin_creador_id')->references('id')->on('users')->onDelete('set null');

            // Index
            $table->index('creado_por_admin');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_creador_id']);
            $table->dropIndex(['creado_por_admin']);
            $table->dropColumn(['foto_perfil', 'creado_por_admin', 'admin_creador_id']);
        });
    }
};
