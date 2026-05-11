@props(['size' => 'md', 'label' => null])

@php
    $sizes = ['xs' => 'w-3.5 h-3.5', 'sm' => 'w-4 h-4', 'md' => 'w-6 h-6', 'lg' => 'w-8 h-8', 'xl' => 'w-12 h-12'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg class="animate-spin {{ $sizeClass }} text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    @if ($label)<span class="text-sm text-cream-700 dark:text-cream-300">{{ $label }}</span>@endif
</div>
