<x-app-layout>
    <x-agromarket.page-header
        title="Evaluación Técnica"
        description="Fase 2 de 3: {{ $proyecto->nombre }}"
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

    <!-- Indicador de Pasos -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0;">
            <!-- Paso 1 Completado -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    <i class="fas fa-check"></i>
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #28a745;">Datos Básicos</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Completado</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #28a745; margin: 0 1rem;"></div>
            <!-- Paso 2 Actual -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #4A7C59; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    2
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #4A7C59;">Evaluación Técnica</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">En progreso</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #e9ecef; margin: 0 1rem;"></div>
            <!-- Paso 3 Pendiente -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; color: #6c757d; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    3
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #6c757d;">Evaluación Financiera</div>
                    <div style="font-size: 0.8rem; color: #adb5bd;">Pendiente</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('admin.projects.registration.phase2.store', $proyecto) }}" method="POST" id="phase2Form">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Columna Izquierda -->
            <div>
                <!-- Tipo de Persona -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-building"></i> Tipo de Persona
                    </h3>

                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 2px solid {{ ($perfil->tipo_persona ?? 'natural') == 'natural' ? '#4A7C59' : '#e9ecef' }}; border-radius: 8px; cursor: pointer; flex: 1; background: {{ ($perfil->tipo_persona ?? 'natural') == 'natural' ? '#f0f7f3' : 'white' }};">
                            <input type="radio" name="tipo_persona" value="natural" {{ ($perfil->tipo_persona ?? 'natural') == 'natural' ? 'checked' : '' }} onchange="toggleTipoPersona()">
                            <div>
                                <div style="font-weight: 500;">Persona Natural</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">Agricultor individual</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 2px solid {{ ($perfil->tipo_persona ?? 'natural') == 'juridica' ? '#4A7C59' : '#e9ecef' }}; border-radius: 8px; cursor: pointer; flex: 1; background: {{ ($perfil->tipo_persona ?? 'natural') == 'juridica' ? '#f0f7f3' : 'white' }};">
                            <input type="radio" name="tipo_persona" value="juridica" {{ ($perfil->tipo_persona ?? '') == 'juridica' ? 'checked' : '' }} onchange="toggleTipoPersona()">
                            <div>
                                <div style="font-weight: 500;">Persona Jurídica</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">Empresa o asociación</div>
                            </div>
                        </label>
                    </div>

                    <!-- Campos Persona Jurídica -->
                    <div id="camposJuridica" style="display: {{ ($perfil->tipo_persona ?? '') == 'juridica' ? 'block' : 'none' }};">
                        <x-agromarket.form-group
                            label="Nombre de la Empresa"
                            name="nombre_empresa"
                            icon="fas fa-building"
                            placeholder="Razón social"
                            :value="$perfil->nombre_empresa ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="NIT"
                            name="nit"
                            icon="fas fa-id-card"
                            placeholder="900.123.456-7"
                            :value="$perfil->nit ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Representante Legal"
                            name="representante_legal"
                            icon="fas fa-user-tie"
                            placeholder="Nombre del representante"
                            :value="$perfil->representante_legal ?? ''"
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Dirección de la Finca"
                        name="direccion_finca"
                        type="textarea"
                        icon="fas fa-map-marker-alt"
                        placeholder="Ubicación detallada del predio"
                        :value="$perfil->direccion_finca ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <div style="margin-top: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="cultivo_asegurado" value="1" {{ ($perfil->cultivo_asegurado ?? false) ? 'checked' : '' }}
                                   style="width: 20px; height: 20px; accent-color: #4A7C59;">
                            <span style="font-weight: 500;">El cultivo está asegurado</span>
                        </label>
                    </div>
                </div>

                <!-- Experiencia -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-graduation-cap"></i> Experiencia del Agricultor
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="Años de Experiencia"
                            name="anos_experiencia"
                            type="number"
                            icon="fas fa-clock"
                            placeholder="5"
                            min="0"
                            max="100"
                            :value="$perfil->anos_experiencia ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Cantidad de Cosechas"
                            name="cantidad_cosechas"
                            type="number"
                            icon="fas fa-seedling"
                            placeholder="10"
                            min="0"
                            :value="$perfil->cantidad_cosechas ?? ''"
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Formación y Capacitaciones"
                        name="formacion_capacitaciones"
                        type="textarea"
                        icon="fas fa-book"
                        placeholder="Cursos, certificaciones, talleres..."
                        :value="$perfil->formacion_capacitaciones ?? ''"
                        rows="3"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Producción Promedio por Cosecha"
                        name="produccion_promedio"
                        icon="fas fa-chart-bar"
                        placeholder="Ej: 50 cargas de café por hectárea"
                        :value="$perfil->produccion_promedio ?? ''"
                    ></x-agromarket.form-group>
                </div>

                <!-- Familia -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-users"></i> Composición Familiar
                        <button type="button" onclick="agregarFamiliar()" style="float: right; background: #4A7C59; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </h3>

                    <div id="familiaContainer">
                        @forelse($familia ?? [] as $index => $familiar)
                            <div class="familiar-item" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; position: relative;">
                                <button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #dc3545; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-size: 0.75rem;">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 0.75rem;">
                                    <select name="familia[{{ $index }}][parentesco]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                                        <option value="">Parentesco</option>
                                        @foreach(['esposa' => 'Esposa', 'esposo' => 'Esposo', 'hijo' => 'Hijo', 'hija' => 'Hija', 'padre' => 'Padre', 'madre' => 'Madre', 'hermano' => 'Hermano', 'hermana' => 'Hermana', 'otro' => 'Otro'] as $value => $label)
                                            <option value="{{ $value }}" {{ ($familiar->parentesco ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="familia[{{ $index }}][nombre]" placeholder="Nombre" value="{{ $familiar->nombre ?? '' }}" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                                    <input type="number" name="familia[{{ $index }}][edad]" placeholder="Edad" value="{{ $familiar->edad ?? '' }}" min="0" max="150" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                                    <select name="familia[{{ $index }}][nivel_educativo]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                                        <option value="">Nivel educativo</option>
                                        @foreach(['ninguno' => 'Ninguno', 'primaria' => 'Primaria', 'secundaria' => 'Secundaria', 'tecnico' => 'Técnico', 'profesional' => 'Profesional', 'posgrado' => 'Posgrado'] as $value => $label)
                                            <option value="{{ $value }}" {{ ($familiar->nivel_educativo ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <select name="familia[{{ $index }}][estudia_actualmente]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                                        <option value="">¿Estudia?</option>
                                        <option value="si" {{ ($familiar->estudia_actualmente ?? '') == 'si' ? 'selected' : '' }}>Sí</option>
                                        <option value="no" {{ ($familiar->estudia_actualmente ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="estudio_aplazado" {{ ($familiar->estudia_actualmente ?? '') == 'estudio_aplazado' ? 'selected' : '' }}>Aplazado</option>
                                    </select>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem;">
                                        <input type="checkbox" name="familia[{{ $index }}][trabaja_en_cultivo]" value="1" {{ ($familiar->trabaja_en_cultivo ?? false) ? 'checked' : '' }}>
                                        <span style="font-size: 0.875rem;">Trabaja en cultivo</span>
                                    </label>
                                </div>
                            </div>
                        @empty
                            <p id="noFamiliares" style="text-align: center; color: #6c757d; padding: 1rem;">No hay familiares registrados. Haga clic en "Agregar" para añadir.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div>
                <!-- Equipo de Trabajo -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-hard-hat"></i> Equipo de Trabajo
                    </h3>

                    <x-agromarket.form-group
                        label="Número de Personas Trabajando"
                        name="num_personas_trabajando"
                        type="number"
                        icon="fas fa-users"
                        placeholder="5"
                        min="0"
                        :value="$perfil->num_personas_trabajando ?? ''"
                    ></x-agromarket.form-group>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="familia_trabaja_cultivo" value="1" {{ ($perfil->familia_trabaja_cultivo ?? false) ? 'checked' : '' }}
                                   style="width: 20px; height: 20px; accent-color: #4A7C59;">
                            <span style="font-weight: 500;">La familia trabaja en el cultivo</span>
                        </label>
                    </div>

                    <x-agromarket.form-group
                        label="Roles Principales del Equipo"
                        name="roles_principales"
                        type="textarea"
                        icon="fas fa-tasks"
                        placeholder="Administrador, jornaleros, supervisor..."
                        :value="$perfil->roles_principales ?? ''"
                        rows="3"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Nivel de Tecnificación"
                        name="nivel_tecnificacion"
                        type="select"
                        icon="fas fa-cogs"
                        :options="[
                            '' => 'Seleccione...',
                            'manual' => 'Manual - Sin maquinaria',
                            'semi_tecnificado' => 'Semi-tecnificado - Algunas herramientas',
                            'tecnificado' => 'Tecnificado - Maquinaria moderna'
                        ]"
                        :value="$perfil->nivel_tecnificacion ?? ''"
                    ></x-agromarket.form-group>
                </div>

                <!-- Estado del Predio -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-home"></i> Estado del Predio
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" name="tiene_riego" value="1" {{ ($perfil->tiene_riego ?? false) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: #4A7C59;">
                            <span><i class="fas fa-tint" style="color: #17a2b8;"></i> Sistema de Riego</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" name="tiene_bodega" value="1" {{ ($perfil->tiene_bodega ?? false) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: #4A7C59;">
                            <span><i class="fas fa-warehouse" style="color: #6c757d;"></i> Bodega</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" name="tiene_transformacion" value="1" {{ ($perfil->tiene_transformacion ?? false) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: #4A7C59;">
                            <span><i class="fas fa-industry" style="color: #ffc107;"></i> Área Transformación</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" name="tiene_transporte" value="1" {{ ($perfil->tiene_transporte ?? false) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: #4A7C59;">
                            <span><i class="fas fa-truck" style="color: #28a745;"></i> Transporte Propio</span>
                        </label>
                    </div>

                    <x-agromarket.form-group
                        label="Accesibilidad al Predio"
                        name="accesibilidad"
                        type="textarea"
                        icon="fas fa-road"
                        placeholder="Condiciones de las vías de acceso..."
                        :value="$perfil->accesibilidad ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgos Naturales"
                        name="riesgos_naturales"
                        type="textarea"
                        icon="fas fa-exclamation-triangle"
                        placeholder="Inundaciones, deslizamientos, heladas..."
                        :value="$perfil->riesgos_naturales ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>
                </div>

                <!-- Datos del Proyecto -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-bullseye"></i> Objetivos del Proyecto
                    </h3>

                    <x-agromarket.form-group
                        label="Objetivo Principal del Proyecto"
                        name="objetivo_proyecto"
                        type="textarea"
                        icon="fas fa-flag"
                        placeholder="¿Qué espera lograr con este proyecto?"
                        :value="$proyecto->objetivo_proyecto ?? ''"
                        rows="3"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Detalle del Proceso Productivo"
                        name="detalle_proceso_productivo"
                        type="textarea"
                        icon="fas fa-cog"
                        placeholder="Describa paso a paso el proceso..."
                        :value="$proyecto->detalle_proceso_productivo ?? ''"
                        rows="4"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Cronograma Estimado"
                        name="cronograma_estimado"
                        type="textarea"
                        icon="fas fa-calendar-alt"
                        placeholder="Fechas clave, hitos del proyecto..."
                        :value="$proyecto->cronograma_estimado ?? ''"
                        rows="3"
                    ></x-agromarket.form-group>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                type="button"
                onclick="window.location.href='{{ route('admin.projects.registration.show', $proyecto) }}'"
            >
                Volver
            </x-agromarket.button>

            <x-agromarket.button
                variant="primary"
                icon="fas fa-arrow-right"
                type="submit"
            >
                Continuar a Fase 3
            </x-agromarket.button>
        </div>
    </form>

    @push('scripts')
    <script>
        let familiarIndex = {{ count($familia ?? []) }};

        function toggleTipoPersona() {
            const tipo = document.querySelector('input[name="tipo_persona"]:checked').value;
            const camposJuridica = document.getElementById('camposJuridica');
            camposJuridica.style.display = tipo === 'juridica' ? 'block' : 'none';
        }

        function agregarFamiliar() {
            const container = document.getElementById('familiaContainer');
            const noFamiliares = document.getElementById('noFamiliares');
            if (noFamiliares) noFamiliares.remove();

            const html = `
                <div class="familiar-item" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; position: relative;">
                    <button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #dc3545; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-size: 0.75rem;">
                        <i class="fas fa-times"></i>
                    </button>
                    <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 0.75rem;">
                        <select name="familia[${familiarIndex}][parentesco]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                            <option value="">Parentesco</option>
                            <option value="esposa">Esposa</option>
                            <option value="esposo">Esposo</option>
                            <option value="hijo">Hijo</option>
                            <option value="hija">Hija</option>
                            <option value="padre">Padre</option>
                            <option value="madre">Madre</option>
                            <option value="hermano">Hermano</option>
                            <option value="hermana">Hermana</option>
                            <option value="otro">Otro</option>
                        </select>
                        <input type="text" name="familia[${familiarIndex}][nombre]" placeholder="Nombre" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                        <input type="number" name="familia[${familiarIndex}][edad]" placeholder="Edad" min="0" max="150" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                        <select name="familia[${familiarIndex}][nivel_educativo]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                            <option value="">Nivel educativo</option>
                            <option value="ninguno">Ninguno</option>
                            <option value="primaria">Primaria</option>
                            <option value="secundaria">Secundaria</option>
                            <option value="tecnico">Técnico</option>
                            <option value="profesional">Profesional</option>
                            <option value="posgrado">Posgrado</option>
                        </select>
                        <select name="familia[${familiarIndex}][estudia_actualmente]" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                            <option value="">¿Estudia?</option>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                            <option value="estudio_aplazado">Aplazado</option>
                        </select>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem;">
                            <input type="checkbox" name="familia[${familiarIndex}][trabaja_en_cultivo]" value="1">
                            <span style="font-size: 0.875rem;">Trabaja en cultivo</span>
                        </label>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            familiarIndex++;
        }

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#4A7C59'
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Excelente',
                text: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
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
        div[style*="grid-template-columns: 1fr 1fr;"] {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)) !important;
        }
    </style>
    @endpush
</x-app-layout>
