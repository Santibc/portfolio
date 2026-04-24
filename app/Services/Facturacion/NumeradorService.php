<?php

namespace App\Services\Facturacion;

use App\Models\Factura;
use App\Services\Settings\ConfigService;
use Illuminate\Support\Facades\DB;

class NumeradorService
{
    public function __construct(private readonly ConfigService $config) {}

    /**
     * Genera el siguiente consecutivo interno atómicamente (REM-YYYY-####).
     * Se usa sólo para facturas NO electrónicas. Si la factura es electrónica,
     * el número oficial lo asigna Siigo/DIAN.
     */
    public function siguienteConsecutivoInterno(): string
    {
        $prefijo = (string) ($this->config->get('facturacion.prefijo_interno') ?: 'REM');
        $ano = date('Y');

        return DB::transaction(function () use ($prefijo, $ano): string {
            $ultimo = Factura::query()
                ->lockForUpdate()
                ->where('numero_interno', 'like', "{$prefijo}-{$ano}-%")
                ->orderByDesc('id')
                ->value('numero_interno');

            $siguiente = 1;
            if (is_string($ultimo)) {
                $partes = explode('-', $ultimo);
                $siguiente = (int) end($partes) + 1;
            }

            return sprintf('%s-%s-%04d', $prefijo, $ano, $siguiente);
        });
    }
}
