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
        Schema::table('clientes', function (Blueprint $table) {
            // Tipo de cliente: persona natural o jurídica
            $table->enum('tipo_cliente', ['natural', 'juridica'])->default('natural')->after('numero_identificacion');

            // Campos para persona jurídica
            $table->string('razon_social')->nullable()->after('tipo_cliente');
            $table->string('nit')->nullable()->after('razon_social');
            $table->string('representante_legal')->nullable()->after('nit');

            // Campos adicionales
            $table->string('direccion')->nullable()->after('telefono');
            $table->decimal('valor_flete', 10, 2)->nullable()->after('direccion');
            $table->boolean('aplica_flete')->default(false)->after('valor_flete');
            $table->text('observaciones')->nullable()->after('aplica_flete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_cliente',
                'razon_social',
                'nit',
                'representante_legal',
                'direccion',
                'valor_flete',
                'aplica_flete',
                'observaciones'
            ]);
        });
    }
};
