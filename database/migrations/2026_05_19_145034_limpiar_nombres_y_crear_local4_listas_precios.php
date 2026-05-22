<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Limpiar saltos de línea y espacios sobrantes en nombres existentes
        $listas = DB::table('listas_precios')->get();
        foreach ($listas as $lista) {
            $limpio = trim(preg_replace('/\s+/', ' ', $lista->nombre));
            if ($limpio !== $lista->nombre) {
                DB::table('listas_precios')
                    ->where('id', $lista->id)
                    ->update(['nombre' => $limpio]);
            }
        }

        // Crear lista local4 si no existe (placeholder hasta que el cliente defina nombre real)
        if (! DB::table('listas_precios')->where('codigo', 'local4')->exists()) {
            DB::table('listas_precios')->insert([
                'nombre'      => 'PRECIO LOCAL 4',
                'codigo'      => 'local4',
                'descripcion' => 'Lista de precios local 4 — pendiente de definir nombre con el cliente.',
                'activo'      => true,
                'orden'       => 6,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No revertimos la limpieza de nombres (no destructivo).
        // Solo borramos la lista local4 si la creamos nosotros.
        DB::table('listas_precios')->where('codigo', 'local4')->delete();
    }
};
