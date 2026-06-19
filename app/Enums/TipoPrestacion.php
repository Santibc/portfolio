<?php

declare(strict_types=1);

namespace App\Enums;

enum TipoPrestacion: string
{
    case Prima = 'prima';
    case Cesantias = 'cesantias';
    case Intereses = 'intereses';
    case Vacaciones = 'vacaciones';

    public function label(): string
    {
        return match ($this) {
            self::Prima => 'Prima de servicios',
            self::Cesantias => 'Cesantías',
            self::Intereses => 'Intereses sobre cesantías',
            self::Vacaciones => 'Vacaciones',
        };
    }

    /** Divisor de prorrateo: vacaciones 720 (15 días/año), el resto 360. */
    public function divisor(): int
    {
        return match ($this) {
            self::Vacaciones => 720,
            default => 360,
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Prima => 'primary',
            self::Cesantias => 'sky',
            self::Intereses => 'warning',
            self::Vacaciones => 'success',
        };
    }
}
