<?php

namespace App\Console\Commands;

use App\Models\PagoSolicitud;
use App\Models\SolicitudCotizacion;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepararPagosDescuadrados extends Command
{
    protected $signature = 'pagos:reparar-descuadrados
                            {--dry-run : Mostrar las solicitudes a reparar sin aplicar cambios}
                            {--force : Ejecutar sin pedir confirmación}
                            {--incluir-historicos : También recalcular cotizaciones donde monto_pagado > suma de pagos aprobados (placeholders de crédito antiguos)}';

    protected $description = 'Repara cotizaciones cuyo monto_pagado/estado_pago quedó desincronizado respecto a sus pagos aprobados (bug truncado en comprobante_pago)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $incluirHistoricos = (bool) $this->option('incluir-historicos');

        $this->info('=== Reparar Pagos Descuadrados ===');
        $this->info('Fecha/Hora: ' . now()->format('Y-m-d H:i:s'));

        if ($dryRun) {
            $this->warn('Modo simulación (dry-run) - No se realizarán cambios');
        }

        // Detectar inconsistencias: monto_pagado almacenado != suma de pagos aprobados reales (sin placeholder de credito)
        // Por defecto solo se reparan las que tienen pagos reales no acreditados (guardado < calculado)
        $operador = $incluirHistoricos ? '!=' : '<';

        $registros = DB::select("
            SELECT * FROM (
                SELECT
                    s.id,
                    s.numero_solicitud,
                    s.estado_pago,
                    s.forma_pago_factura,
                    s.monto_pagado AS guardado,
                    COALESCE((
                        SELECT SUM(p.monto)
                        FROM pagos_solicitud p
                        WHERE p.solicitud_cotizacion_id = s.id
                          AND p.estado = ?
                          AND p.metodo_pago != 'credito'
                    ), 0) AS calculado
                FROM solicitudes_cotizacion s
                WHERE s.deleted_at IS NULL
            ) t
            WHERE ABS(t.guardado - t.calculado) > 0.01
              AND t.guardado {$operador} t.calculado
            ORDER BY t.id ASC
        ", [PagoSolicitud::ESTADO_APROBADO]);

        $total = count($registros);

        if ($total === 0) {
            $this->info('No hay cotizaciones descuadradas para reparar.');
            return 0;
        }

        $this->info("Se encontraron {$total} cotizaciones descuadradas.");
        $this->newLine();

        $this->table(
            ['ID', 'Número', 'Estado pago', 'Forma pago', 'Guardado', 'Calculado', 'Diferencia'],
            collect($registros)->map(function ($r) {
                return [
                    $r->id,
                    $r->numero_solicitud,
                    $r->estado_pago,
                    $r->forma_pago_factura,
                    number_format($r->guardado, 2, ',', '.'),
                    number_format($r->calculado, 2, ',', '.'),
                    number_format($r->calculado - $r->guardado, 2, ',', '.'),
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->warn('Modo dry-run: no se aplicaron cambios.');
            return 0;
        }

        if (!$force && !$this->confirm("¿Confirma reparar {$total} cotizaciones recalculando sus pagos?", false)) {
            $this->warn('Operación cancelada.');
            return 1;
        }

        $reparadas = 0;
        $errores = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($registros as $r) {
            try {
                $solicitud = SolicitudCotizacion::find($r->id);
                if (!$solicitud) {
                    continue;
                }
                $solicitud->recalcularPagos();
                $reparadas++;
            } catch (Exception $e) {
                $errores++;
                Log::error("Error reparando solicitud {$r->numero_solicitud}: " . $e->getMessage());
                $this->newLine();
                $this->error("Error en {$r->numero_solicitud}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Encontradas', $total],
                ['Reparadas', $reparadas],
                ['Errores', $errores],
            ]
        );

        return $errores > 0 ? 1 : 0;
    }
}
