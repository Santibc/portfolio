@php
    $calendario = $calendario ?? [];
    $dias = $calendario['dias'] ?? [];
@endphp

@if(empty($dias))
    <div class="text-center text-muted py-4">
        <p class="mb-0">Sin datos del calendario</p>
    </div>
@else
    <div class="text-center mb-3">
        <small class="text-muted fw-semibold">{{ $calendario['semana'] ?? '' }}</small>
    </div>
    <div class="row g-2">
        @foreach($dias as $dia)
            @php
                $hoyClass = ($dia['es_hoy'] ?? false) ? 'hoy' : '';
                $tieneEventos = ($dia['partes'] ?? 0) > 0 || ($dia['inspecciones'] ?? 0) > 0 || ($dia['vencimientos'] ?? 0) > 0;
            @endphp
            <div class="col">
                <div class="calendario-dia {{ $hoyClass }}">
                    <small class="d-block text-uppercase" style="font-size:0.65rem">{{ $dia['dia'] }}</small>
                    <strong class="fs-5">{{ $dia['dia_mes'] }}</strong>
                    @if($tieneEventos)
                        <div class="calendario-eventos">
                            @if(($dia['partes'] ?? 0) > 0)
                                <span class="bg-primary" title="{{ $dia['partes'] }} partes"></span>
                            @endif
                            @if(($dia['inspecciones'] ?? 0) > 0)
                                <span class="bg-warning" title="{{ $dia['inspecciones'] }} inspecciones"></span>
                            @endif
                            @if(($dia['vencimientos'] ?? 0) > 0)
                                <span class="bg-danger" title="{{ $dia['vencimientos'] }} vencimientos"></span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3 d-flex justify-content-center gap-3">
        <small><span class="badge bg-primary">&nbsp;</span> Partes</small>
        <small><span class="badge bg-warning">&nbsp;</span> Inspecciones</small>
        <small><span class="badge bg-danger">&nbsp;</span> Vencimientos</small>
    </div>
@endif
