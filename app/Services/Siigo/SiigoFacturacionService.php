<?php

namespace App\Services\Siigo;

use App\Models\Cliente;
use App\Models\ConfiguracionPdv;
use App\Models\DevolucionParcialPdv;
use App\Models\FacturaSiigo;
use App\Models\VentaPdv;
use Illuminate\Support\Facades\Log;
use Exception;

class SiigoFacturacionService
{
    private SiigoApiClient $api;

    public function __construct(SiigoApiClient $api)
    {
        $this->api = $api;
    }

    /**
     * Crear factura electrónica para una venta con cliente identificado.
     */
    public function crearFactura(VentaPdv $venta, ?array $datosFiscales = null, bool $enviarEmail = true): FacturaSiigo
    {
        $venta->loadMissing(['items.producto', 'items.variante', 'cliente']);

        $cliente = $venta->cliente;
        $tipoDocumento = 'factura_venta';

        // Resolve or create client in SIIGO
        $customerIdentification = null;
        $emailDestino = null;

        if ($cliente) {
            $this->resolverCliente($cliente, $datosFiscales);
            $customerIdentification = $cliente->numero_identificacion ?? $cliente->nit;
            $emailDestino = $datosFiscales['email'] ?? $cliente->email;
        } elseif ($datosFiscales && !empty($datosFiscales['numero_identificacion'])) {
            $customerIdentification = $datosFiscales['numero_identificacion'];
            $emailDestino = $datosFiscales['email'] ?? null;

            // Create/find the client in SIIGO with provided fiscal data
            $this->resolverClientePorDatosFiscales($datosFiscales);
        }

        if (!$customerIdentification) {
            return $this->crearFacturaConsumidorFinal($venta);
        }

        $payload = $this->construirPayloadFactura($venta, $customerIdentification, $enviarEmail, $emailDestino);

        // Create the factura record first
        $factura = FacturaSiigo::create([
            'venta_pdv_id' => $venta->id,
            'tipo_documento' => $tipoDocumento,
            'siigo_document_type_id' => $payload['document']['id'] ?? null,
            'fecha_emision' => now()->toDateString(),
            'subtotal' => $venta->subtotal,
            'iva' => $venta->iva,
            'total' => $venta->total,
            'estado_dian' => 'pendiente',
            'email_destino' => $emailDestino,
            'siigo_request' => $payload,
            'cliente_id' => $venta->cliente_id,
            'usuario_id' => auth()->id(),
        ]);

        try {
            $factura->incrementarIntento();
            $response = $this->api->post('/v1/invoices', $payload, $factura->id);

            $factura->update([
                'siigo_response' => $response,
                'siigo_invoice_id' => $response['id'] ?? null,
                'numero_factura' => isset($response['prefix'], $response['number'])
                    ? $response['prefix'] . '-' . $response['number']
                    : ($response['name'] ?? null),
            ]);

            $this->procesarEstadoRespuesta($factura, $response);

            // Update venta with factura reference
            $venta->update([
                'factura_siigo_id' => $factura->id,
                'requiere_factura' => true,
            ]);

            return $factura->fresh();
        } catch (Exception $e) {
            $factura->marcarError($e->getMessage());
            Log::error("SIIGO crearFactura error para venta {$venta->numero_venta}: {$e->getMessage()}");
            return $factura->fresh();
        }
    }

