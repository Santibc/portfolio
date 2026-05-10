<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'numero_identificacion',
        'tipo_documento',
        'nombre_contacto',
        'razon_social',
        'email',
        'telefono',
        'celular',
        'direccion',
        'ciudad_texto',
        'departamento_texto',
        'tipo_cliente',
        'observaciones',
        'ciudad_id',
        'vendedor_id',
        'lista_precio_id',
        'activo',
        'pais_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones de Servicio Técnico (clientes unificados)
    public function equipos()
    {
        return $this->hasMany(STEquipo::class, 'cliente_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(STOrdenServicio::class, 'cliente_id');
    }

    // Accessors de compatibilidad con vistas heredadas del módulo ST
    public function getNombreCompletoFormateadoAttribute()
    {
        if ($this->tipo_cliente === 'empresa' && !empty($this->razon_social)) {
            return $this->razon_social;
        }
        return $this->nombre_contacto;
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre_contacto;
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->numero_identificacion;
    }

    // Nota: NO se agregan accessors para `ciudad`/`departamento` porque
    // sobrescribirían la relación Eloquent `ciudad()` del catálogo.
    // Las vistas de ST deben usar `ciudad_texto` y `departamento_texto`.
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class, 'lista_precio_id');
    }

    public function enlacesAcceso()
    {
        return $this->hasMany(EnlaceAcceso::class, 'cliente_id');
    }

    public function enlacesAccesoActivos()
    {
        return $this->hasMany(EnlaceAcceso::class, 'cliente_id')
            ->where('activo', true)
            ->where('expira_en', '>', now());
    }

    public function solicitudesCotizacion()
    {
        return $this->hasMany(SolicitudCotizacion::class, 'cliente_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }
}