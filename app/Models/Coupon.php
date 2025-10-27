<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'usage_limit',
        'usage_count',
        'start_date',
        'expiry_date',
        'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now()->format('Y-m-d');
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('usage_count < usage_limit');
            });
    }

    public function isValid()
    {
        $now = Carbon::now();

        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->expiry_date && $now->gt($this->expiry_date)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($subtotal * $this->discount_value) / 100;
        }

        return $this->discount_value;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
