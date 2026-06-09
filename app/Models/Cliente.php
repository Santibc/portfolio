<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'tipo',
        'tipo_identificacion',
        'identificacion',
        'nombre',
        'nombre_comercial',
        'email',
        'telefono',
        'direccion_facturacion',
        'direccion_envio',
        'pais',
        'ciudad',
        'moneda_preferida_id',
        'incoterm_id',
        'puerto_id',
        'tipo_pago_id',
        'plantilla_factura_id',
        'datos_bancarios_destino',
        'observaciones',
        'siigo_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
    ];

    /**
     * @return BelongsTo<Moneda, Cliente>
     */
    public function monedaPreferida(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'moneda_preferida_id');
    }

    /**
     * @return BelongsTo<Incoterm, Cliente>
     */
    public function incoterm(): BelongsTo
    {
        return $this->belongsTo(Incoterm::class);
    }

    /**
     * @return BelongsTo<Puerto, Cliente>
     */
    public function puerto(): BelongsTo
    {
        return $this->belongsTo(Puerto::class);
    }

    /**
     * @return BelongsTo<TipoPago, Cliente>
     */
    public function tipoPago(): BelongsTo
    {
        return $this->belongsTo(TipoPago::class);
    }

    /**
     * @return BelongsTo<PlantillaFactura, Cliente>
     */
    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(PlantillaFactura::class, 'plantilla_factura_id');
    }

    /**
     * @param  Builder<Cliente>  $query
     * @return Builder<Cliente>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<Cliente>  $query
     * @return Builder<Cliente>
     */
    public function scopeNacionales(Builder $query): Builder
    {
        return $query->where('tipo', 'nacional');
    }

    /**
     * @param  Builder<Cliente>  $query
     * @return Builder<Cliente>
     */
    public function scopeInternacionales(Builder $query): Builder
    {
        return $query->where('tipo', 'internacional');
    }

    public function esInternacional(): bool
    {
        return $this->tipo === 'internacional';
    }
}
