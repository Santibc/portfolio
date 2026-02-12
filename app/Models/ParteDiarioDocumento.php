<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteDiarioDocumento extends Model
{
    protected $table = 'parte_diario_documentos';

    protected $fillable = [
        'parte_diario_id',
        'nombre',
        'archivo_path',
        'archivo_nombre_original',
        'subido_por',
    ];

    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function getArchivoUrlAttribute(): string
    {
        return asset($this->archivo_path);
    }

    public function archivoExiste(): bool
    {
        return file_exists(public_path($this->archivo_path));
    }
}
