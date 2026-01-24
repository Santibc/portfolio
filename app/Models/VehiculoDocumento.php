<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiculoDocumento extends Model
{
    protected $table = 'vehiculo_documentos';

    public $timestamps = false;

    protected $fillable = [
        'vehiculo_id',
        'tipo',
        'nombre',
        'archivo_path',
        'fecha_documento',
        'fecha_caducidad',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_caducidad' => 'date',
    ];

    const TIPOS = [
        'ficha_tecnica' => 'Ficha Técnica',
        'permiso_circulacion' => 'Permiso de Circulación',
        'seguro' => 'Seguro',
        'itv' => 'ITV',
        'otro' => 'Otro',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
