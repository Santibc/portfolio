<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPricingRange extends Model
{
    use HasFactory;

    protected $table = 'landing_pricing_ranges';

    protected $fillable = [
        'sq_ft_min',
        'sq_ft_max',
        'initial_clean',
        'weekly',
        'biweekly',
        'monthly',
        'deep_clean',
        'move_out_clean',
        'order',
    ];

    protected $casts = [
        'sq_ft_min' => 'integer',
        'sq_ft_max' => 'integer',
        'initial_clean' => 'decimal:2',
        'weekly' => 'decimal:2',
        'biweekly' => 'decimal:2',
        'monthly' => 'decimal:2',
        'deep_clean' => 'decimal:2',
        'move_out_clean' => 'decimal:2',
        'order' => 'integer',
    ];
}
