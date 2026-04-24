@php
    $messages = [
        'success' => ['color' => 'green', 'icon' => 'check-circle'],
        'error' => ['color' => 'red', 'icon' => 'x-circle'],
        'warning' => ['color' => 'amber', 'icon' => 'exclamation-triangle'],
        'info' => ['color' => 'sky', 'icon' => 'info-circle'],
    ];
@endphp

@foreach ($messages as $type => $meta)
    @if (session($type))
        <div
            role="alert"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 6000)"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-4 flex items-start gap-3 rounded-xl border p-4 text-sm
                @if ($meta['color'] === 'green') border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-300
                @elseif ($meta['color'] === 'red') border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300
                @elseif ($meta['color'] === 'amber') border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300
                @else border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-300
                @endif"
        >
            <i class="bi bi-{{ $meta['icon'] }} mt-0.5 text-base"></i>
            <p class="flex-1">{{ session($type) }}</p>
            <button type="button" @click="show = false" class="text-current/60 hover:text-current" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif
@endforeach

@if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
    <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
        <div class="flex items-start gap-3">
            <i class="bi bi-exclamation-triangle mt-0.5 text-base"></i>
            <div class="flex-1">
                <div class="font-medium">Se encontraron errores:</div>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
