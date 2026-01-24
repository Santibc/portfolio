@php
    $partes = $partes ?? [];
@endphp

@if(count($partes) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
        <p class="mb-0">Sin partes pendientes</p>
    </div>
@else
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Obra</th>
                <th>Fecha</th>
                <th>Jornada</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partes as $p)
                @php
                    $estadoClass = ($p['estado'] ?? '') === 'borrador' ? 'warning' : 'info';
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('partes-diarios.edit', $p['id']) }}" class="text-decoration-none">
                            {{ $p['obra_codigo'] }}
                        </a>
                    </td>
                    <td><small>{{ $p['fecha'] }}</small></td>
                    <td><small>{{ $p['jornada'] }}</small></td>
                    <td><span class="badge bg-{{ $estadoClass }}">{{ $p['estado'] }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
