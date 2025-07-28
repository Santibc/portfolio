<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImagenProducto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_productos';

    protected $fillable = [
        'producto_id',
        'ruta_imagen',
        'texto_alternativo',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->ruta_imagen);
    }

    protected static function boot()
    {
        parent::boot();

        // Asegurar que solo una imagen sea principal por producto
        static::creating(function ($imagen) {
            if ($imagen->es_principal) {
                static::where('producto_id', $imagen->producto_id)
                    ->update(['es_principal' => false]);
            }
        });

        static::updating(function ($imagen) {
            if ($imagen->es_principal && $imagen->isDirty('es_principal')) {
                static::where('producto_id', $imagen->producto_id)
                    ->where('id', '!=', $imagen->id)
                    ->update(['es_principal' => false]);
            }
        });
    }
}