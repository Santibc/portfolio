<?php

namespace Tests\Feature\Siigo;

use App\Models\SiigoConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiigoConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_key_se_guarda_cifrado(): void
    {
        $config = SiigoConfig::create([
            'username' => 'demo@demo.com',
            'access_key' => 'plaintext_secret',
            'ambiente' => 'sandbox',
            'activo' => false,
        ]);

        // Leemos directo con DB::table sin pasar por el accessor.
        $raw = DB::table('siigo_config')->where('id', $config->id)->value('access_key');
        $this->assertNotNull($raw);
        $this->assertNotSame('plaintext_secret', $raw);
        $this->assertTrue(strlen($raw) > strlen('plaintext_secret'));

        // Con el modelo debe descifrarse correctamente.
        $reloaded = SiigoConfig::find($config->id);
        $this->assertSame('plaintext_secret', $reloaded->access_key);
    }

    public function test_current_crea_fila_si_no_existe(): void
    {
        $this->assertSame(0, SiigoConfig::query()->count());

        $config = SiigoConfig::current();

        $this->assertInstanceOf(SiigoConfig::class, $config);
        $this->assertTrue($config->exists);
        $this->assertSame('sandbox', $config->ambiente);
        $this->assertFalse((bool) $config->activo);
        $this->assertSame(1, SiigoConfig::query()->count());

        // Segunda llamada no crea otra fila.
        $config2 = SiigoConfig::current();
        $this->assertSame($config->id, $config2->id);
        $this->assertSame(1, SiigoConfig::query()->count());
    }
}
