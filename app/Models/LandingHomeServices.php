<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHomeServices extends Model
{
    use HasFactory;

    protected $table = 'landing_home_services';

    protected $fillable = [
        'section_title',
        'section_description',
        'commercial_title',
        'residential_title',
        'eco_friendly_note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
