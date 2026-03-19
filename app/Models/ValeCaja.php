<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValeCaja extends Model
{
    use HasFactory;

    protected $table = 'vales_caja';

    protected $fillable = [
        'sesion_caja_id',
        'caja_id',
        'descripcion',
        'monto',
        'estado',
        'usuario_id',
        'anulado_por',
        'motivo_anulacion',
        'anulado_en',
        'redimido_en',
        'redimido_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'anulado_en' => 'datetime',
        'redimido_en' => 'datetime',
    ];

    // Relaciones
    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function anulador()
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    public function redimidoPor()
    {
        return $this->belongsTo(User::class, 'redimido_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeRedimidos($query)
    {
        return $query->where('estado', 'redimido');
    }

    public function scopeAnulados($query)
    {
        return $query->where('estado', 'anulado');
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['pendiente', 'redimido']);
    }

    // Métodos
    public function redimir($usuarioId)
    {
        $this->update([
            'estado' => 'redimido',
            'redimido_en' => now(),
            'redimido_por' => $usuarioId,
        ]);
    }

    public function anular($usuarioId, $motivo)
    {
        $this->update([
            'estado' => 'anulado',
            'anulado_por' => $usuarioId,
            'anulado_en' => now(),
            'motivo_anulacion' => $motivo,
        ]);
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'redimido' => '<span class="badge bg-success">Redimido</span>',
            'anulado' => '<span class="badge bg-danger">Anulado</span>',
            default => '<span class="badge bg-light text-dark">' . $this->estado . '</span>',
        };
    }
}
