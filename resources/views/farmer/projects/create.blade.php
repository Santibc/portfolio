<x-app-layout>
    <x-agromarket.page-header
        title="Crear Nuevo Proyecto"
        description="Fase 1 de 3: Datos Basicos del Proyecto"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('farmer.projects.index') }}'"
            >
                Volver
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Indicador de Pasos -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0;">
            <!-- Paso 1 -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #4A7C59; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    1
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #4A7C59;">Datos Basicos</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Proyecto y Cultivo</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #e9ecef; margin: 0 1rem;"></div>
            <!-- Paso 2 -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; color: #6c757d; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    2
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #6c757d;">Evaluacion Tecnica</div>
                    <div style="font-size: 0.8rem; color: #adb5bd;">Experiencia y Capacidad</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #e9ecef; margin: 0 1rem;"></div>
            <!-- Paso 3 -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; color: #6c757d; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    3
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #6c757d;">Evaluacion Financiera</div>
                    <div style="font-size: 0.8rem; color: #adb5bd;">Inversion y Proyecciones</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('farmer.projects.store') }}" method="POST" id="phase1Form">
        @csrf

        <!-- Datos del Proyecto -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-seedling"></i> Datos del Proyecto
            </h3>

            <x-agromarket.form-group
                label="Categoria de Inversion"
                name="categoria_id"
                type="select"
                icon="fas fa-tags"
                :options="$categorias->pluck('nombre', 'id')->prepend('Seleccione una categoria...', '')"
                required
            ></x-agromarket.form-group>

            <x-agromarket.form-group
                label="Nombre del Proyecto"
                name="nombre"
                icon="fas fa-project-diagram"
                placeholder="Ej: Cultivo de Cafe Organico - Finca La Esperanza"
                :value="old('nombre')"
                required
            ></x-agromarket.form-group>

            <x-agromarket.form-group
                label="Tipo de Cultivo"
                name="tipo_cultivo"
                icon="fas fa-leaf"
                placeholder="Ej: Cafe, Cacao, Aguacate Hass..."
                :value="old('tipo_cultivo')"
                required
            ></x-agromarket.form-group>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <x-agromarket.form-group
                    label="Area (Hectareas)"
                    name="area_hectareas"
                    type="number"
                    icon="fas fa-ruler-combined"
                    placeholder="10.5"
                    step="0.1"
                    min="0.1"
                    :value="old('area_hectareas')"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Etapa del Cultivo"
                    name="etapa_cultivo"
                    type="select"
                    icon="fas fa-chart-line"
                    :options="[
                        '' => 'Seleccione...',
                        'siembra' => 'Siembra',
                        'crecimiento' => 'Crecimiento',
                        'cosecha' => 'Cosecha',
                        'transformacion' => 'Transformacion',
                        'otro' => 'Otro'
                    ]"
                    required
                ></x-agromarket.form-group>
            </div>

            <x-agromarket.form-group
                label="Ano de Inicio del Cultivo"
                name="ano_inicio_cultivo"
                type="number"
                icon="fas fa-calendar-alt"
                placeholder="{{ date('Y') }}"
                min="1990"
                max="{{ date('Y') + 1 }}"
                :value="old('ano_inicio_cultivo')"
            ></x-agromarket.form-group>

            <x-agromarket.form-group
                label="Ubicacion del Proyecto"
                name="ubicacion"
                icon="fas fa-map-marker-alt"
                placeholder="Vereda, Municipio, Departamento"
                :value="old('ubicacion')"
                required
            ></x-agromarket.form-group>

            <x-agromarket.form-group
                label="Coordenadas GPS"
                name="coordenadas"
                icon="fas fa-globe"
                placeholder="Ej: 4.6097, -74.0817"
                :value="old('coordenadas')"
                help="Opcional. Formato: latitud, longitud"
            ></x-agromarket.form-group>

            <x-agromarket.form-group
                label="Descripcion del Proyecto"
                name="descripcion"
                type="textarea"
                icon="fas fa-align-left"
                placeholder="Descripcion detallada del proyecto agricola..."
                rows="4"
                :value="old('descripcion')"
                required
            ></x-agromarket.form-group>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <x-agromarket.form-group
                    label="Meta de Financiamiento ($)"
                    name="meta_financiamiento"
                    type="number"
                    icon="fas fa-dollar-sign"
                    placeholder="10000000"
                    min="100000"
                    :value="old('meta_financiamiento')"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Plazo (Meses)"
                    name="plazo_meses"
                    type="number"
                    icon="fas fa-clock"
                    placeholder="12"
                    min="1"
                    max="240"
                    :value="old('plazo_meses')"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="ROI Proyectado (%)"
                    name="roi_proyectado"
                    type="number"
                    icon="fas fa-percentage"
                    placeholder="15"
                    min="0"
                    max="100"
                    step="0.1"
                    :value="old('roi_proyectado')"
                    required
                ></x-agromarket.form-group>
            </div>
        </div>

        <!-- Botones de Accion -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-times"
                type="button"
                onclick="window.location.href='{{ route('farmer.projects.index') }}'"
            >
                Cancelar
            </x-agromarket.button>

            <x-agromarket.button
                variant="primary"
                icon="fas fa-arrow-right"
                type="submit"
            >
                Continuar a Fase 2
            </x-agromarket.button>
        </div>
    </form>

    @push('scripts')
    <script>
        // Manejar errores
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#4A7C59'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#4A7C59'
            });
        @endif
    </script>
    @endpush

    @push('styles')
    <style>
        div[style*="grid-template-columns: 1fr 1fr 1fr"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }

        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
        }

        @media (max-width: 768px) {
            div[style*="display: flex; align-items: center; justify-content: center"] {
                flex-wrap: wrap;
                gap: 1rem !important;
            }

            div[style*="max-width: 100px"] {
                display: none;
            }
        }
    </style>
    @endpush
</x-app-layout>
