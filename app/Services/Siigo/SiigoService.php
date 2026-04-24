<?php

namespace App\Services\Siigo;

use App\Models\SiigoCatalogo;
use App\Models\SiigoConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Capa de negocio sobre SiigoClient.
 * Mapea entidades locales <-> payloads Siigo y sincroniza catálogos.
 */
class SiigoService
{
    public function __construct(private readonly SiigoClient $client) {}

    /**
     * Sincroniza catálogos oficiales de Siigo (document-types, taxes, payment-types).
     * Cada catálogo se cachea en la tabla `siigo_catalogos`.
     *
     * @return array<string, int> total de registros sincronizados por tipo
     */
    public function sincronizarCatalogos(): array
    {
        $resumen = [];
        $mapa = [
            'document-types' => '/v1/document-types',
            'taxes' => '/v1/taxes',
            'payment-types' => '/v1/payment-types',
        ];

        foreach ($mapa as $tipo => $path) {
            try {
                $response = $this->client->request('GET', $path);

                if ($response->failed()) {
                    $resumen[$tipo] = 0;

                    continue;
                }

                $items = $response->json();
                if (! is_array($items)) {
                    $resumen[$tipo] = 0;

                    continue;
                }

                $resumen[$tipo] = $this->guardarCatalogo($tipo, $items);
            } catch (\Throwable $e) {
                Log::channel('siigo')->error('Error sincronizando catálogo', [
                    'tipo' => $tipo,
                    'mensaje' => $e->getMessage(),
                ]);
                $resumen[$tipo] = 0;
            }
        }

        SiigoConfig::current()->forceFill(['sync_catalogos_at' => now()])->save();

        return $resumen;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function guardarCatalogo(string $tipo, array $items): int
    {
        return DB::transaction(function () use ($tipo, $items): int {
            SiigoCatalogo::where('tipo', $tipo)->delete();

            $total = 0;
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $codigo = (string) ($item['id'] ?? $item['code'] ?? $item['name'] ?? '');
                $nombre = (string) ($item['name'] ?? $item['description'] ?? $codigo);

                if ($codigo === '') {
                    continue;
                }

                SiigoCatalogo::updateOrCreate(
                    ['tipo' => $tipo, 'codigo' => $codigo],
                    ['nombre' => $nombre, 'payload' => $item],
                );
                $total++;
            }

            return $total;
        });
    }

    /**
     * Devuelve los catálogos cacheados de un tipo.
     *
     * @return array<int, SiigoCatalogo>
     */
    public function catalogosCacheados(string $tipo): array
    {
        return SiigoCatalogo::tipo($tipo)->orderBy('nombre')->get()->all();
    }
}
