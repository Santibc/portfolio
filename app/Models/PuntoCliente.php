<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoCliente extends Model
{
    use HasFactory;

    protected $table = 'puntos_cliente';

    protected $fillable = [
        'user_id',
        'empresa_id',
        'puntos',
        'tipo',
        'concepto',
        'compra_id',
        'fecha_expiracion',
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    // Scopes
    public function scopeGanados($query)
    {
        return $query->where('tipo', 'ganados');
    }

    public function scopeCanjeados($query)
    {
        return $query->where('tipo', 'canjeados');
    }

    public function scopeReferidos($query)
    {
        return $query->where('tipo', 'referido');
    }

    public function scopeNoExpirados($query)
    {
        return $query->where(function($q) {
            $q->whereNull('fecha_expiracion')
              ->orWhere('fecha_expiracion', '>=', now());
        });
    }

    public function scopePorUsuario($query, $userId, $empresaId)
    {
        return $query->where('user_id', $userId)
                     ->where('empresa_id', $empresaId);
    }

    // Obtener balance de puntos del usuario
    public static function getBalance($userId, $empresaId)
    {
        $ganados = self::porUsuario($userId, $empresaId)
            ->whereIn('tipo', ['ganados', 'referido'])
            ->noExpirados()
            ->sum('puntos');

        $canjeados = self::porUsuario($userId, $empresaId)
            ->where('tipo', 'canjeados')
            ->sum('puntos');

        return $ganados - abs($canjeados);
    }

    // Registrar puntos ganados por compra
    public static function registrarPuntosCompra($userId, $empresaId, $compraId, $totalCompra, $porcentajePuntos = 1)
    {
        $puntos = floor($totalCompra * $porcentajePuntos / 100);

        if ($puntos > 0) {
            return self::create([
                'user_id' => $userId,
                'empresa_id' => $empresaId,
                'puntos' => $puntos,
                'tipo' => 'ganados',
                'concepto' => 'Puntos por compra #' . $compraId,
                'compra_id' => $compraId,
                'fecha_expiracion' => now()->addYear(),
            ]);
        }

        return null;
    }

    // Canjear puntos
    public static function canjearPuntos($userId, $empresaId, $puntos, $concepto = 'Canje de puntos')
    {
        $balance = self::getBalance($userId, $empresaId);

        if ($balance < $puntos) {
            return false;
        }

        return self::create([
            'user_id' => $userId,
            'empresa_id' => $empresaId,
            'puntos' => -abs($puntos),
            'tipo' => 'canjeados',
            'concepto' => $concepto,
        ]);
    }

    // Registrar puntos por referido
    public static function registrarPuntosReferido($userId, $empresaId, $referidoNombre, $puntos = 500)
    {
        return self::create([
            'user_id' => $userId,
            'empresa_id' => $empresaId,
            'puntos' => $puntos,
            'tipo' => 'referido',
            'concepto' => 'Puntos por referir a ' . $referidoNombre,
            'fecha_expiracion' => now()->addYear(),
        ]);
    }

    // Registrar puntos de forma genérica
    public static function registrarPuntos($userId, $empresaId, $puntos, $tipo = 'ganados', $concepto = '', $compraId = null)
    {
        return self::create([
            'user_id' => $userId,
            'empresa_id' => $empresaId,
            'puntos' => $puntos,
            'tipo' => $tipo,
            'concepto' => $concepto,
            'compra_id' => $compraId,
            'fecha_expiracion' => in_array($tipo, ['ganados', 'referido']) ? now()->addYear() : null,
        ]);
    }
}
