@props([
    'items' => [],   // [['label' => 'Home', 'href' => '/'], ['label' => 'Current']]
])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex']) }}>
    <ol class="inline-flex items-center flex-wrap gap-1 text-sm">
        @foreach ($items as $i => $item)
            <li class="flex items-center gap-1">
                @if (!empty($item['href']) && !$loop->last)
                    <a href="{{ $item['href'] }}" class="text-cream-600 hover:text-primary-700 dark:text-cream-400 dark:hover:text-primary-300 transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-cream-900 dark:text-cream-100 font-medium">{{ $item['label'] }}</span>
                @endif
                @unless ($loop->last)
                    <x-icon name="chevron-right" class="w-3.5 h-3.5 text-cream-400" />
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
