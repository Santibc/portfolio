<?php

namespace App\Services\Siigo;

use App\Models\Factura;
use App\Models\SiigoConfig;
use App\Services\Facturacion\QrGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Emite facturas electrónicas a la DIAN usando Siigo API.
 *
 * Flujo:
 *   1. Validar que la factura esté en estado 'borrador' y que Siigo esté configurado.
 *   2. Armar payload según documentación oficial de Siigo (POST /v1/invoices).
 *   3. Enviar con `stamp.send = true` para timbrado DIAN.
 *   4. Parsear la respuesta: Siigo devuelve `id`, `number`, `stamp.cufe`, `stamp.status`.
 *      NO devuelve el QR — se genera localmente con los datos de la factura + CUFE
 *      siguiendo el Anexo Técnico DIAN 1.9.
 *   5. Guardar en la factura: cufe, qr_html, qr_url, siigo_id, numero_siigo,
 *      stamp_status, siigo_response (para auditoría), marcar como emitida electrónica.
 *
 * @see https://developers.siigo.com/docs/siigoapi/invoice/1-create-invoice
 * @see https://www.dian.gov.co/impuestos/factura-electronica/Documents/Anexo-Tecnico-Factura-Electronica-de-Venta-vr-1-9.pdf
 */
class SiigoEmisionService
{
    public function __construct(
        private readonly SiigoClient $cliente,
        private readonly QrGeneratorService $qr,
        private readonly SiigoClienteService $clienteSiigo,
    ) {}

    /**
     * Emite una factura electrónica ante la DIAN vía Siigo.
     *
     * @throws RuntimeException si la factura no es elegible o Siigo rechaza el documento.
     */
    public function emitir(Factura $factura): Factura
    {
        // La plantilla determina si la factura es nacional o de exportación.
        $factura->loadMissing(['items', 'cliente', 'moneda', 'plantilla']);

        $this->validarElegible($factura);
        $this->validarConfigSiigo($factura);

        // Garantiza que el cliente exista en Siigo (lo crea si hace falta).
        $this->clienteSiigo->resolver($factura->cliente);

        $payload = $this->construirPayload($factura);

        Log::channel('siigo')->info('Siigo emisión – enviando factura', [
            'factura_id' => $factura->id,
            'numero_interno' => $factura->numero_interno,
        ]);

        $response = $this->cliente->request('POST', '/v1/invoices', $payload);

        if ($response->failed()) {
            $mensaje = (string) ($response->json('Errors.0.Message') ?? $response->json('message') ?? 'Error desconocido al timbrar factura.');

            throw new RuntimeException("Siigo rechazó la factura: {$mensaje}");
        }

        $data = (array) $response->json();

        return DB::transaction(function () use ($factura, $data) {
            $stamp = (array) ($data['stamp'] ?? []);
            $stampStatus = (string) ($stamp['status'] ?? 'Unknown');
            $cufe = (string) ($stamp['cufe'] ?? $stamp['cude'] ?? '');

            $factura->fill([
                'siigo_id' => (string) ($data['id'] ?? ''),
                'numero_siigo' => (string) ($data['name'] ?? $data['number'] ?? ''),
                'cufe' => $cufe,
                'stamp_status' => $stampStatus,
                'siigo_response' => $data,
                'es_electronica' => true,
            ]);

            // Generar QR localmente solo si el timbrado fue aceptado y hay CUFE.
            if ($cufe !== '' && $this->stampAceptado($stampStatus)) {
                $factura->cufe = $cufe;
                $factura->qr_html = $this->qr->generarParaFactura($factura);
                $factura->qr_url = $this->qr->urlConsultaDian($cufe);

                // Solo marcar como emitida si DIAN aceptó.
                $factura->estado = 'emitida';
                $factura->emitida_at = now();
            } else {
                // Timbrado rechazado — la factura queda en estado 'borrador' para reintento/corrección.
                Log::channel('siigo')->warning('Siigo timbrado no aceptado', [
                    'factura_id' => $factura->id,
                    'stamp_status' => $stampStatus,
                    'errors' => $stamp['errors'] ?? null,
                ]);
            }

            $factura->save();

            return $factura;
        });
    }

    private function validarElegible(Factura $factura): void
    {
        if (! $factura->esEditable()) {
            throw new RuntimeException('Solo se pueden emitir facturas en estado borrador.');
        }

        if ($factura->items->isEmpty()) {
            throw new RuntimeException('La factura no tiene ítems.');
        }

        if (empty($factura->cliente?->identificacion)) {
            throw new RuntimeException('El cliente no tiene identificación — requerida por la DIAN.');
        }

        if ($factura->esInternacional() && ($factura->tasa_cambio === null || $factura->moneda?->codigo === 'COP')) {
            throw new RuntimeException('Las facturas de exportación requieren moneda extranjera y tasa de cambio (TRM).');
        }
    }

