<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alerta extends Model
{
    protected $table = 'alertas';

    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'titulo',
        'mensaje',
        'prioridad',
        'alertable_type',
        'alertable_id',
        'para_roles',
        'para_usuario_id',
        'fecha_vencimiento',
        'leida',
        'fecha_lectura',
        'resuelta',
        'fecha_resolucion',
    ];

    protected $casts = [
        'para_roles' => 'array',
        'fecha_vencimiento' => 'date',
        'fecha_lectura' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'leida' => 'boolean',
        'resuelta' => 'boolean',
    ];

    // Relación polimórfica
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function paraUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'para_usuario_id');
    }

    // Métodos
    public function marcarLeida(): void
    {
        $this->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);
    }

    public function marcarResuelta(): void
    {
        $this->update([
            'resuelta' => true,
            'fecha_resolucion' => now(),
        ]);
    }

    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeNoResueltas($query)
    {
        return $query->where('resuelta', false);
    }

    public function scopeCriticas($query)
    {
        return $query->where('prioridad', 'critica');
    }

    public function scopeParaRol($query, string $rol)
    {
        return $query->whereJsonContains('para_roles', $rol);
    }

    public function scopeParaUsuario($query, int $userId)
    {
        return $query->where('para_usuario_id', $userId);
    }
}
