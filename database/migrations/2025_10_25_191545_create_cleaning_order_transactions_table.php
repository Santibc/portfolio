<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('cleaning_order_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cleaning_order_id');

            // Stripe IDs
            $table->string('stripe_session_id')->unique()->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();

            // Transaction details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Status
            $table->enum('status', [
                'pending',      // Session created
                'processing',   // Payment processing
                'succeeded',    // Payment successful
                'failed',       // Payment failed
                'cancelled',    // Payment cancelled
                'refunded'      // Payment refunded
            ])->default('pending');

            // Payment method
            $table->string('payment_method_type')->nullable(); // card, etc
            $table->string('payment_method_brand')->nullable(); // visa, mastercard, etc
            $table->string('payment_method_last4')->nullable();

            // Stripe response data (for debugging/audit)
            $table->json('stripe_session_data')->nullable();
            $table->json('stripe_payment_intent_data')->nullable();

            // Error tracking
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            // Webhook events tracking
            $table->json('webhook_events')->nullable(); // Array of processed webhook events

            // Timestamps
            $table->timestamp('session_created_at')->nullable();
            $table->timestamp('payment_succeeded_at')->nullable();
            $table->timestamp('payment_failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('cleaning_order_id')->references('id')->on('cleaning_orders')->onDelete('cascade');

            // Indexes
            $table->index('stripe_session_id');
            $table->index('stripe_payment_intent_id');
            $table->index(['cleaning_order_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cleaning_order_transactions');
    }
};
