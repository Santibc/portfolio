<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefactura extends Model
{
    use HasFactory;

    protected $table = 'prefacturas';

    protected $fillable = [
        'numero_prefactura',
        'cliente_id',
        'nombre_cliente',
        'lista_precio_id',
        'ubicacion_id',
        'subtotal',
        'descuento_global',
        'iva',
        'total',
        'estado',
        'observaciones',
        'usuario_creador_id',
        'usuario_cajero_id',
        'venta_pdv_id',
        'motivo_anulacion',
        'anulada_por',
        'anulada_en',
        'aceptada_en',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'anulada_en' => 'datetime',
        'aceptada_en' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function usuarioCajero()
    {
        return $this->belongsTo(User::class, 'usuario_cajero_id');
    }

    public function ventaPdv()
    {
        return $this->belongsTo(VentaPdv::class);
    }

    public function anulador()
    {
        return $this->belongsTo(User::class, 'anulada_por');
    }

    public function items()
    {
        return $this->hasMany(ItemPrefactura::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAceptadas($query)
    {
        return $query->where('estado', 'aceptada');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }

    public function scopePorUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_id', $ubicacionId);
    }

    // Métodos
    public function getNombreClienteDisplayAttribute()
    {
        if ($this->cliente) {
            return $this->cliente->nombre;
        }
        return $this->nombre_cliente ?? 'Consumidor Final';
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'aceptada' => '<span class="badge bg-success">Aceptada</span>',
            'anulada' => '<span class="badge bg-danger">Anulada</span>',
            default => '<span class="badge bg-light text-dark">' . $this->estado . '</span>',
        };
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

    public static function generarNumeroPrefactura()
    {
        $fecha = now()->format('Ymd');
        $ultima = self::where('numero_prefactura', 'like', "PF-{$fecha}-%")
            ->orderBy('numero_prefactura', 'desc')
            ->first();

        if ($ultima) {
            $ultimoNumero = (int) substr($ultima->numero_prefactura, -4);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return sprintf('PF-%s-%04d', $fecha, $nuevoNumero);
    }
}
