<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo', function (Blueprint $table) {
            $table->string('og_title', 150)->nullable()->after('focus_keyword');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->string('og_type', 50)->default('website')->after('og_image');
            $table->string('schema_type')->nullable()->after('og_type');
            $table->json('schema_data')->nullable()->after('schema_type');
        });
    }

    public function down(): void
    {
        Schema::table('seo', function (Blueprint $table) {
            $table->dropColumn(['og_title', 'og_description', 'og_image', 'og_type', 'schema_type', 'schema_data']);
        });
    }
};
