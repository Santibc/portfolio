<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Usuario responsable del contrato
            $table->foreignId('responsable_id')->nullable()->after('estado')
                  ->constrained('users')->nullOnDelete();

            // Estado de la garantía
            $table->enum('estado_garantia', ['pendiente', 'retenida', 'liberada'])
                  ->nullable()->after('fecha_liberacion_garantia');

            // Fecha real de liberación de garantía
            $table->date('fecha_liberacion_real')->nullable()->after('estado_garantia');

            // Renovación automática
            $table->boolean('renovacion_automatica')->default(false)->after('notas');

            // Días de preaviso para alertas de vencimiento
            $table->unsignedSmallInteger('dias_preaviso_vencimiento')->default(30)->after('renovacion_automatica');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn([
                'responsable_id',
                'estado_garantia',
                'fecha_liberacion_real',
                'renovacion_automatica',
                'dias_preaviso_vencimiento',
            ]);
        });
    }
};
