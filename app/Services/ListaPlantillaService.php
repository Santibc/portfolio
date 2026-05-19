<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ListaMercado;
use App\Models\ListaMercadoItem;
use App\Models\ProductoMercado;
use DomainException;

class ListaPlantillaService
{
    public function __construct(private MercadoSessionService $session) {}

    private function bloquearSiHayMercadoActivo(): void
    {
        if (! $this->session->puedeEditarPlantilla()) {
            throw new DomainException('Termina el mercado actual para editar la lista.');
        }
    }

    public function agregarProducto(int $productoId, int $cantidadSugerida): ListaMercadoItem
    {
        $this->bloquearSiHayMercadoActivo();

        $producto = ProductoMercado::activos()->find($productoId);
        if (! $producto) {
            throw new DomainException('El producto seleccionado no existe o no está activo.');
        }

        $lista = ListaMercado::actual();

        $existe = $lista->items()->where('producto_mercado_id', $productoId)->exists();
        if ($existe) {
            throw new DomainException('Este producto ya está en la lista.');
        }

        return $lista->items()->create([
            'producto_mercado_id' => $productoId,
            'cantidad_sugerida'   => $cantidadSugerida,
        ]);
    }

    public function actualizarCantidad(ListaMercadoItem $item, int $cantidadSugerida): void
    {
        $this->bloquearSiHayMercadoActivo();

        $item->update(['cantidad_sugerida' => $cantidadSugerida]);
    }

    public function quitar(ListaMercadoItem $item): void
    {
        $this->bloquearSiHayMercadoActivo();

        $item->delete();
    }
}
