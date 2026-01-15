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
        Schema::create('configuracion_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            // Configuración de acumulación de puntos
            $table->integer('puntos_por_monto')->default(100)
                  ->comment('Cada cuántos $ se gana 1 punto (ej: 100 = 1 punto cada $100)');
            $table->integer('puntos_registro')->default(0)
                  ->comment('Puntos bonus por registrarse');
            $table->integer('puntos_primera_compra')->default(0)
                  ->comment('Puntos bonus por primera compra');
            $table->integer('puntos_referir')->default(500)
                  ->comment('Puntos para quien refiere a un amigo');
            $table->integer('puntos_ser_referido')->default(0)
                  ->comment('Puntos para quien es referido');

            // Configuración de canje
            $table->integer('puntos_por_peso')->default(100)
                  ->comment('Cuántos puntos equivalen a $1 (ej: 100 puntos = $1)');
            $table->integer('canje_minimo')->default(100)
                  ->comment('Mínimo de puntos para canjear');
            $table->integer('canje_maximo')->nullable()
                  ->comment('Máximo de puntos que se pueden canjear por compra (null = sin límite)');

            // Configuración de expiración
            $table->integer('meses_expiracion')->default(12)
                  ->comment('Meses hasta que expiran los puntos ganados');

            // Configuración general
            $table->boolean('sistema_activo')->default(true)
                  ->comment('Activar/desactivar todo el sistema de puntos');
            $table->boolean('referidos_activo')->default(true)
                  ->comment('Activar/desactivar sistema de referidos');

            $table->timestamps();

            // Solo puede haber una configuración por empresa
            $table->unique('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion_puntos');
    }
};
