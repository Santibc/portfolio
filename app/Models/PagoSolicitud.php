<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'pagos_solicitud';

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';

    protected $fillable = [
        'solicitud_cotizacion_id',
        'monto',
        'metodo_pago',
        'comprobante',
        'notas',
        'registrado_por',
        'estado',
        'aprobado_por',
        'aprobado_en',
    ];

    protected $casts = [
        'monto' => 'float',
        'aprobado_en' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCotizacion::class, 'solicitud_cotizacion_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function estaAprobado(): bool
    {
        return $this->estado === self::ESTADO_APROBADO;
    }

    public function estaRechazado(): bool
    {
        return $this->estado === self::ESTADO_RECHAZADO;
    }

    public function getEtiquetaMetodoPagoAttribute(): string
    {
        return SolicitudCotizacion::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_PENDIENTE => 'warning',
            self::ESTADO_APROBADO => 'success',
            self::ESTADO_RECHAZADO => 'danger',
            default => 'secondary',
        };
    }

    public function getEtiquetaEstadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_APROBADO => 'Aprobado',
            self::ESTADO_RECHAZADO => 'Rechazado',
            default => '-',
        };
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', self::ESTADO_APROBADO);
    }
}
