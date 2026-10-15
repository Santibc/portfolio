<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Datos del negocio que muestra la landing. Tabla de una sola fila (la crea la migración). */
class LandingAjuste extends Model
{
    protected $table = 'landing_ajustes';

    protected $fillable = [
        'nombre',
        'nombre_largo',
        'lema',
        'propietaria',
        'propietaria_cargo',
        'telefono',
        'whatsapp',
        'whatsapp_mensaje',
        'direccion',
        'barrio',
        'ciudad',
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'updated_by',
    ];

    public static function actual(): self
    {
        return static::query()->firstOrFail();
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return array<string, string> red => URL, solo las que tienen valor */
    public function redes(): array
    {
        return array_filter([
            'instagram' => $this->instagram_url,
            'facebook' => $this->facebook_url,
            'tiktok' => $this->tiktok_url,
        ]);
    }
}
