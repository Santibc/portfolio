<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class STImagenOrden extends Model
{
    use HasFactory;

    protected $table = 'st_imagenes_orden';

    protected $fillable = [
        'st_orden_servicio_id',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_imagen',
        'descripcion',
        'orden'
    ];

    protected $casts = [
        'orden' => 'integer'
    ];

    // Relaciones
    public function ordenServicio()
    {
        return $this->belongsTo(STOrdenServicio::class, 'st_orden_servicio_id');
    }

    // Event listeners
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($imagen) {
            // Eliminar archivo físico al borrar el registro
            if (file_exists(public_path($imagen->ruta_archivo))) {
                unlink(public_path($imagen->ruta_archivo));
            }
        });
    }

    // Accessors
    public function getUrlAttribute()
    {
        return asset($this->ruta_archivo);
    }
}
