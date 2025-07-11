<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PipelineStatus;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Añadir la columna a la tabla de leads
        Schema::table('leads', function (Blueprint $table) {
            $defaultStatusId = PipelineStatus::where('name', 'Llamada agendada')->first()->id ?? 1;
            
            $table->foreignId('pipeline_status_id')
                  ->after('instagram_user')
                  ->default($defaultStatusId)
                  ->constrained('pipeline_statuses');
        });

        // 2. Eliminar las columnas de la tabla de llamadas
        Schema::table('llamadas', function (Blueprint $table) {
            // Se asume que la FK se llama 'llamadas_pipeline_status_id_foreign'
            // Si tiene otro nombre, ajústalo.
            // Primero se debe eliminar la FK si existe.
            // $table->dropForeign(['pipeline_status_id']);
            
            $table->dropColumn('pipeline_status'); // Si la columna se llamaba así
            $table->dropColumn('comentarios'); // También eliminamos comentarios de aquí
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // 1. Añadir las columnas de vuelta a la tabla de llamadas
        Schema::table('llamadas', function (Blueprint $table) {
            $table->string('pipeline_status')->default('Llamada agendada');
            $table->text('comentarios')->nullable();
        });

        // 2. Eliminar la columna de la tabla de leads
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['pipeline_status_id']);
            $table->dropColumn('pipeline_status_id');
        });
    }
};
