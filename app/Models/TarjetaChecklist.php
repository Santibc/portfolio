<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarjetaChecklist extends Model
{
    protected $table = 'tarjeta_checklists';

    protected $fillable = ['tarjeta_id', 'titulo', 'posicion'];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TarjetaChecklistItem::class, 'checklist_id')->orderBy('posicion');
    }
}
