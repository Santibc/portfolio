<?php

namespace App\Services;

use App\Helpers\NumeroALetras;
use App\Models\SolicitudCotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para gestión de facturación
 */
class FacturacionService
{
    /**
     * Generar número de factura secuencial
     */
    public function generarNumeroFactura(): string
    {
        $ultimaFactura = SolicitudCotizacion::whereNotNull('numero_factura')
            ->orderBy('numero_factura', 'desc')
            ->first();

        if ($ultimaFactura && preg_match('/FV-(\d+)/', $ultimaFactura->numero_factura, $matches)) {
            $siguiente = (int) $matches[1] + 1;
        } else {
            $siguiente = 1;
        }

        return 'FV-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular IVA
     */
    public function calcularIva(float $subtotal, float $porcentajeIva): array
    {
        $valorIva = $subtotal * ($porcentajeIva / 100);
        $total = $subtotal + $valorIva;

        return [
            'subtotal' => $subtotal,
            'porcentaje_iva' => $porcentajeIva,
            'valor_iva' => $valorIva,
            'total' => $total,
        ];
    }

    /**
     * Generar factura para una cotización
     */
    public function generarFactura(
        SolicitudCotizacion $solicitud,
        int $usuarioId,
        ?float $porcentajeIva = null,
        ?string $formaPago = 'Contado',
        ?int $diasVencimiento = 0
    ): array {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
        ];

        // Validaciones
        if (!$solicitud->puedeGenerarFactura()) {
            $resultado['exito'] = false;

            if ($solicitud->tieneFactura()) {
                $resultado['errores'][] = 'Esta cotización ya tiene factura generada';
            } elseif (!$solicitud->estaPagada()) {
                $resultado['errores'][] = 'La cotización debe estar pagada para generar factura';
            } elseif ($solicitud->estado !== SolicitudCotizacion::ESTADO_APLICADA) {
                $resultado['errores'][] = 'La cotización debe estar aprobada';
            }

            return $resultado;
        }

        DB::beginTransaction();

        try {
            // Generar número de factura
            $numeroFactura = $this->generarNumeroFactura();

            // Calcular IVA si aplica
            $valorIva = null;
            if ($porcentajeIva && $porcentajeIva > 0) {
                $subtotal = $solicitud->items->sum('precio_total');
                $valorIva = $subtotal * ($porcentajeIva / 100);
            }

            // Calcular fecha de vencimiento
            $fechaVencimiento = $diasVencimiento > 0
                ? now()->addDays($diasVencimiento)->toDateString()
                : now()->toDateString();

            // Actualizar solicitud con datos de factura
            $solicitud->update([
                'numero_factura' => $numeroFactura,
                'facturada_en' => now(),
                'facturada_por' => $usuarioId,
                'porcentaje_iva' => $porcentajeIva,
                'valor_iva' => $valorIva,
                'forma_pago_factura' => $formaPago,
                'fecha_vencimiento' => $fechaVencimiento,
            ]);

            DB::commit();

            $resultado['solicitud'] = $solicitud->fresh();

            Log::info("Factura {$numeroFactura} generada para cotización {$solicitud->numero_solicitud}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al generar factura: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al generar la factura: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Generar PDF de factura
     */
    public function generarPdfFactura(SolicitudCotizacion $solicitud)
    {
        if (!$solicitud->tieneFactura()) {
            throw new \Exception('La cotización no tiene factura generada');
        }

        $solicitud->load([
            'cliente',
            'cliente.ciudad',
            'cliente.ciudad.departamento',
            'cliente.vendedor',
            'items.producto.imagenPrincipal',
            'facturadaPor',
        ]);

        // Calcular valor en letras
        $totalConIva = $solicitud->monto_total + ($solicitud->valor_iva ?? 0);
        $valorEnLetras = NumeroALetras::formatoFactura($totalConIva);

        $pdf = PDF::loadView('pdf.factura', [
            'solicitud' => $solicitud,
            'valorEnLetras' => $valorEnLetras,
        ]);

        $pdf->setPaper('letter', 'portrait');

        return $pdf;
    }

    /**
     * Obtener nombre del archivo de factura
     */
    public function getNombreArchivoFactura(SolicitudCotizacion $solicitud): string
    {
        return 'Factura_' . $solicitud->numero_factura . '.pdf';
    }
}
