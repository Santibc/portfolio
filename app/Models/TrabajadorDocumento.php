<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrabajadorDocumento extends Model
{
    protected $table = 'trabajador_documentos';

    protected $fillable = [
        'trabajador_id',
        'tipo',
        'nombre',
        'archivo_path',
        'fecha_documento',
        'fecha_caducidad',
        'visible_trabajador',
        'requiere_lectura',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_caducidad' => 'date',
        'visible_trabajador' => 'boolean',
        'requiere_lectura' => 'boolean',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function lecturas(): HasMany
    {
        return $this->hasMany(DocumentoLectura::class, 'documento_id');
    }

    public function getProximoACaducarAttribute(): bool
    {
        if (!$this->fecha_caducidad) return false;
        if ($this->fecha_caducidad->isPast()) return false; // Ya caducado
        return $this->fecha_caducidad->diffInDays(now()) <= 30;
    }

    public function getCaducadoAttribute(): bool
    {
        if (!$this->fecha_caducidad) return false;
        return $this->fecha_caducidad->isPast();
    }
}
