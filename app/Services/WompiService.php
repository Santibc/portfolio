<?php

namespace App\Services;

use App\Models\ConfiguracionPasarela;
use App\Models\Compra;
use App\Models\TransaccionPago;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiService
{
    protected $config;
    protected $baseUrl;

    public function __construct()
    {
        $this->config = ConfiguracionPasarela::obtenerConfiguracionActiva('wompi');
        $this->baseUrl = $this->config && $this->config->modo_prueba 
            ? 'https://sandbox.wompi.co/v1' 
            : 'https://production.wompi.co/v1';
    }

    /**
     * Crear link de pago para una compra
     */
    public function crearLinkPago(Compra $compra, TransaccionPago $transaccion)
    {
        if (!$this->config) {
            throw new \Exception('Configuración de Wompi no encontrada');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config->private_key,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/payment_links', [
                'name' => 'Compra #' . $compra->numero_compra,
                'description' => 'Compra en ' . $compra->empresa->nombre,
                'single_use' => true,
                'collect_shipping' => false,
                'currency' => 'COP',
                'amount_in_cents' => intval($compra->total * 100), // Wompi maneja centavos
                'redirect_url' => route('tienda.pago.confirmacion', [
                    'slug' => $compra->empresa->slug,
                    'referencia' => $transaccion->referencia_transaccion
                ]),
                'customer_email' => $compra->email_cliente,
                'customer_data' => [
                    'full_name' => $compra->nombre_cliente,
                    'phone_number' => $compra->telefono_cliente,
                    'legal_id' => $compra->numero_compra, // O documento del cliente si lo tienes
                ],
                'reference' => $transaccion->referencia_transaccion,
                'expiration_date' => now()->addHours(24)->toIso8601String()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Actualizar transacción con ID de Wompi
                $transaccion->update([
                    'id_transaccion_pasarela' => $data['data']['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'payment_url' => $data['data']['public_url'] ?? null,
                    'data' => $data['data']
                ];
            } else {
                Log::error('Error creando link de pago Wompi', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Error desconocido'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Excepción creando link de pago Wompi: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error de conexión con Wompi'
            ];
        }
    }

    /**
     * Verificar estado de una transacción
     */
    public function verificarTransaccion($transaccionId)
    {
        if (!$this->config) {
            throw new \Exception('Configuración de Wompi no encontrada');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config->public_key
            ])->get($this->baseUrl . '/transactions/' . $transaccionId);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error verificando transacción Wompi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Procesar reembolso
     */
    public function procesarReembolso($transaccionId, $monto = null, $razon = '')
    {
        if (!$this->config) {
            throw new \Exception('Configuración de Wompi no encontrada');
        }

        try {
            $data = [
                'transaction_id' => $transaccionId,
                'reason' => $razon
            ];

            if ($monto !== null) {
                $data['amount_in_cents'] = intval($monto * 100);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config->private_key,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/refunds', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Error procesando reembolso'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error procesando reembolso Wompi: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error de conexión con Wompi'
            ];
        }
    }
}