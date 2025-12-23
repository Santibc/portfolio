<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaquinariaInspeccion extends Model
{
    protected $table = 'maquinaria_inspecciones';

    public $timestamps = false;

    protected $fillable = [
        'maquinaria_id',
        'plantilla_id',
        'fecha_inspeccion',
        'fecha_proxima_inspeccion',
        'resultado',
        'observaciones',
        'realizado_por',
        'firma_path',
        'documento_path',
    ];

    protected $casts = [
        'fecha_inspeccion' => 'date',
        'fecha_proxima_inspeccion' => 'date',
    ];

    public function maquinaria(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class);
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(MaquinariaChecklistPlantilla::class, 'plantilla_id');
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaquinariaInspeccionItem::class, 'inspeccion_id');
    }
}
