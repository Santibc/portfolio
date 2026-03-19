<?php

namespace App\Services;

use App\Models\User;
use App\Models\LogAutorizacionPdv;
use App\Models\ConfiguracionPdv;
use Illuminate\Support\Facades\Hash;

class AutorizacionPdvService
{
    public function verificarPin(string $pin): ?User
    {
        $admins = User::role('admin')->whereNotNull('pin_pdv')->get();

        foreach ($admins as $admin) {
            if (Hash::check($pin, $admin->pin_pdv)) {
                return $admin;
            }
        }

        return null;
    }

    public function registrar(string $tipo, string $referenciaTipo, int $referenciaId, int $solicitanteId, int $autorizadorId, ?array $detalle = null): LogAutorizacionPdv
    {
        return LogAutorizacionPdv::registrar($tipo, $referenciaTipo, $referenciaId, $solicitanteId, $autorizadorId, $detalle);
    }

    public function requiereAutorizacionDescuento(float $porcentajeDescuento, User $usuario): bool
    {
        if ($usuario->hasRole('admin')) {
            return false;
        }

        $maximo = ConfiguracionPdv::obtenerNumero('descuento_maximo_cajero', 15);
        return $porcentajeDescuento > $maximo;
    }

    public function requiereAutorizacionPrecio(): bool
    {
        return ConfiguracionPdv::obtenerBoolean('requiere_pin_precio', true);
    }

    public function requiereAutorizacionDescuentoGlobal(User $usuario): bool
    {
        if ($usuario->hasRole('admin')) {
            return false;
        }

        return ConfiguracionPdv::obtenerBoolean('requiere_pin_descuento_global', true);
    }
}
