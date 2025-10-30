<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPricingConfig extends Model
{
    use HasFactory;

    protected $table = 'landing_pricing_config';

    protected $fillable = [
        'whatsapp_number',
        'cleaner_price',
        'hour_price',
        'normal_service_price',
        'deep_service_price',
        'extra_heavy_duty',
        'inside_fridge_ea',
        'inside_oven_ea',
        'post_construction_government',
        'post_construction_private',
        'window_clean_interior',
        'window_clean_exterior',
        'recurring_weekly_discount',
        'recurring_biweekly_discount',
    ];

    protected $casts = [
        'cleaner_price' => 'decimal:2',
        'hour_price' => 'decimal:2',
        'normal_service_price' => 'decimal:2',
        'deep_service_price' => 'decimal:2',
        'extra_heavy_duty' => 'decimal:2',
        'inside_fridge_ea' => 'decimal:2',
        'inside_oven_ea' => 'decimal:2',
        'post_construction_government' => 'decimal:2',
        'post_construction_private' => 'decimal:2',
        'window_clean_interior' => 'decimal:2',
        'window_clean_exterior' => 'decimal:2',
        'recurring_weekly_discount' => 'integer',
        'recurring_biweekly_discount' => 'integer',
    ];
}
