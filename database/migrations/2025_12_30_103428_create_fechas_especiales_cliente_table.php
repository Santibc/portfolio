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
        Schema::create('fechas_especiales_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // Nombre de la persona
            $table->enum('tipo', ['cumpleanos', 'aniversario', 'dia_madre', 'dia_padre', 'navidad', 'otro']);
            $table->date('fecha');
            $table->text('notas')->nullable();
            $table->boolean('recordar_dias_antes')->default(true);
            $table->integer('dias_anticipacion')->default(3);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fechas_especiales_cliente');
    }
};
