<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SeOrdena;
use App\Models\Concerns\TieneImagenLanding;
use Illuminate\Database\Eloquent\Model;

/** Foto de la galería. Sin imagen se muestra como espacio reservado con el título. */
class LandingGaleriaFoto extends Model
{
    use SeOrdena;
    use TieneImagenLanding;

    protected $table = 'landing_galeria_fotos';

    protected $fillable = [
        'titulo',
        'imagen',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function prefijoImagen(): string
    {
        return 'galeria';
    }
}
