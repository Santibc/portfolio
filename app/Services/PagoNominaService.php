<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoNomina;
use App\Models\NominaDetalle;
use App\Models\PagoNomina;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class PagoNominaService
{
    /**
     * Registra un pago contra una línea de nómina, validando que no supere el
     * saldo pendiente (recalculado dentro de la transacción).
     *
     * @param  array{metodo_pago_id:int|string,monto:int|string,fecha_pago:string,referencia?:string|null}  $datos
     */
    public function registrar(NominaDetalle $detalle, array $datos, int $userId): PagoNomina
    {
        $monto = (int) $datos['monto'];

        return DB::transaction(function () use ($detalle, $datos, $monto, $userId): PagoNomina {
            $pagado = (int) $detalle->pagos()->sum('monto');
            $saldo = max(0, (int) $detalle->neto - $pagado);

            if ($monto > $saldo) {
                throw new DomainException(
                    'El pago ('.$this->formato($monto).') supera el saldo pendiente ('.$this->formato($saldo).').'
                );
            }

            $pago = $detalle->pagos()->create([
                'metodo_pago_id' => (int) $datos['metodo_pago_id'],
                'user_id' => $userId,
                'monto' => $monto,
                'referencia' => $datos['referencia'] ?? null,
                'fecha_pago' => Carbon::parse((string) $datos['fecha_pago'])->toDateString(),
            ]);

            $this->sincronizarEstadoNomina($detalle);

            return $pago;
        });
    }

    /**
     * Registra varios pagos de nómina en una sola transacción (uno por línea).
     *
     * @param  array<int, array{nomina_detalle_id:int|string,metodo_pago_id:int|string,monto:int|string,fecha_pago:string,referencia?:string|null}>  $items
     */
    public function registrarMasivo(array $items, int $userId): int
    {
        if ($items === []) {
            throw new DomainException('Selecciona al menos un empleado para pagar.');
        }

        return DB::transaction(function () use ($items, $userId): int {
            $detallesAfectados = [];

            foreach ($items as $item) {
                $detalle = NominaDetalle::findOrFail((int) $item['nomina_detalle_id']);
                $monto = (int) $item['monto'];

                $pagado = (int) $detalle->pagos()->sum('monto');
                $saldo = max(0, (int) $detalle->neto - $pagado);

                if ($monto > $saldo) {
                    throw new DomainException(
                        $detalle->empleado_nombre.': el pago ('.$this->formato($monto)
                        .') supera el saldo pendiente ('.$this->formato($saldo).').'
                    );
                }

                $detalle->pagos()->create([
                    'metodo_pago_id' => (int) $item['metodo_pago_id'],
                    'user_id' => $userId,
                    'monto' => $monto,
                    'referencia' => $item['referencia'] ?? null,
                    'fecha_pago' => Carbon::parse((string) $item['fecha_pago'])->toDateString(),
                ]);

                $detallesAfectados[$detalle->nomina_id] = true;
            }

            foreach (array_keys($detallesAfectados) as $nominaId) {
                $primerDetalle = NominaDetalle::where('nomina_id', $nominaId)->first();
                if ($primerDetalle !== null) {
                    $this->sincronizarEstadoNomina($primerDetalle);
                }
            }

            return count($items);
        });
    }

    public function eliminar(PagoNomina $pago): void
    {
        DB::transaction(function () use ($pago): void {
            $detalle = $pago->detalle;
            $pago->delete();
            if ($detalle !== null) {
                $this->sincronizarEstadoNomina($detalle->fresh());
            }
        });
    }

    /**
     * Si todas las líneas de la nómina quedan totalmente pagadas, marca la
     * nómina como "pagada"; si deja de estarlo, la revierte a "aprobada".
     */
    private function sincronizarEstadoNomina(NominaDetalle $detalle): void
    {
        $nomina = $detalle->nomina()->with('detalles.pagos')->first();
        if ($nomina === null) {
            return;
        }

        $todasPagadas = $nomina->detalles->every(
            fn (NominaDetalle $d) => (int) $d->pagos->sum('monto') >= (int) $d->neto
        );

        if ($todasPagadas && $nomina->estado !== EstadoNomina::Pagada) {
            $nomina->update(['estado' => EstadoNomina::Pagada->value]);
        } elseif (! $todasPagadas && $nomina->estado === EstadoNomina::Pagada) {
            $nomina->update(['estado' => EstadoNomina::Aprobada->value]);
        }
    }

    private function formato(int $valor): string
    {
        return '$ '.number_format($valor, 0, ',', '.');
    }
}
