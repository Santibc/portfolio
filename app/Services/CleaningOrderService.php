<?php

namespace App\Services;

use App\Models\CleaningOrder;
use App\Models\CleaningOrderTransaction;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * CleaningOrderService
 *
 * Business logic layer for cleaning orders
 * Handles order creation, calculation, and management
 */
class CleaningOrderService
{
    /**
     * Create a new cleaning order from form data
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function createOrder(array $data): array
    {
        // Validate data
        $validator = $this->validateOrderData($data);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        DB::beginTransaction();

        try {
            // Calculate pricing
            $pricing = $this->calculatePricing($data);

            // Generate order number
            $orderNumber = CleaningOrder::generateOrderNumber();

            // Create order
            $order = CleaningOrder::create([
                'order_number' => $orderNumber,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'street_address' => $data['street_address'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'formatted_address' => $data['formatted_address'] ?? null,
                'place_id' => $data['place_id'] ?? null,
                'postcode' => $data['postcode'] ?? null,
                'suburb' => $data['suburb'] ?? null,
                'state' => $data['state'] ?? null,
                'unit_apt' => $data['unit_apt'] ?? null,
                'preferred_date' => $data['preferred_date'],
                'preferred_time' => $data['preferred_time'],
                'date_flexible' => $data['date_flexible'] ?? false,
                'time_flexible' => $data['time_flexible'] ?? false,
                'parking' => $data['parking'] ?? null,
                'property_access' => $data['property_access'] ?? null,
                'access_notes' => $data['access_notes'] ?? null,
                'square_footage_range' => $data['square_footage_range'] ?? null,
                'num_bathrooms' => $data['num_bathrooms'] ?? null,
                'num_bedrooms' => $data['num_bedrooms'] ?? null,
                'num_kitchens' => $data['num_kitchens'] ?? null,
                'other_rooms' => $data['other_rooms'] ?? null,
                'num_other_rooms' => $data['num_other_rooms'] ?? null,
                'other_rooms_desc' => $data['other_rooms_desc'] ?? null,
                'num_cleaners' => $data['num_cleaners'] ?? null,
                'num_hours' => $data['num_hours'] ?? null,
                'service_type' => $data['service_type'] ?? null,
                'base_price' => $pricing['base_price'],
                'service_type_price' => $pricing['service_type_price'] ?? 0,
                'extras_total' => $pricing['extras_total'],
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['discount_amount'],
                'total' => $pricing['total'],
                'currency' => 'USD',
                'coupon_id' => $pricing['coupon_id'] ?? null,
                'coupon_code' => $pricing['coupon_code'] ?? null,
                'extras' => $data['extras'] ?? [],
                'status' => 'pending',
            ]);

            // Create transaction record
            $transaction = CleaningOrderTransaction::create([
                'cleaning_order_id' => $order->id,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            DB::commit();

            Log::info('Cleaning order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return [
                'success' => true,
                'order' => $order,
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating cleaning order', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create order: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate pricing for an order
     *
     * @param array $data
     * @return array
     */
    public function calculatePricing(array $data): array
    {
        $roomsPrice = (float) ($data['rooms_price'] ?? 0);
        $basePrice = (float) ($data['base_price'] ?? 0);
        $serviceTypePrice = (float) ($data['service_type_price'] ?? 0);
        $extrasTotal = (float) ($data['extras_total'] ?? 0);
        $subtotal = $roomsPrice + $basePrice + $serviceTypePrice + $extrasTotal;
        $discountAmount = 0;
        $couponId = null;
        $couponCode = null;

        // Apply coupon if provided
        if (!empty($data['coupon_code'])) {
            $couponResult = $this->applyCoupon($data['coupon_code'], $subtotal);
            if ($couponResult['valid']) {
                $discountAmount = $couponResult['discount_amount'];
                $couponId = $couponResult['coupon_id'];
                $couponCode = $couponResult['coupon_code'];
            }
        }

        $total = $subtotal - $discountAmount;

        // Ensure total is not negative
        $total = max(0, $total);

        return [
            'base_price' => $basePrice,
            'service_type_price' => $serviceTypePrice,
            'extras_total' => $extrasTotal,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'coupon_id' => $couponId,
            'coupon_code' => $couponCode,
        ];
    }

