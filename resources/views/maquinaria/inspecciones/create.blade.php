@extends('layouts.app')

@section('title', 'Nueva Inspeccion - ' . $maquinaria->codigo_interno)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nueva Inspeccion</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-tools me-1"></i>{{ $maquinaria->nombre_completo }}
                <span class="mx-2">|</span>
                <code>{{ $maquinaria->codigo_interno }}</code>
            </p>
        </div>
        <a href="{{ route('maquinaria.show', $maquinaria) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    @if($plantillas->count() > 0)
    <form action="{{ route('maquinaria.inspecciones.store', $maquinaria) }}" method="POST" id="inspeccionForm">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Seleccion de Plantilla -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Seleccionar Checklist</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Plantilla de Inspeccion <span class="text-danger">*</span></label>
                            <select name="plantilla_id" id="plantillaSelect" class="form-select form-select-lg @error('plantilla_id') is-invalid @enderror" required>
                                <option value="">Seleccionar plantilla...</option>
                                @foreach($plantillas as $plantilla)
                                    <option value="{{ $plantilla->id }}"
                                            data-items="{{ json_encode($plantilla->items) }}"
                                            {{ old('plantilla_id') == $plantilla->id ? 'selected' : '' }}>
                                        {{ $plantilla->nombre }}
                                        @if($plantilla->descripcion)
                                            - {{ $plantilla->descripcion }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('plantilla_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items del Checklist -->
                <div class="card border-0 shadow-sm mb-4" id="checklistCard" style="display: none;">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Items de Inspeccion</h6>
                    </div>
                    <div class="card-body">
                        <div id="checklistItems">
                            <!-- Los items se cargan dinamicamente -->
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Observaciones</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="4"
                                  placeholder="Observaciones generales de la inspeccion...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Datos de la Inspeccion -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Datos de la Inspeccion</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Resultado -->
                <div class="card border-0 shadow-sm mb-4" id="resultadoCard" style="display: none;">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Resultado</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert mb-3" id="resultadoAlert">
                            <strong id="resultadoTexto"></strong>
                            <p class="mb-0 small" id="resultadoDetalle"></p>
                        </div>
                        <input type="hidden" name="resultado" id="resultadoInput" value="apto">

                        <div class="d-flex gap-2">
                            <span class="badge bg-success" id="badgeAptos">0 aptos</span>
                            <span class="badge bg-danger" id="badgeNoAptos">0 no aptos</span>
                            <span class="badge bg-secondary" id="badgeNA">0 N/A</span>
                        </div>
                    </div>
                </div>

                <!-- Info Maquinaria -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Maquinaria</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted">Codigo</td>
                                <td><code>{{ $maquinaria->codigo_interno }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tipo</td>
                                <td>{{ $maquinaria->tipo->nombre ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Estado</td>
                                <td>
                                    @php
                                        $estadoColors = ['operativa' => 'success', 'en_reparacion' => 'warning', 'baja' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $estadoColors[$maquinaria->estado] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $maquinaria->estado)) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                                <i class="bi bi-check-lg me-2"></i>Guardar Inspeccion
                            </button>
                            <a href="{{ route('maquinaria.show', $maquinaria) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        No hay plantillas de checklist disponibles para el tipo de maquinaria <strong>{{ $maquinaria->tipo->nombre ?? 'desconocido' }}</strong>.
        <br>
        <small class="text-muted">Contacte al administrador para crear plantillas de inspeccion.</small>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const plantillaSelect = document.getElementById('plantillaSelect');
    const checklistCard = document.getElementById('checklistCard');
    const checklistItems = document.getElementById('checklistItems');
    const resultadoCard = document.getElementById('resultadoCard');
    const resultadoAlert = document.getElementById('resultadoAlert');
    const resultadoTexto = document.getElementById('resultadoTexto');
    const resultadoDetalle = document.getElementById('resultadoDetalle');
    const resultadoInput = document.getElementById('resultadoInput');
    const btnGuardar = document.getElementById('btnGuardar');
    const badgeAptos = document.getElementById('badgeAptos');
    const badgeNoAptos = document.getElementById('badgeNoAptos');
    const badgeNA = document.getElementById('badgeNA');

    plantillaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (!this.value) {
            checklistCard.style.display = 'none';
            resultadoCard.style.display = 'none';
            btnGuardar.disabled = true;
            return;
        }

        const items = JSON.parse(selectedOption.dataset.items || '[]');
        renderChecklist(items);
        checklistCard.style.display = 'block';
        resultadoCard.style.display = 'block';
        btnGuardar.disabled = false;
        calcularResultado();
    });

    function renderChecklist(items) {
        if (items.length === 0) {
            checklistItems.innerHTML = '<p class="text-muted">Esta plantilla no tiene items definidos.</p>';
            return;
        }

        // Agrupar por categoria
        const categorias = {};
        items.forEach(item => {
            const cat = item.categoria || 'General';
            if (!categorias[cat]) categorias[cat] = [];
            categorias[cat].push(item);
        });

        let html = '';
        for (const [categoria, catItems] of Object.entries(categorias)) {
            html += `
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-folder me-2"></i>${categoria}
                    </h6>
                    <div class="list-group list-group-flush">
            `;

            catItems.forEach(item => {
                const obligatorioMark = item.obligatorio ? '<span class="text-danger">*</span>' : '';
                html += `
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span>${item.descripcion} ${obligatorioMark}</span>
                            </div>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check item-check" name="items[${item.id}]"
                                       id="item_${item.id}_apto" value="apto" checked>
                                <label class="btn btn-outline-success btn-sm" for="item_${item.id}_apto">
                                    <i class="bi bi-check"></i> Apto
                                </label>

                                <input type="radio" class="btn-check item-check" name="items[${item.id}]"
                                       id="item_${item.id}_no_apto" value="no_apto">
                                <label class="btn btn-outline-danger btn-sm" for="item_${item.id}_no_apto">
                                    <i class="bi bi-x"></i> No Apto
                                </label>

                                <input type="radio" class="btn-check item-check" name="items[${item.id}]"
                                       id="item_${item.id}_na" value="no_aplica">
                                <label class="btn btn-outline-secondary btn-sm" for="item_${item.id}_na">
                                    N/A
                                </label>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="items_observaciones[${item.id}]"
                                   class="form-control form-control-sm"
                                   placeholder="Observacion para este item (opcional)">
                        </div>
                    </div>
                `;
            });

            html += '</div></div>';
        }

        checklistItems.innerHTML = html;

        // Agregar listeners para recalcular resultado
        document.querySelectorAll('.item-check').forEach(radio => {
            radio.addEventListener('change', calcularResultado);
        });
    }

    function calcularResultado() {
        const items = document.querySelectorAll('.item-check:checked');
        let aptos = 0, noAptos = 0, na = 0;

        items.forEach(item => {
            if (item.value === 'apto') aptos++;
            else if (item.value === 'no_apto') noAptos++;
            else na++;
        });

        badgeAptos.textContent = aptos + ' aptos';
        badgeNoAptos.textContent = noAptos + ' no aptos';
        badgeNA.textContent = na + ' N/A';

        if (noAptos > 0) {
            resultadoAlert.className = 'alert alert-danger mb-3';
            resultadoTexto.textContent = 'NO APTO';
            resultadoDetalle.textContent = 'La maquinaria tiene ' + noAptos + ' item(s) no apto(s). Se recomienda pasar a estado "En Reparacion".';
            resultadoInput.value = 'no_apto';
        } else {
            resultadoAlert.className = 'alert alert-success mb-3';
            resultadoTexto.textContent = 'APTO';
            resultadoDetalle.textContent = 'Todos los items obligatorios estan en buen estado.';
            resultadoInput.value = 'apto';
        }
    }

    // Si hay una plantilla preseleccionada (por old())
    if (plantillaSelect.value) {
        plantillaSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
