@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-slate-600 dark:text-slate-400']) }}>
        {{ $status }}
    </div>
@endif
