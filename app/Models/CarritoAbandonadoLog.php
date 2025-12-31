<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoAbandonadoLog extends Model
{
    use HasFactory;

    protected $table = 'carritos_abandonados_log';

    protected $fillable = [
        'carrito_id',
        'empresa_id',
        'email_cliente',
        'valor_carrito',
        'cantidad_items',
        'recordatorio_numero',
        'canal',
        'estado',
        'enviado_at',
        'abierto_at',
        'clickeado_at',
        'recuperado_at',
        'token_recuperacion',
    ];

    protected $casts = [
        'valor_carrito' => 'decimal:2',
        'enviado_at' => 'datetime',
        'abierto_at' => 'datetime',
        'clickeado_at' => 'datetime',
        'recuperado_at' => 'datetime',
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Marcar como abierto
     */
    public function marcarAbierto()
    {
        $this->update([
            'estado' => 'abierto',
            'abierto_at' => now(),
        ]);
    }

    /**
     * Marcar como clickeado
     */
    public function marcarClickeado()
    {
        $this->update([
            'estado' => 'clickeado',
            'clickeado_at' => now(),
        ]);
    }

    /**
     * Marcar como recuperado
     */
    public function marcarRecuperado()
    {
        $this->update([
            'estado' => 'recuperado',
            'recuperado_at' => now(),
        ]);

        // También marcar el carrito como recuperado
        if ($this->carrito) {
            $this->carrito->update(['recuperado' => true]);
        }
    }

    /**
     * Scope para estadísticas por empresa
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para período de tiempo
     */
    public function scopeEnPeriodo($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    /**
     * Scope para emails enviados
     */
    public function scopeEnviados($query)
    {
        return $query->whereNotNull('enviado_at');
    }

    /**
     * Scope para recuperados
     */
    public function scopeRecuperados($query)
    {
        return $query->where('estado', 'recuperado');
    }
}
