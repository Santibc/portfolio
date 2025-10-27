<?php

namespace App\Http\Controllers;

use App\Models\CleaningOrder;
use App\Services\CleaningOrderService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CleaningOrderController extends Controller
{
    protected $orderService;
    protected $stripeService;

    public function __construct(CleaningOrderService $orderService, StripeService $stripeService)
    {
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
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
