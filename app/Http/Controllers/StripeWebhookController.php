<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle incoming Stripe webhooks
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // Construct and verify the event
        $event = $this->stripeService->constructWebhookEvent($payload, $signature);

        if (!$event) {
            Log::warning('Stripe webhook signature verification failed');
            return response('Webhook signature verification failed', 400);
        }

        Log::info('Stripe webhook received', [
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);

        // Handle the event based on type
        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->stripeService->handleCheckoutSessionCompleted($event);
                    break;

                case 'payment_intent.succeeded':
                    $this->stripeService->handlePaymentIntentSucceeded($event);
                    break;

                case 'payment_intent.payment_failed':
                    $this->stripeService->handlePaymentIntentFailed($event);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event type', [
                        'event_type' => $event->type,
                    ]);
            }

            return response('Webhook handled', 200);

        } catch (\Exception $e) {
            Log::error('Error handling Stripe webhook', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response('Webhook handler failed', 500);
        }
    }
}
