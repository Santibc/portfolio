{{-- Widget: Cobros Pendientes con Aging --}}
@php
    $cobros = $cobros ?? [];
    $resumen = $cobros['resumen'] ?? [];
    $total = $cobros['total_pendiente'] ?? 0;
@endphp

@if(empty($resumen))
    <div class="text-center text-muted py-4">Sin datos disponibles</div>
@else
    @php
        $tramos = [
            ['key' => 'al_dia', 'label' => 'Al día', 'color' => '#198754'],
            ['key' => '1_30', 'label' => '1-30d', 'color' => '#0dcaf0'],
            ['key' => '31_60', 'label' => '31-60d', 'color' => '#ffc107'],
            ['key' => '61_90', 'label' => '61-90d', 'color' => '#fd7e14'],
            ['key' => 'mas_90', 'label' => '+90d', 'color' => '#dc3545'],
        ];
    @endphp

    {{-- Barra de aging --}}
    <div class="aging-bar d-flex mb-3" style="height: 30px; border-radius: 4px; overflow: hidden;">
        @foreach($tramos as $tramo)
            @php
                $valor = $resumen[$tramo['key']]['total'] ?? 0;
                $pct = $total > 0 ? ($valor / $total * 100) : 0;
            @endphp
            @if($pct > 0)
                <div style="width: {{ $pct }}%; background: {{ $tramo['color'] }}; position: relative;">
                    @if($pct >= 10)
                        <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.7rem; font-weight: 600; color: white;">
                            {{ round($pct) }}%
                        </span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    {{-- Tabla de resumen --}}
    <table class="table table-sm mb-0">
        <thead class="table-light">
            <tr>
                <th>Tramo</th>
                <th class="text-end">Cantidad</th>
                <th class="text-end">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tramos as $tramo)
                @php
                    $info = $resumen[$tramo['key']] ?? ['count' => 0, 'total' => 0];
                @endphp
                <tr>
                    <td>
                        <span class="badge" style="background: {{ $tramo['color'] }}">{{ $tramo['label'] }}</span>
                    </td>
                    <td class="text-end">{{ $info['count'] }}</td>
                    <td class="text-end fw-semibold">{{ number_format($info['total'], 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th>Total</th>
                <th class="text-end">{{ $cobros['total_registros'] ?? 0 }}</th>
                <th class="text-end">{{ number_format($total, 2, ',', '.') }} €</th>
            </tr>
        </tfoot>
    </table>
@endif
