@extends('cliente.layout')

@section('title', 'Mis Puntos y Beneficios')

@section('header')
    <h2 class="mb-0"><i class="bi bi-star"></i> Mis Puntos y Beneficios</h2>
    <p class="text-muted mb-0">Acumula puntos, refiere amigos y guarda fechas especiales</p>
@endsection

@section('content')
<div class="row g-4">
    {{-- Balance de Puntos --}}
    <div class="col-lg-4">
        <div class="content-card text-center" style="background: linear-gradient(135deg, #FF00C1 0%, #0B00F9 100%); color: white;">
            <i class="bi bi-star-fill" style="font-size: 3rem;"></i>
            <h1 class="display-4 fw-bold my-3">{{ number_format($balance) }}</h1>
            <p class="mb-0">Puntos disponibles</p>
        </div>

        <div class="content-card">
            <h6 class="text-muted mb-3">¿Cómo funcionan los puntos?</h6>
            <ul class="list-unstyled small">
                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Gana 1 punto por cada $100 en compras</li>
                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Gana 500 puntos por cada amigo referido</li>
                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Canjea puntos por descuentos</li>
                <li><i class="bi bi-info-circle text-primary"></i> Los puntos expiran después de 1 año</li>
            </ul>
        </div>

        {{-- Canjear Puntos --}}
        @if($balance >= 100)
        <div class="content-card">
            <h6><i class="bi bi-gift text-primary"></i> Canjear Puntos</h6>
            <p class="text-muted small mb-3">100 puntos = $1 de descuento</p>
            <form action="{{ route('cliente.puntos.canjear') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Puntos a canjear</label>
                    <select name="puntos" class="form-select">
                        @if($balance >= 100)
                            <option value="100">100 puntos = $1</option>
                        @endif
                        @if($balance >= 500)
                            <option value="500">500 puntos = $5</option>
                        @endif
                        @if($balance >= 1000)
                            <option value="1000">1000 puntos = $10</option>
                        @endif
                        @if($balance >= 2000)
                            <option value="2000">2000 puntos = $20</option>
                        @endif
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-repeat"></i> Canjear
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Código de Referido --}}
    <div class="col-lg-8">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5><i class="bi bi-people text-primary"></i> Programa de Referidos</h5>
                    <p class="text-muted mb-0">Invita amigos y gana 500 puntos por cada uno</p>
                </div>
            </div>

            <div class="bg-light p-4 rounded-3 text-center mb-4">
                <p class="text-muted mb-2">Tu código de referido</p>
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <h2 class="mb-0 font-monospace text-primary">{{ $user->codigo_referido }}</h2>
                    <button class="btn btn-outline-primary" onclick="copiarCodigo()">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-6">
                    <a href="https://wa.me/?text=Usa%20mi%20código%20{{ $user->codigo_referido }}%20para%20registrarte%20y%20obtener%20beneficios"
                       target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Compartir en WhatsApp
                    </a>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-primary w-100" onclick="copiarEnlace()">
                        <i class="bi bi-link-45deg"></i> Copiar enlace
                    </button>
                </div>
            </div>

            @if($referidos->count() > 0)
            <hr>
            <h6>Amigos referidos ({{ $referidos->count() }})</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach($referidos as $referido)
                <span class="badge bg-light text-dark">
                    <i class="bi bi-person-check"></i> {{ $referido->name }}
                </span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Historial de Puntos --}}
        <div class="content-card">
            <h5 class="mb-3"><i class="bi bi-clock-history text-primary"></i> Historial de Puntos</h5>

            @if($historial->isEmpty())
                <p class="text-muted text-center py-3">Aún no tienes movimientos de puntos</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th class="text-end">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historial as $mov)
                            <tr>
                                <td class="text-muted">{{ $mov->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($mov->tipo == 'ganados')
                                        <i class="bi bi-arrow-up-circle text-success"></i>
                                    @elseif($mov->tipo == 'canjeados')
                                        <i class="bi bi-arrow-down-circle text-danger"></i>
                                    @elseif($mov->tipo == 'referido')
                                        <i class="bi bi-people text-primary"></i>
                                    @endif
                                    {{ $mov->concepto }}
                                </td>
                                <td class="text-end">
                                    <span class="{{ $mov->puntos > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $mov->puntos > 0 ? '+' : '' }}{{ number_format($mov->puntos) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Fechas Especiales --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5><i class="bi bi-calendar-heart text-primary"></i> Fechas Especiales</h5>
                    <p class="text-muted mb-0">Te recordaremos cuando se acerquen para que nunca olvides regalar flores</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFecha">
                    <i class="bi bi-plus-lg"></i> Agregar fecha
                </button>
            </div>

            @if($fechasEspeciales->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-calendar-plus text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No tienes fechas especiales guardadas</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($fechasEspeciales as $fecha)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $fecha->nombre }}</h6>
                                    <span class="badge bg-light text-dark">{{ $fecha->tipo_nombre }}</span>
                                </div>
                                <form action="{{ route('cliente.puntos.fecha.destroy', $fecha) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta fecha?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                            <p class="mb-0 mt-2">
                                <i class="bi bi-calendar3"></i>
                                {{ $fecha->fecha->format('d M') }}
                                @if($fecha->dias_restantes <= 7 && $fecha->dias_restantes >= 0)
                                    <span class="badge bg-warning text-dark ms-2">
                                        {{ $fecha->dias_restantes == 0 ? '¡Hoy!' : 'En ' . $fecha->dias_restantes . ' días' }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Productos Sugeridos --}}
