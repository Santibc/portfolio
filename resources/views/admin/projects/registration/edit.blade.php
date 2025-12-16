<x-app-layout>
    <x-agromarket.page-header
        title="Editar Proyecto"
        description="{{ $proyecto->nombre }} - {{ $proyecto->codigo }}"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.projects.registration.show', $proyecto) }}'"
            >
                Volver
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Tabs de Navegacion -->
    <div style="background: white; border-radius: 12px 12px 0 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 0;">
        <div style="display: flex; border-bottom: 2px solid #f0f0f0;">
            <button type="button" class="tab-btn active" data-tab="fase1" style="flex: 1; padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 600; color: #4A7C59; border-bottom: 3px solid #4A7C59; margin-bottom: -2px;">
                <i class="fas fa-seedling"></i> Fase 1: Datos Basicos
            </button>
            <button type="button" class="tab-btn" data-tab="fase2" style="flex: 1; padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 500; color: #6c757d;">
                <i class="fas fa-user-tie"></i> Fase 2: Evaluacion Tecnica
            </button>
            <button type="button" class="tab-btn" data-tab="fase3" style="flex: 1; padding: 1rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 500; color: #6c757d;">
                <i class="fas fa-chart-line"></i> Fase 3: Evaluacion Financiera
            </button>
        </div>
    </div>

    <!-- Contenido de Fase 1 -->
    <div id="tab-fase1" class="tab-content" style="display: block;">
        <form action="{{ route('admin.projects.registration.update', $proyecto) }}" method="POST" id="fase1Form">
            @csrf
            @method('PUT')
            <input type="hidden" name="fase" value="1">

            <div style="background: white; padding: 2rem; border-radius: 0 0 12px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-seedling"></i> Datos del Proyecto
                </h3>

                <x-agromarket.form-group
                    label="Categoria de Inversion"
                    name="categoria_id"
                    type="select"
                    icon="fas fa-tags"
                    :options="$categorias->pluck('nombre', 'id')->prepend('Seleccione una categoria...', '')"
                    :value="old('categoria_id', $proyecto->categoria_id)"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Nombre del Proyecto"
                    name="nombre"
                    icon="fas fa-project-diagram"
                    placeholder="Ej: Cultivo de Cafe Organico"
                    :value="old('nombre', $proyecto->nombre)"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Tipo de Cultivo"
                    name="tipo_cultivo"
                    icon="fas fa-leaf"
                    placeholder="Ej: Cafe, Cacao, Aguacate Hass..."
                    :value="old('tipo_cultivo', $proyecto->tipo_cultivo)"
                    required
                ></x-agromarket.form-group>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <x-agromarket.form-group
                        label="Area (Hectareas)"
                        name="area_hectareas"
                        type="number"
                        icon="fas fa-ruler-combined"
                        step="0.1"
                        min="0.1"
                        :value="old('area_hectareas', $proyecto->area_hectareas)"
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
                        :value="old('etapa_cultivo', $proyecto->etapa_cultivo)"
                        required
                    ></x-agromarket.form-group>
                </div>

                <x-agromarket.form-group
                    label="Ano de Inicio del Cultivo"
                    name="ano_inicio_cultivo"
                    type="number"
                    icon="fas fa-calendar-alt"
                    min="1990"
                    max="{{ date('Y') + 1 }}"
                    :value="old('ano_inicio_cultivo', $proyecto->ano_inicio_cultivo)"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Ubicacion del Proyecto"
                    name="ubicacion"
                    icon="fas fa-map-marker-alt"
                    placeholder="Vereda, Municipio, Departamento"
                    :value="old('ubicacion', $proyecto->ubicacion)"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Coordenadas GPS"
                    name="coordenadas"
                    icon="fas fa-globe"
                    placeholder="Ej: 4.6097, -74.0817"
                    :value="old('coordenadas', $proyecto->coordenadas)"
                    help="Opcional. Formato: latitud, longitud"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Descripcion del Proyecto"
                    name="descripcion"
                    type="textarea"
                    icon="fas fa-align-left"
                    rows="4"
                    :value="old('descripcion', $proyecto->descripcion)"
                    required
                ></x-agromarket.form-group>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <x-agromarket.form-group
                        label="Meta de Financiamiento ($)"
                        name="meta_financiamiento"
                        type="number"
                        icon="fas fa-dollar-sign"
                        min="100000"
                        :value="old('meta_financiamiento', $proyecto->monto_objetivo)"
                        required
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Plazo (Meses)"
                        name="plazo_meses"
                        type="number"
                        icon="fas fa-clock"
                        min="1"
                        max="240"
                        :value="old('plazo_meses', $proyecto->duracion_meses)"
                        required
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="ROI Proyectado (%)"
                        name="roi_proyectado"
                        type="number"
                        icon="fas fa-percentage"
                        min="0"
                        max="100"
                        step="0.1"
                        :value="old('roi_proyectado', $proyecto->roi_anual)"
                        required
                    ></x-agromarket.form-group>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
                    <x-agromarket.button variant="primary" icon="fas fa-save" type="submit">
                        Guardar Fase 1
                    </x-agromarket.button>
                </div>
            </div>
        </form>
    </div>

    <!-- Contenido de Fase 2 -->
    <div id="tab-fase2" class="tab-content" style="display: none;">
        <form action="{{ route('admin.projects.registration.phase2.store', $proyecto) }}" method="POST" id="fase2Form">
            @csrf

            @php
                $perfil = $proyecto->agricultor->perfilAgricultor ?? null;
            @endphp

            <div style="background: white; padding: 2rem; border-radius: 0 0 12px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <!-- Tipo de Persona -->
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-user-tie"></i> Tipo de Persona
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Tipo de Persona"
                        name="tipo_persona"
                        type="select"
                        icon="fas fa-user"
                        :options="['natural' => 'Persona Natural', 'juridica' => 'Persona Juridica']"
                        :value="old('tipo_persona', $perfil->tipo_persona ?? 'natural')"
                        required
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Nombre Empresa (si aplica)"
                        name="nombre_empresa"
                        icon="fas fa-building"
                        :value="old('nombre_empresa', $perfil->nombre_empresa ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="NIT (si aplica)"
                        name="nit"
                        icon="fas fa-id-card"
                        :value="old('nit', $perfil->nit ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Representante Legal"
                        name="representante_legal"
                        icon="fas fa-user-tie"
                        :value="old('representante_legal', $perfil->representante_legal ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <!-- Experiencia -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-history"></i> Experiencia Agricola
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Anos de Experiencia"
                        name="anos_experiencia"
                        type="number"
                        icon="fas fa-calendar"
                        min="0"
                        :value="old('anos_experiencia', $perfil->anos_experiencia ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Cantidad de Cosechas"
                        name="cantidad_cosechas"
                        type="number"
                        icon="fas fa-seedling"
                        min="0"
                        :value="old('cantidad_cosechas', $perfil->cantidad_cosechas ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Produccion Promedio"
                        name="produccion_promedio"
                        icon="fas fa-weight"
                        placeholder="Ej: 500 kg/ha"
                        :value="old('produccion_promedio', $perfil->produccion_promedio ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <x-agromarket.form-group
                    label="Formacion y Capacitaciones"
                    name="formacion_capacitaciones"
                    type="textarea"
                    icon="fas fa-graduation-cap"
                    rows="2"
                    placeholder="Cursos, certificaciones, talleres..."
                    :value="old('formacion_capacitaciones', $perfil->formacion_capacitaciones ?? '')"
                ></x-agromarket.form-group>

                <!-- Equipo de Trabajo -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-users"></i> Equipo de Trabajo
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Personas Trabajando"
                        name="num_personas_trabajando"
                        type="number"
                        icon="fas fa-users"
                        min="0"
                        :value="old('num_personas_trabajando', $perfil->num_personas_trabajando ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Nivel de Tecnificacion"
                        name="nivel_tecnificacion"
                        type="select"
                        icon="fas fa-cogs"
                        :options="['' => 'Seleccione...', 'manual' => 'Manual', 'semi_tecnificado' => 'Semi-tecnificado', 'tecnificado' => 'Tecnificado']"
                        :value="old('nivel_tecnificacion', $perfil->nivel_tecnificacion ?? '')"
                    ></x-agromarket.form-group>

                    <div style="display: flex; align-items: center; padding-top: 1.5rem;">
                        <input type="checkbox" name="familia_trabaja_cultivo" id="familia_trabaja_cultivo" value="1" {{ old('familia_trabaja_cultivo', $perfil->familia_trabaja_cultivo ?? false) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        <label for="familia_trabaja_cultivo" style="color: #495057;">Familia trabaja en cultivo</label>
                    </div>
                </div>

                <x-agromarket.form-group
                    label="Roles Principales"
                    name="roles_principales"
                    type="textarea"
                    icon="fas fa-tasks"
                    rows="2"
                    placeholder="Administrador, cosechadores, empacadores..."
                    :value="old('roles_principales', $perfil->roles_principales ?? '')"
                ></x-agromarket.form-group>

                <!-- Estado del Predio -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-home"></i> Estado del Predio
                </h3>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="tiene_riego" id="tiene_riego" value="1" {{ old('tiene_riego', $perfil->tiene_riego ?? false) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        <label for="tiene_riego" style="color: #495057;"><i class="fas fa-tint"></i> Riego</label>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="tiene_bodega" id="tiene_bodega" value="1" {{ old('tiene_bodega', $perfil->tiene_bodega ?? false) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        <label for="tiene_bodega" style="color: #495057;"><i class="fas fa-warehouse"></i> Bodega</label>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="tiene_transformacion" id="tiene_transformacion" value="1" {{ old('tiene_transformacion', $perfil->tiene_transformacion ?? false) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        <label for="tiene_transformacion" style="color: #495057;"><i class="fas fa-industry"></i> Transformacion</label>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="tiene_transporte" id="tiene_transporte" value="1" {{ old('tiene_transporte', $perfil->tiene_transporte ?? false) ? 'checked' : '' }} style="margin-right: 0.5rem;">
                        <label for="tiene_transporte" style="color: #495057;"><i class="fas fa-truck"></i> Transporte</label>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Accesibilidad"
                        name="accesibilidad"
                        type="textarea"
                        icon="fas fa-road"
                        rows="2"
                        placeholder="Vias de acceso, distancia a centros urbanos..."
                        :value="old('accesibilidad', $perfil->accesibilidad ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgos Naturales"
                        name="riesgos_naturales"
                        type="textarea"
                        icon="fas fa-exclamation-triangle"
                        rows="2"
                        placeholder="Inundaciones, heladas, plagas..."
                        :value="old('riesgos_naturales', $perfil->riesgos_naturales ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <!-- Objetivos del Proyecto -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-bullseye"></i> Objetivos del Proyecto
                </h3>

                <x-agromarket.form-group
                    label="Objetivo del Proyecto"
                    name="objetivo_proyecto"
                    type="textarea"
                    icon="fas fa-bullseye"
                    rows="3"
                    placeholder="Que se espera lograr con el financiamiento..."
                    :value="old('objetivo_proyecto', $proyecto->objetivo_proyecto ?? '')"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Detalle del Proceso Productivo"
                    name="detalle_proceso_productivo"
                    type="textarea"
                    icon="fas fa-clipboard-list"
                    rows="3"
                    placeholder="Paso a paso del proceso de produccion..."
                    :value="old('detalle_proceso_productivo', $proyecto->detalle_proceso_productivo ?? '')"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Cronograma Estimado"
                    name="cronograma_estimado"
                    type="textarea"
                    icon="fas fa-calendar-alt"
                    rows="3"
                    placeholder="Tiempos estimados por etapa..."
                    :value="old('cronograma_estimado', $proyecto->cronograma_estimado ?? '')"
                ></x-agromarket.form-group>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
                    <x-agromarket.button variant="primary" icon="fas fa-save" type="submit">
                        Guardar Fase 2
                    </x-agromarket.button>
                </div>
            </div>
        </form>
    </div>

    <!-- Contenido de Fase 3 -->
    <div id="tab-fase3" class="tab-content" style="display: none;">
        <form action="{{ route('admin.projects.registration.phase3.store', $proyecto) }}" method="POST" id="fase3Form">
            @csrf

            @php
                $datosFinancieros = $proyecto->datos_financieros ?? [];
                $inversion = $datosFinancieros['inversion_solicitada'] ?? [];
                $proyecciones = $datosFinancieros['proyecciones'] ?? [];
                $riesgos = $datosFinancieros['riesgos'] ?? [];
            @endphp

            <div style="background: white; padding: 2rem; border-radius: 0 0 12px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <!-- Desglose de Inversion -->
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-coins"></i> Desglose de Inversion Solicitada
                </h3>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Insumos ($)"
                        name="inversion_insumos"
                        type="number"
                        icon="fas fa-box"
                        min="0"
                        :value="old('inversion_insumos', $inversion['insumos'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Mano de Obra ($)"
                        name="inversion_mano_obra"
                        type="number"
                        icon="fas fa-hard-hat"
                        min="0"
                        :value="old('inversion_mano_obra', $inversion['mano_obra'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Equipos ($)"
                        name="inversion_equipos"
                        type="number"
                        icon="fas fa-tools"
                        min="0"
                        :value="old('inversion_equipos', $inversion['equipos'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Transporte ($)"
                        name="inversion_transporte"
                        type="number"
                        icon="fas fa-truck"
                        min="0"
                        :value="old('inversion_transporte', $inversion['transporte'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Certificaciones ($)"
                        name="inversion_certificaciones"
                        type="number"
                        icon="fas fa-certificate"
                        min="0"
                        :value="old('inversion_certificaciones', $inversion['certificaciones'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Empaques ($)"
                        name="inversion_empaques"
                        type="number"
                        icon="fas fa-box-open"
                        min="0"
                        :value="old('inversion_empaques', $inversion['empaques'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Marketing ($)"
                        name="inversion_marketing"
                        type="number"
                        icon="fas fa-bullhorn"
                        min="0"
                        :value="old('inversion_marketing', $inversion['marketing'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <!-- Proyecciones -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-chart-line"></i> Proyecciones Financieras
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Produccion Estimada"
                        name="produccion_estimada"
                        icon="fas fa-balance-scale"
                        placeholder="Ej: 5000 kg/ciclo"
                        :value="old('produccion_estimada', $proyecciones['produccion_estimada'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Precio Venta Estimado ($)"
                        name="precio_venta_estimado"
                        type="number"
                        icon="fas fa-tag"
                        min="0"
                        :value="old('precio_venta_estimado', $proyecciones['precio_venta_estimado'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Margen de Ganancia (%)"
                        name="margen_ganancia"
                        type="number"
                        icon="fas fa-percentage"
                        min="0"
                        max="100"
                        step="0.1"
                        :value="old('margen_ganancia', $proyecciones['margen_ganancia'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Canales de Venta Actuales"
                        name="canales_venta_actuales"
                        type="textarea"
                        icon="fas fa-store"
                        rows="2"
                        :value="old('canales_venta_actuales', $proyecciones['canales_venta_actuales'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Canales de Venta Deseados"
                        name="canales_venta_deseados"
                        type="textarea"
                        icon="fas fa-store-alt"
                        rows="2"
                        :value="old('canales_venta_deseados', $proyecciones['canales_venta_deseados'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <x-agromarket.form-group
                    label="Proyeccion de Ingresos"
                    name="proyeccion_ingresos"
                    type="textarea"
                    icon="fas fa-chart-bar"
                    rows="2"
                    placeholder="Ingresos esperados por periodo..."
                    :value="old('proyeccion_ingresos', $proyecciones['proyeccion_ingresos'] ?? '')"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Punto de Equilibrio"
                    name="punto_equilibrio"
                    icon="fas fa-balance-scale-right"
                    placeholder="Ej: 3000 kg/ciclo"
                    :value="old('punto_equilibrio', $proyecciones['punto_equilibrio'] ?? '')"
                ></x-agromarket.form-group>

                <!-- Riesgos -->
                <h3 style="margin: 2rem 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-exclamation-triangle"></i> Analisis de Riesgos
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Riesgo de Plagas"
                        name="riesgo_plagas"
                        type="textarea"
                        icon="fas fa-bug"
                        rows="2"
                        placeholder="Principales plagas y plan de mitigacion..."
                        :value="old('riesgo_plagas', $riesgos['plagas'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgo Climatico"
                        name="riesgo_clima"
                        type="textarea"
                        icon="fas fa-cloud-sun-rain"
                        rows="2"
                        placeholder="Riesgos climaticos y medidas..."
                        :value="old('riesgo_clima', $riesgos['clima'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.form-group
                        label="Riesgo de Competencia"
                        name="riesgo_competencia"
                        type="textarea"
                        icon="fas fa-users"
                        rows="2"
                        :value="old('riesgo_competencia', $riesgos['competencia'] ?? '')"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgo de Acceso a Mercados"
                        name="riesgo_acceso_mercados"
                        type="textarea"
                        icon="fas fa-shopping-cart"
                        rows="2"
                        :value="old('riesgo_acceso_mercados', $riesgos['acceso_mercados'] ?? '')"
                    ></x-agromarket.form-group>
                </div>

                <x-agromarket.form-group
                    label="Riesgo de Regulaciones"
                    name="riesgo_regulaciones"
                    type="textarea"
                    icon="fas fa-gavel"
                    rows="2"
                    placeholder="Posibles cambios regulatorios..."
                    :value="old('riesgo_regulaciones', $riesgos['regulaciones'] ?? '')"
                ></x-agromarket.form-group>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
                    <x-agromarket.button variant="primary" icon="fas fa-save" type="submit">
                        Guardar Fase 3
                    </x-agromarket.button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;

                // Update buttons
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.color = '#6c757d';
                    b.style.fontWeight = '500';
                    b.style.borderBottom = 'none';
                });

                this.classList.add('active');
                this.style.color = '#4A7C59';
                this.style.fontWeight = '600';
                this.style.borderBottom = '3px solid #4A7C59';

                // Update content
                document.querySelectorAll('.tab-content').forEach(c => {
                    c.style.display = 'none';
                });
                document.getElementById('tab-' + tabId).style.display = 'block';
            });
        });

        // Alerts
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
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

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#4A7C59'
            });
        @endif
    </script>
    @endpush

    @push('styles')
    <style>
        .tab-btn:hover {
            background: #f8f9fa;
        }

        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: repeat(4, 1fr)"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: 1fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
