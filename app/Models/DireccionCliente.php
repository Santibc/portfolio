<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DireccionCliente extends Model
{
    use HasFactory;

    protected $table = 'direcciones_cliente';

    protected $fillable = [
        'email_cliente',
        'alias',
        'nombre_destinatario',
        'telefono',
        'direccion',
        'ciudad_id',
        'instrucciones',
        'es_predeterminada'
    ];

    protected $casts = [
        'es_predeterminada' => 'boolean'
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function scopePorCliente($query, $email)
    {
        return $query->where('email_cliente', $email);
    }
}