    /**
     * Crear factura como Consumidor Final (NIT 222222222222).
     */
    public function crearFacturaConsumidorFinal(VentaPdv $venta): FacturaSiigo
    {
        $venta->loadMissing(['items.producto', 'items.variante']);

        $nitConsumidorFinal = ConfiguracionPdv::obtener('siigo_consumidor_final_nit', '222222222222');
        $payload = $this->construirPayloadFactura($venta, $nitConsumidorFinal, false);

        $factura = FacturaSiigo::create([
            'venta_pdv_id' => $venta->id,
            'tipo_documento' => 'consumidor_final',
            'siigo_document_type_id' => $payload['document']['id'] ?? null,
            'fecha_emision' => now()->toDateString(),
            'subtotal' => $venta->subtotal,
            'iva' => $venta->iva,
            'total' => $venta->total,
            'estado_dian' => 'pendiente',
            'siigo_request' => $payload,
            'cliente_id' => $venta->cliente_id,
            'usuario_id' => auth()->id(),
        ]);

        try {
            $factura->incrementarIntento();

            // Ensure Consumidor Final exists in SIIGO
            $this->asegurarConsumidorFinalEnSiigo($nitConsumidorFinal);

            $response = $this->api->post('/v1/invoices', $payload, $factura->id);

            $factura->update([
                'siigo_response' => $response,
                'siigo_invoice_id' => $response['id'] ?? null,
                'numero_factura' => isset($response['prefix'], $response['number'])
                    ? $response['prefix'] . '-' . $response['number']
                    : ($response['name'] ?? null),
            ]);

            $this->procesarEstadoRespuesta($factura, $response);

            $venta->update([
                'factura_siigo_id' => $factura->id,
                'requiere_factura' => true,
            ]);

            return $factura->fresh();
        } catch (Exception $e) {
            $factura->marcarError($e->getMessage());
            Log::error("SIIGO crearFacturaConsumidorFinal error para venta {$venta->numero_venta}: {$e->getMessage()}");
            return $factura->fresh();
        }
    }

    /**
     * Crear nota crédito electrónica para una factura ya emitida.
     */
    public function crearNotaCredito(FacturaSiigo $facturaOriginal, string $motivo): FacturaSiigo
    {
        if (!$facturaOriginal->siigo_invoice_id) {
            throw new Exception('La factura original no tiene ID de SIIGO.');
        }

        $venta = $facturaOriginal->ventaPdv;
        $venta->loadMissing(['items.producto', 'items.variante']);

        $creditNoteTypeId = (int) ConfiguracionPdv::obtener('siigo_credit_note_type_id');
        if (!$creditNoteTypeId) {
            throw new Exception('No se ha configurado el tipo de documento para notas crédito en SIIGO.');
        }

        $payload = [
            'document' => ['id' => $creditNoteTypeId],
            'date' => now()->format('Y-m-d'),
            'invoice' => $facturaOriginal->siigo_invoice_id,
            'reason' => 1, // Devolución parcial o total
            'observations' => $motivo,
            'items' => $this->construirItems($venta),
            'payments' => $this->construirPayments($venta),
            'stamp' => ['send' => true],
        ];

        $factura = FacturaSiigo::create([
            'venta_pdv_id' => $venta->id,
            'tipo_documento' => 'nota_credito',
            'siigo_document_type_id' => $creditNoteTypeId,
            'fecha_emision' => now()->toDateString(),
            'subtotal' => $venta->subtotal,
            'iva' => $venta->iva,
            'total' => $venta->total,
            'estado_dian' => 'pendiente',
            'siigo_request' => $payload,
            'nota_credito_de' => $facturaOriginal->id,
            'cliente_id' => $venta->cliente_id,
            'usuario_id' => auth()->id(),
        ]);

        try {
            $factura->incrementarIntento();
            $response = $this->api->post('/v1/credit-notes', $payload, $factura->id);

            $factura->update([
                'siigo_response' => $response,
                'siigo_invoice_id' => $response['id'] ?? null,
                'numero_factura' => isset($response['prefix'], $response['number'])
                    ? $response['prefix'] . '-' . $response['number']
                    : ($response['name'] ?? null),
            ]);

            $this->procesarEstadoRespuesta($factura, $response);

            return $factura->fresh();
        } catch (Exception $e) {
            $factura->marcarError($e->getMessage());
            Log::error("SIIGO crearNotaCredito error: {$e->getMessage()}");
            return $factura->fresh();
        }
    }

