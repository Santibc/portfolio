<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';

    protected $fillable = ['clave', 'valor', 'tipo', 'descripcion'];

    public static function get(string $clave, $default = null)
    {
        return cache()->remember("config_sistema.{$clave}", 300, function () use ($clave, $default) {
            $config = static::where('clave', $clave)->first();

            if (!$config) {
                return $default;
            }

            return match ($config->tipo) {
                'entero' => (int) $config->valor,
                'decimal' => (float) $config->valor,
                'booleano' => filter_var($config->valor, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($config->valor, true),
                default => $config->valor,
            };
        });
    }

    public static function set(string $clave, $valor): void
    {
        $valorPersistir = is_array($valor) ? json_encode($valor) : (is_null($valor) ? null : (string) $valor);
        $config = static::where('clave', $clave)->first();

        if ($config) {
            $config->update(['valor' => $valorPersistir]);
        } else {
            static::create([
                'clave' => $clave,
                'valor' => $valorPersistir,
                'tipo' => is_array($valor) ? 'json' : 'texto',
            ]);
        }

        cache()->forget("config_sistema.{$clave}");
    }

    public static function metricaVisible(string $rol, string $metrica): bool
    {
        $config = self::get('metricas_panel_visibles', []);
        if (!is_array($config)) {
            return true;
        }
        return (bool) ($config[$rol][$metrica] ?? true);
    }
}
