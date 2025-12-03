<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AceptacionContrato extends Model
{
    use HasFactory;

    protected $table = 'aceptaciones_contrato';

    protected $fillable = [
        'inversion_id',
        'usuario_id',
        'plantilla_contrato_id',
        'contenido_contrato',
        'ip_aceptacion',
        'user_agent',
        'fecha_aceptacion',
        'firma_digital',
        'acepto_terminos'
    ];

    protected $casts = [
        'fecha_aceptacion' => 'datetime',
        'acepto_terminos' => 'boolean'
    ];

    // Relaciones
    public function inversion()
    {
        return $this->belongsTo(Inversion::class, 'inversion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function plantillaContrato()
    {
        return $this->belongsTo(PlantillaContrato::class, 'plantilla_contrato_id');
    }
}
