<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReglaPenalizacion extends Model
{
    use HasFactory;

    protected $table = 'reglas_penalizacion';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'tipo_penalizacion',
        'valor',
        'aplica_desde_mes',
        'aplica_hasta_mes',
        'pierde_capital',
        'pierde_dividendos',
        'permite_venta_posicion',
        'activo',
        'orden'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'aplica_desde_mes' => 'integer',
        'aplica_hasta_mes' => 'integer',
        'pierde_capital' => 'boolean',
        'pierde_dividendos' => 'boolean',
        'permite_venta_posicion' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProyecto::class, 'categoria_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
