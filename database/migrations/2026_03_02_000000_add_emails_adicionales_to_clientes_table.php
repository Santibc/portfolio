<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE clientes ADD COLUMN emails_adicionales JSON NULL AFTER email');
    }

    public function down()
    {
        DB::statement('ALTER TABLE clientes DROP COLUMN emails_adicionales');
    }
};
