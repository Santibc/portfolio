<?php

declare(strict_types=1);

namespace App\Enums;

enum TipoPeriodo: string
{
    case Quincenal = 'quincenal';
    case Mensual = 'mensual';

    public function label(): string
    {
        return match ($this) {
            self::Quincenal => 'Quincenal',
            self::Mensual => 'Mensual',
        };
    }

    public function diasBase(): int
    {
        return match ($this) {
            self::Quincenal => 15,
            self::Mensual => 30,
        };
    }
}
