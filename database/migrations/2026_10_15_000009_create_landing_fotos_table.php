<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fotos fijas de la landing, una por slot (App\Enums\FotoLanding). Las filas existen siempre;
        // `imagen` null = la landing muestra un espacio reservado.
        Schema::create('landing_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 20)->unique();
            $table->string('imagen')->nullable();
            $table->string('alt', 150)->nullable();
            $table->timestamps();
        });

        $ahora = now();
        DB::table('landing_fotos')->insert(array_map(
            fn (string $slot) => ['slot' => $slot, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['hero', 'hero_mini', 'historia', 'local', 'encargos'],
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_fotos');
    }
};
