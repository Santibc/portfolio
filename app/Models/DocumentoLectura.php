<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoLectura extends Model
{
    protected $table = 'documento_lecturas';

    public $timestamps = false;

    protected $fillable = [
        'documento_id',
        'trabajador_id',
        'fecha_lectura',
        'ip_address',
        'user_agent',
        'aceptado',
    ];

    protected $casts = [
        'fecha_lectura' => 'datetime',
        'aceptado' => 'boolean',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(TrabajadorDocumento::class, 'documento_id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
