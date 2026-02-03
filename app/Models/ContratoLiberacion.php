<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoLiberacion extends Model
{
    use HasFactory;

    protected $table = 'contrato_liberaciones';

    protected $fillable = [
        'contrato_id',
        'porcentaje_liberado',
        'importe_liberado',
        'fecha_liberacion',
        'notas',
        'user_id',
    ];

    protected $casts = [
        'porcentaje_liberado' => 'integer',
        'importe_liberado' => 'decimal:2',
        'fecha_liberacion' => 'date',
    ];

    /**
     * Relación con contrato.
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relación con usuario que realizó la liberación.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: Filtrar liberaciones de un contrato específico.
     */
    public function scopeDeContrato($query, $contratoId)
    {
        return $query->where('contrato_id', $contratoId);
    }

    /**
     * Scope: Ordenar por fecha de liberación cronológicamente.
     */
    public function scopeOrdenCronologico($query)
    {
        return $query->orderBy('fecha_liberacion', 'asc')->orderBy('created_at', 'asc');
    }
}
