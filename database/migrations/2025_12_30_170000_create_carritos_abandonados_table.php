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
        // Agregar campos al carrito existente para tracking de abandono
        Schema::table('carritos', function (Blueprint $table) {
            $table->string('email_cliente')->nullable()->after('mensaje_tarjeta')
                ->comment('Email del cliente para recordatorios');
            $table->string('nombre_cliente')->nullable()->after('email_cliente');
            $table->string('telefono_cliente')->nullable()->after('nombre_cliente');
            $table->timestamp('recordatorio_1_enviado')->nullable()->after('telefono_cliente')
                ->comment('Primer recordatorio (1 hora)');
            $table->timestamp('recordatorio_2_enviado')->nullable()->after('recordatorio_1_enviado')
                ->comment('Segundo recordatorio (24 horas)');
            $table->timestamp('recordatorio_3_enviado')->nullable()->after('recordatorio_2_enviado')
                ->comment('Tercer recordatorio (72 horas)');
            $table->boolean('recuperado')->default(false)->after('recordatorio_3_enviado')
                ->comment('Si el carrito fue recuperado después de abandono');
            $table->string('utm_source')->nullable()->after('recuperado');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
        });

        // Crear tabla para historial de carritos abandonados (para estadísticas)
        Schema::create('carritos_abandonados_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrito_id')->constrained('carritos')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('email_cliente')->nullable();
            $table->decimal('valor_carrito', 12, 2)->default(0);
            $table->integer('cantidad_items')->default(0);
            $table->tinyInteger('recordatorio_numero')->comment('1, 2 o 3');
            $table->enum('canal', ['email', 'whatsapp', 'sms'])->default('email');
            $table->enum('estado', ['enviado', 'abierto', 'clickeado', 'recuperado', 'fallido'])->default('enviado');
            $table->timestamp('enviado_at')->nullable();
            $table->timestamp('abierto_at')->nullable();
            $table->timestamp('clickeado_at')->nullable();
            $table->timestamp('recuperado_at')->nullable();
            $table->string('token_recuperacion', 64)->unique();
            $table->timestamps();

            $table->index(['empresa_id', 'created_at']);
            $table->index(['email_cliente', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carritos_abandonados_log');

        Schema::table('carritos', function (Blueprint $table) {
            $table->dropColumn([
                'email_cliente',
                'nombre_cliente',
                'telefono_cliente',
                'recordatorio_1_enviado',
                'recordatorio_2_enviado',
                'recordatorio_3_enviado',
                'recuperado',
                'utm_source',
                'utm_medium',
                'utm_campaign',
            ]);
        });
    }
};
