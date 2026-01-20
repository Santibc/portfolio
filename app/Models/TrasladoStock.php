<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrasladoStock extends Model
{
    use HasFactory;

    protected $table = 'traslados_stock';

    protected $fillable = [
        'numero_traslado',
        'ubicacion_origen_id',
        'ubicacion_destino_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
        'estado',
        'notas',
        'tipo_operacion',
        'usuario_creador_id',
        'usuario_receptor_id',
        'enviado_en',
        'recibido_en',
    ];

    // Constantes de tipo de operación
    const TIPO_OPERACION_GENERAL = 'general';
    const TIPO_OPERACION_CREDITO = 'credito';

    public static function tiposOperacion(): array
    {
        return [
            self::TIPO_OPERACION_GENERAL => 'General',
            self::TIPO_OPERACION_CREDITO => 'Crédito',
        ];
    }

    protected $casts = [
        'enviado_en' => 'datetime',
        'recibido_en' => 'datetime',
    ];

    // =========================================
    // Constantes de estados
    // =========================================
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_EN_TRANSITO = 'en_transito';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_CANCELADO = 'cancelado';

    public static function estados(): array
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_EN_TRANSITO => 'En Tránsito',
            self::ESTADO_COMPLETADO => 'Completado',
            self::ESTADO_CANCELADO => 'Cancelado',
        ];
    }

    // =========================================
    // Relaciones
    // =========================================
    public function ubicacionOrigen()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_origen_id');
    }

    public function ubicacionDestino()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_destino_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function varianteProducto()
    {
        return $this->belongsTo(VarianteProducto::class);
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function usuarioReceptor()
    {
        return $this->belongsTo(User::class, 'usuario_receptor_id');
    }

    // =========================================
    // Accessors
    // =========================================
    public function getEstadoNombreAttribute(): string
    {
        return self::estados()[$this->estado] ?? $this->estado;
    }

    public function getProductoNombreAttribute(): string
    {
        $nombre = $this->producto->nombre ?? '';
        if ($this->varianteProducto) {
            $nombre .= ' - ' . $this->varianteProducto->nombre_variante;
        }
        return $nombre;
    }

    public function getTipoOperacionNombreAttribute(): string
    {
        return self::tiposOperacion()[$this->tipo_operacion] ?? $this->tipo_operacion;
    }

    // =========================================
    // Scopes
    // =========================================
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeEnTransito($query)
    {
        return $query->where('estado', self::ESTADO_EN_TRANSITO);
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    public function scopeDesdeUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_origen_id', $ubicacionId);
    }

    public function scopeHaciaUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_destino_id', $ubicacionId);
    }

    // =========================================
    // Métodos
    // =========================================
    public static function generarNumeroTraslado(): string
    {
        $prefijo = 'TR';
        $fecha = now()->format('Ymd');
        $ultimo = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $secuencial = $ultimo ? intval(substr($ultimo->numero_traslado, -4)) + 1 : 1;

        return $prefijo . $fecha . str_pad($secuencial, 4, '0', STR_PAD_LEFT);
    }

    public function enviar(): bool
    {
        if ($this->estado !== self::ESTADO_PENDIENTE) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_EN_TRANSITO,
            'enviado_en' => now(),
        ]);

        return true;
    }

    public function completar($usuarioReceptorId): bool
    {
        if ($this->estado !== self::ESTADO_EN_TRANSITO) {
            return false;
        }

        $this->update([
            'estado' => self::ESTADO_COMPLETADO,
            'usuario_receptor_id' => $usuarioReceptorId,
            'recibido_en' => now(),
        ]);

        return true;
    }

    public function cancelar(): bool
    {
        if (in_array($this->estado, [self::ESTADO_COMPLETADO, self::ESTADO_CANCELADO])) {
            return false;
        }

        $this->update(['estado' => self::ESTADO_CANCELADO]);

        return true;
    }

    public function puedeEnviar(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function puedeRecibir(): bool
    {
        return $this->estado === self::ESTADO_EN_TRANSITO;
    }

    public function puedeCancelar(): bool
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_EN_TRANSITO]);
    }
}
