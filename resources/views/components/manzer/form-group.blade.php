@props([
    'label',
    'icon' => null,
    'name',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'options' => [],
    'rows' => 4,
    'step' => null,
    'min' => null,
    'max' => null,
    'help' => null,
])

@php
    $errorCls = $errors->has($name) ? 'ring-red-500 focus:ring-red-500' : '';
@endphp

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
        @if ($icon)<i class="bi bi-{{ $icon }} mr-1"></i>@endif
        {{ $label }}
        @if ($required)<span class="text-red-500">*</span>@endif
    </label>

    @if ($type === 'password')
        <div class="relative" x-data="{ show: false }">
            <input :type="show ? 'text' : 'password'" id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name, $value) }}" @if ($required) required @endif class="input pr-10 {{ $errorCls }}">
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                <i x-show="!show" class="bi bi-eye"></i>
                <i x-show="show" class="bi bi-eye-slash" style="display: none;"></i>
            </button>
        </div>
    @elseif ($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" @if ($required) required @endif class="input {{ $errorCls }}">
            @if (count($options) > 0)
                @foreach ($options as $v => $l)
                    <option value="{{ $v }}" @selected(old($name, $value) == $v)>{{ $l }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}" rows="{{ $rows }}" @if ($required) required @endif class="input {{ $errorCls }}">{{ old($name, $value) }}</textarea>
    @elseif ($type === 'file')
        <input type="file" id="{{ $name }}" name="{{ $name }}" @if ($required) required @endif @if ($attributes->has('accept')) accept="{{ $attributes->get('accept') }}" @endif class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-300 dark:file:bg-zinc-800 dark:file:text-zinc-200">
        {{ $slot }}
    @elseif ($type === 'checkbox')
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" @checked(old($name, $value)) class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
            <span class="text-sm">{{ $placeholder }}</span>
        </label>
    @else
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name, $value) }}" @if ($required) required @endif @if ($step) step="{{ $step }}" @endif @if ($min !== null) min="{{ $min }}" @endif @if ($max !== null) max="{{ $max }}" @endif class="input {{ $errorCls }}">
    @endif

    @if ($help)
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $help }}</p>
    @endif

    @error($name)
        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
