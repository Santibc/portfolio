@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-primary-500 text-left text-base font-medium text-primary-700 bg-primary-50 dark:text-primary-300 dark:bg-primary-950 transition'
    : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-zinc-600 hover:text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
