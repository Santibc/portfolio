<?php

namespace App\Services;

use App\Models\Tablero;
use App\Models\TableroColumna;
use App\Models\Tarjeta;
use App\Models\TarjetaComentario;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableroService
{
    public function crearTablero(array $datos, User $user, array $miembros = []): Tablero
    {
        return DB::transaction(function () use ($datos, $user, $miembros) {
            $tablero = Tablero::create(array_merge($datos, [
                'creado_por' => $user->id,
            ]));

            $tablero->miembros()->attach($user->id, ['rol' => 'propietario']);

            foreach ($miembros as $userId) {
                if ($userId != $user->id) {
                    $tablero->miembros()->attach($userId, ['rol' => 'editor']);
                }
            }

            $columnasDefecto = ['Por hacer', 'En progreso', 'Completado'];
            foreach ($columnasDefecto as $i => $nombre) {
                $tablero->columnas()->create([
                    'nombre' => $nombre,
                    'posicion' => $i,
                ]);
            }

            $etiquetasDefecto = [
                ['nombre' => 'Urgente', 'color' => '#ef4444'],
                ['nombre' => 'Importante', 'color' => '#f59e0b'],
                ['nombre' => 'Normal', 'color' => '#3b82f6'],
                ['nombre' => 'Baja', 'color' => '#6b7280'],
                ['nombre' => 'Bug', 'color' => '#dc2626'],
                ['nombre' => 'Mejora', 'color' => '#10b981'],
            ];
            foreach ($etiquetasDefecto as $etiqueta) {
                $tablero->etiquetas()->create($etiqueta);
            }

            return $tablero;
        });
    }

    public function getTablerosParaUsuario(User $user): Collection
    {
        return Tablero::activos()
            ->accesiblesPor($user)
            ->withCount(['columnas'])
            ->withCount(['tarjetas' => function ($q) {
                $q->where('tarjetas.archivada', false);
            }])
            ->with(['miembros', 'creador'])
            ->orderByDesc('updated_at')
            ->get();
    }

    public function moverTarjeta(Tarjeta $tarjeta, int $nuevaColumnaId, int $nuevaPosicion, User $user): void
    {
        DB::transaction(function () use ($tarjeta, $nuevaColumnaId, $nuevaPosicion, $user) {
            $columnaAnterior = $tarjeta->columna;
            $cambioColumna = $columnaAnterior->id !== $nuevaColumnaId;

            // Move card to new column/position
            $tarjeta->update([
                'columna_id' => $nuevaColumnaId,
                'posicion' => $nuevaPosicion,
            ]);

            // Recalculate positions in destination column: insert moved card at nuevaPosicion
            $tarjetasDestino = Tarjeta::where('columna_id', $nuevaColumnaId)
                ->where('id', '!=', $tarjeta->id)
                ->where('archivada', false)
                ->orderBy('posicion')
                ->pluck('id')
                ->toArray();

            // Insert the moved card at the desired position
            array_splice($tarjetasDestino, $nuevaPosicion, 0, [$tarjeta->id]);

            // Update all positions sequentially
            foreach ($tarjetasDestino as $i => $id) {
                Tarjeta::where('id', $id)->update(['posicion' => $i]);
            }

            // Recalculate source column if changed
            if ($cambioColumna) {
                $tarjetasOrigen = Tarjeta::where('columna_id', $columnaAnterior->id)
                    ->where('archivada', false)
                    ->orderBy('posicion')
                    ->pluck('id');
                foreach ($tarjetasOrigen as $i => $id) {
                    Tarjeta::where('id', $id)->update(['posicion' => $i]);
                }

                $nuevaColumna = TableroColumna::find($nuevaColumnaId);
                $this->registrarActividad(
                    $tarjeta, $user,
                    "movió esta tarjeta de {$columnaAnterior->nombre} a {$nuevaColumna->nombre}"
                );
            }
        });
    }

    public function reordenarColumnas(Tablero $tablero, array $posiciones): void
    {
        DB::transaction(function () use ($posiciones) {
            foreach ($posiciones as $columnaId => $posicion) {
                TableroColumna::where('id', $columnaId)->update(['posicion' => $posicion]);
            }
        });
    }

    public function reordenarTarjetas(array $posiciones): void
    {
        DB::transaction(function () use ($posiciones) {
            foreach ($posiciones as $tarjetaId => $posicion) {
                Tarjeta::where('id', $tarjetaId)->update(['posicion' => $posicion]);
            }
        });
    }

    public function registrarActividad(Tarjeta $tarjeta, User $user, string $texto): TarjetaComentario
    {
        return TarjetaComentario::create([
            'tarjeta_id' => $tarjeta->id,
            'user_id' => $user->id,
            'contenido' => $texto,
            'tipo' => 'actividad',
        ]);
    }
}
