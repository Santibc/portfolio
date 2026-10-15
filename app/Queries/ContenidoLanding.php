<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\Negocio;
use App\Data\TextosLanding;
use App\Models\LandingAjuste;
use App\Models\LandingCifra;
use App\Models\LandingFoto;
use App\Models\LandingGaleriaFoto;
use App\Models\LandingHorario;
use App\Models\LandingPaso;
use App\Models\LandingProducto;
use App\Models\LandingTexto;
use App\Models\LandingValor;

/** Todo lo que necesita la landing pública, leído del módulo "Página web". */
final class ContenidoLanding
{
    /** @return array<string, mixed> variables para la vista landing.index */
    public function obtener(): array
    {
        return [
            'negocio' => Negocio::desde(
                LandingAjuste::actual(),
                LandingHorario::ordenados()->get(),
                config('panaderia.maps_api_key'),
            ),
            'textos' => new TextosLanding(
                LandingTexto::query()->pluck('valor', 'clave')->all(),
            ),
            'cifras' => LandingCifra::ordenados()->get(),
            'valores' => LandingValor::ordenados()->get(),
            'productos' => LandingProducto::activos()->ordenados()->get(),
            'pasos' => LandingPaso::ordenados()->get(),
            'galeria' => LandingGaleriaFoto::ordenados()->get(),
            // slot ("hero", "historia"…) => LandingFoto
            'fotos' => LandingFoto::all()->keyBy(fn (LandingFoto $foto) => $foto->slot->value),
        ];
    }
}
