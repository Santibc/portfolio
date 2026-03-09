<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'orden_id', 'monto', 'metodo_pago', 'referencia_pago',
        'registrado_por', 'aprobado_por', 'aprobado',
        'rechazado_por', 'motivo_rechazo',
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

    public function rechazadoPorUsuario()
    {
        return $this->belongsTo(User::class, 'rechazado_por');
    }
}
