<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingService extends Model
{
    use HasFactory;
    
    protected $table = 'landing_services';
    
    protected $fillable = [
        'icon_class',
        'title',
        'description',
        'order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
