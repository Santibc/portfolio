<?php

namespace Tests\Feature\Siigo;

use App\Models\SiigoConfig;
use App\Services\Siigo\SiigoClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class SiigoClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function configConCredenciales(): SiigoConfig
    {
        $config = SiigoConfig::current();
        $config->username = 'usuario_test@demo.com';
        $config->access_key = 'test_access_key_plaintext';
        $config->ambiente = 'sandbox';
        $config->save();

        return $config->fresh();
    }

    public function test_authenticate_cachea_el_token(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'abc123',
                'expires_in' => 3600,
            ], 200),
        ]);

        $client = new SiigoClient($config);

        $token1 = $client->authenticate();
        $token2 = $client->authenticate();

        $this->assertSame('abc123', $token1);
        $this->assertSame('abc123', $token2);

        // Solo se hizo 1 request a /auth (el segundo reutilizó cache).
        Http::assertSentCount(1);

        $this->assertDatabaseHas('siigo_config', [
            'id' => $config->id,
            'token_cache' => 'abc123',
        ]);

        $this->assertNotNull(SiigoConfig::find($config->id)->token_expires_at);
    }

    public function test_authenticate_renueva_token_cuando_expira(): void
    {
        $config = $this->configConCredenciales();

        // Token previo ya expirado.
        $config->forceFill([
            'token_cache' => 'token_viejo',
            'token_expires_at' => now()->subMinute(),
        ])->save();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'token_nuevo',
                'expires_in' => 3600,
            ], 200),
        ]);

        $client = new SiigoClient($config->fresh());
        $token = $client->authenticate();

        $this->assertSame('token_nuevo', $token);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === 'https://api.siigo.com/auth'
            && $request->method() === 'POST');
    }

    public function test_authenticate_falla_si_credenciales_invalidas(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'error' => 'invalid_credentials',
            ], 401),
        ]);

        $client = new SiigoClient($config);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fallo autenticando contra Siigo');

        $client->authenticate();
    }

    public function test_request_reintenta_si_401(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::sequence()
                ->push(['access_token' => 'token_1', 'expires_in' => 3600], 200)
                ->push(['access_token' => 'token_2', 'expires_in' => 3600], 200),
            'api.siigo.com/v1/customers*' => Http::sequence()
                ->push(['error' => 'unauthorized'], 401)
                ->push(['ok' => true, 'data' => []], 200),
        ]);

        $client = new SiigoClient($config);
        $response = $client->request('GET', '/v1/customers');

        $this->assertSame(200, $response->status());

        // Total: auth1 + endpoint(401) + auth2 + endpoint(200) = 4 requests.
        Http::assertSentCount(4);
    }

    public function test_request_respeta_429_retry_after(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'abc',
                'expires_in' => 3600,
            ], 200),
            'api.siigo.com/v1/taxes*' => Http::sequence()
                ->push(['error' => 'rate_limited'], 429, ['Retry-After' => '1'])
                ->push([['id' => 1, 'name' => 'IVA']], 200),
        ]);

        $client = new SiigoClient($config);

        $inicio = microtime(true);
        $response = $client->request('GET', '/v1/taxes');
        $elapsed = microtime(true) - $inicio;

        $this->assertSame(200, $response->status());

        // Verifica que esperó al menos ~1s por Retry-After.
        $this->assertGreaterThanOrEqual(0.9, $elapsed);

        // auth + endpoint(429) + endpoint(200) = 3 requests (el auth se cachea).
        Http::assertSentCount(3);

        // Verifica que se hicieron 2 requests al endpoint taxes.
        $taxesCount = 0;
        foreach (Http::recorded() as $par) {
            if (str_contains($par[0]->url(), '/v1/taxes')) {
                $taxesCount++;
            }
        }
        $this->assertSame(2, $taxesCount);
    }

    public function test_probar_conexion_devuelve_ok_cuando_auth_exitosa(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'ok_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $client = new SiigoClient($config);
        $resultado = $client->probarConexion();

        $this->assertTrue($resultado['ok']);
        $this->assertStringContainsString('Conexión exitosa', $resultado['mensaje']);
        $this->assertStringContainsString('sandbox', $resultado['mensaje']);
    }

    public function test_probar_conexion_devuelve_error_cuando_auth_falla(): void
    {
        $config = $this->configConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'error' => 'invalid_credentials',
            ], 401),
        ]);

        $client = new SiigoClient($config);
        $resultado = $client->probarConexion();

        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('Fallo autenticando', $resultado['mensaje']);
    }
}
