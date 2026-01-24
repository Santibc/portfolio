<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subcontrata extends Model
{
    use SoftDeletes;

    protected $table = 'subcontratas';

    protected $fillable = [
        'nombre',
        'razon_social',
        'cif',
        'direccion',
        'telefono',
        'email',
        'persona_contacto',
        'tarifa_hora',
        'tarifa_dia',
        'activa',
        'homologada',
        'fecha_homologacion',
        'notas',
    ];

    protected $casts = [
        'tarifa_hora' => 'decimal:2',
        'tarifa_dia' => 'decimal:2',
        'activa' => 'boolean',
        'homologada' => 'boolean',
        'fecha_homologacion' => 'date',
    ];

    // =============================================
    // RELACIONES
    // =============================================

    public function trabajadores(): HasMany
    {
        return $this->hasMany(Trabajador::class);
    }

    public function documentosCae(): HasMany
    {
        return $this->hasMany(SubcontrataDocumentoCae::class);
    }

    public function documentosObra(): HasMany
    {
        return $this->hasMany(SubcontrataDocumentoObra::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Obra::class, 'obra_subcontratas')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'importe_contratado', 'activa', 'notas'])
            ->withTimestamps();
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeInactivas($query)
    {
        return $query->where('activa', false);
    }

    public function scopeHomologadas($query)
    {
        return $query->where('homologada', true);
    }

    public function scopeNoHomologadas($query)
    {
        return $query->where('homologada', false);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('razon_social', 'like', "%{$termino}%")
              ->orWhere('cif', 'like', "%{$termino}%")
              ->orWhere('persona_contacto', 'like', "%{$termino}%");
        });
    }

    // =============================================
    // ACCESSORS
    // =============================================

    public function getNombreCompletoAttribute(): string
    {
        return $this->razon_social ?? $this->nombre;
    }

    public function getDocumentosCaeVencidosAttribute(): int
    {
        return $this->documentosCae()
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<', now())
            ->count();
    }

    public function getDocumentosCaeProximosAttribute(): int
    {
        return $this->documentosCae()
            ->whereNotNull('fecha_caducidad')
            ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
            ->count();
    }

    public function getTrabajadoresActivosCountAttribute(): int
    {
        return $this->trabajadores()->where('activo', true)->count();
    }

    public function getObrasActivasCountAttribute(): int
    {
        return $this->obras()->wherePivot('activa', true)->count();
    }

    // =============================================
    // HELPERS
    // =============================================

    public function tieneDocumentosVencidos(): bool
    {
        return $this->documentos_cae_vencidos > 0;
    }

    public function tieneDocumentosProximosAVencer(): bool
    {
        return $this->documentos_cae_proximos > 0;
    }
}
