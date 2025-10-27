<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | API keys for Stripe payment gateway integration.
    | Test keys start with pk_test_ and sk_test_
    | Live keys start with pk_live_ and sk_live_
    |
    */

    'public_key' => env('STRIPE_PUBLIC_KEY', ''),

    'secret_key' => env('STRIPE_SECRET_KEY', ''),

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Stripe API Version
    |--------------------------------------------------------------------------
    |
    | The Stripe API version to use. Leave null to use the default version.
    |
    */

    'api_version' => null,

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for payments. Use ISO 4217 currency codes.
    | Examples: usd, eur, gbp, aud, cad
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'usd'),

];
