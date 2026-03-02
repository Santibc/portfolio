<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE solicitudes_cotizacion ADD COLUMN sucursal_id BIGINT UNSIGNED NULL AFTER cliente_id');
        DB::statement('ALTER TABLE solicitudes_cotizacion ADD CONSTRAINT fk_solicitud_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE solicitudes_cotizacion DROP FOREIGN KEY fk_solicitud_sucursal');
        DB::statement('ALTER TABLE solicitudes_cotizacion DROP COLUMN sucursal_id');
    }
};
