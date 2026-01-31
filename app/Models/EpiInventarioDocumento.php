<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpiInventarioDocumento extends Model
{
    protected $table = 'epi_inventario_documentos';

    protected $fillable = [
        'epi_inventario_id',
        'nombre',
        'archivo_path',
        'subido_por',
    ];

    public function epiInventario(): BelongsTo
    {
        return $this->belongsTo(EpiInventario::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
