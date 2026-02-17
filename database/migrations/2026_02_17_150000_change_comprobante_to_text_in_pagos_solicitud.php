<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE pagos_solicitud MODIFY comprobante TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE pagos_solicitud MODIFY comprobante VARCHAR(255) NULL');
    }
};
