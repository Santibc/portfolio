@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
    'description' => null,
])

@php
    $id = $id ?? $name;
@endphp

<label for="{{ $id }}" class="flex items-start gap-2.5 cursor-pointer select-none">
    <input
        type="checkbox"
        @if ($name) name="{{ $name }}" @endif
        id="{{ $id }}"
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->merge([
            'class' => 'mt-0.5 h-4 w-4 rounded border-cream-300 text-primary-500 focus:ring-2 focus:ring-primary-500/40 focus:ring-offset-0 dark:border-cream-600 dark:bg-cream-900 transition-colors',
        ]) }}
    />
    @if ($label || $description)
        <span class="text-sm">
            @if ($label)
                <span class="block font-medium text-cream-800 dark:text-cream-200">{{ $label }}</span>
            @endif
            @if ($description)
                <span class="block text-xs text-cream-600 dark:text-cream-400">{{ $description }}</span>
            @endif
        </span>
    @endif
</label>
