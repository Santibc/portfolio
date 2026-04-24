<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiigoCatalogo extends Model
{
    use HasFactory;

    protected $table = 'siigo_catalogos';

    protected $fillable = [
        'tipo',
        'codigo',
        'nombre',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @param  Builder<SiigoCatalogo>  $query
     * @return Builder<SiigoCatalogo>
     */
    public function scopeTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }
}
