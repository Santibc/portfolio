<?php

// 1. Empresa.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'nombre',
        'slug',
        'descripcion',
        'logo',
        'imagen_portada',
        'email',
        'telefono',
        'direccion',
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'whatsapp',
        'horario_atencion',
        'activo',
        'porcentaje_comision'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'porcentaje_comision' => 'decimal:2',
        'horario_atencion' => 'array'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function carruselImagenes()
    {
        return $this->hasMany(CarruselEmpresa::class);
    }

    public function carruselImagenesActivas()
    {
        return $this->hasMany(CarruselEmpresa::class)
            ->where('activo', true)
            ->where(function($q) {
                $q->whereNull('fecha_inicio')
                  ->orWhere('fecha_inicio', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', now());
            })
            ->orderBy('orden');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function enlacesAcceso()
    {
        return $this->hasMany(EnlaceAcceso::class);
    }

    public function solicitudesCotizacion()
    {
        return $this->hasMany(SolicitudCotizacion::class);
    }

    public function comisiones()
    {
        return $this->hasMany(Comision::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoEmpresa::class);
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class);
    }

    public function getUrlAttribute()
    {
        return route('tienda.empresa', $this->slug);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset($this->logo) : asset('images/default-logo.png');
    }

    public function getImagenPortadaUrlAttribute()
    {
        return $this->imagen_portada ? asset($this->imagen_portada) : asset('images/default-cover.jpg');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($empresa) {
            if (empty($empresa->slug)) {
                $empresa->slug = Str::slug($empresa->nombre);
                
                // Asegurar que el slug sea único
                $count = static::where('slug', 'like', $empresa->slug . '%')->count();
                if ($count > 0) {
                    $empresa->slug = $empresa->slug . '-' . ($count + 1);
                }
            }
        });
    }
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function categoriasActivas()
    {
        return $this->hasMany(Categoria::class)
                    ->where('activo', true)
                    ->orderBy('orden');
    }
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }
}