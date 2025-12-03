<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retiro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'retiros';

    protected $fillable = [
        'codigo_retiro',
        'usuario_id',
        'monto_solicitado',
        'monto_aprobado',
        'comision',
        'monto_neto',
        'metodo_pago',
        'datos_pago',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_rechazo',
        'fecha_pago',
        'estado',
        'aprobado_por',
        'pagado_por',
        'notas_aprobacion',
        'motivo_rechazo',
        'comprobante_pago'
    ];

    protected $casts = [
        'monto_solicitado' => 'decimal:2',
        'monto_aprobado' => 'decimal:2',
        'comision' => 'decimal:2',
        'monto_neto' => 'decimal:2',
        'fecha_solicitud' => 'date',
        'fecha_aprobacion' => 'date',
        'fecha_rechazo' => 'date',
        'fecha_pago' => 'date'
    ];

    protected $hidden = [
        'datos_pago'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Alias para compatibilidad
    public function user()
    {
        return $this->usuario();
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function pagadoPor()
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }
}
