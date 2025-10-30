<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CleaningOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'street_address',
        'district_id',
        'unit_apt',
        'preferred_date',
        'preferred_time',
        'date_flexible',
        'time_flexible',
        'parking',
        'property_access',
        'access_notes',
        'square_footage_range',
        'num_bathrooms',
        'num_bedrooms',
        'num_kitchens',
        'other_rooms',
        'num_other_rooms',
        'other_rooms_desc',
        'num_cleaners',
        'num_hours',
        'service_type',
        'base_price',
        'service_type_price',
        'extras_total',
        'subtotal',
        'discount_amount',
        'total',
        'currency',
        'coupon_id',
        'coupon_code',
        'extras',
        'status',
        'notes',
        'admin_notes',
        'paid_at',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'extras' => 'array',
        'date_flexible' => 'boolean',
        'time_flexible' => 'boolean',
        'base_price' => 'decimal:2',
        'service_type_price' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'preferred_date' => 'date',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function transaction()
    {
        return $this->hasOne(CleaningOrderTransaction::class);
    }

    /**
     * Accessors & Mutators
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute()
    {
        $address = $this->street_address;
        if ($this->unit_apt) {
            $address = "{$this->unit_apt}, {$address}";
        }
        if ($this->district) {
            $address .= ", {$this->district->name} ({$this->district->state} {$this->district->postcode})";
        }
        return $address;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending Payment',
            'processing' => 'Processing Payment',
            'paid' => 'Paid',
            'confirmed' => 'Confirmed',
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'paid' => 'success',
            'confirmed' => 'primary',
            'scheduled' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
        ][$this->status] ?? 'secondary';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'confirmed', 'scheduled', 'in_progress', 'completed']);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'refunded']);
    }

    /**
     * Static Methods
     */
    public static function generateOrderNumber()
    {
        $prefix = 'CLN';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return "{$prefix}{$date}{$random}";
    }
}
