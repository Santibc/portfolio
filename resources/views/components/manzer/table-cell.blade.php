@props(['class' => ''])

<td {{ $attributes->merge(['class' => "whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 {$class}"]) }}>
    {{ $slot }}
</td>
