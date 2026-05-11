@props([
    'id',
    'title' => null,
    'size' => 'md',  // sm | md | lg | xl
])

@php
    $sizes = [
        'sm' => 'sm:max-w-md',
        'md' => 'sm:max-w-lg',
        'lg' => 'sm:max-w-2xl',
        'xl' => 'sm:max-w-4xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}"
     class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none"
     role="dialog" tabindex="-1" aria-labelledby="{{ $id }}-label">
    <div class="hs-overlay-animation-target hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all ease-out {{ $sizeClass }} sm:w-full m-3 sm:mx-auto pointer-events-auto sm:my-7">
        <div class="bg-white border border-cream-200 shadow-soft-lg rounded-2xl pointer-events-auto dark:bg-cream-900 dark:border-cream-800 flex flex-col max-h-[90vh]">

            @if ($title || isset($header))
                <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200 dark:border-cream-800 shrink-0">
                    <h3 id="{{ $id }}-label" class="text-lg font-semibold text-cream-900 dark:text-cream-50">
                        {{ $header ?? $title }}
                    </h3>
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center rounded-full text-cream-600 hover:bg-cream-100 dark:text-cream-300 dark:hover:bg-cream-800 transition-colors"
                        aria-label="Cerrar"
                        data-hs-overlay="#{{ $id }}">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <div class="px-6 py-5 overflow-y-auto">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-cream-200 dark:border-cream-800 shrink-0">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
