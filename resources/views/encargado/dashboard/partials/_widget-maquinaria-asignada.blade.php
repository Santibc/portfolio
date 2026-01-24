@php
    $maquinaria = $maquinaria ?? [];
@endphp

@if(count($maquinaria) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-gear fs-1 d-block mb-2"></i>
        <p class="mb-0">Sin maquinaria asignada</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($maquinaria as $m)
            @php
                $estadoClass = match($m['estado'] ?? '') {
                    'operativa' => 'success',
                    'en_reparacion' => 'warning',
                    'baja' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $m['nombre'] }}</strong>
                    <br><small class="text-muted">{{ $m['marca_modelo'] }}</small>
                    <br><small>Obra: {{ $m['obra_codigo'] }} | Op: {{ Str::limit($m['operador'], 15) }}</small>
                </div>
                <span class="badge bg-{{ $estadoClass }}">{{ $m['estado'] }}</span>
            </li>
        @endforeach
    </ul>
@endif
