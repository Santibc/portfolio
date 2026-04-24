@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true,
])

@php
    $config = [
        'success' => ['color' => 'green', 'icon' => 'check-circle-fill'],
        'error' => ['color' => 'red', 'icon' => 'x-circle-fill'],
        'warning' => ['color' => 'amber', 'icon' => 'exclamation-triangle-fill'],
        'info' => ['color' => 'sky', 'icon' => 'info-circle-fill'],
    ][$type] ?? ['color' => 'sky', 'icon' => 'info-circle-fill'];

    $classes = match ($config['color']) {
        'green' => 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-300',
        'red' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300',
        default => 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-300',
    };
@endphp

<div
    role="alert"
    @if ($dismissible) x-data="{ show: true }" x-show="show" @endif
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl border p-4 text-sm {$classes}"]) }}
>
    <i class="bi bi-{{ $config['icon'] }} mt-0.5 text-base"></i>
    <div class="flex-1">{{ $message ?: $slot }}</div>
    @if ($dismissible)
        <button type="button" @click="show = false" class="text-current/60 hover:text-current" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>
    @endif
</div>
