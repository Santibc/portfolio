<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_compra',
        'empresa_id',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'direccion_envio',
        'ciudad_id',
        'subtotal',
        'impuestos',
        'costo_envio',
        'total',
        'estado',
        'notas'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'costo_envio' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function items()
    {
        return $this->hasMany(ItemCompra::class);
    }

    public function transaccionesPago()
    {
        return $this->hasMany(TransaccionPago::class);
    }

    public function transaccionAprobada()
    {
        return $this->hasOne(TransaccionPago::class)->where('estado', 'aprobada');
    }

    public function comision()
    {
        return $this->hasOne(Comision::class);
    }

    public function envio()
    {
        return $this->hasOne(Envio::class);
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'referencia_documento', 'numero_compra');
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('cantidad');
    }

    public function calcularTotales()
    {
        $this->subtotal = $this->items->sum('precio_total');
        $this->total = $this->subtotal + $this->impuestos + $this->costo_envio;
        $this->save();
        return $this->total;
    }

    public function generarComision()
    {
        if ($this->estado === 'pagada' && !$this->comision) {
            return Comision::create([
                'empresa_id' => $this->empresa_id,
                'compra_id' => $this->id,
                'monto_venta' => $this->total,
                'porcentaje_comision' => $this->empresa->porcentaje_comision,
                'monto_comision' => $this->total * ($this->empresa->porcentaje_comision / 100),
                'monto_empresa' => $this->total * (1 - $this->empresa->porcentaje_comision / 100),
                'estado' => 'pendiente'
            ]);
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($compra) {
            if (empty($compra->numero_compra)) {
                $compra->numero_compra = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(\Str::random(4));
            }
        });
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}