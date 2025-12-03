<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billetera extends Model
{
    use HasFactory;

    protected $table = 'billeteras';

    protected $fillable = [
        'usuario_id',
        'saldo_disponible',
        'saldo_bloqueado',
        'saldo_invertido',
        'retornos_acumulados',
        'dividendos_pendientes'
    ];

    protected $casts = [
        'saldo_disponible' => 'decimal:2',
        'saldo_bloqueado' => 'decimal:2',
        'saldo_invertido' => 'decimal:2',
        'retornos_acumulados' => 'decimal:2',
        'dividendos_pendientes' => 'decimal:2'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function transacciones()
    {
        return $this->hasMany(TransaccionBilletera::class, 'billetera_id');
    }

    // Métodos auxiliares
    public function getSaldoTotalAttribute()
    {
        return $this->saldo_disponible + $this->saldo_bloqueado + $this->saldo_invertido;
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}
