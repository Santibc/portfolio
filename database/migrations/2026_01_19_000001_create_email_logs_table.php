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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 100); // factura, alerta, documento, fichaje, bienvenida
            $table->string('destinatario_email');
            $table->foreignId('destinatario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('asunto');
            $table->nullableMorphs('emailable'); // Para vincular a factura, alerta, documento, etc.
            $table->enum('estado', ['enviado', 'fallido', 'pendiente'])->default('pendiente');
            $table->text('error_message')->nullable();
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();

            $table->index(['tipo', 'estado']);
            $table->index('destinatario_email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
