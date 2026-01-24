@php
    $documentos = $documentos ?? [];
@endphp

@if(count($documentos) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
        <p class="mb-0">No tienes documentos asignados</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($documentos as $doc)
            @php
                $pendiente = !($doc['leido'] ?? false);
                $rowClass = $pendiente ? 'documento-pendiente' : 'documento-leido';
            @endphp
            <li class="list-group-item {{ $rowClass }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $doc['tipo_formateado'] ?? $doc['tipo'] ?? 'Documento' }}</strong>
                        <br><small class="text-muted">Subido: {{ $doc['fecha_documento'] ?? '-' }}</small>
                        @if(isset($doc['fecha_lectura']) && $doc['fecha_lectura'])
                            <br><small class="text-success"><i class="bi bi-check-circle"></i> Leido: {{ $doc['fecha_lectura'] }}</small>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @if(isset($doc['archivo_path']) && $doc['archivo_path'])
                            <a href="/{{ $doc['archivo_path'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i>
                            </a>
                        @endif
                        @if($pendiente && ($doc['requiere_lectura'] ?? false))
                            <button type="button" class="btn btn-sm btn-success" onclick="confirmarLecturaDocumento({{ $doc['id'] }})">
                                <i class="bi bi-check2"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif
