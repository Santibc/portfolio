<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteEmailAdicional extends Model
{
    protected $table = 'cliente_emails_adicionales';

    protected $fillable = [
        'cliente_id',
        'email',
        'nombre',
        'cargo',
        'activo',
        'enviar_facturas_por_defecto',
        'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'enviar_facturas_por_defecto' => 'boolean',
    ];

    // Relationships
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorDefecto($query)
    {
        return $query->where('enviar_facturas_por_defecto', true);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        if ($this->nombre && $this->cargo) {
            return "{$this->nombre} ({$this->cargo})";
        }
        return $this->nombre ?? $this->email;
    }
}
