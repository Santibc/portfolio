@props([
    'title' => null,
    'description' => null,
    'id' => null,
])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->merge(['class' => 'space-y-4 mb-10 scroll-mt-24']) }}>
    @if ($title || $description)
        <header class="flex items-end justify-between gap-3 border-b border-cream-200 dark:border-cream-800 pb-3">
            <div>
                @if ($title)
                    <h2 class="text-xl font-bold tracking-tight text-cream-900 dark:text-cream-50">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="text-sm text-cream-600 dark:text-cream-400 mt-0.5">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)<div>{{ $actions }}</div>@endisset
        </header>
    @endif
    {{ $slot }}
</section>
