<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'orden_id', 'monto', 'metodo_pago', 'referencia_pago',
        'registrado_por', 'aprobado_por', 'aprobado',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'aprobado' => 'boolean',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function registradoPorUsuario()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function aprobadoPorUsuario()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
