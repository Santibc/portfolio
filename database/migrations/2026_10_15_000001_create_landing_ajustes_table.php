<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Una sola fila (id = 1): los datos del negocio que muestra la landing.
        Schema::create('landing_ajustes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('nombre_largo', 120);
            $table->string('lema', 60);
            $table->string('propietaria', 80)->nullable();
            $table->string('propietaria_cargo', 40)->nullable();
            $table->string('telefono', 20);
            $table->string('whatsapp', 20);
            $table->string('whatsapp_mensaje', 200);
            $table->string('direccion', 120);
            $table->string('barrio', 60)->nullable();
            $table->string('ciudad', 60);
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('landing_ajustes')->insert([
            'id' => 1,
            'nombre' => 'El Pan Dorado DC',
            'nombre_largo' => 'Panadería y Pastelería El Pan Dorado DC',
            'lema' => 'Calidad & Servicio',
            'propietaria' => 'Dorahaliz Corzo Quijano',
            'propietaria_cargo' => 'Propietaria',
            'telefono' => '3188655792',
            'whatsapp' => '573188655792',
            'whatsapp_mensaje' => 'Hola, El Pan Dorado DC. Quisiera hacer un pedido.',
            'direccion' => 'Carrera 41A # 4-42',
            'barrio' => 'Primavera',
            'ciudad' => 'Bogotá D.C.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_ajustes');
    }
};