    private function validarConfigSiigo(Factura $factura): void
    {
        $config = SiigoConfig::current();

        if (! $config->activo) {
            throw new RuntimeException('La integración con Siigo está desactivada. Actívala en Configuración → Integración Siigo.');
        }

        // Comunes a nacional y exportación.
        $labels = [
            'seller_id' => 'Vendedor (Seller) ID',
            'payment_type_id' => 'Método de pago ID',
        ];

        if ($factura->esInternacional()) {
            $labels['tipo_documento_export_id'] = 'Tipo documento exportación ID';
        } else {
            $labels['tipo_documento_id'] = 'Tipo documento ID (nacional)';
            $labels['tax_id'] = 'Tax ID (IVA nacional)';
        }

        foreach ($labels as $clave => $label) {
            if (empty($config->{$clave})) {
                throw new RuntimeException("Falta configurar el campo «{$label}» en Configuración → Integración Siigo (/admin/siigo).");
            }
        }
    }

    /**
     * Arma el body del POST /v1/invoices según documentación oficial Siigo.
     *
     * @return array<string, mixed>
     */
    private function construirPayload(Factura $factura): array
    {
        $config = SiigoConfig::current();
        $esExport = $factura->esInternacional();

        // Nacional: tipo de documento estándar. Exportación: tipo de documento de exportación.
        $documentoId = $esExport
            ? (int) $config->tipo_documento_export_id
            : (int) $config->tipo_documento_id;

        $sellerId = (int) $config->seller_id;

        $items = $factura->items->map(function ($item) use ($config, $esExport, $sellerId) {
            $linea = [
                'code' => (string) $item->referencia,
                'description' => (string) $item->descripcion,
                'quantity' => (float) $item->cantidad,
                // En clc el precio_unitario ya es la base (sin IVA): el IVA se suma aparte
                // en FacturaService::recalcular(). NO se divide como en miracle.
                'price' => (float) $item->precio_unitario,
                // Siigo solo acepta descuento por línea como monto fijo. Si la línea
                // usa descuento porcentual, se resuelve al valor efectivo aquí.
                'discount' => $item->descuentoValor(),
                'seller' => $sellerId,
            ];

            // Exportación: exenta de IVA (sin taxes).
            // Nacional: si el ítem tiene impuesto, se referencia el tax_id de IVA configurado.
            if (! $esExport && (float) $item->impuesto_porcentaje > 0) {
                $linea['taxes'] = [['id' => (int) $config->tax_id]];
            }

            return $linea;
        })->all();

        $payload = [
            'document' => [
                'id' => $documentoId,
            ],
            'date' => $factura->fecha->format('Y-m-d'),
            'customer' => [
                'identification' => (string) $factura->cliente->identificacion,
                'branch_office' => 0,
            ],
            'seller' => $sellerId,
            'items' => $items,
            'payments' => [[
                'id' => (int) $config->payment_type_id,
                'value' => (float) $factura->total,
                'due_date' => $factura->vencimiento?->format('Y-m-d') ?? $factura->fecha->format('Y-m-d'),
            ]],
            // Timbrado DIAN: requerido para factura electrónica.
            'stamp' => [
                'send' => true,
            ],
            // Envío por correo al cliente (opcional — Siigo lo hace automáticamente si está habilitado).
            'mail' => [
                'send' => false,
            ],
        ];

        if (! empty($factura->observaciones)) {
            $payload['observations'] = (string) $factura->observaciones;
        }

        // Moneda extranjera + TRM. Obligatoria en exportación (validada en validarElegible).
        if ($factura->tasa_cambio !== null && $factura->moneda?->codigo !== 'COP') {
            $payload['currency'] = [
                'code' => (string) $factura->moneda?->codigo,
                'exchange_rate' => (float) $factura->tasa_cambio,
            ];
        }

        return $payload;
    }

    /**
     * Siigo usa diferentes strings para indicar aceptación DIAN según la versión
     * del API: "Accepted", "Aceptado", "Enviado" — aceptamos cualquier variante positiva.
     */
    private function stampAceptado(string $status): bool
    {
        $normalizado = strtolower($status);

        return in_array($normalizado, ['accepted', 'aceptado', 'enviado', 'ok', 'success'], true);
    }
}
