<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\EstadoMercado;
use App\Enums\EstadoMercadoItem;
use App\Models\MercadoItem;
use App\Models\RegistroMercado;

class RegistroMercadoObserver
{
    public function deleting(RegistroMercado $registro): void
    {
        if (! $registro->mercado_id) {
            return;
        }

        $item = MercadoItem::where('registro_mercado_id', $registro->id)->first();
        if (! $item) {
            return;
        }

        $mercado = $item->mercado;
        if (! $mercado || $mercado->estado !== EstadoMercado::EnProgreso) {
            return;
        }

        $item->update([
            'estado'              => EstadoMercadoItem::Pendiente->value,
            'registro_mercado_id' => null,
        ]);
    }
}
