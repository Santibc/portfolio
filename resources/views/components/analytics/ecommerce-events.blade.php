{{--
    E-commerce Events Component

    Uso:
    <x-analytics.ecommerce-events
        event="view_item"
        :product="$producto"
    />

    <x-analytics.ecommerce-events
        event="add_to_cart"
        :items="$items"
        :value="$total"
    />

    <x-analytics.ecommerce-events
        event="purchase"
        :transaction-id="$compra->id"
        :items="$items"
        :value="$total"
        :shipping="$costoEnvio"
    />
--}}

@props([
    'event' => null,
    'product' => null,
    'items' => [],
    'value' => 0,
    'currency' => 'COP',
    'transactionId' => null,
    'shipping' => 0,
    'tax' => 0,
    'coupon' => null,
    'searchTerm' => null,
    'listName' => null,
])

@php
    $analyticsConfig = \App\Services\AnalyticsService::getConfig();
    $enabled = ($analyticsConfig['enabled'] && app()->environment('production')) || config('analytics.debug', false);

    // Formatear producto individual
    $ga4Item = null;
    $fbContent = null;

    if ($product) {
        $ga4Item = [
            'item_id' => $product->referencia ?? $product->id,
            'item_name' => $product->nombre,
            'item_category' => $product->categoria->nombre ?? 'Sin categoría',
            'item_brand' => 'Flores y Algo Más',
            'price' => (float) ($product->precio_actual ?? $product->precio ?? 0),
            'quantity' => 1,
        ];

        $fbContent = [
            'id' => $product->referencia ?? $product->id,
            'name' => $product->nombre,
            'category' => $product->categoria->nombre ?? 'Sin categoría',
        ];
    }

    // Formatear lista de items
    $ga4Items = [];
    $fbContentIds = [];
    $fbContents = [];

    if (is_array($items) && count($items) > 0) {
        foreach ($items as $index => $item) {
            $ga4Items[] = [
                'item_id' => $item['referencia'] ?? $item['producto_id'] ?? $item['id'] ?? '',
                'item_name' => $item['nombre'] ?? '',
                'item_category' => $item['categoria'] ?? 'General',
                'item_brand' => 'Flores y Algo Más',
                'price' => (float) ($item['precio'] ?? 0),
                'quantity' => (int) ($item['cantidad'] ?? 1),
                'index' => $index,
            ];

            $fbContentIds[] = $item['referencia'] ?? $item['producto_id'] ?? $item['id'] ?? '';
            $fbContents[] = [
                'id' => $item['referencia'] ?? $item['producto_id'] ?? $item['id'] ?? '',
                'quantity' => (int) ($item['cantidad'] ?? 1),
                'item_price' => (float) ($item['precio'] ?? 0),
            ];
        }
    }
@endphp

