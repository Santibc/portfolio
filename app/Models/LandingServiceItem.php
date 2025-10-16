<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingServiceItem extends Model
{
    use HasFactory;

    protected $table = 'landing_service_items';

    protected $fillable = [
        'type',
        'icon_class',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
