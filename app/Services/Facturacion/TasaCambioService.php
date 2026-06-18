<?php

namespace App\Services\Facturacion;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Obtiene la tasa de cambio COP por unidad de una moneda extranjera para una
 * fecha dada, lista para precargar el campo `tasa_cambio` de una factura.
 *
 * Fuentes:
 *  - USD → COP: TRM oficial de la Superintendencia Financiera (datos.gov.co,
 *    dataset 32sa-8pi3). Es la tasa fiscalmente válida ante la DIAN.
 *  - Otras monedas (EUR, etc.) → COP: se deriva cruzando la cotización contra
 *    el USD del Banco Central Europeo (frankfurter.app) con la TRM oficial:
 *        moneda → COP = (moneda → USD) * (USD → COP TRM)
 *
 * Ambas APIs son públicas y gratuitas (sin API key). Si alguna falla se lanza
 * RuntimeException con un mensaje claro; el usuario siempre puede digitar la
 * tasa manualmente.
 */
class TasaCambioService
{
    private const TRM_URL = 'https://www.datos.gov.co/resource/32sa-8pi3.json';

    private const CROSS_URL = 'https://api.frankfurter.app';

    private const TIMEOUT_SEGUNDOS = 10;

    private const CACHE_HORAS = 12;

    /**
     * @return array{tasa: float, fuente: string, fecha: string}
     */
    public function obtener(string $codigo, string $fecha): array
    {
        $codigo = strtoupper(trim($codigo));

        if ($codigo === '' || $codigo === 'COP') {
            throw new RuntimeException('La tasa de cambio solo aplica a monedas distintas de COP.');
        }

        $fecha = $this->normalizarFecha($fecha);

        return Cache::remember(
            "tasa_cambio:{$codigo}:{$fecha}",
            now()->addHours(self::CACHE_HORAS),
            fn () => $this->resolver($codigo, $fecha),
        );
    }

    /**
     * @return array{tasa: float, fuente: string, fecha: string}
     */
    private function resolver(string $codigo, string $fecha): array
    {
        $trm = $this->trmOficial($fecha);

        if ($codigo === 'USD') {
            return [
                'tasa' => round($trm['valor'], 4),
                'fuente' => 'TRM oficial (Superintendencia Financiera) '.$trm['fecha'],
                'fecha' => $trm['fecha'],
            ];
        }

        // moneda → USD según el BCE, luego se ancla a la TRM para llegar a COP.
        $monedaPorUsd = $this->cotizacionContraUsd($codigo, $fecha);

        return [
            'tasa' => round($monedaPorUsd * $trm['valor'], 4),
            'fuente' => "Derivada: {$codigo}→USD (BCE) × TRM oficial {$trm['fecha']}",
            'fecha' => $trm['fecha'],
        ];
    }

    /**
     * TRM (COP por USD) vigente en la fecha. Si no hay registro para esa fecha
     * (p. ej. fecha futura o muy reciente), usa la TRM más reciente disponible.
     *
     * @return array{valor: float, fecha: string}
     */
    private function trmOficial(string $fecha): array
    {
        $vigente = $this->consultarTrm([
            '$where' => "vigenciadesde <= '{$fecha}T00:00:00.000' AND vigenciahasta >= '{$fecha}T00:00:00.000'",
            '$order' => 'vigenciadesde DESC',
            '$limit' => 1,
        ]);

        // Fallback: la TRM más reciente publicada (fechas futuras aún sin TRM).
        $registro = $vigente ?? $this->consultarTrm([
            '$order' => 'vigenciadesde DESC',
            '$limit' => 1,
        ]);

        if ($registro === null || ! isset($registro['valor'])) {
            throw new RuntimeException('No se pudo obtener la TRM oficial. Digita la tasa manualmente.');
        }

        return [
            'valor' => (float) $registro['valor'],
            'fecha' => substr((string) ($registro['vigenciadesde'] ?? $fecha), 0, 10),
        ];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>|null
     */
    private function consultarTrm(array $query): ?array
    {
        try {
            $response = Http::timeout(self::TIMEOUT_SEGUNDOS)->acceptJson()->get(self::TRM_URL, $query);
        } catch (\Throwable $e) {
            Log::warning('TasaCambioService: fallo consultando TRM', ['error' => $e->getMessage()]);
            throw new RuntimeException('No se pudo contactar el servicio de la TRM oficial. Digita la tasa manualmente.');
        }

        if ($response->failed()) {
            throw new RuntimeException('El servicio de la TRM oficial respondió con error (HTTP '.$response->status().'). Digita la tasa manualmente.');
        }

        $datos = $response->json();

        return is_array($datos) && isset($datos[0]) && is_array($datos[0]) ? $datos[0] : null;
    }

    /**
     * Cotización de la moneda contra el USD (cuántos USD vale 1 unidad de la
     * moneda) en la fecha, según el Banco Central Europeo (frankfurter.app).
     */
    private function cotizacionContraUsd(string $codigo, string $fecha): float
    {
        try {
            $response = Http::timeout(self::TIMEOUT_SEGUNDOS)->acceptJson()->get(
                self::CROSS_URL.'/'.$fecha,
                ['from' => $codigo, 'to' => 'USD'],
            );
        } catch (\Throwable $e) {
            Log::warning('TasaCambioService: fallo consultando cotización', ['error' => $e->getMessage(), 'codigo' => $codigo]);
            throw new RuntimeException("No se pudo obtener la cotización de {$codigo}. Digita la tasa manualmente.");
        }

        if ($response->failed()) {
            throw new RuntimeException("No hay cotización disponible para {$codigo}. Digita la tasa manualmente.");
        }

        $valor = $response->json('rates.USD');

        if (! is_numeric($valor)) {
            throw new RuntimeException("La moneda {$codigo} no está soportada por el servicio de divisas. Digita la tasa manualmente.");
        }

        return (float) $valor;
    }

    /**
     * Asegura formato Y-m-d; ante un valor inválido usa la fecha de hoy.
     */
    private function normalizarFecha(string $fecha): string
    {
        try {
            return \Illuminate\Support\Carbon::parse($fecha)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }
}
