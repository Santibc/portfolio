<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiigoLog extends Model
{
    public $timestamps = false;

    protected $table = 'siigo_logs';

    protected $fillable = [
        'factura_siigo_id',
        'endpoint',
        'method',
        'request_body',
        'response_code',
        'response_body',
        'duracion_ms',
        'exitoso',
        'error_mensaje',
        'usuario_id',
    ];

    protected $casts = [
        'request_body' => 'array',
        'response_body' => 'array',
        'exitoso' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function facturaSiigo()
    {
        return $this->belongsTo(FacturaSiigo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
