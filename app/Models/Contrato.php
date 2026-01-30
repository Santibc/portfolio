<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use SoftDeletes;

    protected $table = 'contratos';

    // Constantes de estado
    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_ACTIVO = 'activo';
    const ESTADO_VENCIDO = 'vencido';
    const ESTADO_CANCELADO = 'cancelado';

    const ESTADOS = [
        self::ESTADO_BORRADOR => 'Borrador',
        self::ESTADO_ACTIVO => 'Activo',
        self::ESTADO_VENCIDO => 'Vencido',
        self::ESTADO_CANCELADO => 'Cancelado',
    ];

    // Constantes de estado de garantía
    const ESTADO_GARANTIA_PENDIENTE = 'pendiente';
    const ESTADO_GARANTIA_RETENIDA = 'retenida';
    const ESTADO_GARANTIA_PARCIALMENTE_LIBERADA = 'parcialmente_liberada';
    const ESTADO_GARANTIA_LIBERADA = 'liberada';

    const ESTADOS_GARANTIA = [
        self::ESTADO_GARANTIA_PENDIENTE => 'Pendiente',
        self::ESTADO_GARANTIA_RETENIDA => 'Retenida',
        self::ESTADO_GARANTIA_PARCIALMENTE_LIBERADA => 'Parcialmente Liberada',
        self::ESTADO_GARANTIA_LIBERADA => 'Liberada',
    ];

    protected $fillable = [
        'contrato_tipo_id',
        'codigo',
        'titulo',
        'descripcion',
        'cliente_id',
        'subcontrata_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_firma',
        'importe',
        'iva_porcentaje',
        'tiene_retencion',
        'retencion_porcentaje',
        'importe_retenido',
        'fecha_liberacion_garantia',
        'estado_garantia',
        'fecha_liberacion_real',
        'porcentaje_total_liberado',
        'importe_total_liberado',
        'estado',
        'responsable_id',
        'documento_path',
        'notas',
        'renovacion_automatica',
        'dias_preaviso_vencimiento',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_firma' => 'date',
        'fecha_liberacion_garantia' => 'date',
        'fecha_liberacion_real' => 'date',
        'importe' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'retencion_porcentaje' => 'decimal:2',
        'importe_retenido' => 'decimal:2',
        'porcentaje_total_liberado' => 'integer',
        'importe_total_liberado' => 'decimal:2',
        'tiene_retencion' => 'boolean',
        'renovacion_automatica' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ContratoTipo::class, 'contrato_tipo_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function subcontrata(): BelongsTo
    {
        return $this->belongsTo(Subcontrata::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class);
    }

    public function liberaciones(): HasMany
    {
        return $this->hasMany(ContratoLiberacion::class)->ordenCronologico();
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Importe total con IVA.
     */
    public function getImporteTotalAttribute(): float
    {
        $base = floatval($this->importe ?? 0);
        $iva = $base * (floatval($this->iva_porcentaje ?? 21) / 100);
        return $base + $iva;
    }

    /**
     * Importe del IVA.
     */
    public function getImporteIvaAttribute(): float
    {
        return floatval($this->importe ?? 0) * (floatval($this->iva_porcentaje ?? 21) / 100);
    }

    /**
     * Nombre de la parte contratante (cliente o subcontrata).
     */
    public function getParteNombreAttribute(): string
    {
        if ($this->cliente) {
            return $this->cliente->nombre_comercial ?? $this->cliente->razon_social ?? 'Cliente';
        }
        if ($this->subcontrata) {
            return $this->subcontrata->nombre ?? 'Subcontrata';
        }
        return 'Sin asignar';
    }

    /**
     * Tipo de parte contratante.
     */
    public function getParteTipoAttribute(): string
    {
        if ($this->cliente_id) {
            return 'Cliente';
        }
        if ($this->subcontrata_id) {
            return 'Subcontrata';
        }
        return 'Sin asignar';
    }

    /**
     * Color del badge según estado.
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => 'secondary',
            self::ESTADO_ACTIVO => 'success',
            self::ESTADO_VENCIDO => 'warning',
            self::ESTADO_CANCELADO => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Color del badge de garantía.
     */
    public function getGarantiaBadgeAttribute(): string
    {
        return match($this->estado_garantia) {
            self::ESTADO_GARANTIA_PENDIENTE => 'warning',
            self::ESTADO_GARANTIA_RETENIDA => 'info',
            self::ESTADO_GARANTIA_PARCIALMENTE_LIBERADA => 'primary',
            self::ESTADO_GARANTIA_LIBERADA => 'success',
            default => 'secondary',
        };
    }

    /**
     * Días restantes para vencimiento.
     */
    public function getDiasParaVencerAttribute(): ?int
    {
        if (!$this->fecha_fin) {
            return null;
        }
        return now()->diffInDays($this->fecha_fin, false);
    }

    /**
     * Si está próximo a vencer (dentro de 30 días).
     */
    public function getProximoAVencerAttribute(): bool
    {
        $dias = $this->dias_para_vencer;
        return $dias !== null && $dias > 0 && $dias <= ($this->dias_preaviso_vencimiento ?? 30);
    }

    /**
     * Si ya venció.
     */
    public function getVencidoAttribute(): bool
    {
        if (!$this->fecha_fin) {
            return false;
        }
        return $this->fecha_fin->isPast();
    }

    /**
     * Si la garantía está pendiente de liberar.
     */
    public function getGarantiaPendienteAttribute(): bool
    {
        return $this->tiene_retencion && !$this->fecha_liberacion_real;
    }

    /**
     * Porcentaje pendiente de liberar.
     */
    public function getPorcentajePendienteLiberarAttribute(): int
    {
        return 100 - intval($this->porcentaje_total_liberado ?? 0);
    }

    /**
     * Importe pendiente de liberar.
     */
    public function getImportePendienteLiberarAttribute(): float
    {
        return floatval($this->importe_retenido ?? 0) - floatval($this->importe_total_liberado ?? 0);
    }

    /**
     * Si tiene liberaciones parciales.
     */
    public function getTieneLiberacionesParcialesAttribute(): bool
    {
        return $this->tiene_retencion &&
               intval($this->porcentaje_total_liberado ?? 0) > 0 &&
               intval($this->porcentaje_total_liberado ?? 0) < 100;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function scopeBorradores($query)
    {
        return $query->where('estado', self::ESTADO_BORRADOR);
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', self::ESTADO_VENCIDO);
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', self::ESTADO_CANCELADO);
    }

    public function scopeProximosAVencer($query, $dias = 30)
    {
        return $query->where('estado', self::ESTADO_ACTIVO)
                     ->whereNotNull('fecha_fin')
                     ->whereBetween('fecha_fin', [now(), now()->addDays($dias)]);
    }

    public function scopeConRetencion($query)
    {
        return $query->where('tiene_retencion', true);
    }

    public function scopeGarantiasPendientes($query)
    {
        return $query->where('tiene_retencion', true)
                     ->where(function($q) {
                         $q->whereNull('fecha_liberacion_real')
                           ->orWhere('porcentaje_total_liberado', '<', 100);
                     });
    }

    public function scopeGarantiasLiberadas($query)
    {
        return $query->where('tiene_retencion', true)
                     ->whereNotNull('fecha_liberacion_real');
    }

    public function scopeDeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeDeSubcontrata($query, $subcontrataId)
    {
        return $query->where('subcontrata_id', $subcontrataId);
    }

    public function scopeDeTipo($query, $tipoId)
    {
        return $query->where('contrato_tipo_id', $tipoId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('codigo', 'like', "%{$termino}%")
              ->orWhere('titulo', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    // ==========================================
    // MÉTODOS DE TRANSICIÓN DE ESTADO
    // ==========================================

    /**
     * Activar contrato (de borrador a activo).
     */
    public function activar(): bool
    {
        if ($this->estado !== self::ESTADO_BORRADOR) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_ACTIVO,
            'estado_garantia' => $this->tiene_retencion ? self::ESTADO_GARANTIA_RETENIDA : null,
        ]);

        return true;
    }

    /**
     * Marcar contrato como vencido.
     */
    public function marcarVencido(): bool
    {
        if ($this->estado !== self::ESTADO_ACTIVO) {
            return false;
        }

        $this->update(['estado' => self::ESTADO_VENCIDO]);
        return true;
    }

    /**
     * Cancelar contrato.
     */
    public function cancelar(): bool
    {
        if (in_array($this->estado, [self::ESTADO_VENCIDO, self::ESTADO_CANCELADO])) {
            return false;
        }

        $this->update(['estado' => self::ESTADO_CANCELADO]);
        return true;
    }

    /**
     * Liberar garantía retenida.
     *
     * @deprecated Usar liberarGaranciaParcial() para nuevas liberaciones
     */
    public function liberarGarantia(?string $fecha = null): bool
    {
        // Mantener por retrocompatibilidad, pero internamente usa liberarGaranciaParcial
        return $this->liberarGaranciaParcial(100, $fecha, 'Liberación completa (método antiguo)');
    }

    /**
     * Liberar un porcentaje de la garantía retenida.
     *
     * @param int $porcentaje Porcentaje a liberar (1-100)
     * @param string|null $fecha Fecha de liberación
     * @param string|null $notas Notas/motivo
     * @return bool
     * @throws \Exception
     */
    public function liberarGaranciaParcial(int $porcentaje, ?string $fecha = null, ?string $notas = null): bool
    {
        if (!$this->tiene_retencion) {
            throw new \Exception('Este contrato no tiene retención de garantía.');
        }

        // Validar que no exceda el 100%
        $porcentajeDisponible = 100 - intval($this->porcentaje_total_liberado ?? 0);
        if ($porcentaje > $porcentajeDisponible) {
            throw new \Exception("Solo puede liberar hasta {$porcentajeDisponible}% restante.");
        }

        if ($porcentaje <= 0) {
            throw new \Exception('El porcentaje debe ser mayor a 0.');
        }

        // Calcular importe a liberar
        $importeALiberar = floatval($this->importe_retenido) * ($porcentaje / 100);

        // Crear registro de liberación
        $liberacion = $this->liberaciones()->create([
            'porcentaje_liberado' => $porcentaje,
            'importe_liberado' => $importeALiberar,
            'fecha_liberacion' => $fecha ?? now()->toDateString(),
            'notas' => $notas,
            'user_id' => auth()->id(),
        ]);

        // Actualizar totales en contrato
        $nuevoTotal = intval($this->porcentaje_total_liberado ?? 0) + $porcentaje;
        $nuevoImporte = floatval($this->importe_total_liberado ?? 0) + $importeALiberar;

        // Determinar nuevo estado
        $nuevoEstado = $nuevoTotal >= 100
            ? self::ESTADO_GARANTIA_LIBERADA
            : self::ESTADO_GARANTIA_PARCIALMENTE_LIBERADA;

        // Si se liberó el 100%, registrar fecha_liberacion_real
        $dataUpdate = [
            'porcentaje_total_liberado' => $nuevoTotal,
            'importe_total_liberado' => $nuevoImporte,
            'estado_garantia' => $nuevoEstado,
        ];

        if ($nuevoTotal >= 100) {
            $dataUpdate['fecha_liberacion_real'] = $fecha ?? now()->toDateString();
        }

        $this->update($dataUpdate);

        // Registrar en auditoría
        Auditoria::registrar(
            'liberar_garantia_parcial',
            'contratos',
            $this->id,
            ['porcentaje_anterior' => intval($this->porcentaje_total_liberado ?? 0) - $porcentaje],
            ['liberacion_id' => $liberacion->id, 'porcentaje_nuevo' => $nuevoTotal]
        );

        return true;
    }

    /**
     * Reactivar contrato vencido.
     */
    public function reactivar(): bool
    {
        if ($this->estado !== self::ESTADO_VENCIDO) {
            return false;
        }

        $this->update(['estado' => self::ESTADO_ACTIVO]);
        return true;
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS
    // ==========================================

    /**
     * Generar código automático para nuevo contrato.
     */
    public static function generarCodigo(): string
    {
        $year = date('Y');
        $ultimo = self::whereYear('created_at', $year)
                      ->orderByDesc('id')
                      ->value('codigo');

        if ($ultimo && preg_match('/CTR-' . $year . '-(\d+)/', $ultimo, $matches)) {
            $siguiente = intval($matches[1]) + 1;
        } else {
            $siguiente = 1;
        }

        return sprintf('CTR-%s-%04d', $year, $siguiente);
    }

    /**
     * Obtener estadísticas generales.
     */
    public static function getEstadisticas(): array
    {
        return [
            'total' => self::count(),
            'activos' => self::activos()->count(),
            'borradores' => self::borradores()->count(),
            'vencidos' => self::vencidos()->count(),
            'cancelados' => self::cancelados()->count(),
            'proximos_vencer' => self::proximosAVencer(30)->count(),
            'con_retencion' => self::conRetencion()->count(),
            'garantias_pendientes' => self::garantiasPendientes()->count(),
            'importe_retenido_total' => self::garantiasPendientes()->sum('importe_retenido'),
        ];
    }
}
