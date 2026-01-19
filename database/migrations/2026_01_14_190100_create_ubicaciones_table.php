<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->enum('tipo', ['bodega', 'tienda', 'otro'])->default('bodega');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('responsable')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Crear ubicación principal por defecto
        DB::table('ubicaciones')->insert([
            'nombre' => 'Bodega Principal',
            'codigo' => 'BOD-PRINCIPAL',
            'tipo' => 'bodega',
            'es_principal' => true,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ubicaciones');
    }
};
