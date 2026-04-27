<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('siigo_productos_cache', function (Blueprint $table) {
            $table->id();
            $table->string('siigo_id', 64)->nullable()->unique();
            $table->string('code', 50)->index();
            $table->string('name', 255)->nullable();
            $table->string('reference', 100)->nullable()->index();
            $table->string('account_group_name', 100)->nullable();
            $table->string('type', 50)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->index(['name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('siigo_productos_cache');
    }
};
