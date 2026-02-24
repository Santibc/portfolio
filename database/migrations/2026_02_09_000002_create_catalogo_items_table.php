<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalogo_items', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->text('descripcion');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('porcentaje_iva', 5, 2)->default(19.00);
            $table->string('categoria', 50);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogo_items');
    }
};
