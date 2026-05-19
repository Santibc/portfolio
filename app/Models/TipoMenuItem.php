<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMenuItem extends Model
{
    protected $table = 'tipos_menu_item';

    protected $fillable = [
        'nombre',
        'slug',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'tipo_id');
    }
}
