@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'options' => [],
    'tomselect' => false,
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

    <select
        @if ($name) name="{{ $name }}" @endif
        id="{{ $id }}"
        @if ($required) required @endif
        @if ($tomselect) data-tom-select @endif
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl border-cream-300 bg-white px-3 py-2.5 text-sm text-cream-900 shadow-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 transition-all'
                . ($err ? ' border-red-400' : ''),
        ]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if (!empty($options))
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($err)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $err }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">{{ $hint }}</p>
    @endif
</div>
