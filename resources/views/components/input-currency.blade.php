@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => '0',
    'hint' => null,
    'error' => null,
    'required' => false,
])

@php
    $id = $id ?? $name;
    $err = $error ?? ($name ? $errors->first($name) : null);
    $initial = preg_replace('/\D/', '', (string) old($name, $value ?? ''));
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">
            {{ $label }}
            @if ($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <div x-data="{
            raw: @js($initial),
            get display() { return this.raw === '' ? '' : new Intl.NumberFormat('es-CO').format(parseInt(this.raw, 10)); },
            handle(e) {
                this.raw = e.target.value.replace(/\D/g, '');
                this.$nextTick(() => {
                    e.target.value = this.display;
                    const len = e.target.value.length;
                    try { e.target.setSelectionRange(len, len); } catch (err) { /* some types reject selection */ }
                });
                this.$dispatch('currency-changed', parseInt(this.raw || '0', 10));
            }
        }"
        x-init="$dispatch('currency-changed', parseInt(raw || '0', 10))"
        class="relative">

        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-cream-500 font-semibold z-10">
            $
        </span>

        <input
            type="text"
            inputmode="numeric"
            id="{{ $id }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            autocomplete="off"
            x-init="$el.value = display"
            @input="handle($event)"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border-cream-300 bg-white pl-8 pr-3 py-2.5 text-sm text-cream-900 shadow-none placeholder:text-cream-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 dark:placeholder:text-cream-500 transition-all'
                    . ($err ? ' border-red-400 focus:border-red-500 focus:ring-red-500/30' : ''),
            ]) }}
        />

        <input type="hidden" @if ($name) name="{{ $name }}" @endif :value="raw">
    </div>

    @if ($err)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $err }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">{{ $hint }}</p>
    @endif
</div>
