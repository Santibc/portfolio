<?php

declare(strict_types=1);

namespace App\Enums;

enum TipoGasto: string
{
    case General = 'general';
    case Turno = 'turno';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Gasto general',
            self::Turno => 'Pago de turno',
        };
    }
}
