@props(['headers' => []])

<div {{ $attributes->merge(['class' => 'card overflow-hidden p-0']) }}>
    <div class="overflow-x-auto">
        <table class="w-full">
            @if (count($headers) > 0)
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        @foreach ($headers as $header)
                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
