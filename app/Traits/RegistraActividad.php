<?php

namespace App\Traits;

use App\Models\RegistroActividad;
use Illuminate\Support\Facades\Auth;

trait RegistraActividad
{
    protected function registrarActividad(
        string $accion,
        string $descripcion,
        ?int $ordenId = null,
        ?array $datosExtra = null
    ): RegistroActividad {
        return RegistroActividad::create([
            'usuario_id' => Auth::id(),
            'orden_id' => $ordenId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'datos_extra' => $datosExtra,
        ]);
    }
}
