<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // items_prefactura
        DB::statement('ALTER TABLE items_prefactura MODIFY precio_unitario DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY precio_original DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY descuento_valor DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE items_prefactura MODIFY subtotal DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY iva DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE items_prefactura MODIFY total DECIMAL(15,2) NOT NULL');

        // prefacturas
        DB::statement('ALTER TABLE prefacturas MODIFY subtotal DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE prefacturas MODIFY descuento_global DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE prefacturas MODIFY iva DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE prefacturas MODIFY total DECIMAL(15,2) NOT NULL');
    }

    public function down()
    {
        // items_prefactura
        DB::statement('ALTER TABLE items_prefactura MODIFY precio_unitario DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY precio_original DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY descuento_valor DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE items_prefactura MODIFY subtotal DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE items_prefactura MODIFY iva DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE items_prefactura MODIFY total DECIMAL(12,2) NOT NULL');

        // prefacturas
        DB::statement('ALTER TABLE prefacturas MODIFY subtotal DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE prefacturas MODIFY descuento_global DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE prefacturas MODIFY iva DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE prefacturas MODIFY total DECIMAL(12,2) NOT NULL');
    }
};
