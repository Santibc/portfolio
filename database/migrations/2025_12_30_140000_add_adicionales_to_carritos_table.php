<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carritos', function (Blueprint $table) {
            $table->json('adicionales')->nullable()->after('items');
            $table->decimal('total_adicionales', 12, 2)->default(0)->after('subtotal');
            $table->text('mensaje_tarjeta')->nullable()->after('codigo_descuento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carritos', function (Blueprint $table) {
            $table->dropColumn(['adicionales', 'total_adicionales', 'mensaje_tarjeta']);
        });
    }
};
