{{-- Selector de trabajadores con Alpine.js - usado en create y edit --}}
@php
    $preselected = $preselected ?? [];
    $obraId = $obraId ?? null;
@endphp

<div class="card border-0 shadow-sm mb-4"
     x-data="trabajadorSelector()"
     x-init="initSelector()"
     data-preselected='@json($preselected)'
     data-obra-id="{{ $obraId }}"
     id="trabajadorSelectorCard">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>Trabajadores
        </h5>
        <span class="badge bg-primary" x-text="selectedWorkerIds.length">0</span>
    </div>
    <div class="card-body p-0">
        {{-- Loading --}}
        <div x-show="loading" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <p class="text-muted small mt-2 mb-0">Cargando trabajadores...</p>
        </div>

        {{-- Sin obra seleccionada --}}
        <div x-show="!obraId && !loading" class="text-center py-4 text-muted px-3">
            <i class="bi bi-arrow-up-circle fs-1 d-block mb-2 opacity-50"></i>
            <p class="mb-0 small">Selecciona una obra para ver los trabajadores disponibles</p>
        </div>

        {{-- Obra seleccionada: mostrar tabs --}}
        <div x-show="obraId && !loading" x-cloak>
            {{-- Navegación de tabs --}}
            <ul class="nav nav-tabs nav-fill border-bottom-0 px-2 pt-2" role="tablist">
                <li class="nav-item">
                    <button class="nav-link py-1 px-2 small" type="button"
                            :class="{ 'active': activeTab === 'cuadrillas' }"
                            @click="activeTab = 'cuadrillas'">
                        <i class="bi bi-people-fill me-1"></i>Cuadrillas
                        <span class="badge bg-secondary-subtle text-secondary ms-1"
                              x-text="cuadrillas.length" style="font-size: 0.7em"></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-1 px-2 small" type="button"
                            :class="{ 'active': activeTab === 'obra' }"
                            @click="activeTab = 'obra'">
                        <i class="bi bi-building me-1"></i>De la Obra
                        <span class="badge bg-secondary-subtle text-secondary ms-1"
                              x-text="obraTrabajadores.length" style="font-size: 0.7em"></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-1 px-2 small" type="button"
                            :class="{ 'active': activeTab === 'otros' }"
                            @click="activeTab = 'otros'">
                        <i class="bi bi-person-plus me-1"></i>Otros
                        <span class="badge bg-secondary-subtle text-secondary ms-1"
                              x-text="otrosTrabajadores.length" style="font-size: 0.7em"></span>
                    </button>
                </li>
            </ul>

            {{-- TAB 1: Cuadrillas --}}
            <div x-show="activeTab === 'cuadrillas'" class="p-3" style="max-height: 400px; overflow-y: auto;">
                <template x-if="cuadrillas.length === 0">
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-people fs-3 d-block mb-2 opacity-50"></i>
                        <p class="small mb-0">No hay cuadrillas asignadas a esta obra</p>
                    </div>
                </template>
                <template x-for="cuadrilla in cuadrillas" :key="cuadrilla.id">
                    <div class="mb-3">
                        {{-- Header de cuadrilla con toggle --}}
                        <div class="d-flex align-items-center border-bottom pb-2 mb-2">
                            <input type="checkbox" class="form-check-input me-2"
                                   :id="'cuadrilla-' + cuadrilla.id"
                                   :checked="isCuadrillaFullySelected(cuadrilla)"
                                   x-effect="$el.indeterminate = isCuadrillaPartiallySelected(cuadrilla)"
                                   @change="toggleCuadrilla(cuadrilla)">
                            <label class="form-check-label fw-semibold flex-grow-1 cursor-pointer"
                                   :for="'cuadrilla-' + cuadrilla.id">
                                <i class="bi bi-people-fill text-primary me-1"></i>
                                <span x-text="cuadrilla.nombre"></span>
                                <span class="text-muted fw-normal" x-text="'(' + cuadrilla.trabajadores.length + ')'"></span>
                            </label>
                        </div>
                        {{-- Miembros individuales --}}
                        <template x-for="trab in cuadrilla.trabajadores" :key="'c-' + cuadrilla.id + '-' + trab.id">
                            <div class="form-check ms-4 mb-1">
                                <input type="checkbox" class="form-check-input"
                                       :id="'trab-c-' + cuadrilla.id + '-' + trab.id"
                                       :checked="isSelected(trab.id)"
                                       @change="toggleWorker(trab.id)">
                                <label class="form-check-label" :for="'trab-c-' + cuadrilla.id + '-' + trab.id">
                                    <span x-text="trab.nombre + ' ' + trab.apellidos"></span>
                                </label>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- TAB 2: Trabajadores de la obra (directos + cuadrilla) --}}
            <div x-show="activeTab === 'obra'" class="p-3" style="max-height: 400px; overflow-y: auto;">
                <template x-if="obraTrabajadores.length === 0">
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-building fs-3 d-block mb-2 opacity-50"></i>
                        <p class="small mb-0">No hay trabajadores asignados a esta obra</p>
                    </div>
                </template>
                <template x-for="trab in obraTrabajadores" :key="'o-' + trab.id">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input"
                               :id="'trab-o-' + trab.id"
                               :checked="isSelected(trab.id)"
                               @change="toggleWorker(trab.id)">
                        <label class="form-check-label" :for="'trab-o-' + trab.id">
                            <span x-text="trab.nombre + ' ' + trab.apellidos"></span>
                        </label>
                    </div>
                </template>
            </div>

            {{-- TAB 3: Otros trabajadores --}}
            <div x-show="activeTab === 'otros'" class="p-3" style="max-height: 400px; overflow-y: auto;">
                <p class="text-muted small mb-2">
                    <i class="bi bi-info-circle me-1"></i>Trabajadores no asignados a esta obra:
                </p>
                <template x-if="otrosTrabajadores.length === 0">
                    <div class="text-center py-3 text-muted">
                        <p class="small mb-0">No hay otros trabajadores disponibles</p>
                    </div>
                </template>
                <template x-for="trab in otrosTrabajadores" :key="'x-' + trab.id">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input"
                               :id="'trab-x-' + trab.id"
                               :checked="isSelected(trab.id)"
                               @change="toggleWorker(trab.id)">
                        <label class="form-check-label" :for="'trab-x-' + trab.id">
                            <span x-text="trab.nombre + ' ' + trab.apellidos"></span>
                        </label>
                    </div>
                </template>
            </div>
        </div>

        {{-- Hidden inputs para enviar con el formulario --}}
        <template x-for="id in selectedWorkerIds" :key="'hidden-' + id">
            <input type="hidden" name="trabajadores[]" :value="id">
        </template>
    </div>
