<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHeroValue extends Model
{
    use HasFactory;

    protected $table = 'landing_hero_values';

    protected $fillable = [
        'icon_class',
        'title',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
