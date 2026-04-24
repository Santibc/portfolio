<?php

namespace App\Services\Settings;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    private const CACHE_KEY = 'app.configuraciones';

    private const CACHE_TTL = 3600;

    public function get(string $clave, mixed $default = null): mixed
    {
        $all = $this->all();

        if (! array_key_exists($clave, $all)) {
            return $default;
        }

        return $all[$clave];
    }

    public function set(string $clave, mixed $valor, string $tipo = 'string', string $grupo = 'general', ?string $descripcion = null): Configuracion
    {
        $valorAlmacenado = match ($tipo) {
            'json' => json_encode($valor, JSON_UNESCAPED_UNICODE),
            'boolean' => $valor ? '1' : '0',
            default => (string) $valor,
        };

        $config = Configuracion::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorAlmacenado,
                'tipo' => $tipo,
                'grupo' => $grupo,
                'descripcion' => $descripcion,
            ],
        );

        $this->flush();

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function group(string $grupo): array
    {
        return Configuracion::query()
            ->delGrupo($grupo)
            ->get()
            ->mapWithKeys(fn (Configuracion $c) => [$c->clave => $c->valor_tipado])
            ->all();
    }

    public function forget(string $clave): void
    {
        Configuracion::where('clave', $clave)->delete();
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    private function all(): array
    {
        $cached = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return Configuracion::query()
                ->get()
                ->mapWithKeys(fn (Configuracion $c) => [$c->clave => $c->valor_tipado])
                ->all();
        });

        /** @var array<string, mixed> $cached */
        return $cached;
    }
}
