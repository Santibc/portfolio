<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PagoAhorro;
use App\Models\TrabajadorTurno;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PagosAhorroSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@admin.com')->first();
        if (! $user) {
            $this->command->warn('Sin admin; saltando PagosAhorroSeeder.');

            return;
        }

        // Trabajadores con ahorro aportado (desde gastos.ahorro)
        $trabajadores = TrabajadorTurno::withSum('gastos as total_ahorrado', 'ahorro')
            ->get()
            ->filter(fn ($t) => (int) $t->total_ahorrado > 0);

        if ($trabajadores->isEmpty()) {
            $this->command->warn('Sin ahorros aportados; saltando PagosAhorroSeeder.');

            return;
        }

        $observaciones = [
            'Pago parcial solicitado',
            'Retiro de ahorro',
            'Adelanto de ahorro',
            null,
        ];

        // A ~la mitad de los trabajadores se les paga una parte del ahorro acumulado
        foreach ($trabajadores->random(max(1, (int) floor($trabajadores->count() / 2))) as $trab) {
            $acumulado = (int) $trab->total_ahorrado;
            $monto = (int) floor($acumulado * (rand(20, 60) / 100) / 1000) * 1000;
            if ($monto < 1000) {
                continue;
            }

            PagoAhorro::create([
                'trabajador_turno_id' => $trab->id,
                'user_id' => $user->id,
                'monto' => $monto,
                'observacion' => $observaciones[array_rand($observaciones)],
                'pagado_en' => Carbon::now()->subDays(rand(1, 7)),
            ]);
        }

        $this->command->info('Pagos de ahorro sembrados.');
    }
}
