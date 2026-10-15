<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Textos de la landing por clave (App\Enums\TextoLanding). Sin fila = texto por defecto del enum.
        Schema::create('landing_textos', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 40)->unique();
            $table->text('valor');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_textos');
    }
};
