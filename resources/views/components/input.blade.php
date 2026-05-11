@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'icon' => null,
    'required' => false,
])

@php
    $id = $id ?? $name;
    $err = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">
            {{ $label }}
            @if ($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500">
                <x-icon :name="$icon" class="w-4 h-4" />
            </span>
        @endif

        <input
            type="{{ $type }}"
            @if ($name) name="{{ $name }}" @endif
            id="{{ $id }}"
            @if ($value !== null) value="{{ $value }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border-cream-300 bg-white px-3 py-2.5 text-sm text-cream-900 shadow-none placeholder:text-cream-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 dark:placeholder:text-cream-500 transition-all'
                    . ($icon ? ' pl-10' : '')
                    . ($err ? ' border-red-400 focus:border-red-500 focus:ring-red-500/30' : ''),
            ]) }}
        />
    </div>

    @if ($err)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $err }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">{{ $hint }}</p>
    @endif
</div>
