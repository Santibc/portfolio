<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cliente_emails_adicionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('email', 255);
            $table->string('nombre', 150)->nullable()->comment('Nombre del contacto');
            $table->string('cargo', 150)->nullable()->comment('Cargo/Puesto');
            $table->boolean('activo')->default(true);
            $table->boolean('enviar_facturas_por_defecto')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'activo']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_emails_adicionales');
    }
};
