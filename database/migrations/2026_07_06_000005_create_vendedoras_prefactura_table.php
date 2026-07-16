<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vendedoras de prefactura, ahora administrables (antes eran una constante fija en el modelo).
     */
    public function up(): void
    {
        Schema::create('vendedoras_prefactura', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Sembrar las que ya existían fijas en el código.
        $iniciales = [
            'Michell Ballesteros',
            'Andrea Caballero',
            'Yurany Salazar',
            'Maribel Garzón',
            'Mariana Domínguez',
        ];
        foreach ($iniciales as $nombre) {
            DB::table('vendedoras_prefactura')->insert([
                'nombre' => $nombre,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendedoras_prefactura');
    }
};
