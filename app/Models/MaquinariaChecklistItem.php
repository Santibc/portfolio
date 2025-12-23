<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinariaChecklistItem extends Model
{
    protected $table = 'maquinaria_checklist_items';

    public $timestamps = false;

    protected $fillable = [
        'plantilla_id',
        'categoria',
        'descripcion',
        'orden',
        'obligatorio',
    ];

    protected $casts = [
        'obligatorio' => 'boolean',
    ];

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(MaquinariaChecklistPlantilla::class, 'plantilla_id');
    }
}
