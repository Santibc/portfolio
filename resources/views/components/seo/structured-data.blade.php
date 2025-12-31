{{--
    Structured Data (JSON-LD) Component

    Uso:
    @include('components.seo.structured-data', [
        'type' => 'LocalBusiness|Product|BreadcrumbList',
        'empresa' => $empresa,
        'producto' => $producto, // opcional
        'breadcrumbs' => [...] // opcional
    ])
--}}

@php
    $type = $type ?? 'LocalBusiness';
@endphp

@if($type === 'LocalBusiness' || $type === 'Organization')
{{-- Schema.org LocalBusiness --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Florist",
    "name": "{{ $empresa->nombre }}",
    "description": "{{ $empresa->descripcion ?? 'Floristería y tienda de arreglos florales' }}",
    "url": "{{ url('/') }}",
    "logo": "{{ $empresa->logo_url ?? asset('images/logo.png') }}",
    "image": "{{ $empresa->logo_url ?? asset('images/logo.png') }}",
    @if($empresa->telefono)
    "telephone": "{{ $empresa->telefono }}",
    @endif
    @if($empresa->email)
    "email": "{{ $empresa->email }}",
    @endif
    @if($empresa->direccion)
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $empresa->direccion }}",
        @if($empresa->ciudad)
        "addressLocality": "{{ $empresa->ciudad->nombre }}",
        "addressRegion": "{{ $empresa->ciudad->departamento->nombre ?? '' }}",
        @endif
        "addressCountry": "CO"
    },
    @endif
    "priceRange": "$$",
    @if($empresa->horario_atencion)
    "openingHoursSpecification": [
        @php
            $dias = [
                'lunes' => 'Monday',
                'martes' => 'Tuesday',
                'miercoles' => 'Wednesday',
                'jueves' => 'Thursday',
                'viernes' => 'Friday',
                'sabado' => 'Saturday',
                'domingo' => 'Sunday'
            ];
            $horariosJson = [];
            foreach($dias as $key => $dayOfWeek) {
                if(isset($empresa->horario_atencion[$key]) && !($empresa->horario_atencion[$key]['cerrado'] ?? false)) {
                    $horariosJson[] = [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => $dayOfWeek,
                        'opens' => $empresa->horario_atencion[$key]['apertura'] ?? '09:00',
                        'closes' => $empresa->horario_atencion[$key]['cierre'] ?? '18:00'
                    ];
                }
            }
        @endphp
        {!! json_encode($horariosJson, JSON_UNESCAPED_SLASHES) !!}
    ],
    @endif
    "sameAs": [
        @if($empresa->facebook_url)"{{ $empresa->facebook_url }}"@endif
        @if($empresa->facebook_url && $empresa->instagram_url),@endif
        @if($empresa->instagram_url)"{{ $empresa->instagram_url }}"@endif
    ]
}
</script>
@endif

@if($type === 'Product' && isset($producto))
{{-- Schema.org Product --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $producto->nombre }}",
    "description": "{{ Str::limit(strip_tags($producto->descripcion ?? ''), 500) }}",
    "image": [
        @foreach($producto->imagenes ?? [] as $index => $imagen)
        "{{ asset('storage/' . $imagen->url) }}"{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    "sku": "{{ $producto->referencia ?? $producto->id }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $empresa->nombre }}"
    },
    "category": "{{ $producto->categoria->nombre ?? 'Flores' }}",
    "offers": {
        "@type": "Offer",
        "url": "{{ route('tienda.producto', $producto->id) }}",
        "priceCurrency": "COP",
        "price": "{{ $producto->precio_actual->precio ?? 0 }}",
        "availability": "{{ ($producto->stockPrincipal && $producto->stockPrincipal->cantidad > 0) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "seller": {
            "@type": "Organization",
            "name": "{{ $empresa->nombre }}"
        }
    }
    @if(isset($promedioCalificacion) && $promedioCalificacion > 0)
    ,"aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ number_format($promedioCalificacion, 1) }}",
        "reviewCount": "{{ $totalCalificaciones ?? 0 }}",
        "bestRating": "5",
        "worstRating": "1"
    }
    @endif
}
</script>
@endif

@if($type === 'BreadcrumbList' && isset($breadcrumbs))
{{-- Schema.org BreadcrumbList --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $index => $crumb)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ $crumb['name'] }}",
            "item": "{{ $crumb['url'] }}"
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif

@if($type === 'WebSite')
{{-- Schema.org WebSite with SearchAction --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ $empresa->nombre }}",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "{{ route('tienda.categorias') }}?buscar={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>
@endif
