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
        Schema::create('cleaning_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Customer Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            // Service Location
            $table->string('street_address');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('unit_apt')->nullable();

            // Schedule
            $table->date('preferred_date');
            $table->string('preferred_time');
            $table->boolean('date_flexible')->default(false);
            $table->boolean('time_flexible')->default(false);

            // Property Details
            $table->string('parking')->nullable();
            $table->string('property_access')->nullable();
            $table->text('access_notes')->nullable();

            // Service Details
            $table->string('square_footage_range'); // e.g., "1000-1500"
            $table->string('service_type'); // initial, weekly, biweekly, monthly, deep_clean, move_out

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('extras_total', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Coupon
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_code')->nullable();

            // Extras (JSON)
            $table->json('extras')->nullable(); // Array of selected extra services with details

            // Status tracking
            $table->enum('status', [
                'pending',       // Order created, waiting for payment
                'processing',    // Payment in process
                'paid',          // Payment successful
                'confirmed',     // Order confirmed by admin
                'scheduled',     // Service scheduled
                'in_progress',   // Service being performed
                'completed',     // Service completed
                'cancelled',     // Order cancelled
                'refunded'       // Payment refunded
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');

            // Indexes
            $table->index('order_number');
            $table->index(['status', 'created_at']);
            $table->index('email');
            $table->index('preferred_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cleaning_orders');
    }
};
