<?php

namespace App\Services;

use App\Models\Orden;
use Illuminate\Support\Facades\DB;

class OrdenEstadoService
{
    /**
     * Recalcula los 3 estados independientes + totales financieros.
     */
    public function recalcularTodo(Orden $orden): Orden
    {
        $this->recalcularTotales($orden);

        // Solo recalcular estados si no es borrador
        if ($orden->estado_trabajo !== 'borrador') {
            $orden->estado_trabajo = $this->recalcularEstadoTrabajo($orden);
            $orden->estado_entrega = $this->recalcularEstadoEntrega($orden);
        }

        $orden->estado_pago = $this->recalcularEstadoPago($orden);
        $orden->save();

        return $orden;
    }

    /**
     * Recalcula estado_trabajo segun progreso de piezas.
     */
    public function recalcularEstadoTrabajo(Orden $orden): string
    {
        // Borrador y anulada no se recalculan
        if (in_array($orden->estado_trabajo, ['borrador', 'anulada'])) {
            return $orden->estado_trabajo;
        }

        $piezas = $orden->piezas;

        // Sin piezas = venta directa = ejecutada
        if ($piezas->isEmpty()) {
            return 'ejecutada';
        }

        $totalPiezas = $piezas->count();
        $completadas = $piezas->where('porcentaje_avance', '>=', 100)->count();
        $enProgreso = $piezas->where('porcentaje_avance', '>', 0)->count();

        if ($completadas === $totalPiezas) {
            return 'ejecutada';
        }

        if ($completadas > 0) {
            return 'ejecutada_parcialmente';
        }

        if ($enProgreso > 0) {
            return 'en_ejecucion';
        }

        return 'generada';
    }

    /**
     * Recalcula estado_entrega segun flags entregada de piezas.
     */
    public function recalcularEstadoEntrega(Orden $orden): ?string
    {
        if ($orden->estado_trabajo === 'borrador') {
            return null;
        }

        $piezas = $orden->piezas;

        // Sin piezas = venta directa = entregada
        if ($piezas->isEmpty()) {
            return 'entregada';
        }

        $totalPiezas = $piezas->count();
        $entregadas = $piezas->where('entregada', true)->count();

        if ($entregadas === 0) {
            return null;
        }

        if ($entregadas === $totalPiezas) {
            return 'entregada';
        }

        return 'entregada_parcialmente';
    }

    /**
     * Recalcula estado_pago segun saldo.
     */
    public function recalcularEstadoPago(Orden $orden): ?string
    {
        if ($orden->estado_trabajo === 'borrador') {
            return null;
        }

        if ($orden->saldo <= 0) {
            return 'pagado';
        }

        return 'saldo_pendiente';
    }

    /**
     * Recalcula totales financieros desde items y pagos.
     */
    public function recalcularTotales(Orden $orden): Orden
    {
        $orden->subtotal = $orden->items()->sum('subtotal');
        $orden->monto_iva = $orden->items()->sum('monto_iva');
        $orden->total = $orden->subtotal + $orden->monto_iva;
        $orden->total_pagado = $orden->pagos()->where('aprobado', true)->sum('monto');
        $orden->saldo = $orden->total - $orden->total_pagado;

        return $orden;
    }

    /**
     * Genera el siguiente numero consecutivo de orden.
     * Formato: "#0001". Usa lock para evitar colisiones.
     */
    public function generarNumeroConsecutivo(): string
    {
        $maxNumero = DB::table('ordenes')
            ->whereNotNull('numero_orden')
            ->lockForUpdate()
            ->max(DB::raw("CAST(REPLACE(numero_orden, '#', '') AS UNSIGNED)"));

        $siguiente = ($maxNumero ?? 0) + 1;

        return '#' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }
}
