<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionInversion extends Model
{
    use HasFactory;

    protected $table = 'transacciones_inversion';

    protected $fillable = [
        'codigo_transaccion',
        'inversion_id',
        'vendedor_id',
        'comprador_id',
        'monto_venta',
        'valor_libro',
        'ganancia_perdida',
        'comision_plataforma',
        'fecha_transaccion',
        'estado',
        'notas'
    ];

    protected $casts = [
        'monto_venta' => 'decimal:2',
        'valor_libro' => 'decimal:2',
        'ganancia_perdida' => 'decimal:2',
        'comision_plataforma' => 'decimal:2',
        'fecha_transaccion' => 'date'
    ];

    // Relaciones
    public function inversion()
    {
        return $this->belongsTo(Inversion::class, 'inversion_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
}
