<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadStock extends Model
{
    use HasFactory;

    protected $table = 'novedades_stock';

    protected $fillable = [
        'producto_id',
        'variante_producto_id',
        'ubicacion_id',
        'tipo',
        'cantidad',
        'valor_original',
        'valor_saldo',
        'descripcion',
        'estado',
        'numero_garantia',
        'fecha_vencimiento_garantia',
        'usuario_id',
        'usuario_cierre_id',
        'cerrado_en',
        'notas_cierre',
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'valor_saldo' => 'decimal:2',
        'fecha_vencimiento_garantia' => 'date',
        'cerrado_en' => 'datetime',
    ];

    // =========================================
    // Constantes de tipos
    // =========================================
    const TIPO_GARANTIA = 'garantia';
    const TIPO_SALDO = 'saldo';
    const TIPO_PERDIDA = 'perdida';
    const TIPO_DANO = 'dano';

    public static function tipos(): array
    {
        return [
            self::TIPO_GARANTIA => 'Garantía',
            self::TIPO_SALDO => 'Saldo',
            self::TIPO_PERDIDA => 'Pérdida',
            self::TIPO_DANO => 'Daño',
        ];
    }

    // =========================================
    // Constantes de estados
    // =========================================
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_PROCESADO = 'procesado';
    const ESTADO_RECUPERADO = 'recuperado';
    const ESTADO_DADO_DE_BAJA = 'dado_de_baja';

    public static function estados(): array
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_PROCESADO => 'Procesado',
            self::ESTADO_RECUPERADO => 'Recuperado',
            self::ESTADO_DADO_DE_BAJA => 'Dado de Baja',
        ];
    }

    // =========================================
    // Relaciones
    // =========================================
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function varianteProducto()
    {
        return $this->belongsTo(VarianteProducto::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    // =========================================
    // Accessors
    // =========================================
    public function getTipoNombreAttribute(): string
    {
        return self::tipos()[$this->tipo] ?? $this->tipo;
    }

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

    public function getGarantiaVigentAttribute(): bool
    {
        if ($this->tipo !== self::TIPO_GARANTIA || !$this->fecha_vencimiento_garantia) {
            return false;
        }
        return $this->fecha_vencimiento_garantia->isFuture();
    }

    // =========================================
    // Scopes
    // =========================================
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeGarantias($query)
    {
        return $query->where('tipo', self::TIPO_GARANTIA);
    }

    public function scopeSaldos($query)
    {
        return $query->where('tipo', self::TIPO_SALDO);
    }

    public function scopePerdidas($query)
    {
        return $query->where('tipo', self::TIPO_PERDIDA);
    }

    public function scopeDanos($query)
    {
        return $query->where('tipo', self::TIPO_DANO);
    }

    public function scopeEnUbicacion($query, $ubicacionId)
    {
        return $query->where('ubicacion_id', $ubicacionId);
    }

    public function scopeGarantiasVigentes($query)
    {
        return $query->where('tipo', self::TIPO_GARANTIA)
            ->where('fecha_vencimiento_garantia', '>=', now())
            ->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeGarantiasVencidas($query)
    {
        return $query->where('tipo', self::TIPO_GARANTIA)
            ->where('fecha_vencimiento_garantia', '<', now())
            ->where('estado', self::ESTADO_PENDIENTE);
    }

    // =========================================
    // Métodos
    // =========================================
    public function cerrar($usuarioId, $nuevoEstado, $notas = null): bool
    {
        if ($this->estado !== self::ESTADO_PENDIENTE) {
            return false;
        }

        $this->update([
            'estado' => $nuevoEstado,
            'usuario_cierre_id' => $usuarioId,
            'cerrado_en' => now(),
            'notas_cierre' => $notas,
        ]);

        return true;
    }

    public function marcarComoRecuperado($usuarioId, $notas = null): bool
    {
        return $this->cerrar($usuarioId, self::ESTADO_RECUPERADO, $notas);
    }

    public function marcarComoDadoDeBaja($usuarioId, $notas = null): bool
    {
        return $this->cerrar($usuarioId, self::ESTADO_DADO_DE_BAJA, $notas);
    }

    public function marcarComoProcesado($usuarioId, $notas = null): bool
    {
        return $this->cerrar($usuarioId, self::ESTADO_PROCESADO, $notas);
    }

    public function puedeCerrar(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function getValorPerdidaAttribute(): float
    {
        if ($this->tipo === self::TIPO_SALDO && $this->valor_saldo) {
            return $this->valor_original - $this->valor_saldo;
        }
        return $this->valor_original;
    }
}
