<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaquinariaChecklistPlantilla extends Model
{
    protected $table = 'maquinaria_checklist_plantillas';

    protected $fillable = [
        'maquinaria_tipo_id',
        'nombre',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(MaquinariaTipo::class, 'maquinaria_tipo_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaquinariaChecklistItem::class, 'plantilla_id')->orderBy('orden');
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(MaquinariaInspeccion::class, 'plantilla_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
