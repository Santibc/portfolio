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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads');
            $table->foreignId('user_id')->constrained('users')->comment('Closer que realizó la venta');
            $table->string('nombre_cliente');
            $table->string('apellido_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente');
            $table->string('identificacion_personal')->nullable();
            $table->string('domicilio');
            $table->string('metodo_pago');
            $table->string('comprobante_pago_path');
            $table->string('tipo_acuerdo');
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
