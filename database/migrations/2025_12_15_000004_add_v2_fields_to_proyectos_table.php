<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Datos del cultivo (después de descripcion)
            $table->string('tipo_cultivo', 100)->nullable()->after('descripcion');
            $table->decimal('area_hectareas', 10, 2)->nullable()->after('tipo_cultivo');
            $table->enum('etapa_cultivo', ['siembra', 'crecimiento', 'cosecha', 'transformacion', 'otro'])->nullable()->after('area_hectareas');
            $table->integer('ano_inicio_cultivo')->nullable()->after('etapa_cultivo');

            // Detalles del proyecto (después de datos_adicionales)
            $table->text('objetivo_proyecto')->nullable()->after('datos_adicionales');
            $table->text('detalle_proceso_productivo')->nullable()->after('objetivo_proyecto');
            $table->text('cronograma_estimado')->nullable()->after('detalle_proceso_productivo');

            // Datos financieros y específicos por categoría (JSON)
            $table->json('datos_financieros')->nullable()->after('cronograma_estimado');
            $table->json('datos_earn')->nullable()->after('datos_financieros');
            $table->json('datos_futuros')->nullable()->after('datos_earn');
            $table->json('datos_farming')->nullable()->after('datos_futuros');

            // Metadata de creación por admin (después de activo)
            $table->boolean('creado_por_admin')->default(false)->after('activo');
            $table->unsignedBigInteger('admin_creador_id')->nullable()->after('creado_por_admin');

            // Foreign key
            $table->foreign('admin_creador_id')->references('id')->on('users')->onDelete('set null');

            // Index
            $table->index('creado_por_admin');
            $table->index('tipo_cultivo');
        });
    }

    public function down()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['admin_creador_id']);
            $table->dropIndex(['creado_por_admin']);
            $table->dropIndex(['tipo_cultivo']);

            $table->dropColumn([
                'tipo_cultivo',
                'area_hectareas',
                'etapa_cultivo',
                'ano_inicio_cultivo',
                'objetivo_proyecto',
                'detalle_proceso_productivo',
                'cronograma_estimado',
                'datos_financieros',
                'datos_earn',
                'datos_futuros',
                'datos_farming',
                'creado_por_admin',
                'admin_creador_id'
            ]);
        });
    }
};
