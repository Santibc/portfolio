@props([
    'items' => [],   // [['title' => '...', 'content' => '...']] OR use slots inline
    'id' => 'accordion-' . uniqid(),
])

<div class="hs-accordion-group divide-y divide-cream-200 dark:divide-cream-800 surface-card !p-0" {{ $attributes }}>
    @foreach ($items as $i => $item)
        <div class="hs-accordion {{ $i === 0 ? 'active' : '' }}" id="{{ $id }}-{{ $i }}">
            <button class="hs-accordion-toggle w-full flex items-center justify-between gap-3 px-5 py-4 text-left text-sm font-medium text-cream-900 dark:text-cream-100 hover:bg-cream-50 dark:hover:bg-cream-900/40 transition-colors">
                <span>{{ $item['title'] }}</span>
                <x-icon name="chevron-down" class="w-4 h-4 hs-accordion-active:rotate-180 transition-transform" />
            </button>
            <div class="hs-accordion-content {{ $i === 0 ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
                <div class="px-5 pb-4 text-sm text-cream-700 dark:text-cream-300">
                    {!! $item['content'] !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