@if($enabled && $event)
<script>
document.addEventListener('DOMContentLoaded', function() {
    @switch($event)
        @case('view_item')
            @if($ga4Item)
            // GA4: view_item
            if (typeof gtag === 'function') {
                gtag('event', 'view_item', {
                    currency: '{{ $currency }}',
                    value: {{ (float) ($product->precio_actual ?? $product->precio ?? 0) }},
                    items: [@json($ga4Item)]
                });
            }

            // FB Pixel: ViewContent
            if (typeof fbq === 'function') {
                fbq('track', 'ViewContent', {
                    content_type: 'product',
                    content_ids: ['{{ $product->referencia ?? $product->id }}'],
                    content_name: '{{ addslashes($product->nombre) }}',
                    content_category: '{{ addslashes($product->categoria->nombre ?? "Sin categoría") }}',
                    value: {{ (float) ($product->precio_actual ?? $product->precio ?? 0) }},
                    currency: '{{ $currency }}'
                });
            }
            @endif
            @break

        @case('view_item_list')
            @if(count($ga4Items) > 0)
            // GA4: view_item_list
            if (typeof gtag === 'function') {
                gtag('event', 'view_item_list', {
                    item_list_name: '{{ $listName ?? "Productos" }}',
                    items: @json($ga4Items)
                });
            }

            // FB Pixel: ViewCategory
            if (typeof fbq === 'function') {
                fbq('track', 'ViewContent', {
                    content_type: 'product_group',
                    content_ids: @json($fbContentIds),
                    content_name: '{{ $listName ?? "Productos" }}'
                });
            }
            @endif
            @break

        @case('add_to_cart')
            @if($ga4Item || count($ga4Items) > 0)
            // GA4: add_to_cart
            if (typeof gtag === 'function') {
                gtag('event', 'add_to_cart', {
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    items: @json($ga4Item ? [$ga4Item] : $ga4Items)
                });
            }

            // FB Pixel: AddToCart
            if (typeof fbq === 'function') {
                fbq('track', 'AddToCart', {
                    content_type: 'product',
                    content_ids: @json($ga4Item ? [$product->referencia ?? $product->id] : $fbContentIds),
                    value: {{ (float) $value }},
                    currency: '{{ $currency }}'
                });
            }
            @endif
            @break

        @case('remove_from_cart')
            @if($ga4Item || count($ga4Items) > 0)
            // GA4: remove_from_cart
            if (typeof gtag === 'function') {
                gtag('event', 'remove_from_cart', {
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    items: @json($ga4Item ? [$ga4Item] : $ga4Items)
                });
            }
            @endif
            @break

        @case('begin_checkout')
            @if(count($ga4Items) > 0)
            // GA4: begin_checkout
            if (typeof gtag === 'function') {
                gtag('event', 'begin_checkout', {
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    @if($coupon)coupon: '{{ $coupon }}',@endif
                    items: @json($ga4Items)
                });
            }

            // FB Pixel: InitiateCheckout
            if (typeof fbq === 'function') {
                fbq('track', 'InitiateCheckout', {
                    content_type: 'product',
                    content_ids: @json($fbContentIds),
                    contents: @json($fbContents),
                    num_items: {{ count($ga4Items) }},
                    value: {{ (float) $value }},
                    currency: '{{ $currency }}'
                });
            }
            @endif
            @break

        @case('add_shipping_info')
            @if(count($ga4Items) > 0)
            // GA4: add_shipping_info
            if (typeof gtag === 'function') {
                gtag('event', 'add_shipping_info', {
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    @if($coupon)coupon: '{{ $coupon }}',@endif
                    items: @json($ga4Items)
                });
            }
            @endif
            @break

        @case('add_payment_info')
            @if(count($ga4Items) > 0)
            // GA4: add_payment_info
            if (typeof gtag === 'function') {
                gtag('event', 'add_payment_info', {
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    @if($coupon)coupon: '{{ $coupon }}',@endif
                    items: @json($ga4Items)
                });
            }

            // FB Pixel: AddPaymentInfo
            if (typeof fbq === 'function') {
                fbq('track', 'AddPaymentInfo', {
                    content_type: 'product',
                    content_ids: @json($fbContentIds),
                    value: {{ (float) $value }},
                    currency: '{{ $currency }}'
                });
            }
            @endif
            @break

        @case('purchase')
            @if($transactionId && count($ga4Items) > 0)
            // GA4: purchase
            if (typeof gtag === 'function') {
                gtag('event', 'purchase', {
                    transaction_id: '{{ $transactionId }}',
                    currency: '{{ $currency }}',
                    value: {{ (float) $value }},
                    tax: {{ (float) $tax }},
                    shipping: {{ (float) $shipping }},
                    @if($coupon)coupon: '{{ $coupon }}',@endif
                    items: @json($ga4Items)
                });
            }

            // FB Pixel: Purchase
            if (typeof fbq === 'function') {
                fbq('track', 'Purchase', {
                    content_type: 'product',
                    content_ids: @json($fbContentIds),
                    contents: @json($fbContents),
                    num_items: {{ count($ga4Items) }},
                    value: {{ (float) $value }},
                    currency: '{{ $currency }}'
                });
            }
            @endif
            @break

        @case('search')
            @if($searchTerm)
            // GA4: search
            if (typeof gtag === 'function') {
                gtag('event', 'search', {
                    search_term: '{{ addslashes($searchTerm) }}'
                });
            }

            // FB Pixel: Search
            if (typeof fbq === 'function') {
                fbq('track', 'Search', {
                    search_string: '{{ addslashes($searchTerm) }}'
                });
            }
            @endif
            @break

    @endswitch
});
</script>
@endif
