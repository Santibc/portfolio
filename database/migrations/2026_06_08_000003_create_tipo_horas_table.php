<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_horas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio_hora', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Vincular bonos con el tipo de hora (para horas extra)
        Schema::table('trabajador_bonos', function (Blueprint $table) {
            $table->foreignId('tipo_hora_id')->nullable()->after('tipo')
                  ->constrained('tipo_horas')->nullOnDelete();
        });

        // Tipos por defecto (editables después)
        DB::table('tipo_horas')->insert([
            ['nombre' => 'Hora extra normal', 'precio_hora' => 12.00, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Hora extra festiva', 'precio_hora' => 15.00, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Hora nocturna', 'precio_hora' => 14.00, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('trabajador_bonos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_hora_id');
        });
        Schema::dropIfExists('tipo_horas');
    }
};
