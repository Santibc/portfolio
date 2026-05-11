@props([
    'src' => null,
    'name' => '',
    'size' => 'md',   // xs | sm | md | lg | xl
    'ring' => false,
])

@php
    $sizes = [
        'xs' => 'w-7 h-7 text-[10px]',
        'sm' => 'w-9 h-9 text-xs',
        'md' => 'w-11 h-11 text-sm',
        'lg' => 'w-14 h-14 text-base',
        'xl' => 'w-20 h-20 text-2xl',
    ];
    $size_class = $sizes[$size] ?? $sizes['md'];

    $initials = '';
    if ($name) {
        foreach (preg_split('/\s+/', trim($name)) as $w) {
            $initials .= strtoupper(mb_substr($w, 0, 1));
            if (mb_strlen($initials) >= 2) break;
        }
    }

    $base = 'inline-flex items-center justify-center rounded-full font-semibold shrink-0';
    $ringClass = $ring ? 'ring-2 ring-primary-500 ring-offset-2 ring-offset-white dark:ring-offset-surface-dark' : '';
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" {{ $attributes->merge(['class' => $base . ' ' . $size_class . ' ' . $ringClass . ' object-cover']) }} />
@else
    <span {{ $attributes->merge(['class' => $base . ' ' . $size_class . ' ' . $ringClass . ' bg-gradient-to-br from-primary-500 to-primary-700 text-white']) }}>
        {{ $initials ?: '?' }}
    </span>
@endif
