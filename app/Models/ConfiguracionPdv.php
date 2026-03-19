<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfiguracionPdv extends Model
{
    protected $table = 'configuracion_pdv';

    protected $fillable = ['clave', 'valor', 'descripcion'];

    public static function obtener($clave, $default = null)
    {
        return Cache::remember("config_pdv_{$clave}", 3600, function () use ($clave, $default) {
            $config = self::where('clave', $clave)->first();
            return $config ? $config->valor : $default;
        });
    }

    public static function establecer($clave, $valor)
    {
        self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor]
        );
        Cache::forget("config_pdv_{$clave}");
    }

    public static function obtenerBoolean($clave, $default = false)
    {
        $valor = self::obtener($clave, $default ? 'true' : 'false');
        return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
    }

    public static function obtenerNumero($clave, $default = 0)
    {
        return (float) self::obtener($clave, $default);
    }
}
