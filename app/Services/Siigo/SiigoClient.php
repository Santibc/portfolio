<?php

namespace App\Services\Siigo;

use App\Models\SiigoConfig;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Cliente HTTP para la API oficial de Siigo.
 *
 * Documentación: https://developers.siigo.com/ y https://siigoapi.docs.apiary.io/
 * Todos los paths/payloads se derivan de la documentación pública oficial.
 * Lo que no esté documentado públicamente NO se implementa — se espera confirmación del equipo Siigo.
 */
class SiigoClient
{
    private const BASE_URL = 'https://api.siigo.com';

    private const AUTH_URL = 'https://api.siigo.com/auth';

    private const TIMEOUT_SEGUNDOS = 30;

    private const MAX_REINTENTOS = 3;

    public function __construct(private readonly SiigoConfig $config) {}

    /**
     * Solicita un token OAuth a Siigo. Devuelve el token vigente cacheado si aún no expira.
     */
    public function authenticate(): string
    {
        if ($this->tokenEsVigente()) {
            return (string) $this->config->token_cache;
        }

        if (empty($this->config->username) || empty($this->config->access_key)) {
            throw new RuntimeException('Credenciales Siigo no configuradas.');
        }

        $response = Http::timeout(self::TIMEOUT_SEGUNDOS)
            ->acceptJson()
            ->asJson()
            ->post(self::AUTH_URL, [
                'username' => $this->config->username,
                'access_key' => $this->config->access_key,
            ]);

        if ($response->failed()) {
            Log::channel('siigo')->error('Siigo auth failed', [
                'status' => $response->status(),
                'body' => $this->sanitizarRespuesta($response->body()),
            ]);
            throw new RuntimeException('Fallo autenticando contra Siigo: HTTP '.$response->status());
        }

        $token = (string) $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in', 3600);

        $this->config->forceFill([
            'token_cache' => $token,
            'token_expires_at' => now()->addSeconds(max(60, $expiresIn - 30)),
        ])->save();

        return $token;
    }

    /**
     * Ejecuta un request autenticado, reintentando si el token expiró.
     *
     * @param  array<string, mixed>  $payload
     */
    public function request(string $method, string $path, array $payload = [], int $intento = 1): Response
    {
        $token = $this->authenticate();
        $method = strtoupper($method);
        $url = rtrim(self::BASE_URL, '/').'/'.ltrim($path, '/');

        $pending = $this->cliente($token);

        $response = match ($method) {
            'GET' => $pending->get($url, $payload),
            'POST' => $pending->post($url, $payload),
            'PUT' => $pending->put($url, $payload),
            'PATCH' => $pending->patch($url, $payload),
            'DELETE' => $pending->delete($url, $payload),
            default => throw new RuntimeException("Método HTTP no soportado: {$method}"),
        };

        if ($response->status() === 401 && $intento === 1) {
            $this->forgetToken();

            return $this->request($method, $path, $payload, $intento + 1);
        }

        if ($response->status() === 429 && $intento <= self::MAX_REINTENTOS) {
            $espera = (int) ($response->header('Retry-After') ?: pow(2, $intento));
            Log::channel('siigo')->warning('Siigo rate limit', [
                'path' => $path,
                'intento' => $intento,
                'espera_segundos' => $espera,
            ]);
            sleep(min($espera, 30));

            return $this->request($method, $path, $payload, $intento + 1);
        }

        if ($response->failed()) {
            Log::channel('siigo')->error('Siigo request failed', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $this->sanitizarRespuesta($response->body()),
            ]);
        }

        return $response;
    }

    private function cliente(string $token): PendingRequest
    {
        $headers = [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];

        if (! empty($this->config->partner_id)) {
            $headers['Partner-Id'] = (string) $this->config->partner_id;
        }

        return Http::withHeaders($headers)
            ->timeout(self::TIMEOUT_SEGUNDOS)
            ->acceptJson()
            ->asJson();
    }

    private function tokenEsVigente(): bool
    {
        if (empty($this->config->token_cache) || $this->config->token_expires_at === null) {
            return false;
        }

        return $this->config->token_expires_at->isFuture();
    }

    private function forgetToken(): void
    {
        $this->config->forceFill([
            'token_cache' => null,
            'token_expires_at' => null,
        ])->save();
    }

    private function sanitizarRespuesta(string $body): string
    {
        // No loguear tokens ni access_keys si vinieran reflejados en la respuesta.
        $body = preg_replace('/"access_token"\s*:\s*"[^"]*"/', '"access_token":"[REDACTED]"', $body) ?? $body;
        $body = preg_replace('/"access_key"\s*:\s*"[^"]*"/', '"access_key":"[REDACTED]"', $body) ?? $body;

        return mb_substr($body, 0, 1500);
    }

    /**
     * Prueba la conexión con las credenciales actuales.
     *
     * @return array{ok: bool, mensaje: string}
     */
    public function probarConexion(): array
    {
        try {
            $this->forgetToken();
            $this->authenticate();

            return ['ok' => true, 'mensaje' => 'Conexión exitosa con Siigo ('.$this->config->ambiente.').'];
        } catch (RuntimeException $e) {
            // Errores controlados (credenciales faltantes, HTTP 4xx/5xx) — mensaje claro y acotado.
            Log::channel('siigo')->warning('Siigo probarConexion fallo controlado', ['mensaje' => $e->getMessage()]);

            return ['ok' => false, 'mensaje' => $e->getMessage()];
        } catch (RequestException|\Throwable $e) {
            // Errores no previstos — no propagamos el mensaje crudo al cliente.
            Log::channel('siigo')->error('Siigo probarConexion excepción', [
                'mensaje' => $e->getMessage(),
                'clase' => $e::class,
            ]);

            return ['ok' => false, 'mensaje' => 'Error inesperado al contactar Siigo. Revisa los logs.'];
        }
    }
}
