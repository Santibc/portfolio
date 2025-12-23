<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObraDocumento extends Model
{
    protected $table = 'obra_documentos';

    protected $fillable = [
        'obra_id',
        'tipo',
        'nombre',
        'archivo_path',
        'descripcion',
        'fecha_documento',
        'subido_por',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
