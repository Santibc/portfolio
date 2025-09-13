<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingContactInfo extends Model
{
    use HasFactory;
    
    protected $table = 'landing_contact_info';
    
    protected $fillable = [
        'address',
        'phone',
        'email',
        'description',
        'receive_messages_email',
        'google_maps_embed',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
