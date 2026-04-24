<?php

namespace Tests\Feature\Siigo;

use App\Models\SiigoCatalogo;
use App\Models\SiigoConfig;
use App\Services\Siigo\SiigoClient;
use App\Services\Siigo\SiigoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SiigoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function servicioConCredenciales(): SiigoService
    {
        $config = SiigoConfig::current();
        $config->username = 'usuario_test@demo.com';
        $config->access_key = 'test_access_key_plaintext';
        $config->ambiente = 'sandbox';
        $config->save();

        $client = new SiigoClient($config->fresh());

        return new SiigoService($client);
    }

    public function test_sincronizar_catalogos_guarda_en_bd(): void
    {
        $service = $this->servicioConCredenciales();

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'abc',
                'expires_in' => 3600,
            ], 200),
            'api.siigo.com/v1/document-types*' => Http::response([
                ['id' => 'FV-1', 'name' => 'Factura de Venta'],
                ['id' => 'FV-2', 'name' => 'Factura Electrónica'],
                ['id' => 'NC-1', 'name' => 'Nota Crédito'],
            ], 200),
            'api.siigo.com/v1/taxes*' => Http::response([], 200),
            'api.siigo.com/v1/payment-types*' => Http::response([], 200),
        ]);

        $resumen = $service->sincronizarCatalogos();

        $this->assertSame(3, $resumen['document-types']);
        $this->assertSame(3, SiigoCatalogo::where('tipo', 'document-types')->count());

        $this->assertDatabaseHas('siigo_catalogos', [
            'tipo' => 'document-types',
            'codigo' => 'FV-1',
            'nombre' => 'Factura de Venta',
        ]);
        $this->assertDatabaseHas('siigo_catalogos', [
            'tipo' => 'document-types',
            'codigo' => 'FV-2',
            'nombre' => 'Factura Electrónica',
        ]);
        $this->assertDatabaseHas('siigo_catalogos', [
            'tipo' => 'document-types',
            'codigo' => 'NC-1',
            'nombre' => 'Nota Crédito',
        ]);
    }

    public function test_sincronizar_catalogos_borra_previos_del_mismo_tipo(): void
    {
        $service = $this->servicioConCredenciales();

        // Insertamos 5 catálogos viejos de tipo taxes.
        for ($i = 1; $i <= 5; $i++) {
            SiigoCatalogo::create([
                'tipo' => 'taxes',
                'codigo' => "OLD-{$i}",
                'nombre' => "Viejo {$i}",
                'payload' => ['legacy' => true],
            ]);
        }

        $this->assertSame(5, SiigoCatalogo::where('tipo', 'taxes')->count());

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'abc',
                'expires_in' => 3600,
            ], 200),
            'api.siigo.com/v1/document-types*' => Http::response([], 200),
            'api.siigo.com/v1/taxes*' => Http::response([
                ['id' => 'IVA-19', 'name' => 'IVA 19%'],
                ['id' => 'IVA-5', 'name' => 'IVA 5%'],
            ], 200),
            'api.siigo.com/v1/payment-types*' => Http::response([], 200),
        ]);

        $resumen = $service->sincronizarCatalogos();

        $this->assertSame(2, $resumen['taxes']);
        $this->assertSame(2, SiigoCatalogo::where('tipo', 'taxes')->count());
        $this->assertDatabaseMissing('siigo_catalogos', [
            'tipo' => 'taxes',
            'codigo' => 'OLD-1',
        ]);
        $this->assertDatabaseHas('siigo_catalogos', [
            'tipo' => 'taxes',
            'codigo' => 'IVA-19',
            'nombre' => 'IVA 19%',
        ]);
    }

    public function test_sincronizar_catalogos_actualiza_sync_at(): void
    {
        $service = $this->servicioConCredenciales();

        $this->assertNull(SiigoConfig::current()->sync_catalogos_at);

        Http::fake([
            'api.siigo.com/auth' => Http::response([
                'access_token' => 'abc',
                'expires_in' => 3600,
            ], 200),
            'api.siigo.com/v1/document-types*' => Http::response([], 200),
            'api.siigo.com/v1/taxes*' => Http::response([], 200),
            'api.siigo.com/v1/payment-types*' => Http::response([], 200),
        ]);

        $antes = now()->subSecond();
        $service->sincronizarCatalogos();

        $config = SiigoConfig::current();
        $this->assertNotNull($config->sync_catalogos_at);
        $this->assertTrue($config->sync_catalogos_at->greaterThanOrEqualTo($antes));
    }
}
