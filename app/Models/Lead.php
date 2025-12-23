<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'cliente_id',
        'nombre_empresa',
        'persona_contacto',
        'telefono',
        'email',
        'origen',
        'descripcion',
        'importe_estimado',
        'probabilidad',
        'temperatura',
        'capacidad_economica_percibida',
        'fecha_estimada_cierre',
        'estado',
        'motivo_perdida',
        'asignado_a',
    ];

    protected $casts = [
        'importe_estimado' => 'decimal:2',
        'fecha_estimada_cierre' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(LeadInteraccion::class);
    }

    // Scopes
    public function scopeNuevos($query)
    {
        return $query->where('estado', 'nuevo');
    }

    public function scopeCalientes($query)
    {
        return $query->where('temperatura', 'caliente');
    }

    public function scopeAbiertos($query)
    {
        return $query->whereNotIn('estado', ['ganado', 'perdido']);
    }
}
