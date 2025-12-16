<x-app-layout>
    <x-agromarket.page-header
        title="Editar Proyecto"
        description="Modifica la información de tu proyecto"
    />

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
            <strong><i class="fas fa-exclamation-circle"></i> Errores de validación:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('farmer.projects.update', $proyecto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-info-circle"></i> Información General
            </h3>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Categoría del Proyecto"
                        name="categoria_id"
                        type="select"
                        icon="fas fa-folder"
                        required
                    >
                        <option value="">Selecciona una categoría...</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $proyecto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </x-agromarket.form-group>
                </div>

                <div>
                    <x-agromarket.form-group
                        label="Nombre del Proyecto"
                        name="nombre"
                        type="text"
                        icon="fas fa-seedling"
                        :value="old('nombre', $proyecto->nombre)"
                        required
                    />
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <x-agromarket.form-group
                    label="Descripción del Proyecto"
                    name="descripcion"
                    type="textarea"
                    icon="fas fa-align-left"
                    :value="old('descripcion', $proyecto->descripcion)"
                    required
                />
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Ubicación"
                        name="ubicacion"
                        type="text"
                        icon="fas fa-map-marker-alt"
                        :value="old('ubicacion', $proyecto->ubicacion)"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Coordenadas (Opcional)"
                        name="coordenadas"
                        type="text"
                        icon="fas fa-globe"
                        :value="old('coordenadas', $proyecto->coordenadas)"
                    />
                </div>
            </div>
        </div>

        <!-- Información Financiera -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-dollar-sign"></i> Información Financiera
            </h3>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Monto Objetivo"
                        name="monto_objetivo"
                        type="number"
                        icon="fas fa-money-bill-wave"
                        :value="old('monto_objetivo', $proyecto->monto_objetivo)"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Inversión Mínima"
                        name="inversion_minima"
                        type="number"
                        icon="fas fa-coins"
                        :value="old('inversion_minima', $proyecto->inversion_minima)"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Inversión Máxima (Opcional)"
                        name="inversion_maxima"
                        type="number"
                        icon="fas fa-coins"
                        :value="old('inversion_maxima', $proyecto->inversion_maxima)"
                    />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="ROI Anual (%)"
                        name="roi_anual"
                        type="number"
                        icon="fas fa-percentage"
                        :value="old('roi_anual', $proyecto->roi_anual)"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Duración (Meses)"
                        name="duracion_meses"
                        type="number"
                        icon="fas fa-calendar-alt"
                        :value="old('duracion_meses', $proyecto->duracion_meses)"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Período Dividendos (Días)"
                        name="periodo_dividendos_dias"
                        type="number"
                        icon="fas fa-clock"
                        :value="old('periodo_dividendos_dias', $proyecto->periodo_dividendos_dias)"
                        required
                    />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Período Cosecha (Meses - Opcional)"
                        name="periodo_cosecha_meses"
                        type="number"
                        icon="fas fa-leaf"
                        :value="old('periodo_cosecha_meses', $proyecto->periodo_cosecha_meses)"
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Nivel de Riesgo"
                        name="nivel_riesgo"
                        type="select"
                        icon="fas fa-exclamation-triangle"
                        required
                    >
                        <option value="">Selecciona...</option>
                        <option value="bajo" {{ old('nivel_riesgo', $proyecto->nivel_riesgo) == 'bajo' ? 'selected' : '' }}>Bajo</option>
                        <option value="medio" {{ old('nivel_riesgo', $proyecto->nivel_riesgo) == 'medio' ? 'selected' : '' }}>Medio</option>
                        <option value="alto" {{ old('nivel_riesgo', $proyecto->nivel_riesgo) == 'alto' ? 'selected' : '' }}>Alto</option>
                    </x-agromarket.form-group>
                </div>
            </div>
        </div>

        <!-- Fechas -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-calendar"></i> Fechas Importantes
            </h3>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Fecha Inicio Recaudación"
                        name="fecha_inicio_recaudacion"
                        type="date"
                        icon="fas fa-calendar-plus"
                        :value="old('fecha_inicio_recaudacion', $proyecto->fecha_inicio_recaudacion?->format('Y-m-d'))"
                        required
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Fecha Cierre Recaudación"
                        name="fecha_cierre_recaudacion"
                        type="date"
                        icon="fas fa-calendar-minus"
                        :value="old('fecha_cierre_recaudacion', $proyecto->fecha_cierre_recaudacion?->format('Y-m-d'))"
                        required
                    />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
                <div>
                    <x-agromarket.form-group
                        label="Fecha Inicio Proyecto (Opcional)"
                        name="fecha_inicio_proyecto"
                        type="date"
                        icon="fas fa-play-circle"
                        :value="old('fecha_inicio_proyecto', $proyecto->fecha_inicio_proyecto?->format('Y-m-d'))"
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Fecha Fin Proyecto (Opcional)"
                        name="fecha_fin_proyecto"
                        type="date"
                        icon="fas fa-stop-circle"
                        :value="old('fecha_fin_proyecto', $proyecto->fecha_fin_proyecto?->format('Y-m-d'))"
                    />
                </div>
                <div>
                    <x-agromarket.form-group
                        label="Primer Dividendo (Opcional)"
                        name="fecha_primer_dividendo"
                        type="date"
                        icon="fas fa-gift"
                        :value="old('fecha_primer_dividendo', $proyecto->fecha_primer_dividendo?->format('Y-m-d'))"
                    />
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-times"
                onclick="window.location.href='{{ route('farmer.projects.show', $proyecto->id) }}'; return false;"
            >
                Cancelar
            </x-agromarket.button>

            <x-agromarket.button
                variant="primary"
                icon="fas fa-save"
                type="submit"
            >
                Actualizar Proyecto
            </x-agromarket.button>
        </div>
    </form>

    <!-- Link a gestión de archivos -->
    <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem; border: 1px solid #c8e6c9;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="margin: 0 0 0.5rem 0; color: #2D5A27;">
                    <i class="fas fa-images"></i> ¿Necesitas subir imágenes o documentos?
                </h4>
                <p style="margin: 0; color: #666; font-size: 0.875rem;">
                    Gestiona las imágenes y documentos de tu proyecto en una página dedicada.
                </p>
            </div>
            <x-agromarket.button
                variant="primary"
                icon="fas fa-folder-open"
                onclick="window.location.href='{{ route('farmer.projects.files', $proyecto->id) }}'"
            >
                Gestionar Archivos
            </x-agromarket.button>
        </div>
    </div>
</x-app-layout>
