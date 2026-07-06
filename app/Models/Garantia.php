<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;

    protected $table = 'garantias';

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'variante_producto_id',
        'tipo',
        'tipo_otro_descripcion',
        'observacion_creacion',
        'estado',
        'observacion_liberacion',
        'solicitud_cotizacion_id',
        'usuario_creador_id',
        'usuario_liberador_id',
        'liberado_en',
    ];

    protected $casts = [
        'liberado_en' => 'datetime',
    ];

    const TIPO_CAMBIO_PRODUCTO = 'cambio_producto';
    const TIPO_DESCUENTO = 'descuento';
    const TIPO_NOTA_CREDITO = 'nota_credito';
    const TIPO_OTRO = 'otro';

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_LIBERADO = 'liberado';

    public static function tiposDisponibles(): array
    {
        return [
            self::TIPO_CAMBIO_PRODUCTO => 'Cambio de producto',
            self::TIPO_DESCUENTO => 'Descuento',
            self::TIPO_NOTA_CREDITO => 'Nota crédito',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    public function tipoLegible(): string
    {
        $tipos = self::tiposDisponibles();
        $base = $tipos[$this->tipo] ?? $this->tipo;
        if ($this->tipo === self::TIPO_OTRO && $this->tipo_otro_descripcion) {
            return $base . ': ' . $this->tipo_otro_descripcion;
        }
        return $base;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCotizacion::class, 'solicitud_cotizacion_id');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function usuarioLiberador()
    {
        return $this->belongsTo(User::class, 'usuario_liberador_id');
    }

    public function documentos()
    {
        return $this->hasMany(GarantiaDocumento::class, 'garantia_id');
    }

    public function productosLiberacion()
    {
        return $this->hasMany(GarantiaProductoLiberacion::class, 'garantia_id');
    }

    /**
     * Productos reclamados en la garantía (0..N, con cantidad).
     */
    public function items()
    {
        return $this->hasMany(GarantiaItem::class, 'garantia_id');
    }

    /**
     * Resumen legible de los productos reclamados. Usa garantia_items;
     * cae al producto único (registros viejos) como respaldo.
     */
    public function itemsResumen(): string
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return $this->items->map(function ($it) {
                $nombre = $it->producto?->nombre ?? 'Sin producto';
                if ($it->variante && $it->variante->nombre_variante) {
                    $nombre .= ' — ' . $it->variante->nombre_variante;
                }
                if ((int) $it->cantidad > 1) {
                    $nombre .= ' (x' . (int) $it->cantidad . ')';
                }
                return $nombre;
            })->implode(', ');
        }

        if ($this->producto) {
            $nombre = $this->producto->nombre;
            if ($this->variante && $this->variante->nombre_variante) {
                $nombre .= ' — ' . $this->variante->nombre_variante;
            }
            return $nombre;
        }

        return '—';
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeLiberadas($query)
    {
        return $query->where('estado', self::ESTADO_LIBERADO);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeSinSolicitud($query)
    {
        return $query->whereNull('solicitud_cotizacion_id');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function estaLiberada(): bool
    {
        return $this->estado === self::ESTADO_LIBERADO;
    }
}
