@php
    $primas = $primas ?? [];
    $items = $primas['primas'] ?? [];
    $bonos = $primas['bonos'] ?? [];
    $totalAnio = $primas['total_anio'] ?? 0;

    // Combinar items
    $allItems = collect($items)
        ->map(fn($p) => array_merge($p, ['tipo_item' => 'Prima']))
        ->concat(
            collect($bonos)->map(fn($b) => array_merge($b, ['tipo_item' => 'Bono', 'concepto' => $b['tipo_bono'] ?? 'Bono']))
        )
        ->sortByDesc('fecha')
        ->take(10);
@endphp

@if($allItems->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="bi bi-gift fs-1 d-block mb-2"></i>
        <p class="mb-0">No tienes primas ni bonos registrados</p>
    </div>
@else
    <div class="p-3 bg-light border-bottom">
        <div class="text-center">
            <h5 class="mb-0 text-purple">{{ number_format($totalAnio, 2, ',', '.') }} &euro;</h5>
            <small class="text-muted">Total {{ now()->year }}</small>
        </div>
    </div>

    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Concepto</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th class="text-end">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allItems as $item)
                @php
                    $tipoClass = ($item['tipo_item'] ?? '') === 'Prima' ? 'info' : 'success';
                @endphp
                <tr>
                    <td>{{ $item['concepto'] ?? $item['descripcion'] ?? '-' }}</td>
                    <td><span class="badge bg-{{ $tipoClass }}">{{ $item['tipo_item'] ?? '-' }}</span></td>
                    <td><small>{{ $item['fecha'] ?? '-' }}</small></td>
                    <td class="text-end fw-semibold">{{ number_format($item['importe'] ?? 0, 2, ',', '.') }} &euro;</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
