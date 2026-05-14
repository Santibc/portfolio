<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SolicitudCotizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes_cotizacion';

    protected $fillable = [
        'numero_solicitud',
        'cliente_id',
        'enlace_acceso_id',
        'created_by',
        'estado',
        'monto_total',
        'notas_cliente',
        'observaciones_admin',
        'motivo_rechazo',
        'aplicada_en',
        'aplicada_por',
        'rechazada_en',
        'rechazada_por'
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'aplicada_en' => 'datetime',
        'rechazada_en' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function enlaceAcceso()
    {
        return $this->belongsTo(EnlaceAcceso::class, 'enlace_acceso_id');
    }

    public function items()
    {
        return $this->hasMany(ItemSolicitudCotizacion::class, 'solicitud_cotizacion_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function aplicadaPor()
    {
        return $this->belongsTo(User::class, 'aplicada_por');
    }

    public function rechazadaPor()
    {
        return $this->belongsTo(User::class, 'rechazada_por');
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('cantidad');
    }

    public function calcularMontoTotal()
    {
        $this->monto_total = $this->items->sum('precio_total');
        $this->save();
        return $this->monto_total;
    }

    public function marcarComoAplicada($usuarioId, $observaciones = null)
    {
        $this->update([
            'estado' => 'aplicada',
            'aplicada_en' => now(),
            'aplicada_por' => $usuarioId,
            'observaciones_admin' => $observaciones
        ]);
    }

    public function marcarComoRechazada($usuarioId, $motivoRechazo)
    {
        $this->update([
            'estado' => 'rechazada',
            'rechazada_en' => now(),
            'rechazada_por' => $usuarioId,
            'motivo_rechazo' => $motivoRechazo
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (empty($solicitud->numero_solicitud)) {
                $solicitud->numero_solicitud = 'SC-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAplicadas($query)
    {
        return $query->where('estado', 'aplicada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
}