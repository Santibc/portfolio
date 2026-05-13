<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBlockedZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'polygon_coordinates',
        'postcode',
        'suburb',
        'state',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePolygons($query)
    {
        return $query->where('type', 'polygon');
    }

    public function scopePostcodes($query)
    {
        return $query->where('type', 'postcode');
    }

    public function scopeSuburbs($query)
    {
        return $query->where('type', 'suburb');
    }
}
