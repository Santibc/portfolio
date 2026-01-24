<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpiInventario extends Model
{
    protected $table = 'epi_inventario';

    protected $fillable = [
        'epi_catalogo_id',
        'numero_serie',
        'fecha_compra',
        'fecha_caducidad',
        'coste',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_caducidad' => 'date',
        'coste' => 'decimal:2',
    ];

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(EpiCatalogo::class, 'epi_catalogo_id');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(EpiEntrega::class);
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(EpiRevision::class);
    }

    public function entregaActual()
    {
        return $this->entregas()->whereNull('fecha_devolucion')->first();
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeAsignados($query)
    {
        return $query->where('estado', 'asignado');
    }

    public function scopeEnRevision($query)
    {
        return $query->where('estado', 'en_revision');
    }

    public function scopeBaja($query)
    {
        return $query->where('estado', 'baja');
    }

    public function scopeProximosACaducar($query, int $dias = 30)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', now()->addDays($dias))
            ->where('fecha_caducidad', '>', now())
            ->where('estado', '!=', 'baja');
    }

    public function scopeCaducados($query)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<', now())
            ->where('estado', '!=', 'baja');
    }

    public function scopeRevisionesPendientes($query)
    {
        return $query->whereHas('catalogo', function ($q) {
            $q->where('requiere_revision', true);
        })->where(function ($q) {
            $q->whereDoesntHave('revisiones')
              ->orWhereHas('revisiones', function ($subQ) {
                  $subQ->whereRaw('epi_revisiones.id = (SELECT MAX(id) FROM epi_revisiones WHERE epi_revisiones.epi_inventario_id = epi_inventario.id)')
                       ->whereRaw('DATE_ADD(epi_revisiones.fecha_revision, INTERVAL (SELECT periodicidad_revision_meses FROM epi_catalogo WHERE epi_catalogo.id = epi_inventario.epi_catalogo_id) MONTH) <= NOW()');
              });
        })->where('estado', '!=', 'baja');
    }

    // Helpers
    public function estaAsignado(): bool
    {
        return $this->estado === 'asignado';
    }

    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible';
    }

    public function estaCaducado(): bool
    {
        return $this->fecha_caducidad && $this->fecha_caducidad->isPast();
    }

    public function proximoACaducar(int $dias = 30): bool
    {
        if (!$this->fecha_caducidad) {
            return false;
        }

        return $this->fecha_caducidad->isBetween(now(), now()->addDays($dias));
    }

    public function trabajadorActual()
    {
        $entrega = $this->entregaActual();
        return $entrega ? $entrega->trabajador : null;
    }

    public function ultimaRevision()
    {
        return $this->revisiones()->latest('fecha_revision')->first();
    }

    public function necesitaRevision(): bool
    {
        if (!$this->catalogo || !$this->catalogo->requiere_revision) {
            return false;
        }

        $ultimaRevision = $this->ultimaRevision();

        if (!$ultimaRevision) {
            return true;
        }

        $periodicidad = $this->catalogo->periodicidad_revision_meses ?? 12;
        return $ultimaRevision->fecha_revision->addMonths($periodicidad)->isPast();
    }
}
