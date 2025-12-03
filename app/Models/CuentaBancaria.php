<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    use HasFactory;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'usuario_id',
        'banco',
        'tipo_cuenta',
        'numero_cuenta',
        'titular',
        'documento_titular',
        'es_principal',
        'verificada',
        'fecha_verificacion',
        'verificada_por',
        'activa'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'verificada' => 'boolean',
        'activa' => 'boolean',
        'fecha_verificacion' => 'date'
    ];

    protected $hidden = [
        'numero_cuenta'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function verificadaPor()
    {
        return $this->belongsTo(User::class, 'verificada_por');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeVerificadas($query)
    {
        return $query->where('verificada', true);
    }

    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }
}
