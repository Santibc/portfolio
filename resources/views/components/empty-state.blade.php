@props([
    'title' => 'Sin datos',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-6']) }}>
    <div class="mb-5 inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-primary-50 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30">
        @if ($icon)
            <x-icon :name="$icon" class="w-10 h-10 text-primary-600 dark:text-primary-300" />
        @else
            {{-- Default: triángulo tipo nacho (marca DorilokosMix) --}}
            <svg class="w-12 h-12 text-primary-600 dark:text-primary-300" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 10 L58 30 L18 56 Z" />
                <path d="M17 19 L46 31 L22 46 Z" class="text-accent-500" stroke="currentColor" />
                <circle cx="27" cy="31" r="1.5" fill="currentColor" />
                <circle cx="33" cy="36" r="1.5" fill="currentColor" />
            </svg>
        @endif
    </div>
    <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50 mb-1">{{ $title }}</h3>
    @if ($description)
        <p class="text-sm text-cream-600 dark:text-cream-400 max-w-md">{{ $description }}</p>
    @endif
    @isset($actions)
        <div class="mt-5 flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
