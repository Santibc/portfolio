<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DiaSemana;

class MenuDiaService
{
    /**
     * Sincroniza los items ofrecidos en un día de la semana.
     *
     * @param  array<int,int|string>  $itemIds
     */
    public function sincronizar(DiaSemana $dia, array $itemIds): void
    {
        $ids = array_values(array_unique(array_map('intval', $itemIds)));

        $dia->menuItems()->sync($ids);
    }
}
