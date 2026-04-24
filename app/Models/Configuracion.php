<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'grupo',
        'descripcion',
    ];

    /**
     * @param  Builder<Configuracion>  $query
     * @return Builder<Configuracion>
     */
    public function scopeDelGrupo(Builder $query, string $grupo): Builder
    {
        return $query->where('grupo', $grupo);
    }

    public function getValorTipadoAttribute(): mixed
    {
        if ($this->valor === null) {
            return null;
        }

        return match ($this->tipo) {
            'integer' => (int) $this->valor,
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->valor, true),
            default => $this->valor,
        };
    }
}
