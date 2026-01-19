<?php

namespace App\Services;

use App\Models\MovimientoStock;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class NotaMovimientoService
{
    /**
     * Genera un PDF de nota de entrada
     */
    public function generarNotaEntrada($movimientoId)
    {
        $movimiento = MovimientoStock::with([
            'producto',
            'variante',
            'ubicacion',
            'usuario'
        ])->findOrFail($movimientoId);

        if ($movimiento->tipo_movimiento !== 'entrada') {
            throw new \Exception('El movimiento no es de tipo entrada');
        }

        $numero = $this->generarNumeroNota('ENT', $movimiento);

        $pdf = Pdf::loadView('pdf.nota-entrada', [
            'movimiento' => $movimiento,
            'numero' => $numero,
            'fecha' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        return $pdf;
    }

    /**
     * Genera un PDF de nota de salida
     */
    public function generarNotaSalida($movimientoId)
    {
        $movimiento = MovimientoStock::with([
            'producto',
            'variante',
            'ubicacion',
            'usuario'
        ])->findOrFail($movimientoId);

        if ($movimiento->tipo_movimiento !== 'salida') {
            throw new \Exception('El movimiento no es de tipo salida');
        }

        $numero = $this->generarNumeroNota('SAL', $movimiento);

        $pdf = Pdf::loadView('pdf.nota-salida', [
            'movimiento' => $movimiento,
            'numero' => $numero,
            'fecha' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        return $pdf;
    }

    /**
     * Genera un PDF de nota de ajuste
     */
    public function generarNotaAjuste($movimientoId)
    {
        $movimiento = MovimientoStock::with([
            'producto',
            'variante',
            'ubicacion',
            'usuario'
        ])->findOrFail($movimientoId);

        if ($movimiento->tipo_movimiento !== 'ajuste') {
            throw new \Exception('El movimiento no es de tipo ajuste');
        }

        $numero = $this->generarNumeroNota('AJU', $movimiento);

        $pdf = Pdf::loadView('pdf.nota-ajuste', [
            'movimiento' => $movimiento,
            'numero' => $numero,
            'fecha' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        return $pdf;
    }

    /**
     * Genera un número de nota basado en el movimiento
     */
    private function generarNumeroNota(string $prefijo, MovimientoStock $movimiento): string
    {
        return $prefijo . '-' . $movimiento->created_at->format('Ymd') . '-' . str_pad($movimiento->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Genera un reporte de múltiples movimientos
     */
    public function generarReporteMovimientos(array $movimientoIds, string $tipo = null)
    {
        $query = MovimientoStock::with([
            'producto',
            'variante',
            'ubicacion',
            'usuario'
        ])->whereIn('id', $movimientoIds);

        if ($tipo) {
            $query->where('tipo_movimiento', $tipo);
        }

        $movimientos = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('pdf.reporte-movimientos', [
            'movimientos' => $movimientos,
            'fecha' => Carbon::now()->format('d/m/Y H:i'),
            'tipo' => $tipo,
        ]);

        return $pdf;
    }
}