@if($productosSugeridos->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5><i class="bi bi-lightbulb text-warning"></i> Sugerencias para ti</h5>
                    <p class="text-muted mb-0">Productos perfectos para tus proximas fechas especiales</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-3">
                @foreach($productosSugeridos as $producto)
                @php
                    $precio = $producto->precios()->where('activo', true)->first();
                @endphp
                <div class="col-6 col-md-3">
                    <a href="{{ route('tienda.producto', $producto) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="{{ $producto->url_imagen_principal }}"
                                 class="card-img-top" alt="{{ $producto->nombre }}"
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <h6 class="card-title text-dark mb-1" style="font-size: 0.85rem;">
                                    {{ Str::limit($producto->nombre, 25) }}
                                </h6>
                                <p class="card-text text-primary fw-bold mb-0">
                                    @if($precio)
                                        ${{ number_format($precio->precio, 0, ',', '.') }}
                                    @else
                                        Consultar
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Agregar Fecha --}}
<div class="modal fade" id="modalFecha" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cliente.puntos.fecha.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar fecha especial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la persona *</label>
                        <input type="text" name="nombre" class="form-control" required
                               placeholder="Ej: Mamá, María, Aniversario de bodas...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de ocasión *</label>
                        <select name="tipo" class="form-select" required>
                            <option value="cumpleanos">Cumpleaños</option>
                            <option value="aniversario">Aniversario</option>
                            <option value="dia_madre">Día de la Madre</option>
                            <option value="dia_padre">Día del Padre</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha *</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Recordarme</label>
                        <select name="dias_anticipacion" class="form-select">
                            <option value="3">3 días antes</option>
                            <option value="5">5 días antes</option>
                            <option value="7">1 semana antes</option>
                            <option value="14">2 semanas antes</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas (opcional)</label>
                        <textarea name="notas" class="form-control" rows="2"
                                  placeholder="Ideas de regalo, preferencias..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copiarCodigo() {
    navigator.clipboard.writeText('{{ $user->codigo_referido }}');
    Swal.fire({
        icon: 'success',
        title: '¡Copiado!',
        text: 'Código copiado al portapapeles',
        confirmButtonColor: '#8d6c4f',
        timer: 1500,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

function copiarEnlace() {
    const url = '{{ route('register.cliente') }}?ref={{ $user->codigo_referido }}';
    navigator.clipboard.writeText(url);
    Swal.fire({
        icon: 'success',
        title: '¡Copiado!',
        text: 'Enlace copiado al portapapeles',
        confirmButtonColor: '#8d6c4f',
        timer: 1500,
        timerProgressBar: true,
        showConfirmButton: false
    });
}
</script>
@endpush
@endsection
