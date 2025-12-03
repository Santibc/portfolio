<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraCrossFund extends Model
{
    use HasFactory;

    protected $table = 'compras_cross_fund';

    protected $fillable = [
        'codigo_compra',
        'usuario_id',
        'paquete_id',
        'monto_total',
        'fecha_compra',
        'estado'
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'fecha_compra' => 'date'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function paquete()
    {
        return $this->belongsTo(PaqueteCrossFund::class, 'paquete_id');
    }

    public function inversiones()
    {
        return $this->hasMany(Inversion::class, 'compra_cross_fund_id');
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'completada');
    }
}
