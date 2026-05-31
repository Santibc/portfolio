<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TipoGasto;
use App\Models\Gasto;
use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\TrabajadorTurno;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\VentaPago;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TurnosCajaVentasGastosSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@admin.com')->first();
        if (! $user) {
            $this->command->warn('Sin admin; saltando TurnosCajaVentasGastosSeeder.');
            return;
        }

        $menuItems = MenuItem::activos()->get();
        $metodos   = MetodoPago::activos()->get()->keyBy('codigo');
        $trabajadores = TrabajadorTurno::where('activo', true)->get();

        if ($menuItems->isEmpty() || $metodos->isEmpty()) {
            $this->command->warn('Faltan menu items o métodos de pago; saltando.');
            return;
        }

        $efectivo    = $metodos->get('efectivo');
        $noEfectivos = $metodos->reject(fn ($m) => $m->es_efectivo)->values();

        // 14 turnos cerrados (últimos 14 días, uno por día) + 1 turno abierto hoy
        $diasAtras = 14;
        for ($d = $diasAtras; $d >= 1; $d--) {
            $this->crearTurnoCerrado($user, $menuItems, $efectivo, $noEfectivos, $trabajadores, $d);
        }

        $this->crearTurnoAbierto($user, $menuItems, $efectivo, $noEfectivos, $trabajadores);

        $this->command->info("Turnos, ventas y gastos sembrados (15 turnos).");
    }

    private function crearTurnoCerrado(
        User $user,
        $menuItems,
        ?MetodoPago $efectivo,
        $noEfectivos,
        $trabajadores,
        int $diasAtras
    ): void {
        $abierto = Carbon::now()->subDays($diasAtras)->setTime(10, rand(0, 30));
        $cerrado = $abierto->copy()->setTime(rand(20, 22), rand(0, 59));
        $baseInicial = 50000;

        $turno = TurnoCaja::create([
            'user_apertura_id' => $user->id,
            'user_cierre_id'   => $user->id,
            'abierto_en'       => $abierto,
            'base_inicial'     => $baseInicial,
            'cerrado_en'       => $cerrado,
            'total_declarado'  => 0, // se actualiza al final
            'notas'            => null,
            'created_at'       => $abierto,
            'updated_at'       => $cerrado,
        ]);

        $numVentas = rand(18, 35);
        $totalEfectivoCobrado = 0;
        $totalCambioEntregado = 0;

        for ($i = 0; $i < $numVentas; $i++) {
            $fechaVenta = $abierto->copy()->addMinutes(rand(15, (int) max(30, $abierto->diffInMinutes($cerrado) - 15)));
            [$totalVenta, $efRecibido, $cambio] = $this->crearVenta($turno, $user, $menuItems, $efectivo, $noEfectivos, $fechaVenta);
            $totalEfectivoCobrado += $efRecibido;
            $totalCambioEntregado += $cambio;
        }

        // Gastos del turno: 1-2 generales + 1 pago a trabajador
        $totalGastos = 0;
        $cantGenerales = rand(1, 2);
        $observacionesGenerales = ['Recarga gas', 'Compra hielo', 'Reparación lavaplatos', 'Bombillo cocina', 'Domicilio mercado'];
        for ($g = 0; $g < $cantGenerales; $g++) {
            $valor = rand(5000, 25000);
            Gasto::create([
                'turno_caja_id'        => $turno->id,
                'user_id'              => $user->id,
                'tipo'                 => TipoGasto::General,
                'trabajador_turno_id'  => null,
                'metodo_pago_id'       => $efectivo?->id,
                'valor'                => $valor,
                'observacion'          => $observacionesGenerales[array_rand($observacionesGenerales)],
                'created_at'           => $cerrado->copy()->subMinutes(rand(30, 180)),
                'updated_at'           => $cerrado,
            ]);
            $totalGastos += $valor;
        }

        if ($trabajadores->isNotEmpty()) {
            $cantPagosTurno = rand(1, 2);
            $trabSeleccionados = $trabajadores->random(min($cantPagosTurno, $trabajadores->count()));
            $trabSeleccionados = $trabSeleccionados instanceof \Illuminate\Support\Collection ? $trabSeleccionados : collect([$trabSeleccionados]);
            foreach ($trabSeleccionados as $trab) {
                $ahorro = (int) $trab->valor_ahorro_default;
                Gasto::create([
                    'turno_caja_id'        => $turno->id,
                    'user_id'              => $user->id,
                    'tipo'                 => TipoGasto::Turno,
                    'trabajador_turno_id'  => $trab->id,
                    'metodo_pago_id'       => $efectivo?->id,
                    'valor'                => $trab->valor_turno_default,
                    'ahorro'               => $ahorro,
                    'observacion'          => null,
                    'created_at'           => $cerrado->copy()->subMinutes(rand(5, 30)),
                    'updated_at'           => $cerrado,
                ]);
                $totalGastos += $trab->valor_turno_default + $ahorro;
            }
        }

        // Total declarado = base + efectivo cobrado - cambio entregado - gastos +/- pequeña diferencia
        $efectivoEsperado = $baseInicial + $totalEfectivoCobrado - $totalCambioEntregado - $totalGastos;
        $diferencia = rand(-5000, 5000);
        $turno->update([
            'total_declarado' => max(0, $efectivoEsperado + $diferencia),
        ]);
    }

    private function crearTurnoAbierto(
        User $user,
        $menuItems,
        ?MetodoPago $efectivo,
        $noEfectivos,
        $trabajadores
    ): void {
        $abierto = Carbon::now()->startOfDay()->addHours(10)->addMinutes(rand(0, 30));
        $baseInicial = 50000;

        $turno = TurnoCaja::create([
            'user_apertura_id' => $user->id,
            'user_cierre_id'   => null,
            'abierto_en'       => $abierto,
            'base_inicial'     => $baseInicial,
            'cerrado_en'       => null,
            'total_declarado'  => null,
            'notas'            => null,
            'created_at'       => $abierto,
            'updated_at'       => $abierto,
        ]);

        $numVentas = rand(8, 15);
        for ($i = 0; $i < $numVentas; $i++) {
            $fechaVenta = $abierto->copy()->addMinutes(rand(15, (int) max(30, $abierto->diffInMinutes(Carbon::now()) - 5)));
            $this->crearVenta($turno, $user, $menuItems, $efectivo, $noEfectivos, $fechaVenta);
        }

        // Algunos gastos del turno actual
        Gasto::create([
            'turno_caja_id'        => $turno->id,
            'user_id'              => $user->id,
            'tipo'                 => TipoGasto::General,
            'trabajador_turno_id'  => null,
            'metodo_pago_id'       => $efectivo?->id,
            'valor'                => rand(8000, 20000),
            'observacion'          => 'Domicilio mercado',
            'created_at'           => $abierto->copy()->addHours(2),
            'updated_at'           => $abierto->copy()->addHours(2),
        ]);
    }

    /**
     * @return array{0:int,1:int,2:int} [total, efectivoRecibido, cambio]
     */
    private function crearVenta(
        TurnoCaja $turno,
        User $user,
        $menuItems,
        ?MetodoPago $efectivo,
        $noEfectivos,
        Carbon $fecha
    ): array {
        $cantItems = rand(1, 4);
        $itemsElegidos = $menuItems->random(min($cantItems, $menuItems->count()));
        if (! ($itemsElegidos instanceof \Illuminate\Support\Collection)) {
            $itemsElegidos = collect([$itemsElegidos]);
        }

        $total = 0;
        $itemsData = [];
        foreach ($itemsElegidos as $mi) {
            $cantidad = rand(1, 3);
            $subtotal = $mi->precio * $cantidad;
            $total += $subtotal;
            $itemsData[] = [
                'menu_item_id'    => $mi->id,
                'nombre_snapshot' => $mi->nombre,
                'precio_unitario' => $mi->precio,
                'cantidad'        => $cantidad,
                'subtotal'        => $subtotal,
            ];
        }

        // Distribuir pago: 70% solo efectivo, 20% solo no-efectivo, 10% mixto
        $r = rand(1, 100);
        $pagosData = [];
        $efectivoRecibido = 0;
        $cambio = 0;

        if ($r <= 70 && $efectivo) {
            // Solo efectivo, con cambio
            $efectivoRecibido = $this->redondearEfectivo($total);
            $cambio = $efectivoRecibido - $total;
            $pagosData[] = [
                'metodo_pago_id' => $efectivo->id,
                'monto'          => $efectivoRecibido,
                'referencia'     => null,
            ];
        } elseif ($r <= 90 && $noEfectivos->isNotEmpty()) {
            // Solo no-efectivo (exacto)
            $metodo = $noEfectivos->random();
            $pagosData[] = [
                'metodo_pago_id' => $metodo->id,
                'monto'          => $total,
                'referencia'     => $this->referenciaAleatoria($metodo->codigo),
            ];
        } else {
            // Mixto: parte no-efectivo + parte efectivo
            if ($noEfectivos->isNotEmpty() && $efectivo) {
                $partNoEf = (int) round($total * rand(30, 60) / 100);
                $partNoEf = max(1, $partNoEf);
                $partEfRequerido = $total - $partNoEf;
                $efectivoRecibido = $this->redondearEfectivo($partEfRequerido);
                $cambio = $efectivoRecibido - $partEfRequerido;

                $metodoNoEf = $noEfectivos->random();
                $pagosData[] = [
                    'metodo_pago_id' => $metodoNoEf->id,
                    'monto'          => $partNoEf,
                    'referencia'     => $this->referenciaAleatoria($metodoNoEf->codigo),
                ];
                $pagosData[] = [
                    'metodo_pago_id' => $efectivo->id,
                    'monto'          => $efectivoRecibido,
                    'referencia'     => null,
                ];
            } elseif ($efectivo) {
                $efectivoRecibido = $this->redondearEfectivo($total);
                $cambio = $efectivoRecibido - $total;
                $pagosData[] = [
                    'metodo_pago_id' => $efectivo->id,
                    'monto'          => $efectivoRecibido,
                    'referencia'     => null,
                ];
            }
        }

        $venta = Venta::create([
            'turno_caja_id'     => $turno->id,
            'user_id'           => $user->id,
            'total'             => $total,
            'efectivo_recibido' => $efectivoRecibido,
            'cambio'            => $cambio,
            'notas'             => null,
            'created_at'        => $fecha,
            'updated_at'        => $fecha,
        ]);

        foreach ($itemsData as $it) {
            VentaItem::create([
                'venta_id'        => $venta->id,
                'menu_item_id'    => $it['menu_item_id'],
                'nombre_snapshot' => $it['nombre_snapshot'],
                'precio_unitario' => $it['precio_unitario'],
                'cantidad'        => $it['cantidad'],
                'subtotal'        => $it['subtotal'],
                'created_at'      => $fecha,
                'updated_at'      => $fecha,
            ]);
        }

        foreach ($pagosData as $p) {
            VentaPago::create([
                'venta_id'       => $venta->id,
                'metodo_pago_id' => $p['metodo_pago_id'],
                'monto'          => $p['monto'],
                'referencia'     => $p['referencia'],
                'created_at'     => $fecha,
                'updated_at'     => $fecha,
            ]);
        }

        return [$total, $efectivoRecibido, $cambio];
    }

    private function redondearEfectivo(int $total): int
    {
        // Cliente paga billete redondeado hacia arriba a 5000
        $base = (int) (ceil($total / 5000) * 5000);
        if ($base === $total) {
            $base += 5000;
        }
        return $base;
    }

    private function referenciaAleatoria(string $codigo): ?string
    {
        if (rand(1, 100) > 60) {
            return null;
        }
        return strtoupper(substr($codigo, 0, 3)) . '-' . str_pad((string) rand(1000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
