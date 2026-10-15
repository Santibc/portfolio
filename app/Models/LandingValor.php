<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IconoLanding;
use App\Models\Concerns\SeOrdena;
use Illuminate\Database\Eloquent\Model;

class LandingValor extends Model
{
    use SeOrdena;

    protected $table = 'landing_valores';

    protected $fillable = [
        'icono',
        'titulo',
        'texto',
        'orden',
    ];

    protected $casts = [
        'icono' => IconoLanding::class,
        'orden' => 'integer',
    ];
}
