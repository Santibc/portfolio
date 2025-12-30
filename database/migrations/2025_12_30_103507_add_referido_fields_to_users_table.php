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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'codigo_referido')) {
                $table->string('codigo_referido')->unique()->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'referido_por')) {
                $table->foreignId('referido_por')->nullable()->after('codigo_referido')
                      ->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'fecha_nacimiento')) {
                $table->date('fecha_nacimiento')->nullable()->after('telefono');
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referido_por')) {
                $table->dropForeign(['referido_por']);
                $table->dropColumn('referido_por');
            }
            if (Schema::hasColumn('users', 'codigo_referido')) {
                $table->dropColumn('codigo_referido');
            }
            if (Schema::hasColumn('users', 'fecha_nacimiento')) {
                $table->dropColumn('fecha_nacimiento');
            }
        });
    }
};
