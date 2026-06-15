<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Estado de pago de una línea de nómina. NO es una columna: se deriva de la
 * suma de pagos vs. el neto del detalle.
 */
enum EstadoPagoNomina: string
{
    case Pendiente = 'pendiente';
    case Parcial = 'parcial';
    case Pagado = 'pagado';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Parcial => 'Parcial',
            self::Pagado => 'Pagado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::Parcial => 'sky',
            self::Pagado => 'success',
        };
    }
}