    /**
     * Crear nota crédito parcial para una devolución parcial.
     */
    public function crearNotaCreditoParcial(FacturaSiigo $facturaOriginal, DevolucionParcialPdv $devolucion, string $motivo): FacturaSiigo
    {
        if (!$facturaOriginal->siigo_invoice_id) {
            throw new Exception('La factura original no tiene ID de SIIGO.');
        }

        $devolucion->loadMissing(['items.producto', 'items.variante']);

        $creditNoteTypeId = (int) ConfiguracionPdv::obtener('siigo_credit_note_type_id');
        if (!$creditNoteTypeId) {
            throw new Exception('No se ha configurado el tipo de documento para notas crédito en SIIGO.');
        }

        $payload = [
            'document' => ['id' => $creditNoteTypeId],
            'date' => now()->format('Y-m-d'),
            'invoice' => $facturaOriginal->siigo_invoice_id,
            'reason' => 1, // Devolución parcial de bienes
            'observations' => $motivo,
            'items' => $this->construirItemsDesdeDevolucion($devolucion),
            'payments' => $this->construirPaymentsDesdeDevolucion($devolucion->ventaPdv, $devolucion),
            'stamp' => ['send' => true],
        ];

        $factura = FacturaSiigo::create([
            'venta_pdv_id' => $devolucion->venta_pdv_id,
            'tipo_documento' => 'nota_credito',
            'siigo_document_type_id' => $creditNoteTypeId,
            'fecha_emision' => now()->toDateString(),
            'subtotal' => $devolucion->subtotal,
            'iva' => $devolucion->iva,
            'total' => $devolucion->total,
            'estado_dian' => 'pendiente',
            'siigo_request' => $payload,
            'nota_credito_de' => $facturaOriginal->id,
            'cliente_id' => $devolucion->ventaPdv->cliente_id,
            'usuario_id' => auth()->id(),
        ]);

        try {
            $factura->incrementarIntento();
            $response = $this->api->post('/v1/credit-notes', $payload, $factura->id);

            $factura->update([
                'siigo_response' => $response,
                'siigo_invoice_id' => $response['id'] ?? null,
                'numero_factura' => isset($response['prefix'], $response['number'])
                    ? $response['prefix'] . '-' . $response['number']
                    : ($response['name'] ?? null),
            ]);

            $this->procesarEstadoRespuesta($factura, $response);

            // Vincular nota crédito con la devolución
            $devolucion->update(['factura_siigo_id' => $factura->id]);

            return $factura->fresh();
        } catch (Exception $e) {
            $factura->marcarError($e->getMessage());
            Log::error("SIIGO crearNotaCreditoParcial error: {$e->getMessage()}");
            return $factura->fresh();
        }
    }

    /**
     * Reintentar factura fallida.
     */
    public function reintentarFactura(FacturaSiigo $factura): FacturaSiigo
    {
        if (!$factura->puedeReintentar()) {
            throw new Exception('Esta factura no se puede reintentar (máximo de intentos alcanzado o ya aprobada).');
        }

        $payload = $factura->siigo_request;
        if (!$payload) {
            throw new Exception('No se encontró el payload original de la factura.');
        }

        // Update date to today
        $payload['date'] = now()->format('Y-m-d');

        try {
            $factura->incrementarIntento();

            $endpoint = $factura->tipo_documento === 'nota_credito' ? '/v1/credit-notes' : '/v1/invoices';
            $response = $this->api->post($endpoint, $payload, $factura->id);

            $factura->update([
                'siigo_response' => $response,
                'siigo_request' => $payload,
                'siigo_invoice_id' => $response['id'] ?? null,
                'numero_factura' => isset($response['prefix'], $response['number'])
                    ? $response['prefix'] . '-' . $response['number']
                    : ($response['name'] ?? null),
            ]);

            $this->procesarEstadoRespuesta($factura, $response);

            return $factura->fresh();
        } catch (Exception $e) {
            $factura->marcarError($e->getMessage());
            throw $e;
        }
    }

    /**
     * Consultar estado actual de una factura en SIIGO/DIAN.
     */
    public function consultarEstado(FacturaSiigo $factura): array
    {
        if (!$factura->siigo_invoice_id) {
            return ['estado' => $factura->estado_dian, 'mensaje' => 'Sin ID de SIIGO'];
        }

        $endpoint = $factura->tipo_documento === 'nota_credito'
            ? "/v1/credit-notes/{$factura->siigo_invoice_id}"
            : "/v1/invoices/{$factura->siigo_invoice_id}";

        try {
            $response = $this->api->get($endpoint, [], $factura->id);
            $this->procesarEstadoRespuesta($factura, $response);

            return [
                'estado' => $factura->estado_dian,
                'cufe' => $factura->cufe,
                'numero' => $factura->numero_factura,
                'mensaje' => 'Estado actualizado correctamente',
            ];
        } catch (Exception $e) {
            return ['estado' => $factura->estado_dian, 'mensaje' => $e->getMessage()];
        }
    }

