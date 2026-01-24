<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontrataDocumentoObra extends Model
{
    protected $table = 'subcontrata_documentos_obra';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'subcontrata_id',
        'obra_id',
        'tipo',
        'nombre',
        'archivo_path',
        'fecha_documento',
        'fecha_caducidad',
        'obligatorio',
        'verificado',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_caducidad' => 'date',
        'obligatorio' => 'boolean',
        'verificado' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function subcontrata(): BelongsTo
    {
        return $this->belongsTo(Subcontrata::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    // Scopes
    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<', now());
    }

    public function scopeProximosAVencer($query, $dias = 30)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->whereBetween('fecha_caducidad', [now(), now()->addDays($dias)]);
    }

    public function scopeObligatorios($query)
    {
        return $query->where('obligatorio', true);
    }

    public function scopeNoVerificados($query)
    {
        return $query->where('verificado', false);
    }

    // Helpers
    public function estaVencido(): bool
    {
        return $this->fecha_caducidad && $this->fecha_caducidad->isPast();
    }

    public function estaProximoAVencer(int $dias = 30): bool
    {
        if (!$this->fecha_caducidad) {
            return false;
        }

        return $this->fecha_caducidad->isBetween(now(), now()->addDays($dias));
    }
}
