<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionParcialPdv extends Model
{
    protected $table = 'devoluciones_parciales_pdv';

    protected $fillable = [
        'venta_pdv_id',
        'usuario_id',
        'motivo',
        'subtotal',
        'iva',
        'total',
        'factura_siigo_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function ventaPdv()
    {
        return $this->belongsTo(VentaPdv::class, 'venta_pdv_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function facturaSiigo()
    {
        return $this->belongsTo(FacturaSiigo::class, 'factura_siigo_id');
    }

    public function items()
    {
        return $this->hasMany(ItemDevolucionParcialPdv::class, 'devolucion_parcial_pdv_id');
    }
}
