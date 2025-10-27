<?php

namespace App\Services;

use App\Models\CleaningOrder;
use App\Models\CleaningOrderTransaction;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;

/**
 * StripeService
 *
 * Service class for handling Stripe Checkout Sessions and webhooks
 * Following SOLID principles and based on official Stripe PHP documentation
 */
class StripeService
{
    protected $secretKey;
    protected $publicKey;
    protected $webhookSecret;
    protected $currency;

    public function __construct()
    {
        $this->secretKey = config('stripe.secret_key');
        $this->publicKey = config('stripe.public_key');
        $this->webhookSecret = config('stripe.webhook_secret');
        $this->currency = config('stripe.currency', 'usd');

        // Set Stripe API key
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a Checkout Session for a cleaning order
     *
     * @param CleaningOrder $order
     * @param CleaningOrderTransaction $transaction
     * @return array
     */
    public function createCheckoutSession(CleaningOrder $order, CleaningOrderTransaction $transaction): array
    {
        try {
            // Prepare line items based on order details
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => strtolower($order->currency),
                        'unit_amount' => (int)($order->total * 100), // Stripe uses cents
                        'product_data' => [
                            'name' => 'Cleaning Service - ' . $this->getServiceTypeName($order->service_type),
                            'description' => $this->buildServiceDescription($order),
                            'metadata' => [
                                'order_number' => $order->order_number,
                                'service_type' => $order->service_type,
                                'square_footage' => $order->square_footage_range,
                            ],
                        ],
                    ],
                    'quantity' => 1,
                ],
            ];

            // Create checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('cleaning-order.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cleaning-order.cancel') . '?order_number=' . $order->order_number,
                'customer_email' => $order->email,
                'client_reference_id' => $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_id' => $transaction->id,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ],
            ]);

            // Update transaction with Stripe session data
            $transaction->update([
                'stripe_session_id' => $session->id,
                'stripe_session_data' => $session->toArray(),
                'session_created_at' => now(),
            ]);

            Log::info('Stripe Checkout Session created successfully', [
                'order_id' => $order->id,
                'session_id' => $session->id,
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'session_url' => $session->url,
                'session' => $session,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error creating checkout session', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ];
        } catch (\Exception $e) {
            Log::error('Exception creating Stripe checkout session', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while creating the payment session',
            ];
        }
    }

    /**
     * Retrieve a checkout session by ID
     *
     * @param string $sessionId
     * @return Session|null
     */
    public function retrieveSession(string $sessionId): ?Session
    {
        try {
            return Session::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            Log::error('Error retrieving Stripe session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Retrieve a payment intent by ID
     *
     * @param string $paymentIntentId
     * @return PaymentIntent|null
     */
    public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Error retrieving Stripe Payment Intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Construct and verify webhook event
     *
     * @param string $payload
     * @param string $signature
     * @return \Stripe\Event|null
     */
    public function constructWebhookEvent(string $payload, string $signature)
    {
        try {
            return Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error constructing Stripe webhook event', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Handle completed checkout session
     *
     * @param \Stripe\Event $event
     * @return bool
     */
    public function handleCheckoutSessionCompleted($event): bool
    {
        try {
            $session = $event->data->object;

            // Find transaction by session ID
            $transaction = CleaningOrderTransaction::where('stripe_session_id', $session->id)->first();

            if (!$transaction) {
                Log::warning('Transaction not found for checkout session', [
                    'session_id' => $session->id,
                ]);
                return false;
            }

            // Check if event already processed (idempotency)
            if ($transaction->hasProcessedWebhookEvent($event->id)) {
                Log::info('Webhook event already processed', ['event_id' => $event->id]);
                return true;
            }

            $order = $transaction->cleaningOrder;

            // Update transaction
            $transaction->update([
                'stripe_payment_intent_id' => $session->payment_intent,
                'status' => 'processing',
            ]);

            // Add webhook event
            $transaction->addWebhookEvent($event->id, $event->type, [
                'session_status' => $session->payment_status,
            ]);

            Log::info('Checkout session completed', [
                'order_id' => $order->id,
                'session_id' => $session->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling checkout session completed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment intent
     *
     * @param \Stripe\Event $event
     * @return bool
     */
    public function handlePaymentIntentSucceeded($event): bool
    {
        try {
            $paymentIntent = $event->data->object;

            // Find transaction by payment intent ID
            $transaction = CleaningOrderTransaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();

            if (!$transaction) {
                Log::warning('Transaction not found for payment intent', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                return false;
            }

            // Check if event already processed
            if ($transaction->hasProcessedWebhookEvent($event->id)) {
                Log::info('Webhook event already processed', ['event_id' => $event->id]);
                return true;
            }

            $order = $transaction->cleaningOrder;

            // Extract payment method details
            $paymentMethod = $paymentIntent->charges->data[0]->payment_method_details ?? null;

            // Update transaction
            $transaction->update([
                'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                'status' => 'succeeded',
                'payment_method_type' => $paymentMethod->type ?? null,
                'payment_method_brand' => $paymentMethod->card->brand ?? null,
                'payment_method_last4' => $paymentMethod->card->last4 ?? null,
                'stripe_payment_intent_data' => $paymentIntent->toArray(),
                'payment_succeeded_at' => now(),
            ]);

            // Update order
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Add webhook event
            $transaction->addWebhookEvent($event->id, $event->type);

            Log::info('Payment succeeded', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling payment intent succeeded', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle failed payment intent
     *
     * @param \Stripe\Event $event
     * @return bool
     */
    public function handlePaymentIntentFailed($event): bool
    {
        try {
            $paymentIntent = $event->data->object;

            $transaction = CleaningOrderTransaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();

            if (!$transaction) {
                return false;
            }

            // Check if event already processed
            if ($transaction->hasProcessedWebhookEvent($event->id)) {
                return true;
            }

            $order = $transaction->cleaningOrder;

            // Update transaction
            $transaction->update([
                'status' => 'failed',
                'error_code' => $paymentIntent->last_payment_error->code ?? null,
                'error_message' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
                'payment_failed_at' => now(),
            ]);

            // Update order
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Add webhook event
            $transaction->addWebhookEvent($event->id, $event->type, [
                'error' => $paymentIntent->last_payment_error,
            ]);

            Log::warning('Payment failed', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling payment intent failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Helper: Get readable service type name
     */
    private function getServiceTypeName(string $serviceType): string
    {
        return [
            'initial' => 'Initial Clean',
            'weekly' => 'Weekly Service',
            'biweekly' => 'Bi-Weekly Service',
            'monthly' => 'Monthly Service',
            'deep_clean' => 'Deep Clean',
            'move_out' => 'Move Out Clean',
        ][$serviceType] ?? $serviceType;
    }

    /**
     * Helper: Build service description
     */
    private function buildServiceDescription(CleaningOrder $order): string
    {
        $description = "Service: {$this->getServiceTypeName($order->service_type)}\n";
        $description .= "Square Footage: {$order->square_footage_range}\n";
        $description .= "Date: {$order->preferred_date->format('M d, Y')} at {$order->preferred_time}\n";
        $description .= "Location: {$order->street_address}";

        if ($order->extras && count($order->extras) > 0) {
            $description .= "\nExtras included";
        }

        return $description;
    }
}
