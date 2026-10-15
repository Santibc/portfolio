<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** Listas de la landing con columna `orden`. */
trait SeOrdena
{
    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('id');
    }

    /** Orden para un elemento nuevo: al final de la lista. */
    public static function siguienteOrden(): int
    {
        return ((int) static::query()->max('orden')) + 1;
    }
}
