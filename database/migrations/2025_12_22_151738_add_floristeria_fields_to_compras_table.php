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
            // Campos específicos para floristería
            $table->string('nombre_destinatario')->nullable()->after('notas');
            $table->string('telefono_destinatario')->nullable()->after('nombre_destinatario');
            $table->text('mensaje_tarjeta')->nullable()->after('telefono_destinatario');
            $table->date('fecha_entrega_deseada')->nullable()->after('mensaje_tarjeta');
            $table->string('horario_entrega')->nullable()->after('fecha_entrega_deseada');
            $table->boolean('es_sorpresa')->default(false)->after('horario_entrega');
            $table->text('instrucciones_entrega')->nullable()->after('es_sorpresa');
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
            $table->dropColumn([
                'nombre_destinatario',
                'telefono_destinatario',
                'mensaje_tarjeta',
                'fecha_entrega_deseada',
                'horario_entrega',
                'es_sorpresa',
                'instrucciones_entrega'
            ]);
        });
    }
};
