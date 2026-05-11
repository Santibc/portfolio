<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ampliar comprobante_pago de VARCHAR(255) a TEXT.
 * El campo guarda un JSON con todos los archivos comprobantes del último pago aprobado;
 * con varios archivos (6 en algunos casos) excede 255 caracteres y se truncaba,
 * causando que recalcularPagos() fallara y el pago aprobado no actualizara monto_pagado/estado_pago.
 */
return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE solicitudes_cotizacion MODIFY COLUMN comprobante_pago TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE solicitudes_cotizacion MODIFY COLUMN comprobante_pago VARCHAR(255) NULL');
    }
};
