<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoNomina: string
{
    case Borrador = 'borrador';
    case Aprobada = 'aprobada';
    case Pagada = 'pagada';

    public function label(): string
    {
        return match ($this) {
            self::Borrador => 'Borrador',
            self::Aprobada => 'Aprobada',
            self::Pagada => 'Pagada',
        };
    }

    /** Variante de <x-badge> asociada al estado. */
    public function badge(): string
    {
        return match ($this) {
            self::Borrador => 'neutral',
            self::Aprobada => 'sky',
            self::Pagada => 'success',
        };
    }
}
