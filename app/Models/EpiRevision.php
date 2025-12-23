<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpiRevision extends Model
{
    protected $table = 'epi_revisiones';

    public $timestamps = false;

    protected $fillable = [
        'epi_inventario_id',
        'fecha_revision',
        'proxima_revision',
        'resultado',
        'observaciones',
        'realizado_por',
        'documento_path',
    ];

    protected $casts = [
        'fecha_revision' => 'date',
        'proxima_revision' => 'date',
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(EpiInventario::class, 'epi_inventario_id');
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
