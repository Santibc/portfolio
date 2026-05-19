<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TurnoCaja;
use DomainException;
use Illuminate\Support\Facades\DB;

class TurnoCajaService
{
    public function turnoActivo(): ?TurnoCaja
    {
        return TurnoCaja::abierto()->latest('abierto_en')->first();
    }

    public function abrir(int $baseInicial, int $userId, ?string $notas = null): TurnoCaja
    {
        return DB::transaction(function () use ($baseInicial, $userId, $notas) {
            if (TurnoCaja::abierto()->lockForUpdate()->exists()) {
                throw new DomainException('Ya hay un turno de caja abierto. Ciérralo antes de abrir uno nuevo.');
            }

            return TurnoCaja::create([
                'user_apertura_id' => $userId,
                'abierto_en'       => now(),
                'base_inicial'     => $baseInicial,
                'notas'            => $notas,
            ]);
        });
    }

    public function cerrar(TurnoCaja $turno, int $totalDeclarado, int $userId, ?string $notas = null): TurnoCaja
    {
        if ($turno->cerrado_en !== null) {
            throw new DomainException('Este turno ya está cerrado.');
        }

        $turno->fill([
            'user_cierre_id'  => $userId,
            'cerrado_en'      => now(),
            'total_declarado' => $totalDeclarado,
            'notas'           => $notas !== null && $notas !== '' ? trim(($turno->notas ?? '') . "\n[cierre] " . $notas) : $turno->notas,
        ])->save();

        return $turno->fresh();
    }
}
