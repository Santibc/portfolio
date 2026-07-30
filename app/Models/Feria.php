<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feria extends Model
{
    use HasFactory;

    protected $table = 'ferias';

    protected $fillable = [
        'nombre',
        'ubicacion_id',
        'lista_precio_id',
        'lista_precio_base_id',
        'caja_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_CERRADA = 'cerrada';

    public static function estadosDisponibles(): array
    {
        return [
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_ACTIVA => 'Activa',
            self::ESTADO_CERRADA => 'Cerrada',
        ];
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class, 'lista_precio_id');
    }

    public function listaPrecioBase()
    {
        return $this->belongsTo(ListaPrecio::class, 'lista_precio_base_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function promociones()
    {
        return $this->hasMany(FeriaPromocion::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    public function estaEnBorrador(): bool
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function estaCerrada(): bool
    {
        return $this->estado === self::ESTADO_CERRADA;
    }

    public function estadoBadge(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVA => '<span class="badge bg-success">Activa</span>',
            self::ESTADO_CERRADA => '<span class="badge bg-secondary">Cerrada</span>',
            default => '<span class="badge bg-warning text-dark">Borrador</span>',
        };
    }
}
