{{-- Widget: Ranking de Cuadrillas --}}
@php
    $cuadrillas = $cuadrillas ?? [];
@endphp

@if(empty($cuadrillas))
    <div class="text-center text-muted py-4">Sin datos de cuadrillas</div>
@else
    <table class="table table-sm table-hover table-ranking mb-0">
        <thead>
            <tr>
                <th>Cuadrilla</th>
                <th class="text-center">Trab.</th>
                <th class="text-end">Producción</th>
                <th class="text-end">Coste</th>
                <th class="text-end">Margen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuadrillas as $index => $c)
                @php
                    $margenClass = ($c['margen_porcentaje'] ?? 0) >= 20 ? 'success' : (($c['margen_porcentaje'] ?? 0) >= 0 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td>
                        {{ $c['nombre'] }}
                        @if($index === 0)
                            <i class="bi bi-trophy-fill text-warning"></i>
                        @elseif($index === 1)
                            <i class="bi bi-award-fill text-secondary"></i>
                        @endif
                        <br><small class="text-muted">{{ $c['capataz'] ?? 'Sin capataz' }}</small>
                    </td>
                    <td class="text-center">{{ $c['num_trabajadores'] ?? 0 }}</td>
                    <td class="text-end text-success">{{ number_format($c['produccion_total'] ?? 0, 0, ',', '.') }} €</td>
                    <td class="text-end text-danger">{{ number_format($c['coste_estimado'] ?? 0, 0, ',', '.') }} €</td>
                    <td class="text-end">
                        <span class="badge bg-{{ $margenClass }}">{{ $c['margen_porcentaje'] ?? 0 }}%</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
