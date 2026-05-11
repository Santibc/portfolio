@props([
    'align' => 'right',  // left | right
])

@php
    $alignClass = $align === 'left' ? 'start-0' : 'end-0';
    $id = 'dd-' . uniqid();
@endphp

<div {{ $attributes->merge(['class' => 'hs-dropdown [--auto-close:inside] relative inline-flex']) }}>
    <button id="{{ $id }}" type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-1.5 text-sm font-medium" aria-haspopup="menu" aria-expanded="false">
        {{ $trigger ?? 'Open' }}
    </button>

    <div class="hs-dropdown-menu transition-[opacity,margin] duration-200 hs-dropdown-open:opacity-100 opacity-0 hidden min-w-56 z-50 mt-2 surface-elevated p-2 absolute top-full {{ $alignClass }}"
         role="menu" aria-orientation="vertical" aria-labelledby="{{ $id }}">
        {{ $slot }}
    </div>
</div>
