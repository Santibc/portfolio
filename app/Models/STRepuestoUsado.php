<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STRepuestoUsado extends Model
{
    use HasFactory;

    protected $table = 'st_repuestos_usados';

    protected $fillable = [
        'st_orden_servicio_id',
        'st_repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relaciones
    public function ordenServicio()
    {
        return $this->belongsTo(STOrdenServicio::class, 'st_orden_servicio_id');
    }

    public function repuesto()
    {
        return $this->belongsTo(STRepuesto::class, 'st_repuesto_id');
    }

    // Event listeners
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($repuestoUsado) {
            // Calcular subtotal automáticamente
            $repuestoUsado->subtotal = $repuestoUsado->cantidad * $repuestoUsado->precio_unitario;

            // Descontar del stock
            $repuesto = STRepuesto::find($repuestoUsado->st_repuesto_id);
            if ($repuesto) {
                $repuesto->ajustarStock($repuestoUsado->cantidad, 'salida');
            }
        });

        static::deleting(function ($repuestoUsado) {
            // Devolver al stock
            $repuesto = STRepuesto::find($repuestoUsado->st_repuesto_id);
            if ($repuesto) {
                $repuesto->ajustarStock($repuestoUsado->cantidad, 'entrada');
            }
        });
    }
}
