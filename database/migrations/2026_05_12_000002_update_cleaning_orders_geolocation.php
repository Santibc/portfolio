<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cleaning_orders', function (Blueprint $table) {
            // Drop FK and column district_id
            if (Schema::hasColumn('cleaning_orders', 'district_id')) {
                try {
                    $table->dropForeign(['district_id']);
                } catch (\Throwable $e) {
                    // FK puede no existir si se modificó previamente
                }
                $table->dropColumn('district_id');
            }
        });

        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('street_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('formatted_address', 500)->nullable()->after('longitude');
            $table->string('place_id', 255)->nullable()->after('formatted_address');
            $table->string('postcode', 20)->nullable()->after('place_id');
            $table->string('suburb', 150)->nullable()->after('postcode');
            $table->string('state', 50)->nullable()->after('suburb');

            $table->index(['postcode']);
            $table->index(['suburb']);
        });

        // Drop old districts table
        Schema::dropIfExists('districts');
    }

    public function down()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->dropIndex(['postcode']);
            $table->dropIndex(['suburb']);
            $table->dropColumn([
                'latitude', 'longitude', 'formatted_address', 'place_id',
                'postcode', 'suburb', 'state'
            ]);
            $table->unsignedBigInteger('district_id')->nullable()->after('street_address');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
        });
    }
};