    /**
     * Obtener URL/contenido del PDF de la factura.
     */
    public function obtenerPdf(FacturaSiigo $factura)
    {
        if (!$factura->siigo_invoice_id) {
            throw new Exception('La factura no tiene ID de SIIGO.');
        }

        return $this->api->getRaw("/v1/invoices/{$factura->siigo_invoice_id}/pdf", $factura->id);
    }

    /**
     * Reenviar email de la factura.
     */
    public function reenviarEmail(FacturaSiigo $factura): bool
    {
        if (!$factura->siigo_invoice_id) {
            throw new Exception('La factura no tiene ID de SIIGO.');
        }

        try {
            $this->api->post("/v1/invoices/{$factura->siigo_invoice_id}/mail", [], $factura->id);
            $factura->update(['estado_envio_email' => 'enviado']);
            return true;
        } catch (Exception $e) {
            $factura->update(['estado_envio_email' => 'error']);
            throw $e;
        }
    }

    // ========================================
    // Private helper methods
    // ========================================

    /**
     * Resolver (buscar o crear) un cliente en SIIGO.
     */
    private function resolverCliente(Cliente $cliente, ?array $datosFiscales = null): void
    {
        if ($cliente->siigo_id) {
            return; // Already synced
        }

        $identificacion = $datosFiscales['numero_identificacion'] ?? $cliente->numero_identificacion ?? $cliente->nit;
        if (!$identificacion) {
            return;
        }

        // Search in SIIGO
        try {
            $response = $this->api->get('/v1/customers', [
                'identification' => $identificacion,
            ]);

            $results = $response['results'] ?? $response;
            if (!empty($results) && is_array($results)) {
                $siigoCustomer = is_array($results[0] ?? null) ? $results[0] : $results;
                if (isset($siigoCustomer['id'])) {
                    $cliente->update(['siigo_id' => $siigoCustomer['id']]);
                    return;
                }
            }
        } catch (Exception $e) {
            // Customer not found, will create below
            Log::info("SIIGO: Cliente {$identificacion} no encontrado, se creará.");
        }

        // Create in SIIGO
        $this->crearClienteEnSiigo($cliente, $datosFiscales);
    }

    /**
     * Resolve client by fiscal data (when no Cliente model linked).
     */
    private function resolverClientePorDatosFiscales(array $datos): void
    {
        $identificacion = $datos['numero_identificacion'] ?? null;
        if (!$identificacion) return;

        try {
            $response = $this->api->get('/v1/customers', [
                'identification' => $identificacion,
            ]);
            $results = $response['results'] ?? $response;
            if (!empty($results) && is_array($results)) {
                return; // Already exists
            }
        } catch (Exception $e) {
            // Will create below
        }

        $tipoDoc = $datos['tipo_documento'] ?? '13';
        $esEmpresa = $tipoDoc === '31';

        $payload = [
            'type' => 'Customer',
            'person_type' => $esEmpresa ? 'Company' : 'Person',
            'id_type' => $tipoDoc,
            'identification' => $identificacion,
            'branch_office' => 0,
            'active' => true,
            'vat_responsible' => $esEmpresa,
            'fiscal_responsibilities' => [['code' => 'R-99-PN']],
            'name' => $esEmpresa
                ? [$datos['razon_social'] ?? $datos['nombre'] ?? 'Cliente']
                : $this->parsearNombre($datos['nombre'] ?? 'Cliente'),
            'contacts' => [
                [
                    'first_name' => $esEmpresa ? ($datos['nombre'] ?? 'Contacto') : (explode(' ', $datos['nombre'] ?? 'Cliente')[0]),
                    'last_name' => $esEmpresa ? '' : (explode(' ', $datos['nombre'] ?? 'Cliente')[1] ?? ''),
                    'email' => $datos['email'] ?? 'sin-email@miracle.com',
                ],
            ],
        ];

        if (!empty($datos['telefono'])) {
            $payload['phones'] = [['indicative' => '57', 'number' => $datos['telefono']]];
        }

        try {
            $this->api->post('/v1/customers', $payload);
        } catch (Exception $e) {
            Log::warning("SIIGO: No se pudo crear cliente por datos fiscales: {$e->getMessage()}");
        }
    }

