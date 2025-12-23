<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajadorHistorialDisciplinario extends Model
{
    protected $table = 'trabajador_historial_disciplinario';

    public $timestamps = false;

    protected $fillable = [
        'trabajador_id',
        'fecha',
        'tipo',
        'descripcion',
        'documento_path',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
