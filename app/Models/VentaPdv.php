<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaPdv extends Model
{
    use HasFactory;

    protected $table = 'ventas_pdv';

    protected $fillable = [
        'numero_venta',
        'sesion_caja_id',
        'caja_id',
        'prefactura_id',
        'ubicacion_id',
        'cliente_id',
        'nombre_cliente',
        'lista_precio_id',
        'subtotal',
        'descuento',
        'descuento_global',
        'iva',
        'total',
        'metodo_pago',
        'monto_efectivo',
        'monto_tarjeta',
        'monto_transferencia',
        'monto_recibido',
        'cambio',
        'tipo_transferencia',
        'comprobante_pago',
        'estado',
        'notas',
        'usuario_id',
        'descuento_autorizado_por',
        'precio_autorizado_por',
        'anulada_por',
        'anulada_en',
        'motivo_anulacion',
        'factura_siigo_id',
        'requiere_factura',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_efectivo' => 'decimal:2',
        'monto_tarjeta' => 'decimal:2',
        'monto_transferencia' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'cambio' => 'decimal:2',
        'anulada_en' => 'datetime',
        'requiere_factura' => 'boolean',
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

    public function prefactura()
    {
        return $this->belongsTo(Prefactura::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function anulador()
    {
        return $this->belongsTo(User::class, 'anulada_por');
    }

    public function descuentoAutorizadoPor()
    {
        return $this->belongsTo(User::class, 'descuento_autorizado_por');
    }

    public function precioAutorizadoPor()
    {
        return $this->belongsTo(User::class, 'precio_autorizado_por');
    }

    public function items()
    {
        return $this->hasMany(ItemVentaPdv::class, 'venta_pdv_id');
    }

    public function facturaSiigo()
    {
        return $this->hasOne(FacturaSiigo::class, 'venta_pdv_id');
    }

    // Scopes
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }

    public function scopePorUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_id', $ubicacionId);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('created_at', $fecha);
    }

    public function scopeDelMes($query, $mes = null, $anio = null)
    {
        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;
        return $query->whereMonth('created_at', $mes)->whereYear('created_at', $anio);
    }

    public function scopePorMetodoPago($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    // Métodos
    public function getNombreClienteDisplayAttribute()
    {
        if ($this->cliente) {
            return $this->cliente->nombre;
        }
        return $this->nombre_cliente ?? 'Cliente General';
    }

    public function getEstaAnuladaAttribute()
    {
        return $this->estado === 'anulada';
    }

    public function anular($usuarioId, $motivo)
    {
        $this->update([
            'estado' => 'anulada',
            'anulada_por' => $usuarioId,
            'anulada_en' => now(),
            'motivo_anulacion' => $motivo,
        ]);
    }

    // Generar número de venta único
    public static function generarNumeroVenta($ubicacionId)
    {
        $ubicacion = Ubicacion::find($ubicacionId);
        $prefijo = $ubicacion ? strtoupper(substr($ubicacion->codigo, 0, 3)) : 'PDV';
        $fecha = now()->format('Ymd');

        $ultimaVenta = self::where('numero_venta', 'like', "{$prefijo}-{$fecha}-%")
            ->orderBy('numero_venta', 'desc')
            ->first();

        if ($ultimaVenta) {
            $ultimoNumero = (int) substr($ultimaVenta->numero_venta, -4);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return sprintf('%s-%s-%04d', $prefijo, $fecha, $nuevoNumero);
    }

    // Calcular totales del día
    public static function totalVentasDelDia($ubicacionId = null, $fecha = null)
    {
        $query = self::completadas()->delDia($fecha);

        if ($ubicacionId) {
            $query->porUbicacion($ubicacionId);
        }

        return $query->sum('total');
    }

    // Contar ventas del día
    public static function contarVentasDelDia($ubicacionId = null, $fecha = null)
    {
        $query = self::completadas()->delDia($fecha);

        if ($ubicacionId) {
            $query->porUbicacion($ubicacionId);
        }

        return $query->count();
    }
}
