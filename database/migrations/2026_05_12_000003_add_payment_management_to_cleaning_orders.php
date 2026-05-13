<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->string('payment_proof_path', 500)->nullable()->after('admin_notes');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof_path');
            $table->string('payment_method_manual', 50)->nullable()->after('payment_proof_uploaded_at');
            $table->string('payment_reference', 255)->nullable()->after('payment_method_manual');
            $table->unsignedBigInteger('confirmed_by_user_id')->nullable()->after('payment_reference');

            $table->foreign('confirmed_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by_user_id']);
            $table->dropColumn([
                'payment_proof_path',
                'payment_proof_uploaded_at',
                'payment_method_manual',
                'payment_reference',
                'confirmed_by_user_id',
            ]);
        });
    }
};
