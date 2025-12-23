<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontrataDocumentoCae extends Model
{
    protected $table = 'subcontrata_documentos_cae';

    protected $fillable = [
        'subcontrata_id',
        'tipo',
        'nombre',
        'archivo_path',
        'fecha_documento',
        'fecha_caducidad',
        'verificado',
        'verificado_por',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_caducidad' => 'date',
        'verificado' => 'boolean',
    ];

    public function subcontrata(): BelongsTo
    {
        return $this->belongsTo(Subcontrata::class);
    }

    public function verificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }
}
