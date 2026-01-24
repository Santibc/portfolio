{{-- Widget: Rentabilidad por Obra (Top/Bottom) --}}
@php
    $obras = $obras ?? [];
    $top = $obras['top'] ?? [];
    $bottom = $obras['bottom'] ?? [];
@endphp

@if(empty($top) && empty($bottom))
    <div class="text-center text-muted py-4">Sin datos de obras</div>
@else
    {{-- Top obras (mejores) --}}
    @if(!empty($top))
        <div class="px-3 pt-3">
            <h6 class="text-success mb-2"><i class="bi bi-arrow-up me-1"></i>Mejores</h6>
        </div>
        <table class="table table-sm table-hover table-ranking mb-0">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th class="text-end">Ingresos</th>
                    <th class="text-end">Gastos</th>
                    <th class="text-end">Margen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top as $obra)
                    @php
                        $margenClass = ($obra['margen_porcentaje'] ?? 0) >= 0 ? 'success' : 'danger';
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('obras.show', $obra['id']) }}" class="text-decoration-none">
                                {{ $obra['codigo'] }}
                            </a>
                            <br><small class="text-muted">{{ Str::limit($obra['nombre'], 25) }}</small>
                        </td>
                        <td class="text-end text-success">{{ number_format($obra['total_ingresos'] ?? 0, 0, ',', '.') }} €</td>
                        <td class="text-end text-danger">{{ number_format($obra['total_gastos'] ?? 0, 0, ',', '.') }} €</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $margenClass }}">{{ $obra['margen_porcentaje'] ?? 0 }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Bottom obras (peores) --}}
    @if(!empty($bottom))
        <div class="px-3 pt-3 border-top">
            <h6 class="text-danger mb-2"><i class="bi bi-arrow-down me-1"></i>Peores</h6>
        </div>
        <table class="table table-sm table-hover table-ranking mb-0">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th class="text-end">Ingresos</th>
                    <th class="text-end">Gastos</th>
                    <th class="text-end">Margen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bottom as $obra)
                    @php
                        $margenClass = ($obra['margen_porcentaje'] ?? 0) >= 0 ? 'success' : 'danger';
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('obras.show', $obra['id']) }}" class="text-decoration-none">
                                {{ $obra['codigo'] }}
                            </a>
                            <br><small class="text-muted">{{ Str::limit($obra['nombre'], 25) }}</small>
                        </td>
                        <td class="text-end text-success">{{ number_format($obra['total_ingresos'] ?? 0, 0, ',', '.') }} €</td>
                        <td class="text-end text-danger">{{ number_format($obra['total_gastos'] ?? 0, 0, ',', '.') }} €</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $margenClass }}">{{ $obra['margen_porcentaje'] ?? 0 }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif
