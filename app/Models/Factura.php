<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'numero_interno',
        'numero_siigo',
        'cufe',
        'qr_html',
        'qr_url',
        'siigo_response',
        'siigo_id',
        'stamp_status',
        'xml_firmado_path',
        'fecha',
        'vencimiento',
        'cliente_id',
        'moneda_id',
        'tasa_cambio',
        'subtotal',
        'descuento_total',
        'iva_total',
        'flete',
        'seguro',
        'total',
        'total_cop',
        'observaciones',
        'po_numero',
        'awb',
        'shipper',
        'estado',
        'es_electronica',
        'plantilla_factura_id',
        'pdf_path',
        'token_publico',
        'created_by',
        'emitida_at',
        'enviada_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vencimiento' => 'date',
        'tasa_cambio' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'iva_total' => 'decimal:2',
        'flete' => 'decimal:2',
        'seguro' => 'decimal:2',
        'total' => 'decimal:2',
        'total_cop' => 'decimal:2',
        'es_electronica' => 'bool',
        'siigo_response' => 'array',
        'emitida_at' => 'datetime',
        'enviada_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Cliente, Factura>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @return BelongsTo<Moneda, Factura>
     */
    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    /**
     * @return BelongsTo<PlantillaFactura, Factura>
     */
    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(PlantillaFactura::class, 'plantilla_factura_id');
    }

    /**
     * @return HasMany<FacturaItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(FacturaItem::class);
    }

    /**
     * @return BelongsTo<User, Factura>
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function esEditable(): bool
    {
        return $this->estado === 'borrador';
    }

    public function yaEmitida(): bool
    {
        return in_array($this->estado, ['emitida', 'enviada', 'pagada'], true);
    }

    /**
     * @param  Builder<Factura>  $query
     * @return Builder<Factura>
     */
    public function scopeEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
}
