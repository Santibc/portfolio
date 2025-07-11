<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'llamada_id',
        'nombre_cliente',
        'apellido_cliente',
        'email_cliente',
        'telefono_cliente',
        'identificacion_personal',
        'domicilio',
        'metodo_pago',
        'comprobante_pago_path',
        'tipo_acuerdo',
        'comentarios',
    ];

    // Relaciones

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function llamada()
    {
        return $this->belongsTo(Llamada::class);
    }
}
