<x-app-layout>
    <x-agromarket.page-header
        title="{{ $proyecto->nombre }}"
        description="Código: {{ $proyecto->codigo }}"
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

    @php
        $perfil = $proyecto->agricultor->perfilAgricultor;
        $familia = $proyecto->agricultor->familia;
        $datosFinancieros = $proyecto->datos_financieros ?? [];
    @endphp

    <!-- Indicador de Progreso -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                @for($i = 1; $i <= 3; $i++)
                    <div style="display: flex; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;
                            {{ $i < $currentPhase ? 'background: #28a745; color: white;' : ($i == $currentPhase ? 'background: #ffc107; color: #333;' : 'background: #e9ecef; color: #6c757d;') }}">
                            @if($i < $currentPhase)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        <span style="margin-left: 0.5rem; font-weight: 500; color: {{ $i <= $currentPhase ? '#333' : '#adb5bd' }};">
                            {{ match($i) { 1 => 'Básicos', 2 => 'Técnica', 3 => 'Financiera' } }}
                        </span>
                    </div>
                    @if($i < 3)
                        <div style="width: 40px; height: 2px; background: {{ $i < $currentPhase ? '#28a745' : '#e9ecef' }};"></div>
                    @endif
                @endfor
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <!-- Botón de Archivos -->
                <x-agromarket.button
                    variant="info"
                    icon="fas fa-paperclip"
                    onclick="window.location.href='{{ route('admin.projects.registration.files', $proyecto) }}'"
                >
                    Archivos
                </x-agromarket.button>

                @if(!$isComplete)
                    @if($currentPhase == 1)
                        <x-agromarket.button
                            variant="warning"
                            icon="fas fa-arrow-right"
                            onclick="window.location.href='{{ route('admin.projects.registration.phase2', $proyecto) }}'"
                        >
                            Continuar Fase 2
                        </x-agromarket.button>
                    @elseif($currentPhase == 2)
                        <x-agromarket.button
                            variant="warning"
                            icon="fas fa-arrow-right"
                            onclick="window.location.href='{{ route('admin.projects.registration.phase3', $proyecto) }}'"
                        >
                            Continuar Fase 3
                        </x-agromarket.button>
                    @endif
                @else
                    @if($proyecto->estado === 'borrador')
                        <form action="{{ route('admin.projects.registration.submit-review', $proyecto) }}" method="POST" style="display: inline;">
                            @csrf
                            <x-agromarket.button
                                variant="primary"
                                icon="fas fa-paper-plane"
                                type="submit"
                            >
                                Enviar a Revisión
                            </x-agromarket.button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Columna Principal -->
        <div>
            <!-- Información del Proyecto -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="margin: 0; color: #2D5A27;">{{ $proyecto->nombre }}</h2>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                            <x-agromarket.badge variant="primary" type="category">
                                {{ $proyecto->categoria->nombre }}
                            </x-agromarket.badge>
                            @php
                                $estadoConfig = match($proyecto->estado) {
                                    'borrador' => ['variant' => 'secondary', 'text' => 'Borrador'],
                                    'en_revision' => ['variant' => 'warning', 'text' => 'En Revisión'],
                                    'en_recaudacion' => ['variant' => 'success', 'text' => 'En Recaudación'],
                                    'rechazado' => ['variant' => 'danger', 'text' => 'Rechazado'],
                                    default => ['variant' => 'secondary', 'text' => ucfirst($proyecto->estado)]
                                };
                            @endphp
                            <x-agromarket.badge :variant="$estadoConfig['variant']">
                                {{ $estadoConfig['text'] }}
                            </x-agromarket.badge>
                        </div>
                    </div>
                    @if($proyecto->estado === 'borrador' || $proyecto->estado === 'rechazado')
                        <a href="{{ route('admin.projects.registration.edit', $proyecto) }}" style="color: #4A7C59; text-decoration: none;">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif
                </div>

                <p style="color: #495057; line-height: 1.6; margin-bottom: 1.5rem;">{{ $proyecto->descripcion }}</p>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Tipo de Cultivo</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #333;">{{ $proyecto->tipo_cultivo }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Área</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #333;">{{ number_format($proyecto->area_hectareas, 1) }} ha</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Etapa</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #333;">{{ ucfirst($proyecto->etapa_cultivo) }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Ubicación</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #333;">{{ $proyecto->ubicacion }}</p>
                    </div>
                </div>
            </div>

            <!-- Métricas Financieras -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                <x-agromarket.stat-card
                    icon="fas fa-dollar-sign"
                    :value="'$' . number_format($proyecto->monto_objetivo, 0)"
                    title="Meta Financiamiento"
                    color="primary"
                />
                <x-agromarket.stat-card
                    icon="fas fa-clock"
                    :value="$proyecto->duracion_meses . ' meses'"
                    title="Plazo"
                    color="secondary"
                />
                <x-agromarket.stat-card
                    icon="fas fa-percentage"
                    :value="number_format($proyecto->roi_anual, 1) . '%'"
                    title="ROI Proyectado"
                    color="success"
                />
            </div>

            <!-- Objetivos del Proyecto -->
            @if($proyecto->objetivo_proyecto || $proyecto->detalle_proceso_productivo)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-bullseye"></i> Objetivos y Proceso
                </h3>

                @if($proyecto->objetivo_proyecto)
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #495057; font-size: 1rem;">Objetivo Principal</h4>
                    <p style="margin: 0; color: #6c757d; line-height: 1.6;">{{ $proyecto->objetivo_proyecto }}</p>
                </div>
                @endif

                @if($proyecto->detalle_proceso_productivo)
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #495057; font-size: 1rem;">Proceso Productivo</h4>
                    <p style="margin: 0; color: #6c757d; line-height: 1.6;">{{ $proyecto->detalle_proceso_productivo }}</p>
                </div>
                @endif

                @if($proyecto->cronograma_estimado)
                <div>
                    <h4 style="margin: 0 0 0.5rem 0; color: #495057; font-size: 1rem;">Cronograma</h4>
                    <p style="margin: 0; color: #6c757d; line-height: 1.6;">{{ $proyecto->cronograma_estimado }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Datos Financieros -->
            @if(!empty($datosFinancieros))
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-chart-pie"></i> Información Financiera
                </h3>

                @if(!empty($datosFinancieros['inversion_solicitada']))
                <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">Desglose de Inversión</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($datosFinancieros['inversion_solicitada'] as $concepto => $valor)
                        @if($valor > 0)
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d; text-transform: capitalize;">{{ str_replace('_', ' ', $concepto) }}</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #333;">${{ number_format($valor, 0) }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                @if(!empty($datosFinancieros['proyecciones']))
                <h4 style="margin: 1.5rem 0 1rem 0; color: #495057; font-size: 1rem;">Proyecciones</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    @if(!empty($datosFinancieros['proyecciones']['produccion_estimada']))
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Producción Estimada</p>
                        <p style="margin: 0.25rem 0 0 0; color: #333;">{{ $datosFinancieros['proyecciones']['produccion_estimada'] }}</p>
                    </div>
                    @endif
                    @if(!empty($datosFinancieros['proyecciones']['margen_ganancia']))
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6c757d;">Margen de Ganancia</p>
                        <p style="margin: 0.25rem 0 0 0; color: #333;">{{ $datosFinancieros['proyecciones']['margen_ganancia'] }}%</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Documentos e Imágenes -->
            @if($proyecto->imagenes->count() > 0 || $proyecto->documentos->count() > 0)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-paperclip"></i> Archivos Adjuntos
                </h3>

                @if($proyecto->imagenes->count() > 0)
                <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">Imágenes ({{ $proyecto->imagenes->count() }})</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($proyecto->imagenes->sortBy('orden') as $imagen)
                        <a href="{{ asset($imagen->ruta_imagen) }}" target="_blank" style="display: block; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <img src="{{ asset($imagen->thumbnail ?? $imagen->ruta_imagen) }}" alt="{{ $imagen->titulo }}" style="width: 100%; height: 80px; object-fit: cover;">
                        </a>
                    @endforeach
                </div>
                @endif

                @if($proyecto->documentos->count() > 0)
                <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">Documentos ({{ $proyecto->documentos->count() }})</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($proyecto->documentos as $documento)
                        <a href="{{ asset($documento->ruta_archivo) }}" target="_blank" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; text-decoration: none; color: #333;">
                            <i class="fas fa-file-pdf" style="color: #dc3545;"></i>
                            <span>{{ $documento->nombre_archivo }}</span>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div>
            <!-- Información del Agricultor -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-user"></i> Agricultor
                </h3>

                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: #e9ecef; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #6c757d;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 style="margin: 0; color: #333;">{{ $proyecto->agricultor->name }}</h4>
                    <p style="margin: 0.25rem 0 0 0; color: #6c757d; font-size: 0.875rem;">{{ $proyecto->agricultor->email }}</p>
                </div>

                <div style="font-size: 0.875rem;">
                    @if($proyecto->agricultor->telefono)
                    <p style="margin: 0 0 0.5rem 0; color: #495057;">
                        <i class="fas fa-phone" style="width: 20px; color: #6c757d;"></i> {{ $proyecto->agricultor->telefono }}
                    </p>
                    @endif
                    @if($proyecto->agricultor->documento_identidad)
                    <p style="margin: 0 0 0.5rem 0; color: #495057;">
                        <i class="fas fa-id-card" style="width: 20px; color: #6c757d;"></i> {{ $proyecto->agricultor->documento_identidad }}
                    </p>
                    @endif
                    @if($proyecto->agricultor->ciudad)
                    <p style="margin: 0; color: #495057;">
                        <i class="fas fa-map-marker-alt" style="width: 20px; color: #6c757d;"></i> {{ $proyecto->agricultor->ciudad }}, {{ $proyecto->agricultor->pais }}
                    </p>
                    @endif
                </div>

                @if($proyecto->agricultor->creado_por_admin)
                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
                    <form action="{{ route('admin.projects.registration.resend-email', $proyecto) }}" method="POST">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 0.75rem; background: #17a2b8; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                            <i class="fas fa-envelope"></i> Reenviar Email de Bienvenida
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Perfil del Agricultor -->
            @if($perfil)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-chart-bar"></i> Perfil Técnico
                </h3>

                <div style="font-size: 0.875rem;">
                    <p style="margin: 0 0 0.75rem 0;">
                        <strong>Tipo:</strong> {{ $perfil->tipo_persona == 'juridica' ? 'Persona Jurídica' : 'Persona Natural' }}
                    </p>
                    @if($perfil->anos_experiencia)
                    <p style="margin: 0 0 0.75rem 0;">
                        <strong>Experiencia:</strong> {{ $perfil->anos_experiencia }} años
                    </p>
                    @endif
                    @if($perfil->cantidad_cosechas)
                    <p style="margin: 0 0 0.75rem 0;">
                        <strong>Cosechas:</strong> {{ $perfil->cantidad_cosechas }}
                    </p>
                    @endif
                    @if($perfil->nivel_tecnificacion)
                    <p style="margin: 0 0 0.75rem 0;">
                        <strong>Tecnificación:</strong> {{ ucfirst(str_replace('_', ' ', $perfil->nivel_tecnificacion)) }}
                    </p>
                    @endif
                    @if($perfil->num_personas_trabajando)
                    <p style="margin: 0;">
                        <strong>Equipo:</strong> {{ $perfil->num_personas_trabajando }} personas
                    </p>
                    @endif
                </div>

                <!-- Infraestructura -->
                @if($perfil->tiene_riego || $perfil->tiene_bodega || $perfil->tiene_transformacion || $perfil->tiene_transporte)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
                    <p style="margin: 0 0 0.5rem 0; font-weight: 500; font-size: 0.875rem;">Infraestructura:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @if($perfil->tiene_riego)
                            <span style="background: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <i class="fas fa-tint"></i> Riego
                            </span>
                        @endif
                        @if($perfil->tiene_bodega)
                            <span style="background: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <i class="fas fa-warehouse"></i> Bodega
                            </span>
                        @endif
                        @if($perfil->tiene_transformacion)
                            <span style="background: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <i class="fas fa-industry"></i> Transformación
                            </span>
                        @endif
                        @if($perfil->tiene_transporte)
                            <span style="background: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <i class="fas fa-truck"></i> Transporte
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Familia -->
            @if($familia && $familia->count() > 0)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-users"></i> Familia ({{ $familia->count() }})
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($familia as $familiar)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8f9fa; border-radius: 6px;">
                            <div>
                                <p style="margin: 0; font-weight: 500; color: #333;">{{ $familiar->nombre }}</p>
                                <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">{{ ucfirst($familiar->parentesco) }} · {{ $familiar->edad }} años</p>
                            </div>
                            @if($familiar->trabaja_en_cultivo)
                                <span style="background: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem;">
                                    Trabaja
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-info-circle"></i> Información
                </h3>

                <div style="font-size: 0.875rem; color: #6c757d;">
                    <p style="margin: 0 0 0.5rem 0;">
                        <strong>Creado:</strong> {{ $proyecto->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p style="margin: 0 0 0.5rem 0;">
                        <strong>Actualizado:</strong> {{ $proyecto->updated_at->format('d/m/Y H:i') }}
                    </p>
                    @if($proyecto->creadoPorAdmin)
                    <p style="margin: 0;">
                        <strong>Registrado por:</strong> {{ $proyecto->creadoPorAdmin->name }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif
    </script>
    @endpush

    @push('styles')
    <style>
        div[style*="grid-template-columns: 2fr 1fr"] {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
        }

        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }

        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
        }
    </style>
    @endpush
</x-app-layout>
