<?php

declare(strict_types=1);

namespace App\Enums;

enum TipoMovimientoInventario: string
{
    case Compra = 'compra';
    case Venta  = 'venta';
    case Ajuste = 'ajuste';

    public function label(): string
    {
        return match ($this) {
            self::Compra => 'Compra',
            self::Venta  => 'Venta',
            self::Ajuste => 'Ajuste',
        };
    }

    /** Variante de <x-badge>. */
    public function badge(): string
    {
        return match ($this) {
            self::Compra => 'success',
            self::Venta  => 'sky',
            self::Ajuste => 'warning',
        };
    }
}
