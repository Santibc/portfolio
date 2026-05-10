<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si st_clientes ya no existe (migración corrida antes), salir.
        if (!Schema::hasTable('st_clientes')) {
            return;
        }

        // Soltar FKs hacia st_clientes para poder repuntarlos a clientes durante la migración.
        if ($this->fkExists('st_equipos', 'st_equipos_st_cliente_id_foreign')) {
            DB::statement('ALTER TABLE st_equipos DROP FOREIGN KEY st_equipos_st_cliente_id_foreign');
        }
        if ($this->fkExists('st_ordenes_servicio', 'st_ordenes_servicio_st_cliente_id_foreign')) {
            DB::statement('ALTER TABLE st_ordenes_servicio DROP FOREIGN KEY st_ordenes_servicio_st_cliente_id_foreign');
        }

        $stClientes = DB::table('st_clientes')->get();

        // mapping[st_cliente_id] = cliente_id  (clientes unificado)
        $mapping = [];

        foreach ($stClientes as $st) {
            $clienteId = null;

            // 1) Match por documento
            if (!empty($st->numero_documento)) {
                $clienteId = DB::table('clientes')
                    ->where('numero_identificacion', $st->numero_documento)
                    ->value('id');
            }

            // 2) Match por email (si no hubo por documento)
            if (!$clienteId && !empty($st->email)) {
                $clienteId = DB::table('clientes')
                    ->whereRaw('LOWER(email) = ?', [strtolower($st->email)])
                    ->value('id');
            }

            if ($clienteId) {
                // Actualizar SOLO campos vacíos del cliente existente con datos de ST
                $cliente = DB::table('clientes')->where('id', $clienteId)->first();
                $updates = [];

                if (empty($cliente->tipo_documento) && !empty($st->tipo_documento)) {
                    $updates['tipo_documento'] = $st->tipo_documento;
                }
                if (empty($cliente->razon_social) && !empty($st->razon_social)) {
                    $updates['razon_social'] = $st->razon_social;
                }
                if (empty($cliente->celular) && !empty($st->celular)) {
                    $updates['celular'] = $st->celular;
                }
                if (empty($cliente->telefono) && !empty($st->telefono)) {
                    $updates['telefono'] = $st->telefono;
                }
                if (empty($cliente->email) && !empty($st->email)) {
                    $updates['email'] = $st->email;
                }
                if (empty($cliente->direccion) && !empty($st->direccion)) {
                    $updates['direccion'] = $st->direccion;
                }
                if (empty($cliente->ciudad_texto) && !empty($st->ciudad)) {
                    $updates['ciudad_texto'] = $st->ciudad;
                }
                if (empty($cliente->departamento_texto) && !empty($st->departamento)) {
                    $updates['departamento_texto'] = $st->departamento;
                }
                // tipo_cliente: si en clientes está como default y ST tiene info, preferir ST
                if (!empty($st->tipo_cliente) && (empty($cliente->tipo_cliente) || $cliente->tipo_cliente === 'particular')) {
                    $updates['tipo_cliente'] = $st->tipo_cliente;
                }
                if (empty($cliente->observaciones) && !empty($st->observaciones)) {
                    $updates['observaciones'] = $st->observaciones;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('clientes')->where('id', $clienteId)->update($updates);
                }
            } else {
                // Insertar nuevo cliente desde ST
                // numero_identificacion = numero_documento (no hay conflicto, ya buscamos por doc)
                $clienteId = DB::table('clientes')->insertGetId([
                    'numero_identificacion' => $st->numero_documento,
                    'tipo_documento'        => $st->tipo_documento,
                    'nombre_contacto'       => $st->nombre_completo,
                    'razon_social'          => $st->razon_social,
                    'email'                 => $st->email,
                    'telefono'              => $st->telefono,
                    'celular'               => $st->celular,
                    'direccion'             => $st->direccion,
                    'ciudad_texto'          => $st->ciudad,
                    'departamento_texto'    => $st->departamento,
                    'tipo_cliente'          => $st->tipo_cliente ?: 'particular',
                    'observaciones'         => $st->observaciones,
                    'pais_id'               => null,
                    'ciudad_id'             => null,
                    'vendedor_id'           => null,
                    'lista_precio_id'       => null,
                    'activo'                => $st->activo,
                    'created_at'            => $st->created_at ?? now(),
                    'updated_at'            => now(),
                ]);
            }

            $mapping[$st->id] = $clienteId;
        }

        // Actualizar FKs en st_equipos y st_ordenes_servicio
        // Hacemos batch updates por valor mapeado
        foreach ($mapping as $oldId => $newId) {
            if ($oldId == $newId) {
                continue; // mismo id, no hace falta
            }

            if (Schema::hasTable('st_equipos') && Schema::hasColumn('st_equipos', 'st_cliente_id')) {
                DB::table('st_equipos')
                    ->where('st_cliente_id', $oldId)
                    ->update(['st_cliente_id' => $newId]);
            }

            if (Schema::hasTable('st_ordenes_servicio') && Schema::hasColumn('st_ordenes_servicio', 'st_cliente_id')) {
                DB::table('st_ordenes_servicio')
                    ->where('st_cliente_id', $oldId)
                    ->update(['st_cliente_id' => $newId]);
            }
        }
    }

    public function down(): void
    {
        // No reversible: los datos ya quedaron unificados. Restaurar desde backup SQL.
    }

    private function fkExists(string $table, string $name): bool
    {
        $row = DB::selectOne(
            "SELECT 1 AS x FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $name]
        );
        return $row !== null;
    }
};