</div>

@push('scripts')
<script>
function trabajadorSelector() {
    return {
        obraId: null,
        loading: false,
        cuadrillas: [],
        obraTrabajadores: [],
        otrosTrabajadores: [],
        selectedWorkerIds: [],
        activeTab: 'cuadrillas',

        initSelector() {
            const preselected = JSON.parse(this.$el.dataset.preselected || '[]');
            this.selectedWorkerIds = preselected.map(Number);

            // Escuchar cambio de obra desde el formulario
            window.addEventListener('parte-obra-changed', (e) => {
                this.loadTrabajadores(e.detail.obraId);
            });

            // Auto-cargar si hay obra pre-seleccionada
            const obraId = this.$el.dataset.obraId;
            if (obraId) {
                this.loadTrabajadores(obraId);
            }
        },

        async loadTrabajadores(obraId) {
            this.obraId = obraId;
            if (!obraId) {
                this.cuadrillas = [];
                this.obraTrabajadores = [];
                this.otrosTrabajadores = [];
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/partes-diarios/ajax/trabajadores-obra/${obraId}`);
                if (!response.ok) throw new Error('Error de red');
                const data = await response.json();
                this.cuadrillas = data.cuadrillas;
                this.obraTrabajadores = data.obra_trabajadores;
                this.otrosTrabajadores = data.otros_trabajadores;
            } catch (e) {
                console.error('Error cargando trabajadores:', e);
            } finally {
                this.loading = false;
            }
        },

        isSelected(workerId) {
            return this.selectedWorkerIds.includes(workerId);
        },

        toggleWorker(workerId) {
            const idx = this.selectedWorkerIds.indexOf(workerId);
            if (idx >= 0) {
                this.selectedWorkerIds.splice(idx, 1);
            } else {
                this.selectedWorkerIds.push(workerId);
            }
        },

        toggleCuadrilla(cuadrilla) {
            const memberIds = cuadrilla.trabajadores.map(t => t.id);
            const allSelected = memberIds.every(id => this.selectedWorkerIds.includes(id));

            if (allSelected) {
                // Deseleccionar todos los miembros
                this.selectedWorkerIds = this.selectedWorkerIds.filter(id => !memberIds.includes(id));
            } else {
                // Seleccionar todos los miembros
                memberIds.forEach(id => {
                    if (!this.selectedWorkerIds.includes(id)) {
                        this.selectedWorkerIds.push(id);
                    }
                });
            }
        },

        isCuadrillaFullySelected(cuadrilla) {
            if (cuadrilla.trabajadores.length === 0) return false;
            return cuadrilla.trabajadores.every(t => this.selectedWorkerIds.includes(t.id));
        },

        isCuadrillaPartiallySelected(cuadrilla) {
            const selected = cuadrilla.trabajadores.filter(t => this.selectedWorkerIds.includes(t.id));
            return selected.length > 0 && selected.length < cuadrilla.trabajadores.length;
        },
    };
}
</script>
@endpush
