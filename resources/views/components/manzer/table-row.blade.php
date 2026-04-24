@props(['class' => ''])

<tr {{ $attributes->merge(['class' => "border-t border-zinc-200 transition hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/50 {$class}"]) }}>
    {{ $slot }}
</tr>
