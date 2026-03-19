<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->foreignId('sesion_caja_id')->nullable()->after('id')->constrained('sesiones_caja')->nullOnDelete();
            $table->foreignId('caja_id')->nullable()->after('sesion_caja_id')->constrained('cajas')->nullOnDelete();
            $table->foreignId('prefactura_id')->nullable()->after('caja_id');
            $table->foreignId('lista_precio_id')->nullable()->after('nombre_cliente');
            $table->decimal('descuento_global', 10, 2)->default(0)->after('subtotal');
            $table->decimal('monto_recibido', 12, 2)->nullable()->after('monto_transferencia');
            $table->decimal('cambio', 12, 2)->nullable()->after('monto_recibido');
            $table->string('tipo_transferencia')->nullable()->after('cambio');
            $table->string('comprobante_pago')->nullable()->after('tipo_transferencia');
            $table->foreignId('descuento_autorizado_por')->nullable()->after('usuario_id')->constrained('users')->nullOnDelete();
            $table->foreignId('precio_autorizado_por')->nullable()->after('descuento_autorizado_por')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->dropForeign(['sesion_caja_id']);
            $table->dropForeign(['caja_id']);
            $table->dropForeign(['lista_precio_id']);
            $table->dropForeign(['descuento_autorizado_por']);
            $table->dropForeign(['precio_autorizado_por']);
            $table->dropColumn([
                'sesion_caja_id', 'caja_id', 'prefactura_id', 'lista_precio_id',
                'descuento_global', 'monto_recibido', 'cambio',
                'tipo_transferencia', 'comprobante_pago',
                'descuento_autorizado_por', 'precio_autorizado_por'
            ]);
        });
    }
};
