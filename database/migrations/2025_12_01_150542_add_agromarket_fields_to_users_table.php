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
            // Información personal
            $table->string('telefono', 20)->nullable()->after('email');
            $table->boolean('activo')->default(true)->after('telefono');
            $table->timestamp('ultimo_login')->nullable()->after('activo');
            $table->string('documento_identidad', 50)->unique()->nullable()->after('ultimo_login');
            $table->enum('tipo_documento', ['CC', 'CE', 'NIT', 'PASSPORT', 'DNI'])->nullable()->after('documento_identidad');
            $table->date('fecha_nacimiento')->nullable()->after('tipo_documento');
            $table->string('pais', 2)->nullable()->after('fecha_nacimiento');
            $table->string('ciudad', 100)->nullable()->after('pais');
            $table->text('direccion')->nullable()->after('ciudad');

            // KYC (Know Your Customer)
            $table->enum('kyc_status', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente')->after('direccion');
            $table->timestamp('kyc_aprobado_at')->nullable()->after('kyc_status');
            $table->unsignedBigInteger('kyc_aprobado_por')->nullable()->after('kyc_aprobado_at');
            $table->text('kyc_notas')->nullable()->after('kyc_aprobado_por');

            // Sistema de referidos
            $table->string('codigo_referido', 20)->unique()->nullable()->after('kyc_notas');
            $table->unsignedBigInteger('referido_por')->nullable()->after('codigo_referido');

            // Soft deletes
            $table->softDeletes()->after('referido_por');

            // Índices
            $table->index('activo');
            $table->index('kyc_status');
            $table->index('codigo_referido');
            $table->index('referido_por');

            // Foreign keys
            $table->foreign('kyc_aprobado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('referido_por')->references('id')->on('users')->onDelete('set null');
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
            // Eliminar foreign keys primero
            $table->dropForeign(['kyc_aprobado_por']);
            $table->dropForeign(['referido_por']);

            // Eliminar índices
            $table->dropIndex(['activo']);
            $table->dropIndex(['kyc_status']);
            $table->dropIndex(['codigo_referido']);
            $table->dropIndex(['referido_por']);

            // Eliminar columnas
            $table->dropColumn([
                'telefono',
                'activo',
                'ultimo_login',
                'documento_identidad',
                'tipo_documento',
                'fecha_nacimiento',
                'pais',
                'ciudad',
                'direccion',
                'kyc_status',
                'kyc_aprobado_at',
                'kyc_aprobado_por',
                'kyc_notas',
                'codigo_referido',
                'referido_por',
                'deleted_at'
            ]);
        });
    }
};
