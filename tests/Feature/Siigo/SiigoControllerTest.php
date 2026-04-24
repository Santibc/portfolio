<?php

namespace Tests\Feature\Siigo;

use App\Models\SiigoConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SiigoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->admin = User::where('email', 'admin@admin.com')->firstOrFail();
    }

    public function test_admin_puede_ver_formulario(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/siigo');

        $response->assertOk();
        $response->assertViewIs('admin.siigo.edit');
        $response->assertViewHas('config');
        $response->assertViewHas('catalogos');
    }

    public function test_usuario_sin_rol_no_puede_ver(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/admin/siigo');

        $response->assertForbidden();
    }

    public function test_guarda_configuracion(): void
    {
        // Aseguramos que la fila existe.
        SiigoConfig::current();

        $payload = [
            'username' => 'nuevo@demo.com',
            'access_key' => 'mi_access_key',
            'partner_id' => 'partner-xyz',
            'ambiente' => 'sandbox',
            'activo' => 1,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/siigo', $payload);

        $response->assertRedirect(route('admin.siigo.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siigo_config', [
            'username' => 'nuevo@demo.com',
            'partner_id' => 'partner-xyz',
            'ambiente' => 'sandbox',
            'activo' => 1,
        ]);

        // El access_key se guardó cifrado, pero debe descifrar a lo que se envió.
        $config = SiigoConfig::current();
        $this->assertSame('mi_access_key', $config->access_key);

        // Al guardar se invalida el token cacheado.
        $this->assertNull($config->token_cache);
        $this->assertNull($config->token_expires_at);
    }

    public function test_probar_conexion_endpoint_devuelve_json(): void
    {
        $config = SiigoConfig::current();
        $config->username = 'demo@demo.com';
        $config->access_key = 'access_key_test';
        $config->save();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'token_ok',
                'expires_in' => 3600,
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/siigo/probar');

        $response->assertOk();
        $response->assertJson(['ok' => true]);
        $response->assertJsonStructure(['ok', 'mensaje']);
    }

    public function test_sincronizar_endpoint_redirige_con_success(): void
    {
        $config = SiigoConfig::current();
        $config->username = 'demo@demo.com';
        $config->access_key = 'access_key_test';
        $config->save();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'token_ok',
                'expires_in' => 3600,
            ], 200),
            'api.siigo.com/v1/document-types*' => Http::response([
                ['id' => 'FV-1', 'name' => 'Factura de Venta'],
            ], 200),
            'api.siigo.com/v1/taxes*' => Http::response([
                ['id' => 'IVA-19', 'name' => 'IVA 19%'],
            ], 200),
            'api.siigo.com/v1/payment-types*' => Http::response([
                ['id' => 'PAY-1', 'name' => 'Efectivo'],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/siigo/sincronizar');

        $response->assertRedirect(route('admin.siigo.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('siigo_catalogos', 3);
    }
}
