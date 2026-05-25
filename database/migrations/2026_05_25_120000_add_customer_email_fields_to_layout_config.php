<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->string('customer_email_subject', 200)->nullable()->after('admin_notification_email');
            $table->text('customer_email_intro')->nullable()->after('customer_email_subject');
            $table->string('customer_email_next_title', 200)->nullable()->after('customer_email_intro');
            $table->text('customer_email_next_text')->nullable()->after('customer_email_next_title');
            $table->text('customer_email_footer_text')->nullable()->after('customer_email_next_text');
            $table->string('customer_email_signature', 150)->nullable()->after('customer_email_footer_text');
        });
    }

    public function down()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->dropColumn([
                'customer_email_subject',
                'customer_email_intro',
                'customer_email_next_title',
                'customer_email_next_text',
                'customer_email_footer_text',
                'customer_email_signature',
            ]);
        });
    }
};
