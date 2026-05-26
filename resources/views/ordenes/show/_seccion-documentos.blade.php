{{-- Seccion: Documentos adjuntos (detalle de orden) --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center">
        <h5 class="mb-0"><i class="bi bi-paperclip me-2 text-primary"></i>Documentos adjuntos</h5>
        @if($orden->documentos && $orden->documentos->count() > 0)
            <span class="badge bg-secondary ms-2">{{ $orden->documentos->count() }}</span>
        @endif
    </div>
    <div class="card-body">
        @if($orden->documentos && $orden->documentos->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Nombre</th>
                            <th style="width: 110px;">Tamaño</th>
                            <th style="width: 160px;">Subido por</th>
                            <th style="width: 150px;">Fecha</th>
                            <th style="width: 120px;" class="text-end">Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->documentos as $doc)
                            <tr>
                                <td class="text-center"><i class="bi {{ $doc->icono }} fs-4"></i></td>
                                <td style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $doc->nombre_original }}">{{ $doc->nombre_original }}</td>
                                <td>{{ $doc->tamano_legible }}</td>
                                <td>{{ optional($doc->subidoPorUsuario)->name ?? '-' }}</td>
                                <td>{{ optional($doc->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ $doc->url_descarga }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download me-1"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-3">
                <i class="bi bi-inbox me-1"></i> Sin documentos adjuntos.
            </div>
        @endif
    </div>
</div>
