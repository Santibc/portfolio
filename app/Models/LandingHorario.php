<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SeOrdena;
use Illuminate\Database\Eloquent\Model;

class LandingHorario extends Model
{
    use SeOrdena;

    protected $table = 'landing_horarios';

    protected $fillable = [
        'dias',
        'horas',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];
}
