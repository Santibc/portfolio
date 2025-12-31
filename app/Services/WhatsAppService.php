<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Compra;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl = 'https://graph.facebook.com/v18.0';
    private ?string $accessToken;
    private ?string $phoneNumberId;
    private ?string $businessId;

    public function __construct(?Empresa $empresa = null)
    {
        if ($empresa) {
            $this->accessToken = $empresa->whatsapp_api_token;
            $this->phoneNumberId = $empresa->whatsapp_phone_id;
            $this->businessId = $empresa->whatsapp_business_id;
        } else {
            $this->accessToken = config('services.whatsapp.token');
            $this->phoneNumberId = config('services.whatsapp.phone_number_id');
            $this->businessId = config('services.whatsapp.business_id');
        }
    }

    /**
     * Verificar si WhatsApp Business está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) && !empty($this->phoneNumberId);
    }

    /**
     * Enviar mensaje de plantilla
     */
    public function enviarTemplate(string $telefono, string $templateName, array $parameters = [], string $language = 'es'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp Business no está configurado'];
        }

        $telefono = $this->formatearTelefono($telefono);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $telefono,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
            ],
        ];

        // Agregar parámetros si existen
        if (!empty($parameters)) {
            $body['template']['components'] = [
                [
                    'type' => 'body',
                    'parameters' => array_map(function ($param) {
                        return ['type' => 'text', 'text' => $param];
                    }, $parameters),
                ],
            ];
        }

        return $this->enviarMensaje($body);
    }

    /**
     * Enviar mensaje de texto simple
     */
    public function enviarTexto(string $telefono, string $mensaje): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp Business no está configurado'];
        }

        $telefono = $this->formatearTelefono($telefono);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $telefono,
            'type' => 'text',
            'text' => [
                'body' => $mensaje,
            ],
        ];

        return $this->enviarMensaje($body);
    }

    /**
     * Enviar mensaje con botones interactivos
     */
    public function enviarConBotones(string $telefono, string $mensaje, array $botones): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp Business no está configurado'];
        }

        $telefono = $this->formatearTelefono($telefono);

        $botonesFormateados = array_map(function ($boton, $index) {
            return [
                'type' => 'reply',
                'reply' => [
                    'id' => $boton['id'] ?? "btn_{$index}",
                    'title' => substr($boton['titulo'], 0, 20), // Máx 20 caracteres
                ],
            ];
        }, $botones, array_keys($botones));

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $telefono,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $mensaje,
                ],
                'action' => [
                    'buttons' => array_slice($botonesFormateados, 0, 3), // Máx 3 botones
                ],
            ],
        ];

        return $this->enviarMensaje($body);
    }

    /**
     * Enviar notificación de confirmación de pedido
     */
    public function notificarConfirmacionPedido(Compra $compra): array
    {
        $cliente = $compra->cliente;
        $telefono = $cliente->telefono ?? $compra->telefono;

        if (!$telefono) {
            return ['success' => false, 'error' => 'Cliente sin teléfono'];
        }

        $mensaje = "🎉 *¡Gracias por tu compra!*\n\n";
        $mensaje .= "Pedido: *#{$compra->numero_compra}*\n";
        $mensaje .= "Total: *$" . number_format($compra->total, 0, ',', '.') . "*\n\n";
        $mensaje .= "Estado: ✅ Confirmado\n\n";
        $mensaje .= "Te notificaremos cuando tu pedido esté en camino.\n\n";
        $mensaje .= "_{$compra->empresa->nombre}_";

        return $this->enviarTexto($telefono, $mensaje);
    }

    /**
     * Enviar notificación de envío
     */
    public function notificarEnvio(Compra $compra, ?string $urlSeguimiento = null): array
    {
        $cliente = $compra->cliente;
        $telefono = $cliente->telefono ?? $compra->telefono;

        if (!$telefono) {
            return ['success' => false, 'error' => 'Cliente sin teléfono'];
        }

        $mensaje = "📦 *Tu pedido está en camino!*\n\n";
        $mensaje .= "Pedido: *#{$compra->numero_compra}*\n";

        if ($compra->fecha_entrega) {
            $mensaje .= "Fecha estimada: *" . $compra->fecha_entrega->format('d/m/Y') . "*\n";
        }

        if ($compra->horario_entrega) {
            $mensaje .= "Horario: *{$compra->horario_entrega}*\n";
        }

        if ($urlSeguimiento) {
            $mensaje .= "\nSeguimiento: {$urlSeguimiento}\n";
        }

        $mensaje .= "\n_{$compra->empresa->nombre}_";

        return $this->enviarTexto($telefono, $mensaje);
    }

    /**
     * Enviar recordatorio de carrito abandonado
     */
    public function notificarCarritoAbandonado(string $telefono, string $nombreCliente, float $total, string $urlRecuperacion): array
    {
        $mensaje = "🛒 *¡Hola {$nombreCliente}!*\n\n";
        $mensaje .= "Notamos que dejaste productos en tu carrito por un valor de *$" . number_format($total, 0, ',', '.') . "*\n\n";
        $mensaje .= "¿Te gustaría completar tu compra? 🌸\n\n";
        $mensaje .= "👉 {$urlRecuperacion}\n\n";
        $mensaje .= "_¡Tus flores favoritas te esperan!_";

        return $this->enviarTexto($telefono, $mensaje);
    }

    /**
     * Enviar mensaje a través de la API
     */
    private function enviarMensaje(array $body): array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $body);

            if ($response->successful()) {
                Log::info('WhatsApp mensaje enviado', [
                    'to' => $body['to'],
                    'type' => $body['type'],
                    'message_id' => $response->json('messages.0.id'),
                ]);

                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                    'response' => $response->json(),
                ];
            }

            Log::error('WhatsApp error al enviar', [
                'to' => $body['to'],
                'error' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('error.message') ?? 'Error desconocido',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp excepción', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Formatear número de teléfono al formato internacional
     */
    private function formatearTelefono(string $telefono): string
    {
        // Remover todo excepto números
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        // Si empieza con 0, removerlo
        if (str_starts_with($telefono, '0')) {
            $telefono = substr($telefono, 1);
        }

        // Si no tiene código de país (Colombia = 57), agregarlo
        if (strlen($telefono) === 10 && str_starts_with($telefono, '3')) {
            $telefono = '57' . $telefono;
        }

        return $telefono;
    }

    /**
     * Verificar el estado de un mensaje
     */
    public function verificarEstadoMensaje(string $messageId): array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get("{$this->apiUrl}/{$messageId}");

            return [
                'success' => $response->successful(),
                'status' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
