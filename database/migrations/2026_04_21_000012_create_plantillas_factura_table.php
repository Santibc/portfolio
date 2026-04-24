<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_factura', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();
            $table->longText('html_content');
            $table->text('css_content')->nullable();
            $table->boolean('es_default')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('plantilla_factura_id')
                ->nullable()
                ->after('idioma_documento')
                ->constrained('plantillas_factura')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plantilla_factura_id');
        });

        Schema::dropIfExists('plantillas_factura');
    }
};
