<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleanerHourPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_cleaners',
        'num_hours',
        'price',
        'order',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}
