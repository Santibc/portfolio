<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SeOrdena;
use App\Models\Concerns\TieneImagenLanding;
use Illuminate\Database\Eloquent\Model;

/** Paso de la sección "Del horno a tu mesa". */
class LandingPaso extends Model
{
    use SeOrdena;
    use TieneImagenLanding;

    protected $table = 'landing_pasos';

    protected $fillable = [
        'titulo',
        'texto',
        'imagen',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function prefijoImagen(): string
    {
        return 'paso';
    }
}
