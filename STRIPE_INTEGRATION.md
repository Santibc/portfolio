# Stripe Payment Integration for Cleaning Services Calculator

## Overview

This document describes the complete Stripe payment integration implemented for the Cleaning Services Calculator module. The implementation follows official Stripe documentation, SOLID principles, and Laravel best practices.

---

## Table of Contents

1. [Architecture](#architecture)
2. [Installation & Configuration](#installation--configuration)
3. [Database Schema](#database-schema)
4. [Service Layer](#service-layer)
5. [Controllers](#controllers)
6. [Routes](#routes)
7. [Frontend Integration](#frontend-integration)
8. [Webhooks](#webhooks)
9. [Admin Panel](#admin-panel)
10. [Testing](#testing)
11. [Deployment](#deployment)

---

## Architecture

### Design Pattern: Service Layer + Repository Pattern

The implementation follows SOLID principles with clear separation of concerns:

```
┌─────────────────┐
│   Frontend      │  services_calculator.blade.php
│   (Vue/JS)      │  → Collects form data
└────────┬────────┘
         │ POST /services-calculator/checkout
         ↓
┌─────────────────┐
│  Controller     │  CleaningOrderController
│  Layer          │  → Handles HTTP requests/responses
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│  Service        │  CleaningOrderService
│  Layer          │  → Business logic & validation
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│  Payment        │  StripeService
│  Gateway        │  → Stripe API integration
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│  Models         │  CleaningOrder, CleaningOrderTransaction
│  (Eloquent)     │  → Data persistence
└─────────────────┘
```

---

## Installation & Configuration

### 1. Install Stripe PHP SDK

```bash
composer require stripe/stripe-php
```

**Current Version:** v18.0.0

### 2. Environment Configuration

Add the following to your `.env` file:

```env
# Stripe Configuration
STRIPE_PUBLIC_KEY=pk_test_51SKhRJB0xo9qAnnamaGU93qsmTEU3KJmjyTPpWSoiyjgkkJoSquZQQ4YI3RT7y7s4o9GuqzbvqXZ2R7ijQMrRpb100a58Rn8bZ
STRIPE_SECRET_KEY=sk_test_51SKhRJB0xo9qAnnal1vkjxIl3OLi9jDdg7iMYZiAPi57kdEWQN1vw5RL0dKGxYbBbllhF2BFFgSriXcW2nzgyr0g00rtifT2qE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE
STRIPE_CURRENCY=usd
```

### 3. Configuration File

Location: `config/stripe.php`

Contains Stripe API keys and currency settings.

### 4. Run Migrations

```bash
php artisan migrate --path=database/migrations/2025_10_25_191521_create_cleaning_orders_table.php
php artisan migrate --path=database/migrations/2025_10_25_191545_create_cleaning_order_transactions_table.php
```

---

## Database Schema

### Table: `cleaning_orders`

Stores complete order information including customer details, service specifications, and pricing.

**Key Fields:**
- `order_number` (unique) - Auto-generated order identifier
- Customer info: `first_name`, `last_name`, `email`, `phone`
- Location: `street_address`, `district_id`, `unit_apt`
- Service: `service_type`, `square_footage_range`, `preferred_date`, `preferred_time`
- Pricing: `base_price`, `extras_total`, `subtotal`, `discount_amount`, `total`
- `extras` (JSON) - Array of selected extra services
- `status` (enum) - Order workflow status
- Timestamps: `paid_at`, `confirmed_at`, `completed_at`, `cancelled_at`

**Status Flow:**
```
pending → processing → paid → confirmed → scheduled → in_progress → completed
                  ↓
              cancelled / refunded
```

### Table: `cleaning_order_transactions`

Tracks Stripe payment transactions with complete audit trail.

**Key Fields:**
- `stripe_session_id` - Stripe Checkout Session ID
- `stripe_payment_intent_id` - Payment Intent ID
- `stripe_charge_id` - Charge ID
- Payment method details: `payment_method_type`, `payment_method_brand`, `payment_method_last4`
- `stripe_session_data` (JSON) - Full session object for audit
- `webhook_events` (JSON) - Array of processed webhook events
- Status timestamps

---

## Service Layer

### CleaningOrderService

**Location:** `app/Services/CleaningOrderService.php`

**Responsibilities:**
- Order creation and validation
- Pricing calculation with extras and coupons
- Coupon validation and application
- Order status management

**Key Methods:**

```php
createOrder(array $data): array
calculatePricing(array $data): array
applyCoupon(string $code, float $subtotal): array
updateOrderStatus(CleaningOrder $order, string $status, ?string $notes): bool
```

### StripeService

**Location:** `app/Services/StripeService.php`

**Responsibilities:**
- Stripe Checkout Session creation
- Session retrieval and verification
- Webhook event handling
- Payment status tracking

**Key Methods:**

```php
createCheckoutSession(CleaningOrder $order, CleaningOrderTransaction $transaction): array
retrieveSession(string $sessionId): ?Session
constructWebhookEvent(string $payload, string $signature)
handleCheckoutSessionCompleted($event): bool
handlePaymentIntentSucceeded($event): bool
handlePaymentIntentFailed($event): bool
```

**Webhook Events Handled:**
- `checkout.session.completed` - Session created successfully
- `payment_intent.succeeded` - Payment successful
- `payment_intent.payment_failed` - Payment failed

---

## Controllers

### CleaningOrderController

**Location:** `app/Http/Controllers/CleaningOrderController.php`

Frontend-facing controller for customer interactions.

**Routes:**
- `POST /services-calculator/checkout` → `checkout()`
- `GET /order/success` → `success()`
- `GET /order/cancel` → `cancel()`

### Admin\CleaningOrderController

**Location:** `app/Http/Controllers/Admin/CleaningOrderController.php`

Admin panel for order management with DataTables integration.

**Routes:**
- `GET /admin/cleaning-orders` → `index()` (with AJAX DataTables)
- `GET /admin/cleaning-orders/{order}` → `show()`
- `POST /admin/cleaning-orders/{order}/update-status` → `updateStatus()`
- `DELETE /admin/cleaning-orders/{order}` → `destroy()`

### StripeWebhookController

**Location:** `app/Http/Controllers/StripeWebhookController.php`

Handles incoming Stripe webhooks with signature verification.

**Route:**
- `POST /webhook/stripe` → `handle()`

**IMPORTANT:** This route is excluded from CSRF protection in `VerifyCsrfToken` middleware.

---

## Routes

### Frontend Routes

```php
// Cleaning Orders - Frontend
Route::post('/services-calculator/checkout', [CleaningOrderController::class, 'checkout'])
    ->name('cleaning-order.checkout');
Route::get('/order/success', [CleaningOrderController::class, 'success'])
    ->name('cleaning-order.success');
Route::get('/order/cancel', [CleaningOrderController::class, 'cancel'])
    ->name('cleaning-order.cancel');
```

### Admin Routes (Protected)

```php
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('cleaning-orders', Admin\CleaningOrderController::class);
    Route::post('cleaning-orders/{order}/update-status', [Admin\CleaningOrderController::class, 'updateStatus'])
        ->name('cleaning-orders.update-status');
});
```

### Webhook Route (No CSRF)

```php
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');
```

---

## Frontend Integration

### Services Calculator Update

**File:** `resources/views/landing_page/services_calculator.blade.php`

**Changes:**
1. Button changed from "Send to WhatsApp" to "Proceed to Payment"
2. AJAX submission to `/services-calculator/checkout`
3. Automatic redirect to Stripe Checkout
4. Loading states and error handling

**JavaScript Flow:**

```javascript
// Collect form data
const orderData = {
    first_name, last_name, email, phone,
    street_address, district_id, unit_apt,
    preferred_date, preferred_time,
    service_type, square_footage_range,
    base_price, extras_total,
    coupon_code, extras: [...]
};

// Send to backend
fetch('/services-calculator/checkout', {
    method: 'POST',
    body: JSON.stringify(orderData)
})
.then(data => {
    // Redirect to Stripe
    window.location.href = data.session_url;
});
```

### Success Page

**File:** `resources/views/cleaning_orders/success.blade.php`

Displays:
- Order confirmation
- Order details (service, date, location)
- Payment amount
- Next steps
- Contact information

### Cancel Page

**File:** `resources/views/cleaning_orders/cancel.blade.php`

Displays:
- Cancellation message
- Common reasons for cancellation
- Option to try again
- Contact support

---

## Webhooks

### Configuration Steps

1. **Set up webhook endpoint in Stripe Dashboard:**
   - Go to: Developers → Webhooks
   - Add endpoint: `https://yourdomain.com/webhook/stripe`
   - Select events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`

2. **Copy Webhook Signing Secret:**
   - After creating endpoint, copy the signing secret
   - Add to `.env` as `STRIPE_WEBHOOK_SECRET`

### Security

- ✅ Signature verification using Stripe SDK
- ✅ Idempotency handling (prevents duplicate processing)
- ✅ Event logging for audit trail
- ✅ Error handling and rollback

### Event Processing

**checkout.session.completed:**
- Updates transaction status to "processing"
- Records Payment Intent ID
- Logs event

**payment_intent.succeeded:**
- Updates transaction to "succeeded"
- Extracts payment method details
- Updates order to "paid"
- Sets `paid_at` timestamp
- Increments coupon usage

**payment_intent.payment_failed:**
- Updates transaction to "failed"
- Records error details
- Cancels order
- Logs failure

---

## Admin Panel

### Orders List (DataTables)

**File:** `resources/views/admin/cleaning-orders/index.blade.php`

**Features:**
- Server-side DataTables for performance
- Real-time search and filtering
- Sortable columns
- Status badges with color coding
- Quick actions (View, Delete)
- Statistics dashboard

**Columns:**
- Order Number
- Customer (name, email, phone)
- Service Type & Square Footage
- Scheduled Date & Time
- Amount
- Payment Method
- Status
- Actions

### Order Details

**File:** `resources/views/admin/cleaning-orders/show.blade.php`

**Sections:**
1. **Customer Information** - Contact details and address
2. **Service Details** - Service type, date, extras
3. **Payment Information** - Transaction status, Stripe IDs
4. **Order Summary** - Pricing breakdown
5. **Status Management** - Update status form
6. **Timeline** - Event history

**Status Management:**
- Dropdown to change order status
- Admin notes field
- AJAX update without page reload

---

## Testing

### Manual Testing Checklist

#### 1. Frontend Flow

- [ ] Complete all 9 steps of calculator
- [ ] Verify pricing calculations
- [ ] Apply coupon code
- [ ] Click "Proceed to Payment"
- [ ] Verify redirect to Stripe
- [ ] Complete test payment (use test card: 4242 4242 4242 4242)
- [ ] Verify redirect to success page
- [ ] Check order details on success page

#### 2. Stripe Test Cards

```
Success: 4242 4242 4242 4242
Decline: 4000 0000 0000 0002
Requires Authentication: 4000 0027 6000 3184
```

**Any future date, any CVC, any ZIP**

#### 3. Webhook Testing

```bash
# Install Stripe CLI
stripe login

# Forward webhooks to local
stripe listen --forward-to http://127.0.0.1:8000/webhook/stripe

# Trigger test event
stripe trigger payment_intent.succeeded
```

#### 4. Admin Panel

- [ ] View orders list
- [ ] Search and filter orders
- [ ] View order details
- [ ] Update order status
- [ ] Add admin notes
- [ ] Delete test orders

### Database Verification

```sql
-- Check order created
SELECT * FROM cleaning_orders ORDER BY created_at DESC LIMIT 1;

-- Check transaction
SELECT * FROM cleaning_order_transactions ORDER BY created_at DESC LIMIT 1;

-- Check webhook events
SELECT webhook_events FROM cleaning_order_transactions WHERE id = ?;
```

---

## Deployment

### Pre-Deployment Checklist

1. **Environment Configuration**
   - [ ] Update `.env` with production Stripe keys
   - [ ] Set `STRIPE_WEBHOOK_SECRET`
   - [ ] Verify `APP_URL` is correct

2. **Stripe Dashboard**
   - [ ] Switch to Live mode
   - [ ] Create production webhook endpoint
   - [ ] Copy webhook secret to `.env`
   - [ ] Verify webhook is active

3. **Database**
   - [ ] Run migrations on production
   - [ ] Backup database before deployment

4. **Testing**
   - [ ] Complete a live test transaction (small amount)
   - [ ] Verify webhook is triggered
   - [ ] Check order appears in admin
   - [ ] Refund test transaction

### Post-Deployment Monitoring

**Monitor these logs:**
```bash
# Application logs
tail -f storage/logs/laravel.log | grep -i stripe

# Webhook deliveries (in Stripe Dashboard)
# Developers → Webhooks → [Your endpoint] → Attempts
```

**Key Metrics to Track:**
- Successful payments
- Failed payments
- Webhook delivery success rate
- Average checkout completion time

---

## Security Best Practices

### Implemented

- ✅ Webhook signature verification
- ✅ HTTPS required for production
- ✅ API keys in environment variables
- ✅ CSRF protection (except webhook)
- ✅ Input validation on all forms
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (Blade escaping)

### Recommendations

1. **PCI Compliance**: Stripe handles all card data (we never touch it)
2. **SSL Certificate**: Required for production
3. **Rate Limiting**: Consider adding to webhook endpoint
4. **Monitoring**: Set up alerts for failed payments
5. **Backup**: Regular database backups

---

## Troubleshooting

### Common Issues

#### 1. Webhook Not Working

**Symptoms:** Payments succeed but order status doesn't update

**Solutions:**
- Verify webhook secret in `.env`
- Check webhook is active in Stripe Dashboard
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Verify `/webhook/stripe` is excluded from CSRF

#### 2. Redirect to Stripe Fails

**Symptoms:** JavaScript error on checkout

**Solutions:**
- Check browser console for errors
- Verify CSRF token is included
- Check order is created in database
- Verify Stripe keys are correct

#### 3. Payment Succeeds but Success Page Shows Error

**Symptoms:** Payment goes through but user sees error

**Solutions:**
- Check `session_id` parameter in URL
- Verify order number matches in database
- Check Stripe API connectivity

### Debug Commands

```bash
# Check database
php artisan tinker
>>> CleaningOrder::latest()->first()
>>> CleaningOrderTransaction::latest()->first()

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Check Stripe connectivity
php artisan tinker
>>> \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
>>> \Stripe\Balance::retrieve();
```

---

## API Reference

### Stripe API Calls Made

```php
// Create Checkout Session
\Stripe\Checkout\Session::create([...]);

// Retrieve Session
\Stripe\Checkout\Session::retrieve($sessionId);

// Retrieve Payment Intent
\Stripe\PaymentIntent::retrieve($paymentIntentId);

// Verify Webhook
\Stripe\Webhook::constructEvent($payload, $signature, $secret);
```

### Official Documentation Links

- [Stripe PHP Library](https://github.com/stripe/stripe-php)
- [Checkout Quickstart](https://docs.stripe.com/checkout/quickstart)
- [Webhooks Guide](https://docs.stripe.com/webhooks)
- [Testing](https://docs.stripe.com/testing)

---

## Support

For issues or questions:

1. Check this documentation
2. Review Stripe official docs
3. Check Laravel logs
4. Check Stripe Dashboard → Logs
5. Contact development team

---

## Changelog

**Version 1.0.0** - 2025-10-25
- Initial implementation
- Stripe Checkout integration
- Webhook handling
- Admin panel
- Success/Cancel pages

---

**Last Updated:** 2025-10-25
**Stripe SDK Version:** 18.0.0
**Laravel Version:** 9.x
