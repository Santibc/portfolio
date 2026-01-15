<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordatorioEnviado extends Model
{
    use HasFactory;

    protected $table = 'recordatorios_enviados';

    protected $fillable = [
        'fecha_especial_id',
        'user_id',
        'enviado_en',
        'tipo_recordatorio',
        'productos_sugeridos',
        'descuento_ofrecido',
        'cupon_generado',
        'abierto',
        'fecha_apertura',
        'click_realizado',
        'fecha_click',
        'compra_realizada',
        'compra_id',
    ];

    protected $casts = [
        'enviado_en' => 'datetime',
        'productos_sugeridos' => 'array',
        'abierto' => 'boolean',
        'fecha_apertura' => 'datetime',
        'click_realizado' => 'boolean',
        'fecha_click' => 'datetime',
        'compra_realizada' => 'boolean',
    ];

    // Relaciones
    public function fechaEspecial()
    {
        return $this->belongsTo(FechaEspecialCliente::class, 'fecha_especial_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    // Scopes
    public function scopeEnviados($query)
    {
        return $query->whereNotNull('enviado_en');
    }

    public function scopeAbiertos($query)
    {
        return $query->where('abierto', true);
    }

    public function scopeConClick($query)
    {
        return $query->where('click_realizado', true);
    }

    public function scopeConCompra($query)
    {
        return $query->where('compra_realizada', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_recordatorio', $tipo);
    }

    // Métodos
    public function marcarComoAbierto()
    {
        if (!$this->abierto) {
            $this->update([
                'abierto' => true,
                'fecha_apertura' => now(),
            ]);
        }
    }

    public function marcarClickRealizado()
    {
        if (!$this->click_realizado) {
            $this->update([
                'click_realizado' => true,
                'fecha_click' => now(),
            ]);
        }
    }

    public function asociarCompra($compraId)
    {
        $this->update([
            'compra_realizada' => true,
            'compra_id' => $compraId,
        ]);
    }

    // Calcular tasa de conversión
    public static function tasaConversion($fechaInicio = null, $fechaFin = null)
    {
        $query = self::query();

        if ($fechaInicio) {
            $query->where('enviado_en', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('enviado_en', '<=', $fechaFin);
        }

        $total = $query->count();
        $compras = $query->where('compra_realizada', true)->count();

        return $total > 0 ? round(($compras / $total) * 100, 2) : 0;
    }
}
