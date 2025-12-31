<?php

namespace App\Services;

use App\Models\ConfiguracionPasarela;
use App\Models\Compra;
use App\Models\TransaccionPago;
use Illuminate\Support\Facades\Log;
use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\Options;

class TransbankService
{
    protected $config;
    protected Transaction $transaction;
    protected bool $isProduction;

    // Credenciales de integración/sandbox de Transbank
    const INTEGRATION_COMMERCE_CODE = '597055555532';
    const INTEGRATION_API_KEY = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';

    public function __construct()
    {
        $this->config = ConfiguracionPasarela::obtenerConfiguracionActiva('transbank');

        // Determinar si es producción o integración (sandbox)
        $this->isProduction = $this->config && !$this->config->modo_prueba;

        $this->configurarAmbiente();
    }

    /**
     * Configurar el ambiente de Transbank (integración o producción)
     * Nota: Options constructor order is: apiKey, commerceCode, integrationType
     */
    protected function configurarAmbiente(): void
    {
        if ($this->isProduction && $this->config) {
            // Producción - usar credenciales reales
            $options = new Options(
                $this->config->private_key ?? '',                           // API Key primero
                $this->config->configuracion_adicional['commerce_code'] ?? '', // Commerce Code segundo
                Options::ENVIRONMENT_PRODUCTION
            );
        } else {
            // Integración/Sandbox - usar credenciales de prueba
            $options = new Options(
                self::INTEGRATION_API_KEY,       // API Key primero
                self::INTEGRATION_COMMERCE_CODE, // Commerce Code segundo
                Options::ENVIRONMENT_INTEGRATION
            );
        }

        $this->transaction = new Transaction($options);
    }

    /**
     * Verificar si Transbank está configurado
     */
    public function isConfigured(): bool
    {
        // En modo integración siempre está "configurado"
        if (!$this->isProduction) {
            return true;
        }

        return $this->config &&
            !empty($this->config->configuracion_adicional['commerce_code']) &&
            !empty($this->config->private_key);
    }

    /**
     * Crear una transacción en WebPay Plus
     */
    public function crearTransaccion(Compra $compra, TransaccionPago $transaccion): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Transbank no está configurado correctamente'
            ];
        }

        try {
            $buyOrder = $transaccion->referencia_transaccion;
            $sessionId = 'session_' . $compra->id . '_' . time();
            $amount = intval($compra->total); // WebPay usa enteros (sin decimales para CLP)
            $returnUrl = route('transbank.retorno', ['referencia' => $buyOrder]);

            Log::info('Creando transacción WebPay', [
                'buy_order' => $buyOrder,
                'session_id' => $sessionId,
                'amount' => $amount,
                'return_url' => $returnUrl
            ]);

            $response = $this->transaction->create(
                $buyOrder,
                $sessionId,
                $amount,
                $returnUrl
            );

            // Guardar token en la transacción
            $transaccion->update([
                'token_pasarela' => $response->getToken(),
                'respuesta_pasarela' => [
                    'token' => $response->getToken(),
                    'url' => $response->getUrl(),
                    'created_at' => now()->toIso8601String()
                ]
            ]);

            Log::info('Transacción WebPay creada', [
                'token' => $response->getToken(),
                'url' => $response->getUrl()
            ]);

            return [
                'success' => true,
                'token' => $response->getToken(),
                'url' => $response->getUrl(),
                'form_action' => $response->getUrl(),
                'token_ws' => $response->getToken()
            ];

        } catch (\Exception $e) {
            Log::error('Error creando transacción WebPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Confirmar una transacción (commit)
     */
    public function confirmarTransaccion(string $token): array
    {
        try {
            Log::info('Confirmando transacción WebPay', ['token' => $token]);

            $response = $this->transaction->commit($token);

            $resultado = [
                'success' => true,
                'vci' => $response->getVci(),
                'amount' => $response->getAmount(),
                'status' => $response->getStatus(),
                'buy_order' => $response->getBuyOrder(),
                'session_id' => $response->getSessionId(),
                'card_detail' => $response->getCardDetail(),
                'accounting_date' => $response->getAccountingDate(),
                'transaction_date' => $response->getTransactionDate(),
                'authorization_code' => $response->getAuthorizationCode(),
                'payment_type_code' => $response->getPaymentTypeCode(),
                'response_code' => $response->getResponseCode(),
                'installments_amount' => $response->getInstallmentsAmount(),
                'installments_number' => $response->getInstallmentsNumber(),
                'balance' => $response->getBalance(),
            ];

            Log::info('Transacción WebPay confirmada', $resultado);

            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error confirmando transacción WebPay', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar estado de una transacción
     */
    public function consultarEstado(string $token): array
    {
        try {
            $response = $this->transaction->status($token);

            return [
                'success' => true,
                'vci' => $response->getVci(),
                'amount' => $response->getAmount(),
                'status' => $response->getStatus(),
                'buy_order' => $response->getBuyOrder(),
                'session_id' => $response->getSessionId(),
                'card_detail' => $response->getCardDetail(),
                'authorization_code' => $response->getAuthorizationCode(),
                'payment_type_code' => $response->getPaymentTypeCode(),
                'response_code' => $response->getResponseCode(),
            ];

        } catch (\Exception $e) {
            Log::error('Error consultando estado WebPay', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Reversar una transacción (anular)
     */
    public function reversarTransaccion(string $token, float $amount): array
    {
        try {
            $response = $this->transaction->refund($token, $amount);

            return [
                'success' => true,
                'type' => $response->getType(),
                'authorization_code' => $response->getAuthorizationCode(),
                'authorization_date' => $response->getAuthorizationDate(),
                'nullified_amount' => $response->getNullifiedAmount(),
                'balance' => $response->getBalance(),
                'response_code' => $response->getResponseCode(),
            ];

        } catch (\Exception $e) {
            Log::error('Error reversando transacción WebPay', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Interpretar el código de respuesta
     */
    public static function interpretarCodigoRespuesta(int $responseCode): string
    {
        return match ($responseCode) {
            0 => 'Transacción aprobada',
            -1 => 'Rechazo de transacción - Reintente',
            -2 => 'Transacción debe reintentarse',
            -3 => 'Error en transacción - Reintente',
            -4 => 'Rechazo de emisor - Reintente',
            -5 => 'Rechazo - Loss/Fraud/Capture',
            -6 => 'Rechazo - Error crítico',
            -7 => 'Rechazo - Excede límite',
            -8 => 'Rechazo - Transacción no permitida',
            default => 'Error desconocido'
        };
    }

    /**
     * Interpretar tipo de pago
     */
    public static function interpretarTipoPago(string $paymentTypeCode): string
    {
        return match ($paymentTypeCode) {
            'VD' => 'Débito',
            'VN' => 'Crédito - Sin cuotas',
            'VC' => 'Crédito - Cuotas normales',
            'SI' => 'Crédito - Sin interés',
            'S2' => 'Crédito - 2 cuotas sin interés',
            'NC' => 'Crédito - N cuotas sin interés',
            'VP' => 'Prepago',
            default => 'Desconocido'
        };
    }

    /**
     * Verificar si la transacción fue exitosa
     */
    public static function esTransaccionExitosa(array $response): bool
    {
        return isset($response['response_code']) &&
            $response['response_code'] === 0 &&
            isset($response['status']) &&
            $response['status'] === 'AUTHORIZED';
    }

    /**
     * Obtener modo actual (producción o integración)
     */
    public function getModo(): string
    {
        return $this->isProduction ? 'produccion' : 'integracion';
    }
}
