<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('codigo_barras_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('codigo_anterior', 50)->nullable();
            $table->string('codigo_nuevo', 50)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('origen', 30)->default('formulario');
            $table->timestamps();

            $table->index(['producto_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('codigo_barras_logs');
    }
};
