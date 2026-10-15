<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FotoLanding;
use App\Models\Concerns\TieneImagenLanding;
use Illuminate\Database\Eloquent\Model;

/** Foto fija de la landing (un registro por slot, los crea la migración). */
class LandingFoto extends Model
{
    use TieneImagenLanding;

    protected $table = 'landing_fotos';

    protected $fillable = [
        'imagen',
        'alt',
    ];

    protected $casts = [
        'slot' => FotoLanding::class,
    ];

    public function prefijoImagen(): string
    {
        return 'foto-'.str_replace('_', '-', $this->slot->value);
    }

    /** Texto alternativo: el escrito en el panel o el del slot. */
    public function textoAlternativo(): string
    {
        return $this->alt ?: $this->slot->altPorDefecto();
    }
}
