<?php

// Script para generar el contenido de las migraciones restantes de AGROMARKET
// Este script lee el plan y genera automáticamente todas las migraciones

$migraciones = [
    'documentos_proyecto' => [
        'file' => 'database/migrations/2025_12_01_150856_create_documentos_proyecto_table.php',
        'schema' => "
            \$table->id();
            \$table->unsignedBigInteger('proyecto_id');
            \$table->enum('tipo_documento', ['escritura', 'certificado_camara', 'cedula_catastral', 'plan_cultivo', 'estudio_suelos', 'licencia_ambiental', 'poliza_seguro', 'contrato_compra', 'foto_terreno', 'otro']);
            \$table->string('nombre_archivo', 255);
            \$table->string('ruta_archivo', 500);
            \$table->string('tipo_mime', 100);
            \$table->unsignedBigInteger('tamano_bytes');
            \$table->text('descripcion')->nullable();
            \$table->boolean('verificado')->default(false);
            \$table->unsignedBigInteger('verificado_por')->nullable();
            \$table->timestamp('verificado_at')->nullable();
            \$table->unsignedBigInteger('subido_por');
            \$table->timestamps();
            \$table->softDeletes();

            \$table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            \$table->foreign('verificado_por')->references('id')->on('users')->onDelete('set null');
            \$table->foreign('subido_por')->references('id')->on('users')->onDelete('restrict');

            \$table->index('proyecto_id');
            \$table->index('tipo_documento');
            \$table->index('verificado');
        "
    ],
    'imagenes_proyecto' => [
        'file' => 'database/migrations/2025_12_01_150915_create_imagenes_proyecto_table.php',
        'schema' => "
            \$table->id();
            \$table->unsignedBigInteger('proyecto_id');
            \$table->string('ruta_imagen', 500);
            \$table->string('thumbnail', 500)->nullable();
            \$table->string('titulo', 200)->nullable();
            \$table->text('descripcion')->nullable();
            \$table->boolean('es_principal')->default(false);
            \$table->integer('orden')->default(0);
            \$table->timestamps();

            \$table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');

            \$table->index('proyecto_id');
            \$table->index(['proyecto_id', 'es_principal']);
        "
    ],
    'actualizaciones_proyecto' => [
        'file' => 'database/migrations/2025_12_01_150923_create_actualizaciones_proyecto_table.php',
        'schema' => "
            \$table->id();
            \$table->unsignedBigInteger('proyecto_id');
            \$table->unsignedBigInteger('autor_id');
            \$table->string('titulo', 200);
            \$table->text('contenido');
            \$table->enum('tipo', ['informativo', 'hito', 'alerta', 'financiero'])->default('informativo');
            \$table->boolean('visible_inversores')->default(true);
            \$table->timestamp('publicado_at')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            \$table->foreign('autor_id')->references('id')->on('users')->onDelete('restrict');

            \$table->index('proyecto_id');
            \$table->index(['proyecto_id', 'publicado_at']);
        "
    ],
];

foreach ($migraciones as $tabla => $config) {
    $contenido = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$tabla}', function (Blueprint $table) {
{$config['schema']}
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tabla}');
    }
};
";

    file_put_contents($config['file'], $contenido);
    echo "✓ Migración {$tabla} generada\n";
}

echo "\nMigraciones generadas exitosamente!\n";
