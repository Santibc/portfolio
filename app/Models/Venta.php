<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'user_id',
        'almacen_id',
        'cliente_id',
        'fecha',
        'monto',
        'descripcion',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(ItemVenta::class, 'venta_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalcularMonto(): void
    {
        $total = (float) $this->items()->sum('precio_total');
        $this->monto = $total;
        $this->saveQuietly();
    }

    public function scopeDelMes($query, int $anio, int $mes)
    {
        return $query->whereYear('fecha', $anio)->whereMonth('fecha', $mes);
    }

    public function scopeDelVendedor($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
