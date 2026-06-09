<?php

namespace App\Services\Facturacion;

use App\Models\Factura;
use App\Services\Settings\ConfigService;
use Illuminate\Support\Facades\DB;

class NumeradorService
{
    public function __construct(private readonly ConfigService $config) {}

    /**
     * Genera el siguiente consecutivo interno atómicamente (PREFIJO-####).
     * Se usa sólo para facturas NO electrónicas. Si la factura es electrónica,
     * el número oficial lo asigna Siigo/DIAN. El prefijo es configurable desde
     * el módulo de empresa (clave facturacion.prefijo_interno).
     */
    public function siguienteConsecutivoInterno(): string
    {
        $prefijo = (string) ($this->config->get('facturacion.prefijo_interno') ?: 'REM');

        return DB::transaction(function () use ($prefijo): string {
            $ultimo = Factura::query()
                ->lockForUpdate()
                ->where('numero_interno', 'like', "{$prefijo}-%")
                ->orderByDesc('id')
                ->value('numero_interno');

            $siguiente = 1;
            if (is_string($ultimo)) {
                $partes = explode('-', $ultimo);
                $siguiente = (int) end($partes) + 1;
            }

            return sprintf('%s-%04d', $prefijo, $siguiente);
        });
    }
}
