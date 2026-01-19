<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'direccion',
        'telefono',
        'responsable',
        'es_principal',
        'activo',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo' => 'boolean',
    ];

    // =========================================
    // Constantes de tipos
    // =========================================
    const TIPO_BODEGA = 'bodega';
    const TIPO_TIENDA = 'tienda';
    const TIPO_OTRO = 'otro';

    public static function tipos(): array
    {
        return [
            self::TIPO_BODEGA => 'Bodega',
            self::TIPO_TIENDA => 'Tienda',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    // =========================================
    // Relaciones
    // =========================================
    public function stockProductos()
    {
        return $this->hasMany(StockProducto::class, 'ubicacion_id');
    }

    public function trasladosOrigen()
    {
        return $this->hasMany(TrasladoStock::class, 'ubicacion_origen_id');
    }

    public function trasladosDestino()
    {
        return $this->hasMany(TrasladoStock::class, 'ubicacion_destino_id');
    }

    // =========================================
    // Accessors
    // =========================================
    public function getTipoNombreAttribute(): string
    {
        return self::tipos()[$this->tipo] ?? $this->tipo;
    }

    // =========================================
    // Scopes
    // =========================================
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBodegas($query)
    {
        return $query->where('tipo', self::TIPO_BODEGA);
    }

    public function scopeTiendas($query)
    {
        return $query->where('tipo', self::TIPO_TIENDA);
    }

    // =========================================
    // Métodos
    // =========================================
    public function marcarComoPrincipal(): void
    {
        // Desmarcar todas las ubicaciones del mismo tipo
        self::where('tipo', $this->tipo)
            ->where('id', '!=', $this->id)
            ->update(['es_principal' => false]);

        $this->update(['es_principal' => true]);
    }

    public static function principal($tipo = self::TIPO_BODEGA)
    {
        return self::where('tipo', $tipo)
                   ->where('es_principal', true)
                   ->first();
    }
}
