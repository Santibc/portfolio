<x-app-layout>
    <x-agromarket.page-header
        title="Registrar Nuevo Proyecto"
        description="Fase 1 de 3: Datos del Agricultor y Proyecto"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.projects.registration.index') }}'"
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
                    <div style="font-weight: 600; color: #4A7C59;">Datos Básicos</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Agricultor y Proyecto</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #e9ecef; margin: 0 1rem;"></div>
            <!-- Paso 2 -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; color: #6c757d; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    2
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #6c757d;">Evaluación Técnica</div>
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
                    <div style="font-weight: 600; color: #6c757d;">Evaluación Financiera</div>
                    <div style="font-size: 0.8rem; color: #adb5bd;">Inversión y Proyecciones</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('admin.projects.registration.phase1.store') }}" method="POST" id="phase1Form" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Columna Izquierda: Datos del Agricultor -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-user"></i> Datos del Agricultor
                </h3>

                <!-- Selector: Nuevo o Existente -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">
                        Tipo de Agricultor
                    </label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; flex: 1; transition: all 0.2s;" id="optionNuevo">
                            <input type="radio" name="agricultor_tipo" value="nuevo" checked onchange="toggleAgricultorForm()">
                            <div>
                                <div style="font-weight: 500;">Nuevo Agricultor</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">Crear cuenta nueva</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; flex: 1; transition: all 0.2s;" id="optionExistente">
                            <input type="radio" name="agricultor_tipo" value="existente" onchange="toggleAgricultorForm()">
                            <div>
                                <div style="font-weight: 500;">Agricultor Existente</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">Seleccionar de la lista</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Formulario Nuevo Agricultor -->
                <div id="nuevoAgricultorForm">
                    <input type="hidden" name="agricultor_nuevo" value="1">

                    <x-agromarket.form-group
                        label="Nombre Completo"
                        name="agricultor_name"
                        icon="fas fa-user"
                        placeholder="Nombre del agricultor"
                        required
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Correo Electrónico"
                        name="agricultor_email"
                        type="email"
                        icon="fas fa-envelope"
                        placeholder="correo@ejemplo.com"
                        required
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Teléfono"
                        name="agricultor_telefono"
                        icon="fas fa-phone"
                        placeholder="+57 300 123 4567"
                    ></x-agromarket.form-group>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="Tipo Documento"
                            name="agricultor_tipo_documento"
                            type="select"
                            icon="fas fa-id-card"
                            :options="[
                                'CC' => 'Cédula de Ciudadanía',
                                'CE' => 'Cédula de Extranjería',
                                'NIT' => 'NIT',
                                'PASSPORT' => 'Pasaporte',
                                'DNI' => 'DNI'
                            ]"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Número de Documento"
                            name="agricultor_documento_identidad"
                            icon="fas fa-id-card"
                            placeholder="1234567890"
                            required
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Fecha de Nacimiento"
                        name="agricultor_fecha_nacimiento"
                        type="date"
                        icon="fas fa-calendar"
                    ></x-agromarket.form-group>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="País"
                            name="agricultor_pais"
                            icon="fas fa-globe"
                            placeholder="Colombia"
                            value="Colombia"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Ciudad"
                            name="agricultor_ciudad"
                            icon="fas fa-city"
                            placeholder="Ciudad"
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Dirección"
                        name="agricultor_direccion"
                        type="textarea"
                        icon="fas fa-map-marker-alt"
                        placeholder="Dirección completa"
                        rows="2"
                    ></x-agromarket.form-group>

                    <!-- Foto del Agricultor (Opcional) -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">
                            <i class="fas fa-camera" style="color: #6c757d; margin-right: 0.5rem;"></i>
                            Foto del Agricultor <span style="color: #6c757d; font-weight: normal;">(Opcional)</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div id="fotoPreview" style="width: 80px; height: 80px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <i class="fas fa-user" style="font-size: 2rem; color: #adb5bd;"></i>
                            </div>
                            <div style="flex: 1;">
                                <input type="file" name="agricultor_foto" id="inputFotoAgricultor" accept="image/jpeg,image/png,image/webp"
                                    style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px; font-size: 0.875rem;">
                                <small style="color: #6c757d; display: block; margin-top: 0.25rem;">JPG, PNG o WEBP. Máximo 2MB.</small>
                            </div>
                        </div>
                    </div>

                    <div style="background: #d4edda; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #28a745;">
                        <p style="margin: 0; color: #155724; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Se enviará un correo al agricultor con sus credenciales de acceso. La contraseña temporal será su número de documento.
                        </p>
                    </div>
                </div>

                <!-- Selector Agricultor Existente -->
                <div id="existenteAgricultorForm" style="display: none;">
                    <input type="hidden" name="agricultor_nuevo" value="0" disabled>

                    <x-agromarket.form-group
                        label="Seleccionar Agricultor"
                        name="agricultor_id"
                        type="select"
                        icon="fas fa-user"
                        :options="$agricultoresExistentes->pluck('name', 'id')->prepend('Seleccione un agricultor...', '')"
                    ></x-agromarket.form-group>

                    <div id="agricultorInfo" style="display: none; background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                        <p style="margin: 0; font-size: 0.875rem; color: #495057;">
                            <strong>Email:</strong> <span id="agricultorEmail"></span><br>
                            <strong>Documento:</strong> <span id="agricultorDoc"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Datos del Proyecto -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-seedling"></i> Datos del Proyecto
                </h3>

                <x-agromarket.form-group
                    label="Categoría de Inversión"
                    name="categoria_id"
                    type="select"
                    icon="fas fa-tags"
                    :options="$categorias->pluck('nombre', 'id')->prepend('Seleccione una categoría...', '')"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Nombre del Proyecto"
                    name="nombre"
                    icon="fas fa-project-diagram"
                    placeholder="Ej: Cultivo de Café Orgánico - Finca La Esperanza"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Tipo de Cultivo"
                    name="tipo_cultivo"
                    icon="fas fa-leaf"
                    placeholder="Ej: Café, Cacao, Aguacate Hass..."
                    required
                ></x-agromarket.form-group>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <x-agromarket.form-group
                        label="Área (Hectáreas)"
                        name="area_hectareas"
                        type="number"
                        icon="fas fa-ruler-combined"
                        placeholder="10.5"
                        step="0.1"
                        min="0.1"
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
                            'transformacion' => 'Transformación',
                            'otro' => 'Otro'
                        ]"
                        required
                    ></x-agromarket.form-group>
                </div>

                <x-agromarket.form-group
                    label="Año de Inicio del Cultivo"
                    name="ano_inicio_cultivo"
                    type="number"
                    icon="fas fa-calendar-alt"
                    placeholder="{{ date('Y') }}"
                    min="1990"
                    max="{{ date('Y') + 1 }}"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Ubicación del Proyecto"
                    name="ubicacion"
                    icon="fas fa-map-marker-alt"
                    placeholder="Vereda, Municipio, Departamento"
                    required
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Coordenadas GPS"
                    name="coordenadas"
                    icon="fas fa-globe"
                    placeholder="Ej: 4.6097, -74.0817"
                    help="Opcional. Formato: latitud, longitud"
                ></x-agromarket.form-group>

                <x-agromarket.form-group
                    label="Descripción del Proyecto"
                    name="descripcion"
                    type="textarea"
                    icon="fas fa-align-left"
                    placeholder="Descripción detallada del proyecto agrícola..."
                    rows="4"
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
                        required
                    ></x-agromarket.form-group>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-times"
                type="button"
                onclick="window.location.href='{{ route('admin.projects.registration.index') }}'"
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
        // Datos de agricultores existentes
        const agricultores = @json($agricultoresExistentes->keyBy('id'));

        function toggleAgricultorForm() {
            const tipo = document.querySelector('input[name="agricultor_tipo"]:checked').value;
            const nuevoForm = document.getElementById('nuevoAgricultorForm');
            const existenteForm = document.getElementById('existenteAgricultorForm');
            const optionNuevo = document.getElementById('optionNuevo');
            const optionExistente = document.getElementById('optionExistente');

            if (tipo === 'nuevo') {
                nuevoForm.style.display = 'block';
                existenteForm.style.display = 'none';
                optionNuevo.style.borderColor = '#4A7C59';
                optionNuevo.style.background = '#f0f7f3';
                optionExistente.style.borderColor = '#e9ecef';
                optionExistente.style.background = 'white';

                // Habilitar campos nuevo, deshabilitar existente
                nuevoForm.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
                existenteForm.querySelectorAll('input, select').forEach(el => el.disabled = true);
            } else {
                nuevoForm.style.display = 'none';
                existenteForm.style.display = 'block';
                optionExistente.style.borderColor = '#4A7C59';
                optionExistente.style.background = '#f0f7f3';
                optionNuevo.style.borderColor = '#e9ecef';
                optionNuevo.style.background = 'white';

                // Deshabilitar campos nuevo, habilitar existente
                nuevoForm.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
                existenteForm.querySelectorAll('input, select').forEach(el => el.disabled = false);
            }
        }

        // Mostrar info del agricultor seleccionado
        document.querySelector('select[name="agricultor_id"]')?.addEventListener('change', function() {
            const id = this.value;
            const infoDiv = document.getElementById('agricultorInfo');

            if (id && agricultores[id]) {
                document.getElementById('agricultorEmail').textContent = agricultores[id].email;
                document.getElementById('agricultorDoc').textContent = agricultores[id].documento_identidad || 'No registrado';
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        });

        // Inicializar estado
        toggleAgricultorForm();

        // Preview de foto del agricultor
        document.getElementById('inputFotoAgricultor')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('fotoPreview');

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Archivo muy grande',
                        text: 'La imagen no debe superar los 2MB',
                        confirmButtonColor: '#4A7C59'
                    });
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<i class="fas fa-user" style="font-size: 2rem; color: #adb5bd;"></i>';
            }
        });

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
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)) !important;
        }

        @media (max-width: 900px) {
            div[style*="grid-template-columns: 1fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