    /**
     * Create a Miracle client in SIIGO.
     */
    private function crearClienteEnSiigo(Cliente $cliente, ?array $datosFiscales = null): void
    {
        $tipoDoc = $datosFiscales['tipo_documento'] ?? $cliente->tipo_documento ?? ($cliente->es_persona_juridica ? '31' : '13');
        $esEmpresa = $cliente->es_persona_juridica || $tipoDoc === '31';
        $identificacion = $datosFiscales['numero_identificacion'] ?? $cliente->numero_identificacion ?? $cliente->nit;

        $nombre = $esEmpresa
            ? [$cliente->razon_social ?? $cliente->nombre_contacto ?? 'Cliente']
            : $this->parsearNombre($cliente->nombre_contacto ?? 'Cliente');

        $payload = [
            'type' => 'Customer',
            'person_type' => $esEmpresa ? 'Company' : 'Person',
            'id_type' => $tipoDoc,
            'identification' => $identificacion,
            'branch_office' => 0,
            'active' => true,
            'vat_responsible' => $esEmpresa,
            'fiscal_responsibilities' => [['code' => 'R-99-PN']],
            'name' => $nombre,
            'contacts' => [
                [
                    'first_name' => $esEmpresa
                        ? ($cliente->nombre_contacto ?? 'Contacto')
                        : (explode(' ', $cliente->nombre_contacto ?? 'Cliente')[0]),
                    'last_name' => $esEmpresa
                        ? ''
                        : (explode(' ', $cliente->nombre_contacto ?? 'Cliente')[1] ?? ''),
                    'email' => $datosFiscales['email'] ?? $cliente->email ?? 'sin-email@miracle.com',
                ],
            ],
        ];

        if ($cliente->telefono) {
            $payload['phones'] = [['indicative' => '57', 'number' => $cliente->telefono]];
        }

        if ($cliente->direccion) {
            $payload['address'] = [
                'address' => $cliente->direccion,
                'city' => [
                    'country_code' => 'Co',
                    'state_code' => '11',
                    'city_code' => '11001',
                ],
            ];
        }

        try {
            $response = $this->api->post('/v1/customers', $payload);
            if (isset($response['id'])) {
                $cliente->update([
                    'siigo_id' => $response['id'],
                    'tipo_documento' => $tipoDoc,
                ]);
            }
        } catch (Exception $e) {
            Log::warning("SIIGO: No se pudo crear cliente {$identificacion}: {$e->getMessage()}");
            // Don't throw - invoice creation might still work if client exists
        }
    }

    /**
     * Ensure "Consumidor Final" exists in SIIGO.
     */
    private function asegurarConsumidorFinalEnSiigo(string $nit): void
    {
        try {
            $response = $this->api->get('/v1/customers', ['identification' => $nit]);
            $results = $response['results'] ?? $response;
            if (!empty($results)) return;
        } catch (Exception $e) {
            // Will try to create
        }

        try {
            $this->api->post('/v1/customers', [
                'type' => 'Customer',
                'person_type' => 'Person',
                'id_type' => '13',
                'identification' => $nit,
                'branch_office' => 0,
                'active' => true,
                'vat_responsible' => false,
                'fiscal_responsibilities' => [['code' => 'R-99-PN']],
                'name' => ['Consumidor', 'Final'],
                'contacts' => [
                    [
                        'first_name' => 'Consumidor',
                        'last_name' => 'Final',
                        'email' => 'consumidorfinal@miracle.com',
                    ],
                ],
            ]);
        } catch (Exception $e) {
            Log::info("SIIGO: Consumidor Final might already exist: {$e->getMessage()}");
        }
    }

