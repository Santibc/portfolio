{{-- Widget: Obras en Riesgo --}}
@php
    $obras = $obras ?? [];
@endphp

@if(empty($obras))
    <div class="text-center text-muted py-4">
        <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
        <p class="mb-0">Sin obras en riesgo</p>
    </div>
@else
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Obra</th>
                <th>Encargado</th>
                <th class="text-end">Coste Est.</th>
                <th class="text-end">Gasto Real</th>
                <th class="text-end">Desviación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obras as $obra)
                <tr class="table-danger bg-opacity-10">
                    <td>
                        <a href="{{ route('obras.show', $obra['id']) }}" class="text-decoration-none fw-semibold">
                            {{ $obra['codigo'] }}
                        </a>
                        <br><small class="text-muted">{{ Str::limit($obra['nombre'], 30) }}</small>
                    </td>
                    <td><small>{{ $obra['encargado']['name'] ?? 'Sin asignar' }}</small></td>
                    <td class="text-end">{{ number_format($obra['coste_estimado'] ?? 0, 0, ',', '.') }} €</td>
                    <td class="text-end text-danger fw-semibold">{{ number_format($obra['gasto_real'] ?? 0, 0, ',', '.') }} €</td>
                    <td class="text-end">
                        <span class="badge bg-danger">+{{ number_format($obra['desviacion'] ?? 0, 0, ',', '.') }} €</span>
                        <br><small class="text-danger">(+{{ $obra['desviacion_porcentaje'] ?? 0 }}%)</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
