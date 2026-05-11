@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'checked' => false,
    'description' => null,
])

@php
    $id = $id ?? $name;
@endphp

<label for="{{ $id }}" class="flex items-start gap-3 cursor-pointer select-none">
    <span class="relative inline-flex items-center" x-data="{ on: @js((bool) $checked) }">
        <input
            type="checkbox"
            @if ($name) name="{{ $name }}" @endif
            id="{{ $id }}"
            value="1"
            x-model="on"
            class="sr-only peer"
            {{ $attributes }}
        />
        <span class="w-11 h-6 rounded-full bg-cream-300 peer-checked:bg-primary-500 transition-colors duration-200 dark:bg-cream-700"></span>
        <span class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
    </span>
    @if ($label || $description)
        <span class="text-sm">
            @if ($label)<span class="block font-medium text-cream-800 dark:text-cream-200">{{ $label }}</span>@endif
            @if ($description)<span class="block text-xs text-cream-600 dark:text-cream-400">{{ $description }}</span>@endif
        </span>
    @endif
</label>
