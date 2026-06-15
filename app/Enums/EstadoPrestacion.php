<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoPrestacion: string
{
    case Pendiente = 'pendiente';
    case Pagada = 'pagada';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Pagada => 'Pagada',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::Pagada => 'success',
        };
    }
}
