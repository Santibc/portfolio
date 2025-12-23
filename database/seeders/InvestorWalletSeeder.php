<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Billetera;
use Illuminate\Database\Seeder;

class InvestorWalletSeeder extends Seeder
{
    /**
     * Crear billeteras para inversionistas existentes que no tienen una.
     */
    public function run(): void
    {
        // Obtener todos los usuarios con rol Inversionista que no tienen billetera
        $inversionistasSinBilletera = User::role('Inversionista')
            ->whereDoesntHave('billetera')
            ->get();

        $count = 0;

        foreach ($inversionistasSinBilletera as $inversionista) {
            Billetera::create([
                'usuario_id' => $inversionista->id,
                'saldo_disponible' => 0,
                'saldo_bloqueado' => 0,
                'saldo_invertido' => 0,
                'retornos_acumulados' => 0,
                'dividendos_pendientes' => 0,
            ]);
            $count++;
        }

        $this->command->info("Se crearon {$count} billeteras para inversionistas existentes.");
    }
}
