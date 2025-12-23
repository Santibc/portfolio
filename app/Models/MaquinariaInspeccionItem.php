<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinariaInspeccionItem extends Model
{
    protected $table = 'maquinaria_inspeccion_items';

    public $timestamps = false;

    protected $fillable = [
        'inspeccion_id',
        'checklist_item_id',
        'cumple',
        'observacion',
    ];

    protected $casts = [
        'cumple' => 'boolean',
    ];

    public function inspeccion(): BelongsTo
    {
        return $this->belongsTo(MaquinariaInspeccion::class, 'inspeccion_id');
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(MaquinariaChecklistItem::class, 'checklist_item_id');
    }
}
