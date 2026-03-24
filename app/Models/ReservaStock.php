<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para gestionar las reservas de stock por cotización.
 *
 * Cada reserva vincula un item de cotización con su stock correspondiente,
 * permitiendo reservar productos por un tiempo determinado (24h por defecto).
 */
class ReservaStock extends Model
{
    use HasFactory;

    protected $table = 'reservas_stock';

    protected $fillable = [
        'solicitud_cotizacion_id',
        'item_solicitud_id',
        'stock_producto_id',
        'cantidad_reservada',
        'expira_en',
        'liberada_en',
        'estado',
        'motivo_liberacion',
        'liberada_por',
    ];

    protected $casts = [
        'expira_en' => 'datetime',
        'liberada_en' => 'datetime',
        'cantidad_reservada' => 'integer',
    ];

    /**
     * Estados posibles de una reserva
     */
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_APLICADA = 'aplicada';
    const ESTADO_EXPIRADA = 'expirada';
    const ESTADO_LIBERADA_MANUAL = 'liberada_manual';

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Solicitud de cotización a la que pertenece esta reserva
     */
    public function solicitudCotizacion()
    {
        return $this->belongsTo(SolicitudCotizacion::class);
    }

    /**
     * Item de solicitud específico que generó esta reserva
     */
    public function itemSolicitud()
    {
        return $this->belongsTo(ItemSolicitudCotizacion::class, 'item_solicitud_id');
    }

    /**
     * Stock del producto que está siendo reservado
     */
    public function stockProducto()
    {
        return $this->belongsTo(StockProducto::class);
    }

    /**
     * Usuario que liberó la reserva manualmente
     */
    public function liberadaPor()
    {
        return $this->belongsTo(User::class, 'liberada_por');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Reservas activas (aún válidas)
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    /**
     * Reservas que han expirado (por tiempo)
     */
    public function scopeExpiradas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA)
                     ->where('expira_en', '<', now());
    }

    /**
     * Reservas que fueron aplicadas (convertidas a venta)
     */
    public function scopeAplicadas($query)
    {
        return $query->where('estado', self::ESTADO_APLICADA);
    }

    /**
     * Reservas por solicitud específica
     */
    public function scopePorSolicitud($query, $solicitudId)
    {
        return $query->where('solicitud_cotizacion_id', $solicitudId);
    }

    /**
     * Reservas por producto/stock específico
     */
    public function scopePorStock($query, $stockProductoId)
    {
        return $query->where('stock_producto_id', $stockProductoId);
    }

    /**
     * Reservas próximas a expirar (dentro de X horas)
     */
    public function scopeProximasAExpirar($query, $horas = 2)
    {
        return $query->where('estado', self::ESTADO_ACTIVA)
                     ->where('expira_en', '>', now())
                     ->where('expira_en', '<=', now()->addHours($horas));
    }

    // ==========================================
    // MÉTODOS DE INSTANCIA
    // ==========================================

    /**
     * Verifica si la reserva está activa y válida
     */
    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA && $this->expira_en > now();
    }

    /**
     * Verifica si la reserva ha expirado
     */
    public function haExpirado(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA && $this->expira_en <= now();
    }

    /**
     * Liberar la reserva manualmente o por expiración
     *
     * @param string $motivo Razón de la liberación
     * @param int|null $usuarioId ID del usuario que libera (null si es automático)
     * @return bool
     */
    public function liberar(string $motivo, ?int $usuarioId = null): bool
    {
        if (!$this->estaActiva() && !$this->haExpirado()) {
            return false;
        }

        $nuevoEstado = $usuarioId ? self::ESTADO_LIBERADA_MANUAL : self::ESTADO_EXPIRADA;

        $this->update([
            'estado' => $nuevoEstado,
            'liberada_en' => now(),
            'motivo_liberacion' => $motivo,
            'liberada_por' => $usuarioId,
        ]);

        // Actualizar el stock del producto - con guardia contra negativos
        $stock = $this->stockProducto;
        if ($stock && $stock->cantidad_reservada > 0) {
            $cantidadALiberar = min($this->cantidad_reservada, $stock->cantidad_reservada);
            $stock->decrement('cantidad_reservada', $cantidadALiberar);
        }

        return true;
    }

    /**
     * Aplicar la reserva (convertir a salida real de stock)
     *
     * @return bool
     */
    public function aplicar(): bool
    {
        if (!$this->estaActiva()) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_APLICADA,
        ]);

        // Liberar la reserva del conteo de stock reservado - con guardia contra negativos
        $stock = $this->stockProducto;
        if ($stock && $stock->cantidad_reservada > 0) {
            $cantidadALiberar = min($this->cantidad_reservada, $stock->cantidad_reservada);
            $stock->decrement('cantidad_reservada', $cantidadALiberar);
        }

        return true;
    }

    /**
     * Renovar la reserva extendiendo su tiempo de expiración
     *
     * @param int $horasAdicionales
     * @return bool
     */
    public function renovar(int $horasAdicionales = 24): bool
    {
        if ($this->estado !== self::ESTADO_ACTIVA) {
            return false;
        }

        $this->update([
            'expira_en' => now()->addHours($horasAdicionales),
        ]);

        return true;
    }

    /**
     * Obtener tiempo restante hasta expiración en formato legible
     */
    public function getTiempoRestanteAttribute(): string
    {
        if (!$this->estaActiva()) {
            return 'Expirada';
        }

        return $this->expira_en->diffForHumans(now(), [
            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        ]);
    }

    /**
     * Obtener color para badge según estado
     */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ACTIVA => $this->haExpirado() ? 'warning' : 'success',
            self::ESTADO_APLICADA => 'primary',
            self::ESTADO_EXPIRADA => 'secondary',
            self::ESTADO_LIBERADA_MANUAL => 'info',
            default => 'secondary',
        };
    }

    /**
     * Obtener etiqueta del estado en español
     */
    public function getEtiquetaEstadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ACTIVA => $this->haExpirado() ? 'Por expirar' : 'Activa',
            self::ESTADO_APLICADA => 'Aplicada',
            self::ESTADO_EXPIRADA => 'Expirada',
            self::ESTADO_LIBERADA_MANUAL => 'Liberada',
            default => 'Desconocido',
        };
    }
}
