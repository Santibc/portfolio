<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Billetera;
use App\Models\TransaccionBilletera;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WalletDemoSeeder extends Seeder
{
    /**
     * Crear transacciones de demostración para un inversionista.
     */
    public function run(): void
    {
        // Buscar el usuario inversionista
        $user = User::where('email', 'inversionista@agromarket.com')->first();

        if (!$user) {
            $this->command->error('Usuario inversionista@agromarket.com no encontrado.');
            return;
        }

        // Obtener o crear billetera
        $billetera = Billetera::where('usuario_id', $user->id)->first();

        if (!$billetera) {
            $billetera = Billetera::create([
                'usuario_id' => $user->id,
                'saldo_disponible' => 0,
                'saldo_bloqueado' => 0,
                'saldo_invertido' => 0,
                'retornos_acumulados' => 0,
                'dividendos_pendientes' => 0,
            ]);
        }

        // Limpiar transacciones anteriores de demo (opcional)
        TransaccionBilletera::where('billetera_id', $billetera->id)->delete();

        // Resetear saldos
        $billetera->update([
            'saldo_disponible' => 0,
            'saldo_bloqueado' => 0,
            'saldo_invertido' => 0,
            'retornos_acumulados' => 0,
            'dividendos_pendientes' => 0,
        ]);

        $saldoActual = 0;

        // Array de transacciones de demostración
        $transacciones = [
            [
                'fecha' => Carbon::now()->subDays(45),
                'tipo' => 'deposito',
                'monto' => 5000000,
                'naturaleza' => 'credito',
                'descripcion' => 'Depósito inicial - Transferencia bancaria',
            ],
            [
                'fecha' => Carbon::now()->subDays(40),
                'tipo' => 'inversion',
                'monto' => 2000000,
                'naturaleza' => 'debito',
                'descripcion' => 'Inversión en Proyecto Café Premium Huila',
            ],
            [
                'fecha' => Carbon::now()->subDays(35),
                'tipo' => 'deposito',
                'monto' => 3000000,
                'naturaleza' => 'credito',
                'descripcion' => 'Depósito - PSE Bancolombia',
            ],
            [
                'fecha' => Carbon::now()->subDays(30),
                'tipo' => 'inversion',
                'monto' => 1500000,
                'naturaleza' => 'debito',
                'descripcion' => 'Inversión en Proyecto Aguacate Hass Antioquia',
            ],
            [
                'fecha' => Carbon::now()->subDays(25),
                'tipo' => 'dividendo',
                'monto' => 125000,
                'naturaleza' => 'credito',
                'descripcion' => 'Dividendo Q4 - Café Premium Huila',
            ],
            [
                'fecha' => Carbon::now()->subDays(20),
                'tipo' => 'inversion',
                'monto' => 2500000,
                'naturaleza' => 'debito',
                'descripcion' => 'Inversión en Proyecto Cacao Tumaco',
            ],
            [
                'fecha' => Carbon::now()->subDays(15),
                'tipo' => 'deposito',
                'monto' => 2000000,
                'naturaleza' => 'credito',
                'descripcion' => 'Depósito - Nequi',
            ],
            [
                'fecha' => Carbon::now()->subDays(10),
                'tipo' => 'dividendo',
                'monto' => 95000,
                'naturaleza' => 'credito',
                'descripcion' => 'Dividendo mensual - Aguacate Hass',
            ],
            [
                'fecha' => Carbon::now()->subDays(7),
                'tipo' => 'retiro',
                'monto' => 500000,
                'naturaleza' => 'debito',
                'descripcion' => 'Retiro a cuenta bancaria ****4532',
            ],
            [
                'fecha' => Carbon::now()->subDays(5),
                'tipo' => 'dividendo',
                'monto' => 180000,
                'naturaleza' => 'credito',
                'descripcion' => 'Dividendo extraordinario - Cacao Tumaco',
            ],
            [
                'fecha' => Carbon::now()->subDays(2),
                'tipo' => 'deposito',
                'monto' => 1000000,
                'naturaleza' => 'credito',
                'descripcion' => 'Depósito - Daviplata',
            ],
        ];

        foreach ($transacciones as $trx) {
            $saldoAnterior = $saldoActual;

            if ($trx['naturaleza'] === 'credito') {
                $saldoActual += $trx['monto'];
            } else {
                $saldoActual -= $trx['monto'];
            }

            TransaccionBilletera::create([
                'codigo_transaccion' => 'TRX-' . $trx['fecha']->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                'billetera_id' => $billetera->id,
                'usuario_id' => $user->id,
                'tipo' => $trx['tipo'],
                'monto' => $trx['monto'],
                'naturaleza' => $trx['naturaleza'],
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoActual,
                'descripcion' => $trx['descripcion'],
                'fecha_transaccion' => $trx['fecha'],
                'created_at' => $trx['fecha'],
                'updated_at' => $trx['fecha'],
            ]);
        }

        // Calcular totales para actualizar billetera
        $totalInvertido = 2000000 + 1500000 + 2500000; // 6,000,000
        $totalRetornos = 125000 + 95000 + 180000; // 400,000
        $dividendosPendientes = 250000; // Próximo dividendo simulado

        // Actualizar billetera con saldos finales
        $billetera->update([
            'saldo_disponible' => $saldoActual,
            'saldo_bloqueado' => 0,
            'saldo_invertido' => $totalInvertido,
            'retornos_acumulados' => $totalRetornos,
            'dividendos_pendientes' => $dividendosPendientes,
        ]);

        $this->command->info('Transacciones de demostración creadas exitosamente.');
        $this->command->info("Saldo disponible: $" . number_format($saldoActual, 0, ',', '.'));
        $this->command->info("Saldo invertido: $" . number_format($totalInvertido, 0, ',', '.'));
        $this->command->info("Retornos acumulados: $" . number_format($totalRetornos, 0, ',', '.'));
        $this->command->info("Dividendos pendientes: $" . number_format($dividendosPendientes, 0, ',', '.'));
        $this->command->info("Total transacciones: " . count($transacciones));
    }
}
