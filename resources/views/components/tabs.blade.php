@props([
    'id' => 'tabs-' . uniqid(),
    'tabs' => [],   // [['key' => 'one', 'label' => 'One', 'icon' => 'home']]
])

<div {{ $attributes }}>
    <div class="border-b border-cream-200 dark:border-cream-800">
        <nav class="-mb-px flex flex-wrap gap-x-1" aria-label="Tabs" role="tablist">
            @foreach ($tabs as $i => $tab)
                <button type="button"
                    class="hs-tab-active:font-semibold hs-tab-active:border-primary-500 hs-tab-active:text-primary-700 dark:hs-tab-active:text-primary-300 py-3 px-4 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-cream-600 hover:text-primary-600 dark:text-cream-400 dark:hover:text-primary-300 transition-colors {{ $i === 0 ? 'active' : '' }}"
                    id="{{ $id }}-item-{{ $tab['key'] }}"
                    data-hs-tab="#{{ $id }}-{{ $tab['key'] }}"
                    aria-controls="{{ $id }}-{{ $tab['key'] }}"
                    role="tab">
                    @if (!empty($tab['icon']))<x-icon :name="$tab['icon']" class="w-4 h-4" />@endif
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="mt-4">
        @foreach ($tabs as $i => $tab)
            <div id="{{ $id }}-{{ $tab['key'] }}"
                role="tabpanel"
                class="{{ $i === 0 ? '' : 'hidden' }}"
                aria-labelledby="{{ $id }}-item-{{ $tab['key'] }}">
                {{ ${ $tab['key'] } ?? '' }}
            </div>
        @endforeach
    </div>
</div>
