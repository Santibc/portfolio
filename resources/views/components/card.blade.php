@props([
    'padding' => 'p-6',
    'hover' => false,
    'clip' => false,   // si necesitas clip de border-radius para imagenes/headers, pasa clip=true
])

@php
    $base = 'surface-card';
    if ($clip) $base .= ' overflow-hidden';
    $classes = $base . ' ' . ($hover ? 'hover-lift cursor-pointer ' : '') . $padding;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="-mx-6 -mt-6 px-6 py-4 border-b border-cream-200 dark:border-cream-800 mb-5">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="-mx-6 -mb-6 px-6 py-4 border-t border-cream-200 dark:border-cream-800 mt-5 bg-cream-50 dark:bg-cream-900/40">
            {{ $footer }}
        </div>
    @endisset
</div>
