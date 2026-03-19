<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracion_pdv', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Insert default configuration
        DB::table('configuracion_pdv')->insert([
            ['clave' => 'descuento_maximo_cajero', 'valor' => '15', 'descripcion' => 'Porcentaje máximo de descuento que puede aplicar el cajero sin autorización', 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'iva_porcentaje', 'valor' => '0', 'descripcion' => 'Porcentaje de IVA aplicado a las ventas (0 = sin IVA)', 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'requiere_pin_precio', 'valor' => 'true', 'descripcion' => 'Requiere PIN de administrador para cambiar precios manualmente', 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'requiere_pin_descuento_global', 'valor' => 'true', 'descripcion' => 'Requiere PIN de administrador para aplicar descuento global', 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'lista_precio_consumidor_final', 'valor' => '1', 'descripcion' => 'ID de la lista de precios por defecto para Consumidor Final', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('configuracion_pdv');
    }
};
