{{--
    Componente: Confirm Button
    Uso: <x-confirm-button action="/eliminar/1" method="DELETE" title="¿Eliminar?" />

    Props:
    - action: string - URL de acción del formulario
    - method: string - Método HTTP (POST, PUT, PATCH, DELETE) - default: POST
    - title: string - Título de la confirmación - default: "¿Estás seguro?"
    - text: string - Texto descriptivo - default: "Esta acción no se puede deshacer."
    - confirmText: string - Texto del botón confirmar - default: "Sí, continuar"
    - cancelText: string - Texto del botón cancelar - default: "Cancelar"
    - icon: string - Icono de SweetAlert (warning, error, success, info, question) - default: warning
    - buttonClass: string - Clases CSS del botón - default: "btn btn-danger btn-sm"
    - buttonIcon: string - Icono del botón - default: "bi-trash"
    - buttonText: string - Texto del botón - default: ""

    Ejemplo:
    <x-confirm-button
        action="{{ route('productos.eliminar', $producto->id) }}"
        method="DELETE"
        title="¿Eliminar producto?"
        text="El producto será eliminado permanentemente."
        buttonText="Eliminar"
    />
--}}

@props([
    'action',
    'method' => 'POST',
    'title' => '¿Estás seguro?',
    'text' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Sí, continuar',
    'cancelText' => 'Cancelar',
    'icon' => 'warning',
    'buttonClass' => 'btn btn-danger btn-sm',
    'buttonIcon' => 'bi-trash',
    'buttonText' => '',
])

@php
    $formId = 'confirm-form-' . uniqid();
@endphp

<form id="{{ $formId }}" action="{{ $action }}" method="POST" class="d-inline">
    @csrf
    @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <button type="button"
            class="{{ $buttonClass }}"
            onclick="confirmarAccion('{{ $formId }}', '{{ $title }}', '{{ $text }}', '{{ $confirmText }}', '{{ $cancelText }}', '{{ $icon }}')"
            title="{{ $buttonText ?: $title }}">
        @if($buttonIcon)
            <i class="{{ $buttonIcon }}"></i>
        @endif
        @if($buttonText)
            <span class="ms-1">{{ $buttonText }}</span>
        @endif
    </button>
</form>

@once
@push('scripts')
<script>
function confirmarAccion(formId, title, text, confirmText, cancelText, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#FF84D5',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endpush
@endonce
