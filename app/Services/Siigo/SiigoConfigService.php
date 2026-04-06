<?php

namespace App\Services\Siigo;

use App\Models\ConfiguracionPdv;
use Exception;

class SiigoConfigService
{
    private SiigoApiClient $api;

    public function __construct(SiigoApiClient $api)
    {
        $this->api = $api;
    }

    public function testConexion(): array
    {
        try {
            $this->api->getToken(true);
            return ['exito' => true, 'mensaje' => 'Conexión exitosa con SIIGO.'];
        } catch (Exception $e) {
            return ['exito' => false, 'mensaje' => $e->getMessage()];
        }
    }

    public function obtenerDocumentTypes(): array
    {
        try {
            $response = $this->api->get('/v1/document-types', ['type' => 'FV']);
            return $response;
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerCreditNoteTypes(): array
    {
        try {
            $response = $this->api->get('/v1/document-types', ['type' => 'NC']);
            return $response;
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerPaymentTypes(): array
    {
        try {
            $response = $this->api->get('/v1/payment-types', ['document_type' => 'FV']);
            return $response;
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerTaxes(): array
    {
        try {
            return $this->api->get('/v1/taxes');
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerSellers(): array
    {
        try {
            return $this->api->get('/v1/users');
        } catch (Exception $e) {
            return [];
        }
    }

    public function guardarConfiguracion(array $data): void
    {
        $claves = [
            'siigo_activo', 'siigo_modo', 'siigo_username', 'siigo_access_key',
            'siigo_username_test', 'siigo_access_key_test',
            'siigo_partner_id', 'siigo_document_type_id', 'siigo_credit_note_type_id',
            'siigo_payment_type_efectivo_id', 'siigo_payment_type_transferencia_id',
            'siigo_tax_id', 'siigo_seller_id',
            'siigo_consumidor_final_nit', 'siigo_facturar_siempre', 'siigo_max_reintentos',
        ];

        foreach ($claves as $clave) {
            if (array_key_exists($clave, $data)) {
                $valor = $data[$clave] ?? '';
                ConfiguracionPdv::establecer($clave, (string) $valor);
            }
        }

        // Clear token cache when credentials or mode change
        if (isset($data['siigo_username']) || isset($data['siigo_access_key'])
            || isset($data['siigo_username_test']) || isset($data['siigo_access_key_test'])
            || isset($data['siigo_modo'])) {
            SiigoApiClient::limpiarTokenCache();
        }
    }

    public function obtenerConfiguracionActual(): array
    {
        return [
            'siigo_activo' => ConfiguracionPdv::obtenerBoolean('siigo_activo', false),
            'siigo_modo' => ConfiguracionPdv::obtener('siigo_modo', 'test'),
            'siigo_username' => ConfiguracionPdv::obtener('siigo_username', ''),
            'siigo_access_key' => ConfiguracionPdv::obtener('siigo_access_key', ''),
            'siigo_username_test' => ConfiguracionPdv::obtener('siigo_username_test', ''),
            'siigo_access_key_test' => ConfiguracionPdv::obtener('siigo_access_key_test', ''),
            'siigo_partner_id' => ConfiguracionPdv::obtener('siigo_partner_id', 'MiraclePdV'),
            'siigo_document_type_id' => ConfiguracionPdv::obtener('siigo_document_type_id', ''),
            'siigo_credit_note_type_id' => ConfiguracionPdv::obtener('siigo_credit_note_type_id', ''),
            'siigo_payment_type_efectivo_id' => ConfiguracionPdv::obtener('siigo_payment_type_efectivo_id', ''),
            'siigo_payment_type_transferencia_id' => ConfiguracionPdv::obtener('siigo_payment_type_transferencia_id', ''),
            'siigo_tax_id' => ConfiguracionPdv::obtener('siigo_tax_id', ''),
            'siigo_seller_id' => ConfiguracionPdv::obtener('siigo_seller_id', ''),
            'siigo_consumidor_final_nit' => ConfiguracionPdv::obtener('siigo_consumidor_final_nit', '222222222222'),
            'siigo_facturar_siempre' => ConfiguracionPdv::obtenerBoolean('siigo_facturar_siempre', false),
            'siigo_max_reintentos' => ConfiguracionPdv::obtenerNumero('siigo_max_reintentos', 3),
        ];
    }
}
