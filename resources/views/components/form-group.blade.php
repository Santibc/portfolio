{{--
    Componente: Form Group
    Uso: <x-form-group label="Nombre" name="nombre" type="text" />

    Props:
    - label: string - Etiqueta del campo
    - name: string - Nombre del campo (también usado para id y error)
    - type: string - Tipo de input (text, email, password, number, date, textarea, select) - default: text
    - value: mixed - Valor del campo - default: old($name)
    - placeholder: string|null - Placeholder opcional
    - required: bool - Si el campo es requerido - default: false
    - disabled: bool - Si el campo está deshabilitado - default: false
    - readonly: bool - Si el campo es solo lectura - default: false
    - help: string|null - Texto de ayuda opcional
    - options: array - Opciones para select (formato: ['value' => 'label'])

    Ejemplo:
    <x-form-group label="Email" name="email" type="email" required />
    <x-form-group label="Descripción" name="descripcion" type="textarea" />
    <x-form-group label="Estado" name="estado" type="select" :options="['activo' => 'Activo', 'inactivo' => 'Inactivo']" />
--}}

@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'options' => [],
    'rows' => 3,
    'step' => null,
    'min' => null,
    'max' => null,
])

@php
    $inputValue = $value ?? old($name);
    $hasError = $errors->has($name);
    $inputClass = 'form-control' . ($hasError ? ' is-invalid' : '');
@endphp

<div {{ $attributes->merge(['class' => 'mb-3']) }}>
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        >{{ $inputValue }}</textarea>

    @elseif($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @if($inputValue == $optValue) selected @endif>
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>

    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            value="{{ $inputValue }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($step) step="{{ $step }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
        >
    @endif

    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
