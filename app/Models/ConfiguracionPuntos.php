<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfiguracionPuntos extends Model
{
    use HasFactory;

    protected $table = 'configuracion_puntos';

    protected $fillable = [
        'empresa_id',
        'puntos_por_monto',
        'puntos_registro',
        'puntos_primera_compra',
        'puntos_referir',
        'puntos_ser_referido',
        'puntos_por_peso',
        'canje_minimo',
        'canje_maximo',
        'meses_expiracion',
        'sistema_activo',
        'referidos_activo',
    ];

    protected $casts = [
        'sistema_activo' => 'boolean',
        'referidos_activo' => 'boolean',
    ];

    // === RELACIONES ===

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // === MÉTODOS ESTÁTICOS ===

    /**
     * Obtener configuración para una empresa (con cache)
     */
    public static function getConfiguracion($empresaId)
    {
        return Cache::remember("config_puntos_empresa_{$empresaId}", 3600, function () use ($empresaId) {
            return self::where('empresa_id', $empresaId)->first()
                   ?? self::getConfiguracionDefault($empresaId);
        });
    }

    /**
     * Crear configuración por defecto si no existe
     */
    public static function getConfiguracionDefault($empresaId)
    {
        return self::create([
            'empresa_id' => $empresaId,
            'puntos_por_monto' => 100,
            'puntos_registro' => 0,
            'puntos_primera_compra' => 0,
            'puntos_referir' => 500,
            'puntos_ser_referido' => 0,
            'puntos_por_peso' => 100,
            'canje_minimo' => 100,
            'canje_maximo' => null,
            'meses_expiracion' => 12,
            'sistema_activo' => true,
            'referidos_activo' => true,
        ]);
    }

    /**
     * Limpiar cache de configuración
     */
    public static function clearCache($empresaId)
    {
        Cache::forget("config_puntos_empresa_{$empresaId}");
    }

    // === MÉTODOS DE INSTANCIA ===

    /**
     * Calcular puntos por compra
     */
    public function calcularPuntosCompra($montoCompra)
    {
        if (!$this->sistema_activo) {
            return 0;
        }

        return floor($montoCompra / $this->puntos_por_monto);
    }

    /**
     * Calcular descuento por puntos
     */
    public function calcularDescuentoPorPuntos($puntos)
    {
        if (!$this->sistema_activo) {
            return 0;
        }

        if ($puntos < $this->canje_minimo) {
            return 0;
        }

        // Verificar límite máximo de canje
        if ($this->canje_maximo && $puntos > $this->canje_maximo) {
            $puntos = $this->canje_maximo;
        }

        return floor($puntos / $this->puntos_por_peso);
    }

    /**
     * Verificar si se pueden canjear puntos
     */
    public function puedeCanJear($puntos)
    {
        if (!$this->sistema_activo) {
            return false;
        }

        return $puntos >= $this->canje_minimo;
    }

    /**
     * Obtener fecha de expiración de puntos
     */
    public function getFechaExpiracion()
    {
        return now()->addMonths($this->meses_expiracion);
    }

    // === EVENTOS ===

    protected static function boot()
    {
        parent::boot();

        // Limpiar cache al guardar
        static::saved(function ($config) {
            self::clearCache($config->empresa_id);
        });

        // Limpiar cache al eliminar
        static::deleted(function ($config) {
            self::clearCache($config->empresa_id);
        });
    }
}
