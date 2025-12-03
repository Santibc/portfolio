<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionBilletera extends Model
{
    use HasFactory;

    protected $table = 'transacciones_billetera';

    protected $fillable = [
        'codigo_transaccion',
        'billetera_id',
        'usuario_id',
        'tipo',
        'monto',
        'naturaleza',
        'saldo_anterior',
        'saldo_posterior',
        'descripcion',
        'referencia_externa',
        'referencia_id',
        'referencia_type',
        'procesado_por',
        'fecha_transaccion'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'fecha_transaccion' => 'datetime'
    ];

    // Relaciones
    public function billetera()
    {
        return $this->belongsTo(Billetera::class, 'billetera_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function procesadoPor()
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    public function referencia()
    {
        return $this->morphTo();
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
