<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
// database/migrations/xxxx_xx_xx_xxxxxx_create_llamadas_table.php
public function up()
{
    Schema::create('llamadas', function (Blueprint $table) {
        $table->id();
        $table->string('uri')->unique(); // URI del evento de Calendly
        $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // El closer asignado
        
        $table->string('nombre_evento');
        $table->string('status'); // active, canceled, etc. [cite: 81, 88, 94]
        $table->timestamp('start_time')->nullable();
        $table->timestamp('end_time')->nullable();
        $table->string('join_url', 512)->nullable(); // URL para unirse a la llamada [cite: 80, 93]
        $table->string('event_type_uri', 512);

        // Información de cancelación [cite: 75, 83, 89]
        $table->string('cancelado_por')->nullable();
        $table->string('motivo_cancelacion')->nullable();
        
        // Pipeline de ventas (requerimiento del SOP)
        $table->string('pipeline_status')->default('Llamada agendada');
        $table->text('comentarios')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('llamadas');
    }
};
