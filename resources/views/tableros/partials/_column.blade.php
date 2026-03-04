<div class="tablero-columna" data-columna-id="{{ $columna->id }}">
    <div class="columna-header">
        <h3 class="columna-titulo" @if($puedeEditar) contenteditable="true" @endif
            data-original="{{ $columna->nombre }}"
            data-columna-id="{{ $columna->id }}">{{ $columna->nombre }}</h3>
        @if($puedeEditar)
        <div class="dropdown">
            <button class="btn-columna-menu" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="eliminarColumna({{ $columna->id }}, event)">
                    <i class="bi bi-trash text-danger me-2"></i>Eliminar lista
                </a></li>
            </ul>
        </div>
        @endif
    </div>
    <div class="columna-tarjetas" data-columna-id="{{ $columna->id }}">
        @foreach($columna->tarjetas as $tarjeta)
            @include('tableros.partials._card', ['tarjeta' => $tarjeta])
        @endforeach
    </div>
    @if($puedeEditar)
    <div class="columna-footer">
        <button class="btn-nueva-tarjeta" type="button" data-columna-id="{{ $columna->id }}" onclick="mostrarFormNuevaTarjeta(this)">
            <i class="bi bi-plus-lg"></i> Agregar tarjeta
        </button>
    </div>
    @endif
</div>
