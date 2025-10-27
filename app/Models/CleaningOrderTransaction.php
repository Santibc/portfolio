<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningOrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleaning_order_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'payment_method_type',
        'payment_method_brand',
        'payment_method_last4',
        'stripe_session_data',
        'stripe_payment_intent_data',
        'error_code',
        'error_message',
        'webhook_events',
        'session_created_at',
        'payment_succeeded_at',
        'payment_failed_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'stripe_session_data' => 'array',
        'stripe_payment_intent_data' => 'array',
        'webhook_events' => 'array',
        'session_created_at' => 'datetime',
        'payment_succeeded_at' => 'datetime',
        'payment_failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function cleaningOrder()
    {
        return $this->belongsTo(CleaningOrder::class);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'succeeded' => 'Succeeded',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ][$this->status] ?? $this->status;
    }

    public function getPaymentMethodDisplayAttribute()
    {
        if (!$this->payment_method_brand || !$this->payment_method_last4) {
            return 'N/A';
        }
        return ucfirst($this->payment_method_brand) . ' •••• ' . $this->payment_method_last4;
    }

    /**
     * Scopes
     */
    public function scopeSucceeded($query)
    {
        return $query->where('status', 'succeeded');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Methods
     */
    public function addWebhookEvent($eventId, $eventType, $eventData = null)
    {
        $events = $this->webhook_events ?? [];
        $events[] = [
            'id' => $eventId,
            'type' => $eventType,
            'data' => $eventData,
            'processed_at' => now()->toIso8601String(),
        ];
        $this->update(['webhook_events' => $events]);
    }

    public function hasProcessedWebhookEvent($eventId)
    {
        $events = $this->webhook_events ?? [];
        return collect($events)->contains('id', $eventId);
    }
}
