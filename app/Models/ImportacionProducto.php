<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportacionProducto extends Model
{
    use HasFactory;

    protected $table = 'importaciones_productos';

    protected $fillable = [
        'usuario_id',
        'nombre_archivo',
        'ruta_archivo',
        'estado',
        'total_filas',
        'productos_creados',
        'productos_fallidos',
        'errores',
        'detalles_procesados'
    ];

    protected $casts = [
        'errores' => 'array',
        'detalles_procesados' => 'array'
    ];

    protected $appends = [
        'porcentaje_exito'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Método para agregar un error
    public function agregarError($fila, $item, $mensaje)
    {
        $errores = $this->errores ?? [];
        $errores[] = [
            'fila' => $fila,
            'item' => $item,
            'mensaje' => $mensaje
        ];
        $this->errores = $errores;
        $this->save();
    }

    // Método para agregar un producto procesado
    public function agregarProcesado($fila, $item, $referencia, $categoria)
    {
        $procesados = $this->detalles_procesados ?? [];
        $procesados[] = [
            'fila' => $fila,
            'item' => $item,
            'referencia' => $referencia,
            'categoria' => $categoria
        ];
        $this->detalles_procesados = $procesados;
        $this->save();
    }

    // Calcular porcentaje de éxito
    public function getPorcentajeExitoAttribute()
    {
        if ($this->total_filas === 0) {
            return 0;
        }
        return round(($this->productos_creados / $this->total_filas) * 100, 2);
    }
}
