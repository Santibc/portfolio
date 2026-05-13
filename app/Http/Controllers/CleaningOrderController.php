<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequestAdmin;
use App\Mail\BookingRequestCustomer;
use App\Models\CleaningOrder;
use App\Services\CleaningOrderService;
use App\Services\ServiceAvailabilityService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CleaningOrderController extends Controller
{
    protected $orderService;
    protected $stripeService;
    protected $availabilityService;

    public function __construct(
        CleaningOrderService $orderService,
        StripeService $stripeService,
        ServiceAvailabilityService $availabilityService
    ) {
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
        $this->availabilityService = $availabilityService;
    }

    /**
     * Submit a booking request without payment.
     * Creates the order, dispatches notifications and returns redirect URL.
     */
    public function submitBooking(Request $request)
    {
        try {
            $data = $request->all();

            // Re-validate service availability server-side (defense in depth)
            $availability = $this->availabilityService->check(
                isset($data['latitude']) ? (float) $data['latitude'] : null,
                isset($data['longitude']) ? (float) $data['longitude'] : null,
                $data['postcode'] ?? null,
                $data['suburb'] ?? null
            );

            if (!$availability['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $availability['reason'] ?? "Sorry, we don't service this area.",
                ], 422);
            }

            $result = $this->orderService->createOrder($data);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to create booking',
                ], 400);
            }

            $order = $result['order'];

            // Send notification emails (best effort — don't fail the request if mail breaks)
            try {
                Mail::to($order->email)->send(new BookingRequestCustomer($order));
            } catch (\Throwable $e) {
                Log::warning('Customer booking email failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }

            try {
                $layoutConfig = \App\Models\LandingLayoutConfig::first();
                $adminEmail = $layoutConfig->admin_notification_email ?? config('mail.from.address');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new BookingRequestAdmin($order));
                }
            } catch (\Throwable $e) {
                Log::warning('Admin booking email failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'redirect_url' => route('cleaning-order.booking-confirmed', $order->order_number),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Booking error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Booking confirmation page (no payment flow).
     */
    public function bookingConfirmed(string $orderNumber)
    {
        $order = CleaningOrder::where('order_number', $orderNumber)->firstOrFail();

        $layoutConfig = \App\Models\LandingLayoutConfig::first();
        $seo = null;

        return view('cleaning_orders.booking_confirmed', compact('order', 'layoutConfig', 'seo'));
    }

    /**
     * Create order and redirect to Stripe Checkout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        try {
            // Create the order
            $result = $this->orderService->createOrder($request->all());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to create order',
                ], 400);
            }

            $order = $result['order'];
            $transaction = $result['transaction'];

            // Create Stripe Checkout Session
            $sessionResult = $this->stripeService->createCheckoutSession($order, $transaction);

            if (!$sessionResult['success']) {
                Log::error('Failed to create Stripe session', [
                    'order_id' => $order->id,
                    'error' => $sessionResult['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment session. Please try again.',
                ], 500);
            }

            // Return the session URL for redirect
            return response()->json([
                'success' => true,
                'session_id' => $sessionResult['session_id'],
                'session_url' => $sessionResult['session_url'],
                'order_number' => $order->order_number,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Success page after payment
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('welcome')->with('error', 'Invalid session');
        }

        // Retrieve the Stripe session to verify payment
        $session = $this->stripeService->retrieveSession($sessionId);

        if (!$session) {
            return redirect()->route('welcome')->with('error', 'Payment session not found');
        }

        // Find the order
        $order = CleaningOrder::where('order_number', $session->client_reference_id)->first();

        if (!$order) {
            return redirect()->route('welcome')->with('error', 'Order not found');
        }

        // Check payment status
        if ($session->payment_status !== 'paid') {
            return redirect()->route('cleaning-order.cancel')
                ->with('warning', 'Payment was not completed');
        }

        // Update order and transaction status if payment is successful
        // This handles cases where webhooks are not configured (local development)
        if ($session->payment_status === 'paid' && $order->status === 'pending') {
            $transaction = $order->transaction;

            if ($transaction && $transaction->status !== 'succeeded') {
                // Update transaction with payment details
                $transaction->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'stripe_charge_id' => $session->latest_charge ?? null,
                    'payment_method' => $session->payment_method_types[0] ?? 'card',
                    'status' => 'succeeded',
                    'paid_at' => now(),
                ]);

                // Update order status
                $this->orderService->updateOrderStatus($order, 'paid');

                Log::info('Order status updated via success page', [
                    'order_id' => $order->id,
                    'session_id' => $sessionId,
                ]);
            }
        }

        // Refresh order to get updated status
        $order->refresh();

        // Get layout config and SEO
        $layoutConfig = \App\Models\LandingLayoutConfig::first();
        $seo = null; // No specific SEO page for order success

        return view('cleaning_orders.success', compact('order', 'session', 'layoutConfig', 'seo'));
    }

    /**
     * Cancel page if payment is cancelled
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function cancel(Request $request)
    {
        $orderNumber = $request->query('order_number');

        $order = null;
        if ($orderNumber) {
            $order = CleaningOrder::where('order_number', $orderNumber)->first();
        }

        // Get layout config and SEO
        $layoutConfig = \App\Models\LandingLayoutConfig::first();
        $seo = null; // No specific SEO page for order cancellation

        return view('cleaning_orders.cancel', compact('order', 'layoutConfig', 'seo'));
    }
}
