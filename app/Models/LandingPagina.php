<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPagina extends Model
{
    use HasFactory;

    protected $table = 'landing_paginas';

    protected $fillable = [
        'slug',
        'titulo',
        'contenido',
        'activo',
    ];

    protected $casts = [
        'contenido' => 'array',
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public static function porSlug($slug)
    {
        return self::where('slug', $slug)->first();
    }
}
