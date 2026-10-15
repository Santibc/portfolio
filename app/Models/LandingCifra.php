<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SeOrdena;
use Illuminate\Database\Eloquent\Model;

class LandingCifra extends Model
{
    use SeOrdena;

    protected $table = 'landing_cifras';

    protected $fillable = [
        'valor',
        'prefijo',
        'sufijo',
        'texto',
        'orden',
    ];

    protected $casts = [
        'valor' => 'integer',
        'orden' => 'integer',
    ];

    /** "a.m." se muestra separado del número ("6 a.m."); "%" va pegado ("100%"). */
    public function getSufijoVisibleAttribute(): string
    {
        $sufijo = (string) $this->sufijo;

        return preg_match('/^\p{L}/u', $sufijo) ? ' '.$sufijo : $sufijo;
    }
}
