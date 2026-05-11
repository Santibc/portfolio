@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'breadcrumb' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between']) }}>
    <div class="space-y-1.5 min-w-0">
        @if ($breadcrumb)
            <x-breadcrumb :items="$breadcrumb" class="mb-2" />
        @endif
        <div class="flex items-center gap-3">
            @if ($icon)
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200 shrink-0">
                    <x-icon :name="$icon" class="w-5 h-5" />
                </span>
            @endif
            <div class="min-w-0">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-cream-900 dark:text-cream-50 truncate">
                    {{ $title }}
                </h1>
                @if ($subtitle)
                    <p class="text-sm text-cream-600 dark:text-cream-400">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>

    @isset($actions)
        <div class="flex items-center flex-wrap gap-2 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>
