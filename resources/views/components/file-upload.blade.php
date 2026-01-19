{{--
    Componente: File Upload
    Uso: <x-file-upload name="archivo" label="Seleccionar archivo" />

    Props:
    - name: string - Nombre del campo
    - label: string - Etiqueta del campo - default: "Seleccionar archivo"
    - accept: string - Tipos de archivo aceptados - default: "*"
    - multiple: bool - Si permite múltiples archivos - default: false
    - required: bool - Si es requerido - default: false
    - help: string|null - Texto de ayuda
    - maxSize: string|null - Tamaño máximo a mostrar (ej: "10MB")
    - preview: bool - Si muestra preview de imagen - default: false

    Ejemplo:
    <x-file-upload name="documento" accept=".pdf,.doc,.docx" help="Máximo 5MB" />
    <x-file-upload name="imagen" accept="image/*" preview />
--}}

@props([
    'name',
    'label' => 'Seleccionar archivo',
    'accept' => '*',
    'multiple' => false,
    'required' => false,
    'help' => null,
    'maxSize' => null,
    'preview' => false
])

@php
    $inputId = $name . '-' . uniqid();
    $hasError = $errors->has($name);
    $inputClass = 'form-control' . ($hasError ? ' is-invalid' : '');
@endphp

<div {{ $attributes->merge(['class' => 'mb-3']) }}>
    <label for="{{ $inputId }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <input
        type="file"
        id="{{ $inputId }}"
        name="{{ $multiple ? $name . '[]' : $name }}"
        class="{{ $inputClass }}"
        accept="{{ $accept }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($preview) onchange="previewFile(this, '{{ $inputId }}-preview')" @endif
    >

    @if($help || $maxSize)
        <small class="form-text text-muted">
            {{ $help }}
            @if($maxSize)
                @if($help) - @endif
                Tamaño máximo: {{ $maxSize }}
            @endif
        </small>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if($preview)
        <div id="{{ $inputId }}-preview" class="mt-2"></div>
    @endif
</div>

@if($preview)
@once
@push('scripts')
<script>
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<span class="text-muted"><i class="bi bi-file-earmark"></i> ' + file.name + '</span>';
        }
    }
}
</script>
@endpush
@endonce
@endif
