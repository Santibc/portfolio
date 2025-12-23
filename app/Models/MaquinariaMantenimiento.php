<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinariaMantenimiento extends Model
{
    protected $table = 'maquinaria_mantenimientos';

    protected $fillable = [
        'maquinaria_id',
        'tipo',
        'fecha',
        'descripcion',
        'coste',
        'proveedor',
        'realizado_por',
        'proxima_revision',
        'documento_path',
    ];

    protected $casts = [
        'fecha' => 'date',
        'proxima_revision' => 'date',
        'coste' => 'decimal:2',
    ];

    public function maquinaria(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class);
    }
}
