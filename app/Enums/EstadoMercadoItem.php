<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoMercadoItem: string
{
    case Pendiente  = 'pendiente';
    case Registrado = 'registrado';
    case Saltado    = 'saltado';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente  => 'Pendiente',
            self::Registrado => 'Registrado',
            self::Saltado    => 'Saltado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendiente  => 'amber',
            self::Registrado => 'emerald',
            self::Saltado    => 'stone',
        };
    }
}
