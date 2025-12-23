<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadInteraccion extends Model
{
    protected $table = 'lead_interacciones';

    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'cliente_id',
        'tipo',
        'fecha',
        'descripcion',
        'proximo_paso',
        'fecha_proximo_contacto',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_proximo_contacto' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