    /**
     * Apply and validate a coupon
     *
     * @param string $code
     * @param float $subtotal
     * @return array
     */
    public function applyCoupon(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code',
            ];
        }

        // Check expiration
        if ($coupon->expires_at && $coupon->expires_at < now()) {
            return [
                'valid' => false,
                'message' => 'This coupon has expired',
            ];
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            return [
                'valid' => false,
                'message' => 'This coupon has reached its usage limit',
            ];
        }

        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of \${$coupon->min_order_amount} required",
            ];
        }

        // Calculate discount
        $discountAmount = 0;
        if ($coupon->discount_type === 'fixed') {
            $discountAmount = min($coupon->discount_value, $subtotal);
        } elseif ($coupon->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount) {
                $discountAmount = min($discountAmount, $coupon->max_discount_amount);
            }
        }

        return [
            'valid' => true,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'discount_amount' => round($discountAmount, 2),
            'message' => 'Coupon applied successfully',
            'coupon' => $coupon,
        ];
    }

    /**
     * Update order status
     *
     * @param CleaningOrder $order
     * @param string $status
     * @param string|null $adminNotes
     * @return bool
     */
    public function updateOrderStatus(CleaningOrder $order, string $status, ?string $adminNotes = null): bool
    {
        try {
            $updates = ['status' => $status];

            // Set timestamps based on status
            switch ($status) {
                case 'paid':
                    if (!$order->paid_at) {
                        $updates['paid_at'] = now();
                    }
                    break;
                case 'confirmed':
                    if (!$order->confirmed_at) {
                        $updates['confirmed_at'] = now();
                    }
                    break;
                case 'completed':
                    if (!$order->completed_at) {
                        $updates['completed_at'] = now();
                    }
                    break;
                case 'cancelled':
                case 'refunded':
                    if (!$order->cancelled_at) {
                        $updates['cancelled_at'] = now();
                    }
                    break;
            }

            if ($adminNotes) {
                $updates['admin_notes'] = $adminNotes;
            }

            $order->update($updates);

            // Increment coupon usage if order is paid
            if ($status === 'paid' && $order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                if ($coupon) {
                    $coupon->increment('uses');
                }
            }

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $status,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'order_id' => $order->id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Validate order data
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateOrderData(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'formatted_address' => 'nullable|string|max:500',
            'place_id' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'suburb' => 'nullable|string|max:150',
            'state' => 'nullable|string|max:50',
            'unit_apt' => 'nullable|string|max:100',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string',
            'date_flexible' => 'nullable|boolean',
            'time_flexible' => 'nullable|boolean',
            'parking' => 'nullable|string|max:255',
            'property_access' => 'nullable|string|max:255',
            'access_notes' => 'nullable|string|max:1000',
            'square_footage_range' => 'nullable|string',
            'num_bathrooms' => 'required|integer|min:0',
            'num_bedrooms' => 'required|integer|min:0',
            'num_kitchens' => 'required|integer|min:0',
            'other_rooms' => 'nullable|string|max:255',
            'num_other_rooms' => 'nullable|integer|min:0',
            'other_rooms_desc' => 'nullable|string|max:255',
            'num_cleaners' => 'required|integer|min:1',
            'num_hours' => 'required|integer|min:1',
            'service_type' => 'nullable|in:normal,deep',
            'rooms_price' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'service_type_price' => 'nullable|numeric|min:0',
            'extras_total' => 'nullable|numeric|min:0',
            'extras' => 'nullable|array',
            'coupon_code' => 'nullable|string',
        ]);
    }
}
