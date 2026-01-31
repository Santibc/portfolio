<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'tipo',
        'nombre_comercial',
        'razon_social',
        'cif',
        'direccion',
        'codigo_postal',
        'ciudad',
        'provincia',
        'pais',
        'telefono',
        'email',
        'persona_contacto',
        'telefono_contacto',
        'email_contacto',
        'condiciones_pago',
        'retencion_porcentaje',
        'notas',
        'activo',
    ];

    protected $casts = [
        'retencion_porcentaje' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(LeadInteraccion::class);
    }

    public function emailsAdicionales(): HasMany
    {
        return $this->hasMany(ClienteEmailAdicional::class);
    }

    public function emailsAdicionalesActivos(): HasMany
    {
        return $this->hasMany(ClienteEmailAdicional::class)->where('activo', true);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return $this->razon_social ?? $this->nombre_comercial;
    }

    /**
     * Get all available emails for this client (main + contact + additional)
     */
    public function getTodosEmailsAttribute(): array
    {
        $emails = [];

        if ($this->email) {
            $emails[] = [
                'email' => $this->email,
                'tipo' => 'principal',
                'label' => "Email principal: {$this->email}",
            ];
        }

        if ($this->email_contacto && $this->email_contacto !== $this->email) {
            $emails[] = [
                'email' => $this->email_contacto,
                'tipo' => 'contacto',
                'label' => "Email contacto: {$this->email_contacto}",
            ];
        }

        foreach ($this->emailsAdicionalesActivos as $adicional) {
            $emails[] = [
                'email' => $adicional->email,
                'tipo' => 'adicional',
                'id' => $adicional->id,
                'label' => $adicional->nombre
                    ? "{$adicional->nombre} ({$adicional->email})"
                    : $adicional->email,
                'por_defecto' => $adicional->enviar_facturas_por_defecto,
            ];
        }

        return $emails;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePublicos($query)
    {
        return $query->where('tipo', 'publico');
    }

    public function scopePrivados($query)
    {
        return $query->where('tipo', 'privado');
    }
}
