<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE cleaning_orders MODIFY square_footage_range VARCHAR(255) NULL');
        DB::statement('ALTER TABLE cleaning_orders MODIFY service_type VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE cleaning_orders MODIFY square_footage_range VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE cleaning_orders MODIFY service_type VARCHAR(255) NOT NULL');
    }
};
