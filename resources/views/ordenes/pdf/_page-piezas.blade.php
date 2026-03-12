{{-- PAGINA 2: Piezas --}}
<div class="page-break page-con-margen">
    <div class="section-title" style="margin-bottom: 12px;">PIEZAS</div>

    @php
        $labelEstado = [
            'pendiente' => 'PENDIENTE',
            'en_proceso' => 'EN PROCESO',
            'completada' => 'COMPLETADA',
            'entregada' => 'ENTREGADA',
        ];
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">#</th>
                <th>Nombre</th>
                <th class="text-center" style="width: 32px;">Cant.</th>
                <th style="width: 65px;">Material</th>
                <th style="width: 45px;">Calibre</th>
                <th>Especificacion</th>
                <th class="text-center" style="width: 45px;">Avance</th>
                <th style="width: 65px;">Estado</th>
                <th style="width: 60px;">Operario</th>
                <th class="text-center" style="width: 50px;">Entregadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->piezas->sortBy('orden_visual') as $i => $pieza)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $pieza->nombre }}</td>
                    <td class="text-center">{{ $pieza->cantidad }}</td>
                    <td>{{ $pieza->material ?? '-' }}</td>
                    <td>{{ $pieza->calibre ?? '-' }}</td>
                    <td style="font-size: 8px;">{{ $pieza->especificacion ?? '-' }}</td>
                    <td class="text-center">
                        @php $avance = (float) $pieza->porcentaje_avance; @endphp
                        <span style="color: {{ $avance >= 100 ? '#16a34a' : ($avance > 0 ? '#d97706' : '#6b7280') }};">
                            {{ number_format($avance, 0) }}%
                        </span>
                    </td>
                    <td>
                        <span class="estado-badge estado-{{ $pieza->estado }}">
                            {{ $labelEstado[$pieza->estado] ?? strtoupper($pieza->estado) }}
                        </span>
                    </td>
                    <td style="font-size: 8px;">
                        @if(!$pieza->requiere_operario)
                            <span class="text-muted" style="font-style: italic;">Auto</span>
                        @else
                            {{ $pieza->operarioActual->name ?? '-' }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $pieza->cantidad_entregada ?? 0 }} / {{ $pieza->cantidad }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Resumen de entregas --}}
    @if($orden->entregas->count() > 0)
        <div style="margin-top: 15px;">
            <div class="section-title">HISTORIAL DE ENTREGAS</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        <th style="width: 80px;">Fecha</th>
                        <th>Piezas Entregadas</th>
                        <th style="width: 100px;">Entregado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->entregas->sortByDesc('created_at') as $ei => $entrega)
                        <tr>
                            <td>{{ $ei + 1 }}</td>
                            <td>{{ $entrega->created_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</td>
                            <td style="font-size: 8px;">
                                @foreach($entrega->piezas as $ep)
                                    {{ $ep->ordenPieza->nombre ?? '-' }} (x{{ $ep->cantidad }}){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </td>
                            <td style="font-size: 8px;">{{ $entrega->entregadaPorUsuario->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
