<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividendo extends Model
{
    use HasFactory;

    protected $table = 'dividendos';

    protected $fillable = [
        'codigo_dividendo',
        'inversion_id',
        'proyecto_id',
        'usuario_id',
        'numero_periodo',
        'monto',
        'fecha_programada',
        'fecha_pagada',
        'estado',
        'pagado_por',
        'notas'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'numero_periodo' => 'integer',
        'fecha_programada' => 'date',
        'fecha_pagada' => 'date'
    ];

    // Relaciones
    public function inversion()
    {
        return $this->belongsTo(Inversion::class, 'inversion_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function pagadoPor()
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'programado');
    }

    public function scopeAtrasados($query)
    {
        return $query->where('estado', 'atrasado');
    }
}
