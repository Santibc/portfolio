<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoMercado;
use App\Enums\EstadoMercadoItem;
use App\Models\ListaMercado;
use App\Models\Mercado;
use App\Models\MercadoItem;
use App\Models\RegistroMercado;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MercadoSessionService
{
    public function obtenerMercadoActivo(): ?Mercado
    {
        return Mercado::enProgreso()->latest('iniciado_en')->first();
    }

    public function iniciarMercado(int $userId): Mercado
    {
        return DB::transaction(function () use ($userId) {
            if (Mercado::enProgreso()->lockForUpdate()->exists()) {
                throw new DomainException('Ya hay un mercado en progreso. Termínalo antes de iniciar uno nuevo.');
            }

            $lista = ListaMercado::actual();

            $items = $lista->items()->with('producto')->get();

            if ($items->isEmpty()) {
                throw new DomainException('La lista no tiene productos. Agrega productos a la lista antes de iniciar un mercado.');
            }

            $mercado = Mercado::create([
                'lista_id'    => $lista->id,
                'user_id'     => $userId,
                'estado'      => EstadoMercado::EnProgreso->value,
                'iniciado_en' => now(),
            ]);

            foreach ($items as $item) {
                if (! $item->producto || ! $item->producto->activo) {
                    continue;
                }

                MercadoItem::create([
                    'mercado_id'               => $mercado->id,
                    'lista_mercado_item_id'    => $item->id,
                    'producto_mercado_id'      => $item->producto_mercado_id,
                    'tipo_producto_mercado_id' => $item->producto->tipo_id,
                    'cantidad_sugerida'        => $item->cantidad_sugerida,
                    'estado'                   => EstadoMercadoItem::Pendiente->value,
                ]);
            }

            return $mercado->fresh('items');
        });
    }

    public function registrarItem(MercadoItem $item, float $cantidad, int $valor): RegistroMercado
    {
        return DB::transaction(function () use ($item, $cantidad, $valor) {
            $mercado = $item->mercado;

            if ($mercado->estado !== EstadoMercado::EnProgreso) {
                throw new DomainException('Este mercado ya no está en progreso.');
            }

            $registro = RegistroMercado::create([
                'producto_mercado_id' => $item->producto_mercado_id,
                'mercado_id'          => $mercado->id,
                'cantidad'            => $cantidad,
                'valor'               => $valor,
            ]);

            $item->update([
                'estado'              => EstadoMercadoItem::Registrado->value,
                'registro_mercado_id' => $registro->id,
            ]);

            $this->evaluarCierre($mercado->fresh());

            return $registro;
        });
    }

    public function saltarItem(MercadoItem $item): void
    {
        DB::transaction(function () use ($item) {
            $mercado = $item->mercado;

            if ($mercado->estado !== EstadoMercado::EnProgreso) {
                throw new DomainException('Este mercado ya no está en progreso.');
            }

            $item->update([
                'estado'              => EstadoMercadoItem::Saltado->value,
                'registro_mercado_id' => null,
            ]);

            $this->evaluarCierre($mercado->fresh());
        });
    }

    public function evaluarCierre(Mercado $mercado): void
    {
        if ($mercado->estado !== EstadoMercado::EnProgreso) {
            return;
        }

        $quedanPendientes = $mercado->items()
            ->where('estado', EstadoMercadoItem::Pendiente->value)
            ->exists();

        if (! $quedanPendientes) {
            $mercado->update([
                'estado'        => EstadoMercado::Completado->value,
                'finalizado_en' => now(),
            ]);
        }
    }

    public function finalizarManual(Mercado $mercado): void
    {
        if ($mercado->estado !== EstadoMercado::EnProgreso) {
            throw new DomainException('Este mercado no está en progreso.');
        }

        DB::transaction(function () use ($mercado) {
            // Marcar pendientes como saltados (decisión del usuario al cerrar manualmente)
            $mercado->items()
                ->where('estado', EstadoMercadoItem::Pendiente->value)
                ->update(['estado' => EstadoMercadoItem::Saltado->value]);

            $mercado->update([
                'estado'        => EstadoMercado::Completado->value,
                'finalizado_en' => now(),
            ]);
        });
    }

    public function cancelar(Mercado $mercado): void
    {
        if ($mercado->estado !== EstadoMercado::EnProgreso) {
            throw new DomainException('Este mercado no está en progreso.');
        }

        $mercado->update([
            'estado'        => EstadoMercado::Cancelado->value,
            'finalizado_en' => now(),
        ]);
    }

    public function tiposPendientes(Mercado $mercado): Collection
    {
        return $mercado->items()
            ->with('tipo')
            ->get()
            ->groupBy('tipo_producto_mercado_id')
            ->map(function (Collection $items) {
                $tipo = $items->first()->tipo;
                $pendientes  = $items->where('estado', EstadoMercadoItem::Pendiente)->count();
                $registrados = $items->where('estado', EstadoMercadoItem::Registrado)->count();
                $saltados    = $items->where('estado', EstadoMercadoItem::Saltado)->count();
                $total       = $items->count();

                return (object) [
                    'tipo'         => $tipo,
                    'total'        => $total,
                    'pendientes'   => $pendientes,
                    'registrados'  => $registrados,
                    'saltados'     => $saltados,
                    'completados'  => $registrados + $saltados,
                    'progreso'     => $total > 0 ? (int) round((($registrados + $saltados) / $total) * 100) : 0,
                    'finalizado'   => $pendientes === 0,
                ];
            })
            ->sortBy(fn ($g) => $g->finalizado ? 1 : 0)
            ->values();
    }

    public function itemsDeTipo(Mercado $mercado, int $tipoId): Collection
    {
        return $mercado->items()
            ->with(['producto.tipo', 'tipo'])
            ->where('tipo_producto_mercado_id', $tipoId)
            ->get()
            ->sortBy(fn ($i) => [$i->estado->value === 'pendiente' ? 0 : 1, $i->producto?->nombre ?? ''])
            ->values();
    }

    public function puedeEditarPlantilla(): bool
    {
        return $this->obtenerMercadoActivo() === null;
    }
}
