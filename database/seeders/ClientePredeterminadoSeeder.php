<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientePredeterminadoSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $clienteId = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Mostrador',
            'cedula' => null,
            'direccion' => null,
            'correo' => null,
            'celular_1' => null,
            'celular_2' => null,
            'activo' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Guardar referencia en configuracion_sistema
        DB::table('configuracion_sistema')->updateOrInsert(
            ['clave' => 'cliente_predeterminado_id'],
            [
                'valor' => (string) $clienteId,
                'tipo' => 'entero',
                'descripcion' => 'ID del cliente predeterminado (mostrador)',
                'updated_at' => $now,
            ]
        );
    }
}
