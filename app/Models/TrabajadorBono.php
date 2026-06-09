<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajadorBono extends Model
{
    use HasFactory;

    protected $table = 'trabajador_bonos';

    public $timestamps = false; // Solo tiene created_at

    protected $fillable = [
        'trabajador_id',
        'obra_id',
        'tipo',
        'tipo_hora_id',
        'concepto',
        'fecha',
        'importe',
        'horas',
        'pagado',
        'fecha_pago',
        'notas',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_pago' => 'date',
        'importe' => 'decimal:2',
        'horas' => 'decimal:2',
        'pagado' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function tipoHora(): BelongsTo
    {
        return $this->belongsTo(TipoHora::class, 'tipo_hora_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Scopes
    public function scopePendientesPago($query)
    {
        return $query->where('pagado', false);
    }

    public function scopePagados($query)
    {
        return $query->where('pagado', true);
    }

    public function scopePorTrabajador($query, $trabajadorId)
    {
        return $query->where('trabajador_id', $trabajadorId);
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha', [$desde, $hasta]);
    }

    // Accessors
    public function getImporteFormateadoAttribute(): string
    {
        return number_format($this->importe, 2, ',', '.') . ' €';
    }

    public function getTipoFormateadoAttribute(): string
    {
        $tipos = [
            'prima_produccion' => 'Prima por Producción',
            'bono_especial' => 'Bono Especial',
            'plus_nocturnidad' => 'Plus Nocturnidad',
            'horas' => 'Horas',
            'otro' => 'Otro',
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    public function getEstadoPagoAttribute(): string
    {
        return $this->pagado ? 'Pagado' : 'Pendiente';
    }

    public function getHorasFormateadasAttribute(): string
    {
        if ($this->horas) {
            return number_format($this->horas, 2, ',', '.') . ' h';
        }
        return '';
    }

    // Métodos helper
    public function marcarPagado($fechaPago = null): bool
    {
        $this->pagado = true;
        $this->fecha_pago = $fechaPago ?? now();
        return $this->save();
    }

    public function marcarPendiente(): bool
    {
        $this->pagado = false;
        $this->fecha_pago = null;
        return $this->save();
    }
}
