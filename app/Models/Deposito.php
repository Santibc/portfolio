<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    use HasFactory;

    protected $table = 'depositos';

    protected $fillable = [
        'codigo_deposito',
        'usuario_id',
        'monto',
        'metodo_pago',
        'referencia_pago',
        'comprobante',
        'fecha_deposito',
        'estado',
        'verificado_por',
        'verificado_at',
        'notas'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_deposito' => 'date',
        'verificado_at' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Alias para compatibilidad
    public function user()
    {
        return $this->usuario();
    }

    public function verificadoPor()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVerificados($query)
    {
        return $query->where('estado', 'verificado');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }
}
