<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TextoLanding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingTexto extends Model
{
    protected $table = 'landing_textos';

    protected $fillable = [
        'clave',
        'valor',
        'updated_by',
    ];

    protected $casts = [
        'clave' => TextoLanding::class,
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
