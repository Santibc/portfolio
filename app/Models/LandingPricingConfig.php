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
        'booking_time_start',
        'booking_time_end',
    ];

    /**
     * Genera array de slots ['HH:00 AM/PM', ...] entre booking_time_start y booking_time_end (cada hora exacta).
     */
    public function getBookingTimeSlots(): array
    {
        $start = $this->booking_time_start ?? '08:00:00';
        $end = $this->booking_time_end ?? '20:00:00';

        // Tomar solo la hora (entero) de cada valor
        $startHour = (int) substr($start, 0, 2);
        $endHour = (int) substr($end, 0, 2);

        if ($endHour < $startHour) {
            return [];
        }

        $slots = [];
        for ($h = $startHour; $h <= $endHour; $h++) {
            $period = $h < 12 ? 'AM' : 'PM';
            $display = $h === 0 ? 12 : ($h > 12 ? $h - 12 : $h);
            $slots[] = sprintf('%d:00 %s', $display, $period);
        }
        return $slots;
    }

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