    /**
     * Build the invoice payload for SIIGO.
     */
    private function construirPayloadFactura(VentaPdv $venta, string $customerIdentification, bool $sendEmail = true, ?string $emailDestino = null): array
    {
        $documentTypeId = (int) ConfiguracionPdv::obtener('siigo_document_type_id');
        $sellerId = (int) ConfiguracionPdv::obtener('siigo_seller_id');

        if (!$documentTypeId) {
            throw new Exception('No se ha configurado el tipo de documento para facturas en SIIGO.');
        }

        $payload = [
            'document' => ['id' => $documentTypeId],
            'date' => now()->format('Y-m-d'),
            'customer' => [
                'identification' => $customerIdentification,
                'branch_office' => 0,
            ],
            // Indica a SIIGO que los precios de los ítems ya incluyen IVA, para que
            // calcule la base imponible y el valor del IVA a partir del precio total.
            'tax_included' => true,
            'stamp' => ['send' => true],
            'mail' => ['send' => $sendEmail],
            'observations' => "Venta PdV #{$venta->numero_venta}",
            'items' => $this->construirItems($venta),
            'payments' => $this->construirPayments($venta),
        ];

        return $payload;
    }

    /**
     * Build items array for SIIGO.
     */
    private function construirItems(VentaPdv $venta): array
    {
        $taxId = ConfiguracionPdv::obtener('siigo_tax_id');
        $sellerId = (int) ConfiguracionPdv::obtener('siigo_seller_id');
        $items = [];

        foreach ($venta->items as $item) {
            $producto = $item->producto;
            $variante = $item->variante;

            $code = $variante
                ? ($variante->siigo_product_code
                    ?? $producto->siigo_product_code
                    ?? $variante->sku
                    ?? $variante->referencia_variante
                    ?? $producto->referencia
                    ?? 'PROD-' . $item->producto_id)
                : ($producto->siigo_product_code
                    ?? $producto->referencia
                    ?? 'PROD-' . $item->producto_id);

            $description = $producto->nombre ?? 'Producto';
            if ($variante) {
                $description .= ' - ' . ($variante->referencia_variante ?? $variante->sku ?? '');
            }

            $itemData = [
                'code' => substr($code, 0, 50), // SIIGO code max length
                'description' => substr($description, 0, 250),
                'quantity' => (int) $item->cantidad,
                'price' => round((float) $item->precio_unitario, 2),
            ];

            // Add discount if any
            $descuentoPorcentaje = (float) ($item->descuento_porcentaje ?? 0);
            if ($descuentoPorcentaje > 0) {
                $itemData['discount'] = round($descuentoPorcentaje, 2);
            }

            if ($taxId) {
                $itemData['taxes'] = [['id' => (int) $taxId]];
            }

            // Add seller per item if configured (required when seller_by_item is true in SIIGO)
            if ($sellerId) {
                $itemData['seller'] = $sellerId;
            }

            $items[] = $itemData;
        }

        return $items;
    }

