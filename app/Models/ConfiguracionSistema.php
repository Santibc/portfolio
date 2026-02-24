<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';

    protected $fillable = ['clave', 'valor', 'tipo', 'descripcion'];

    public static function get(string $clave, $default = null)
    {
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
    }

    public static function set(string $clave, $valor): void
    {
        $config = static::where('clave', $clave)->first();

        if ($config) {
            $valor = is_array($valor) ? json_encode($valor) : (string) $valor;
            $config->update(['valor' => $valor]);
        }
    }
}
