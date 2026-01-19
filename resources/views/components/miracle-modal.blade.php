{{--
    Componente: Miracle Modal
    Uso: <x-miracle-modal id="miModal" title="Título del Modal">Contenido</x-miracle-modal>

    Props:
    - id: string - ID único del modal (requerido)
    - title: string - Título del modal
    - size: string - Tamaño (sm, md, lg, xl) - default: md
    - centered: bool - Si está centrado verticalmente - default: true
    - static: bool - Si no se cierra al hacer clic fuera - default: false
    - scrollable: bool - Si el body es scrollable - default: false

    Slots:
    - default: Contenido del modal body
    - footer: Contenido del footer (opcional)

    Ejemplo:
    <x-miracle-modal id="editarModal" title="Editar Producto" size="lg">
        <form>...</form>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </x-slot>
    </x-miracle-modal>

    // Abrir con JavaScript:
    const modal = new bootstrap.Modal(document.getElementById('editarModal'));
    modal.show();
--}}

@props([
    'id',
    'title',
    'size' => 'md',
    'centered' => true,
    'static' => false,
    'scrollable' => false
])

@php
    $dialogClass = 'modal-dialog';
    $dialogClass .= ' modal-' . $size;
    if ($centered) $dialogClass .= ' modal-dialog-centered';
    if ($scrollable) $dialogClass .= ' modal-dialog-scrollable';
@endphp

<div class="modal fade"
     id="{{ $id }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}Label"
     aria-hidden="true"
     @if($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif>
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                <h5 class="modal-title" id="{{ $id }}Label" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($footer))
                <div class="modal-footer" style="background-color: #faf8fc;">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