    /**
     * Build payments array for SIIGO.
     * The payment value must match what SIIGO calculates from items (before global discount).
     */
    private function construirPayments(VentaPdv $venta): array
    {
        // Seleccionar tipo de pago según el método de pago de la venta
        if ($venta->metodo_pago === 'efectivo') {
            $paymentTypeId = (int) ConfiguracionPdv::obtener('siigo_payment_type_efectivo_id');
        } else {
            // 'transferencia' y 'mixto' usan el tipo de pago de transferencia
            $paymentTypeId = (int) ConfiguracionPdv::obtener('siigo_payment_type_transferencia_id');
        }

        if (!$paymentTypeId) {
            throw new Exception('No se ha configurado el tipo de pago en SIIGO para el método: ' . ($venta->metodo_pago ?? 'desconocido'));
        }

        // Calculate total from items (as SIIGO sees them) - includes item discounts but NOT global discount
        $totalItems = 0;
        foreach ($venta->items as $item) {
            $itemTotal = ((float) $item->precio_unitario * (int) $item->cantidad);
            $descPorcentaje = (float) ($item->descuento_porcentaje ?? 0);
            if ($descPorcentaje > 0) {
                $itemTotal -= $itemTotal * ($descPorcentaje / 100);
            }
            // Add IVA if applicable
            $totalItems += $itemTotal + (float) ($item->iva ?? 0);
        }

        return [
            [
                'id' => $paymentTypeId,
                'value' => round($totalItems, 2),
                'due_date' => now()->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Parse SIIGO response and update factura status.
     */
    private function procesarEstadoRespuesta(FacturaSiigo $factura, array $response): void
    {
        $stamp = $response['stamp'] ?? null;

        if ($stamp) {
            $status = strtolower($stamp['status'] ?? '');
            $cufe = $stamp['cufe'] ?? $stamp['cude'] ?? null;
            $errors = $stamp['errors'] ?? $stamp['observations'] ?? null;

            if ($cufe && !empty($cufe) && $cufe !== 'string') {
                $factura->marcarAprobada($cufe, $factura->numero_factura);
            } elseif ($status === 'rejected' || $status === 'rechazada') {
                $factura->marcarRechazada($errors);
            }
            // Otherwise stays as 'pendiente'
        }

        $mail = $response['mail'] ?? null;
        if ($mail) {
            $mailStatus = strtolower($mail['status'] ?? '');
            if (in_array($mailStatus, ['sent', 'enviado', 'success'])) {
                $factura->update(['estado_envio_email' => 'enviado']);
            }
        }
    }

    /**
     * Build items array from a partial return (DevolucionParcialPdv).
     */
    private function construirItemsDesdeDevolucion(DevolucionParcialPdv $devolucion): array
    {
        $taxId = ConfiguracionPdv::obtener('siigo_tax_id');
        $sellerId = (int) ConfiguracionPdv::obtener('siigo_seller_id');
        $items = [];

        foreach ($devolucion->items as $itemDev) {
            $producto = $itemDev->producto;
            $variante = $itemDev->variante;

            $code = $variante
                ? ($variante->siigo_product_code
                    ?? $producto->siigo_product_code
                    ?? $variante->sku
                    ?? $variante->referencia_variante
                    ?? $producto->referencia
                    ?? 'PROD-' . $itemDev->producto_id)
                : ($producto->siigo_product_code
                    ?? $producto->referencia
                    ?? 'PROD-' . $itemDev->producto_id);

            $description = $producto->nombre ?? 'Producto';
            if ($variante) {
                $description .= ' - ' . ($variante->referencia_variante ?? $variante->sku ?? '');
            }

            $itemData = [
                'code' => substr($code, 0, 50),
                'description' => substr($description, 0, 250),
                'quantity' => (int) $itemDev->cantidad_devuelta,
                'price' => round((float) $itemDev->precio_unitario, 2),
            ];

            $descuentoPorcentaje = (float) ($itemDev->descuento_porcentaje ?? 0);
            if ($descuentoPorcentaje > 0) {
                $itemData['discount'] = round($descuentoPorcentaje, 2);
            }

            if ($taxId) {
                $itemData['taxes'] = [['id' => (int) $taxId]];
            }

            if ($sellerId) {
                $itemData['seller'] = $sellerId;
            }

            $items[] = $itemData;
        }

        return $items;
    }

    /**
     * Build payments array from a partial return.
     */
    private function construirPaymentsDesdeDevolucion(VentaPdv $venta, DevolucionParcialPdv $devolucion): array
    {
        if ($venta->metodo_pago === 'efectivo') {
            $paymentTypeId = (int) ConfiguracionPdv::obtener('siigo_payment_type_efectivo_id');
        } else {
            $paymentTypeId = (int) ConfiguracionPdv::obtener('siigo_payment_type_transferencia_id');
        }

        if (!$paymentTypeId) {
            throw new Exception('No se ha configurado el tipo de pago en SIIGO para el método: ' . ($venta->metodo_pago ?? 'desconocido'));
        }

        // Calculate total from devolucion items
        $totalItems = 0;
        foreach ($devolucion->items as $itemDev) {
            $itemTotal = (float) $itemDev->precio_unitario * (int) $itemDev->cantidad_devuelta;
            $descPorcentaje = (float) ($itemDev->descuento_porcentaje ?? 0);
            if ($descPorcentaje > 0) {
                $itemTotal -= $itemTotal * ($descPorcentaje / 100);
            }
            $totalItems += $itemTotal + (float) ($itemDev->iva ?? 0);
        }

        return [
            [
                'id' => $paymentTypeId,
                'value' => round($totalItems, 2),
                'due_date' => now()->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Parse a full name into [first_name, last_name] array.
     */
    private function parsearNombre(string $nombre): array
    {
        $parts = explode(' ', trim($nombre), 2);
        return [
            $parts[0] ?? 'Cliente',
            $parts[1] ?? '',
        ];
    }
}
