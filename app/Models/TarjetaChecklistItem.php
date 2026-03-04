<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarjetaChecklistItem extends Model
{
    protected $table = 'tarjeta_checklist_items';

    protected $fillable = ['checklist_id', 'texto', 'completado', 'completado_por', 'fecha_completado', 'posicion'];

    protected $casts = [
        'completado' => 'boolean',
        'fecha_completado' => 'datetime',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(TarjetaChecklist::class, 'checklist_id');
    }

    public function completadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completado_por');
    }
}
