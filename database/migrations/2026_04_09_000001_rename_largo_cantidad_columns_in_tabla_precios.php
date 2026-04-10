<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Renombra columnas para corregir confusion semantica:
     *  - largo_rango_*    -> cantidad_servicios_*  (en realidad eran rangos de cantidad de servicios)
     *  - cantidad_rango_* -> largo_mm_*            (en realidad eran rangos de largo en mm de la pieza)
     *
     * Ver PDF "Tabla valores servicios Cortes cizalla.pdf": cada pagina es un servicio,
     * agrupado por sub-tabla "X-Y Servicios" (cantidad), con filas "1-60 / 61-120 / 121-320 / >320"
     * que son el largo en mm de la pieza.
     *
     * Se usa SQL crudo (ALTER TABLE ... CHANGE) porque doctrine/dbal 4.x no es compatible
     * con Laravel 9 y por tanto Schema::renameColumn() falla.
     */
    public function up()
    {
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE largo_rango_min cantidad_servicios_min INT NOT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE largo_rango_max cantidad_servicios_max INT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE cantidad_rango_min largo_mm_min INT NOT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE cantidad_rango_max largo_mm_max INT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE cantidad_servicios_min largo_rango_min INT NOT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE cantidad_servicios_max largo_rango_max INT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE largo_mm_min cantidad_rango_min INT NOT NULL');
        DB::statement('ALTER TABLE tabla_precios_servicios CHANGE largo_mm_max cantidad_rango_max INT NULL');
    }
};
