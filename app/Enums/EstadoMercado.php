<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoMercado: string
{
    case EnProgreso = 'en_progreso';
    case Completado = 'completado';
    case Cancelado  = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::EnProgreso => 'En progreso',
            self::Completado => 'Completado',
            self::Cancelado  => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnProgreso => 'amber',
            self::Completado => 'emerald',
            self::Cancelado  => 'rose',
        };
    }
}
