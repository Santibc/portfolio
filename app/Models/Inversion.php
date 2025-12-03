<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inversion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inversiones';

    protected $fillable = [
        'codigo_inversion',
        'usuario_id',
        'proyecto_id',
        'compra_cross_fund_id',
        'monto_invertido',
        'valor_actual',
        'ganancia_acumulada',
        'dividendos_acumulados',
        'fecha_inversion',
        'fecha_vencimiento',
        'fecha_retiro',
        'estado',
        'disponible_trading',
        'precio_venta_sugerido',
        'contrato_id',
        'notas'
    ];

    protected $casts = [
        'monto_invertido' => 'decimal:2',
        'valor_actual' => 'decimal:2',
        'ganancia_acumulada' => 'decimal:2',
        'dividendos_acumulados' => 'decimal:2',
        'precio_venta_sugerido' => 'decimal:2',
        'fecha_inversion' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_retiro' => 'date',
        'disponible_trading' => 'boolean'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function compraCrossFund()
    {
        return $this->belongsTo(CompraCrossFund::class, 'compra_cross_fund_id');
    }

    public function contrato()
    {
        return $this->belongsTo(PlantillaContrato::class, 'contrato_id');
    }

    public function dividendos()
    {
        return $this->hasMany(Dividendo::class, 'inversion_id');
    }

    public function transacciones()
    {
        return $this->hasMany(TransaccionInversion::class, 'inversion_id');
    }

    public function aceptacionContrato()
    {
        return $this->hasOne(AceptacionContrato::class, 'inversion_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeDisponiblesTrading($query)
    {
        return $query->where('disponible_trading', true)->where('estado', 'activa');
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}
