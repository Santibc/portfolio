<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActualizacionPrecio extends Model
{
    use HasFactory;

    protected $table = 'actualizaciones_precios';

    protected $fillable = [
        'usuario_id',
        'nombre_archivo',
        'ruta_archivo',
        'total_filas',
        'actualizaciones_exitosas',
        'actualizaciones_fallidas',
        'errores'
    ];

    protected $casts = [
        'errores' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getPorcentajeExitoAttribute()
    {
        if ($this->total_filas === 0) return 0;
        return round(($this->actualizaciones_exitosas / $this->total_filas) * 100, 2);
    }

    public function agregarError($fila, $mensaje)
    {
        $errores = $this->errores ?? [];
        $errores[] = [
            'fila' => $fila,
            'mensaje' => $mensaje,
            'timestamp' => now()->toISOString()
        ];
        $this->errores = $errores;
    }
}