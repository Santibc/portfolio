<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiaSemana extends Model
{
    protected $table = 'dias_semana';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nombre',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_dia', 'dia_semana_id', 'menu_item_id')
            ->withTimestamps();
    }
}
