<x-app-layout>
    <x-agromarket.page-header
        title="Revisar Proyecto"
        description="Codigo: {{ $proyecto->codigo }}"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.projects.review.index') }}'"
            >
                Volver
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Columna izquierda - Informacion completa del proyecto -->
        <div>
            {{-- ==================== FASE 1: INFORMACION BASICA ==================== --}}
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
                    <i class="fas fa-file-alt"></i> FASE 1: Informacion Basica
                </h3>

                {{-- Informacion del Agricultor --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-user-circle"></i> Datos del Agricultor
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        @if($proyecto->agricultor->foto_perfil)
                        <div style="grid-column: span 2; text-align: center; margin-bottom: 1rem;">
                            <img src="{{ asset($proyecto->agricultor->foto_perfil) }}"
                                 alt="Foto del agricultor"
                                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #4A7C59;">
                        </div>
                        @endif

                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Nombre Completo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->name }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Documento de Identidad</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                {{ strtoupper($proyecto->agricultor->tipo_documento ?? 'CC') }}: {{ $proyecto->agricultor->documento_identidad ?? 'No registrado' }}
                            </p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Telefono</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->telefono ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Email</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->email }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Pais</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->pais ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Ciudad</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->ciudad ?? 'No registrado' }}</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Direccion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->direccion ?? 'No registrada' }}</p>
                        </div>
                        @if($proyecto->agricultor->fecha_nacimiento)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Fecha de Nacimiento</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->agricultor->fecha_nacimiento->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($proyecto->creado_por_admin && $proyecto->creadoPorAdmin)
                        <div style="grid-column: span 2; background: #e8f5e9; padding: 0.75rem; border-radius: 6px; margin-top: 0.5rem;">
                            <p style="margin: 0; font-size: 0.8rem; color: #2e7d32;">
                                <i class="fas fa-user-shield"></i> Registrado por admin: <strong>{{ $proyecto->creadoPorAdmin->name }}</strong>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Informacion del Proyecto --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-project-diagram"></i> Datos del Proyecto
                    </h4>

                    <div style="margin-bottom: 1rem;">
                        <h5 style="margin: 0 0 0.5rem 0; color: #2D5A27; font-size: 1.25rem;">{{ $proyecto->nombre }}</h5>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <x-agromarket.badge variant="primary" type="category">
                                {{ $proyecto->categoria->nombre }}
                            </x-agromarket.badge>
                            <x-agromarket.badge variant="{{ match($proyecto->nivel_riesgo) {
                                'bajo' => 'success',
                                'medio' => 'warning',
                                'alto' => 'danger',
                                default => 'secondary'
                            } }}">
                                Riesgo: {{ ucfirst($proyecto->nivel_riesgo ?? 'N/A') }}
                            </x-agromarket.badge>
                            <x-agromarket.badge variant="secondary">
                                {{ ucfirst($proyecto->estado) }}
                            </x-agromarket.badge>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Descripcion</p>
                        <p style="margin: 0.25rem 0 0 0; line-height: 1.6;">{{ $proyecto->descripcion }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Ubicacion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                <i class="fas fa-map-marker-alt" style="color: #dc3545;"></i> {{ $proyecto->ubicacion }}
                            </p>
                        </div>
                        @if($proyecto->coordenadas)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Coordenadas GPS</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                <i class="fas fa-globe" style="color: #17a2b8;"></i> {{ $proyecto->coordenadas }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Informacion del Cultivo --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-seedling"></i> Datos del Cultivo
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        @if($proyecto->tipo_cultivo)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Tipo de Cultivo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ ucfirst($proyecto->tipo_cultivo) }}</p>
                        </div>
                        @endif
                        @if($proyecto->area_hectareas)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Area del Terreno</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ number_format($proyecto->area_hectareas, 2) }} hectareas</p>
                        </div>
                        @endif
                        @if($proyecto->etapa_cultivo)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Etapa del Cultivo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ ucfirst($proyecto->etapa_cultivo) }}</p>
                        </div>
                        @endif
                        @if($proyecto->ano_inicio_cultivo)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Ano de Inicio</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->ano_inicio_cultivo }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ==================== FASE 2: EVALUACION TECNICA ==================== --}}
            @php $perfil = $proyecto->agricultor->perfilAgricultor; @endphp
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
                    <i class="fas fa-clipboard-check"></i> FASE 2: Evaluacion Tecnica
                </h3>

                @if($perfil)
                {{-- Tipo de Persona / Datos Empresa --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-id-card"></i> Tipo de Persona
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Tipo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->tipo_persona === 'juridica')
                                    <i class="fas fa-building" style="color: #6f42c1;"></i> Persona Juridica
                                @else
                                    <i class="fas fa-user" style="color: #28a745;"></i> Persona Natural
                                @endif
                            </p>
                        </div>

                        @if($perfil->tipo_persona === 'juridica')
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Nombre de Empresa</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->nombre_empresa ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">NIT</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->nit ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Representante Legal</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->representante_legal ?? 'No registrado' }}</p>
                        </div>
                        @endif

                        @if($perfil->direccion_finca)
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Direccion de la Finca</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->direccion_finca }}</p>
                        </div>
                        @endif

                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Cultivo Asegurado</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->cultivo_asegurado)
                                    <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Si</span>
                                @else
                                    <span style="color: #dc3545;"><i class="fas fa-times-circle"></i> No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Experiencia --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-award"></i> Experiencia Agricola
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Anos de Experiencia</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->anos_experiencia ?? 0 }} anos</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Cantidad de Cosechas</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->cantidad_cosechas ?? 0 }} cosechas</p>
                        </div>
                        @if($perfil->produccion_promedio)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Produccion Promedio</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->produccion_promedio }}</p>
                        </div>
                        @endif
                        @if($perfil->formacion_capacitaciones)
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Formacion y Capacitaciones</p>
                            <p style="margin: 0.25rem 0 0 0;">{{ $perfil->formacion_capacitaciones }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Equipo de Trabajo --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-users"></i> Equipo de Trabajo
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Personas Trabajando</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->num_personas_trabajando ?? 0 }} personas</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Familia Trabaja en Cultivo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->familia_trabaja_cultivo)
                                    <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Si</span>
                                @else
                                    <span style="color: #6c757d;"><i class="fas fa-minus-circle"></i> No</span>
                                @endif
                            </p>
                        </div>
                        @if($perfil->roles_principales)
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Roles Principales</p>
                            <p style="margin: 0.25rem 0 0 0;">{{ $perfil->roles_principales }}</p>
                        </div>
                        @endif
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Nivel de Tecnificacion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @php
                                    $nivelColor = match($perfil->nivel_tecnificacion) {
                                        'alto' => '#28a745',
                                        'medio' => '#ffc107',
                                        'bajo' => '#dc3545',
                                        default => '#6c757d'
                                    };
                                @endphp
                                <span style="color: {{ $nivelColor }};">{{ ucfirst($perfil->nivel_tecnificacion ?? 'No especificado') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Estado del Predio --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-home"></i> Estado del Predio
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Sistema de Riego</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->tiene_riego)
                                    <span style="color: #28a745;"><i class="fas fa-tint"></i> Si tiene</span>
                                @else
                                    <span style="color: #dc3545;"><i class="fas fa-tint-slash"></i> No tiene</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Bodega/Almacenamiento</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->tiene_bodega)
                                    <span style="color: #28a745;"><i class="fas fa-warehouse"></i> Si tiene</span>
                                @else
                                    <span style="color: #dc3545;"><i class="fas fa-times"></i> No tiene</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Area de Transformacion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->tiene_transformacion)
                                    <span style="color: #28a745;"><i class="fas fa-industry"></i> Si tiene</span>
                                @else
                                    <span style="color: #6c757d;"><i class="fas fa-times"></i> No tiene</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Transporte Propio</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                @if($perfil->tiene_transporte)
                                    <span style="color: #28a745;"><i class="fas fa-truck"></i> Si tiene</span>
                                @else
                                    <span style="color: #6c757d;"><i class="fas fa-times"></i> No tiene</span>
                                @endif
                            </p>
                        </div>
                        @if($perfil->accesibilidad)
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Accesibilidad al Predio</p>
                            <p style="margin: 0.25rem 0 0 0;">{{ $perfil->accesibilidad }}</p>
                        </div>
                        @endif
                        @if($perfil->riesgos_naturales)
                        <div style="grid-column: span 2;">
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Riesgos Naturales</p>
                            <p style="margin: 0.25rem 0 0 0;">{{ $perfil->riesgos_naturales }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Familia del Agricultor --}}
                @if($proyecto->agricultor->familia && $proyecto->agricultor->familia->count() > 0)
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-users"></i> Composicion Familiar
                        <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->agricultor->familia->count() }} miembros)</span>
                    </h4>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                            <thead>
                                <tr style="background: #e9ecef;">
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #dee2e6;">Parentesco</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #dee2e6;">Nombre</th>
                                    <th style="padding: 0.75rem; text-align: center; border-bottom: 2px solid #dee2e6;">Edad</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #dee2e6;">Nivel Educativo</th>
                                    <th style="padding: 0.75rem; text-align: center; border-bottom: 2px solid #dee2e6;">Estudia</th>
                                    <th style="padding: 0.75rem; text-align: center; border-bottom: 2px solid #dee2e6;">Trabaja Cultivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proyecto->agricultor->familia as $familiar)
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 0.75rem;">{{ $familiar->parentesco_label }}</td>
                                    <td style="padding: 0.75rem;">{{ $familiar->nombre }}</td>
                                    <td style="padding: 0.75rem; text-align: center;">{{ $familiar->edad }} anos</td>
                                    <td style="padding: 0.75rem;">{{ $familiar->nivel_educativo_label }}</td>
                                    <td style="padding: 0.75rem; text-align: center;">{{ $familiar->estudia_label }}</td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        @if($familiar->trabaja_en_cultivo)
                                            <span style="color: #28a745;"><i class="fas fa-check"></i></span>
                                        @else
                                            <span style="color: #6c757d;"><i class="fas fa-minus"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @else
                <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; color: #856404;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Sin datos de Fase 2:</strong> No se ha completado la evaluacion tecnica del agricultor.
                    </p>
                </div>
                @endif
            </div>

            {{-- ==================== FASE 3: EVALUACION FINANCIERA ==================== --}}
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
                    <i class="fas fa-chart-line"></i> FASE 3: Evaluacion Financiera
                </h3>

                {{-- Metricas Principales --}}
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.stat-card
                        icon="fas fa-money-bill-wave"
                        :value="'$' . number_format($proyecto->monto_objetivo, 0)"
                        title="Monto Objetivo"
                        color="primary"
                    />
                    <x-agromarket.stat-card
                        icon="fas fa-percentage"
                        :value="$proyecto->roi_anual . '%'"
                        title="ROI Anual"
                        color="success"
                    />
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <x-agromarket.stat-card
                        icon="fas fa-coins"
                        :value="'$' . number_format($proyecto->inversion_minima, 0)"
                        title="Inversion Minima"
                        color="secondary"
                    />
                    @if($proyecto->inversion_maxima)
                    <x-agromarket.stat-card
                        icon="fas fa-coins"
                        :value="'$' . number_format($proyecto->inversion_maxima, 0)"
                        title="Inversion Maxima"
                        color="secondary"
                    />
                    @endif
                    <x-agromarket.stat-card
                        icon="fas fa-calendar-alt"
                        :value="$proyecto->duracion_meses . ' meses'"
                        title="Duracion"
                        color="warning"
                    />
                </div>

                {{-- Fechas Importantes --}}
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-calendar"></i> Fechas Importantes
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Inicio Recaudacion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_inicio_recaudacion?->format('d/m/Y') ?? 'No definida' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Cierre Recaudacion</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_cierre_recaudacion?->format('d/m/Y') ?? 'No definida' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Inicio Proyecto</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_inicio_proyecto?->format('d/m/Y') ?? 'No definida' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Fin Proyecto</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_fin_proyecto?->format('d/m/Y') ?? 'No definida' }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Periodo Dividendos</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->periodo_dividendos_dias ?? 0 }} dias</p>
                        </div>
                        @if($proyecto->periodo_cosecha_meses)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Periodo Cosecha</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->periodo_cosecha_meses }} meses</p>
                        </div>
                        @endif
                        @if($proyecto->fecha_primer_dividendo)
                        <div>
                            <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Primer Dividendo</p>
                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_primer_dividendo->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Objetivo y Proceso Productivo --}}
                @if($proyecto->objetivo_proyecto || $proyecto->detalle_proceso_productivo || $proyecto->cronograma_estimado)
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-bullseye"></i> Detalles del Proyecto
                    </h4>

                    @if($proyecto->objetivo_proyecto)
                    <div style="margin-bottom: 1rem;">
                        <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Objetivo del Proyecto</p>
                        <p style="margin: 0.25rem 0 0 0; line-height: 1.6;">{{ $proyecto->objetivo_proyecto }}</p>
                    </div>
                    @endif

                    @if($proyecto->detalle_proceso_productivo)
                    <div style="margin-bottom: 1rem;">
                        <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Detalle del Proceso Productivo</p>
                        <p style="margin: 0.25rem 0 0 0; line-height: 1.6;">{{ $proyecto->detalle_proceso_productivo }}</p>
                    </div>
                    @endif

                    @if($proyecto->cronograma_estimado)
                    <div>
                        <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Cronograma Estimado</p>
                        <p style="margin: 0.25rem 0 0 0; line-height: 1.6;">{{ $proyecto->cronograma_estimado }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Datos Financieros JSON --}}
                @if($proyecto->datos_financieros && is_array($proyecto->datos_financieros))
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                        <i class="fas fa-file-invoice-dollar"></i> Datos Financieros Detallados
                    </h4>

                    @php
                        $labelsInversion = [
                            'insumos' => 'Insumos',
                            'mano_obra' => 'Mano de Obra',
                            'equipos' => 'Equipos',
                            'transporte' => 'Transporte',
                            'certificaciones' => 'Certificaciones',
                            'empaques' => 'Empaques',
                            'marketing' => 'Marketing',
                        ];
                        $labelsProyecciones = [
                            'produccion_estimada' => 'Produccion Estimada',
                            'precio_venta_estimado' => 'Precio Venta Estimado',
                            'canales_venta_actuales' => 'Canales de Venta Actuales',
                            'canales_venta_deseados' => 'Canales de Venta Deseados',
                            'proyeccion_ingresos' => 'Proyeccion de Ingresos',
                            'punto_equilibrio' => 'Punto de Equilibrio',
                            'margen_ganancia' => 'Margen de Ganancia (%)',
                        ];
                        $labelsRiesgos = [
                            'plagas' => 'Riesgo de Plagas',
                            'clima' => 'Riesgo Climatico',
                            'competencia' => 'Competencia',
                            'acceso_mercados' => 'Acceso a Mercados',
                            'regulaciones' => 'Regulaciones',
                        ];
                    @endphp

                    {{-- Inversion Solicitada --}}
                    @if(isset($proyecto->datos_financieros['inversion_solicitada']) && is_array($proyecto->datos_financieros['inversion_solicitada']))
                    <div style="margin-bottom: 1.5rem;">
                        <p style="margin: 0 0 0.75rem 0; font-weight: 600; color: #2D5A27;">
                            <i class="fas fa-coins"></i> Inversion Solicitada
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            @foreach($proyecto->datos_financieros['inversion_solicitada'] as $key => $value)
                                @if($value)
                                <div style="background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid #e9ecef;">
                                    <p style="margin: 0; font-size: 0.75rem; color: #6c757d;">{{ $labelsInversion[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</p>
                                    <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                                        @if(is_numeric($value))
                                            ${{ number_format($value, 0) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Proyecciones --}}
                    @if(isset($proyecto->datos_financieros['proyecciones']) && is_array($proyecto->datos_financieros['proyecciones']))
                    <div style="margin-bottom: 1.5rem;">
                        <p style="margin: 0 0 0.75rem 0; font-weight: 600; color: #2D5A27;">
                            <i class="fas fa-chart-line"></i> Proyecciones
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            @foreach($proyecto->datos_financieros['proyecciones'] as $key => $value)
                                @if($value)
                                <div style="background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid #e9ecef;">
                                    <p style="margin: 0; font-size: 0.75rem; color: #6c757d;">{{ $labelsProyecciones[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</p>
                                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                        @if($key === 'precio_venta_estimado' || $key === 'proyeccion_ingresos')
                                            @if(is_numeric($value))
                                                ${{ number_format($value, 0) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        @elseif($key === 'margen_ganancia')
                                            {{ $value }}%
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Riesgos --}}
                    @if(isset($proyecto->datos_financieros['riesgos']) && is_array($proyecto->datos_financieros['riesgos']))
                    <div>
                        <p style="margin: 0 0 0.75rem 0; font-weight: 600; color: #2D5A27;">
                            <i class="fas fa-exclamation-triangle"></i> Evaluacion de Riesgos
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            @foreach($proyecto->datos_financieros['riesgos'] as $key => $value)
                                @if($value)
                                @php
                                    $riesgoColor = match(strtolower($value)) {
                                        'alto', 'mucha', 'mucho' => '#dc3545',
                                        'medio', 'masomenos' => '#ffc107',
                                        'bajo', 'poco', 'poca' => '#28a745',
                                        'ninguno', 'ninguna' => '#6c757d',
                                        default => '#495057'
                                    };
                                @endphp
                                <div style="background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid #e9ecef;">
                                    <p style="margin: 0; font-size: 0.75rem; color: #6c757d;">{{ $labelsRiesgos[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</p>
                                    <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: {{ $riesgoColor }};">{{ ucfirst($value) }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Datos especificos por Categoria --}}
                @php $datosEspecificos = $proyecto->datos_especificos; @endphp
                @if($datosEspecificos && is_array($datosEspecificos) && count($datosEspecificos) > 0)
                <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #4A7C59;">
                    <h4 style="margin: 0 0 1rem 0; color: #2D5A27; font-size: 1rem;">
                        <i class="fas fa-cubes"></i> Datos Especificos - {{ $proyecto->categoria->nombre }}
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        @foreach($datosEspecificos as $key => $value)
                            @if($value)
                            <div @if(strlen($value) > 100) style="grid-column: span 2;" @endif>
                                <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                <p style="margin: 0.25rem 0 0 0; @if(strlen($value) > 100) white-space: pre-line; @else font-weight: 600; @endif">{{ $value }}</p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- ==================== GALERIA DE IMAGENES ==================== --}}
            @if($proyecto->imagenes->count() > 0)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
                    <i class="fas fa-images"></i> Galeria de Imagenes
                    <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->imagenes->count() }})</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                    @foreach($proyecto->imagenes->sortBy('orden') as $imagen)
                        <div style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @if($imagen->es_principal)
                                <span style="position: absolute; top: 6px; left: 6px; background: #D4AF37; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; z-index: 1;">
                                    <i class="fas fa-star"></i> Principal
                                </span>
                            @endif
                            <a href="{{ asset($imagen->ruta_imagen) }}" target="_blank" title="{{ $imagen->titulo }}">
                                <img src="{{ asset($imagen->thumbnail ?? $imagen->ruta_imagen) }}"
                                     alt="{{ $imagen->titulo }}"
                                     style="width: 100%; height: 120px; object-fit: cover; display: block;">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div style="background: #fff3cd; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ffc107;">
                <p style="margin: 0; color: #856404;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Sin imagenes:</strong> Este proyecto no tiene imagenes adjuntas.
                </p>
            </div>
            @endif

            {{-- ==================== DOCUMENTOS DEL PROYECTO ==================== --}}
            @if($proyecto->documentos->count() > 0)
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
                    <i class="fas fa-file-alt"></i> Documentos del Proyecto
                    <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->documentos->count() }})</span>
                </h3>

                @php
                    $tiposDocumento = [
                        'escritura' => 'Escritura del terreno',
                        'certificado_camara' => 'Certificado Camara de Comercio',
                        'cedula_catastral' => 'Cedula Catastral',
                        'plan_cultivo' => 'Plan de Cultivo',
                        'estudio_suelos' => 'Estudio de Suelos',
                        'licencia_ambiental' => 'Licencia Ambiental',
                        'poliza_seguro' => 'Poliza de Seguro',
                        'contrato_compra' => 'Contrato de Compra',
                        'foto_terreno' => 'Fotografia del Terreno',
                        'documento_tenencia' => 'Documento de Tenencia',
                        'certificado_agricola' => 'Certificado Agricola',
                        'certificaciones_asociacion' => 'Certificaciones de la Asociacion',
                        'otro' => 'Otro documento',
                    ];
                @endphp

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($proyecto->documentos as $documento)
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                            <div style="width: 48px; height: 48px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                @if(str_ends_with($documento->nombre_archivo, '.pdf'))
                                    <i class="fas fa-file-pdf" style="color: #dc3545;"></i>
                                @elseif(str_ends_with($documento->nombre_archivo, '.doc') || str_ends_with($documento->nombre_archivo, '.docx'))
                                    <i class="fas fa-file-word" style="color: #2b5797;"></i>
                                @else
                                    <i class="fas fa-file" style="color: #6c757d;"></i>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; color: #333; margin-bottom: 0.25rem;">
                                    {{ $tiposDocumento[$documento->tipo_documento] ?? $documento->tipo_documento }}
                                </div>
                                <div style="font-size: 0.85rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $documento->nombre_archivo }}
                                    <span style="margin-left: 0.5rem;">
                                        @php
                                            $bytes = $documento->tamano_bytes;
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $i = 0;
                                            while ($bytes >= 1024 && $i < count($units) - 1) {
                                                $bytes /= 1024;
                                                $i++;
                                            }
                                            echo '(' . round($bytes, 2) . ' ' . $units[$i] . ')';
                                        @endphp
                                    </span>
                                </div>
                                @if($documento->descripcion)
                                <div style="font-size: 0.8rem; color: #495057; margin-top: 0.25rem;">
                                    {{ $documento->descripcion }}
                                </div>
                                @endif
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                @if($documento->verificado)
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #d4edda; color: #28a745; border-radius: 6px;" title="Verificado">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @endif
                                <a href="{{ asset($documento->ruta_archivo) }}"
                                   download="{{ $documento->nombre_archivo }}"
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #4A7C59; color: white; border-radius: 6px; text-decoration: none;"
                                   title="Descargar documento">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="{{ asset($documento->ruta_archivo) }}"
                                   target="_blank"
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #17a2b8; color: white; border-radius: 6px; text-decoration: none;"
                                   title="Ver documento">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div style="background: #fff3cd; padding: 1rem 1.5rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                <p style="margin: 0; color: #856404;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Sin documentos:</strong> Este proyecto no tiene documentos adjuntos.
                </p>
            </div>
            @endif
        </div>

        <!-- Columna derecha - Acciones de Revision -->
        <div>
            {{-- Resumen Rapido --}}
            <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #2D5A27; font-size: 1rem;">
                    <i class="fas fa-clipboard-list"></i> Resumen del Proyecto
                </h4>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">Codigo:</span>
                        <span style="font-weight: 600;">{{ $proyecto->codigo }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">Categoria:</span>
                        <span style="font-weight: 600;">{{ $proyecto->categoria->nombre }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">Monto:</span>
                        <span style="font-weight: 600; color: #2D5A27;">${{ number_format($proyecto->monto_objetivo, 0) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">ROI:</span>
                        <span style="font-weight: 600; color: #28a745;">{{ $proyecto->roi_anual }}%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">Duracion:</span>
                        <span style="font-weight: 600;">{{ $proyecto->duracion_meses }} meses</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="color: #6c757d;">Imagenes:</span>
                        <span style="font-weight: 600;">{{ $proyecto->imagenes->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: #6c757d;">Documentos:</span>
                        <span style="font-weight: 600;">{{ $proyecto->documentos->count() }}</span>
                    </div>
                </div>
            </div>

            @if($proyecto->estado === 'en_revision')
            <!-- Formulario de Aprobacion -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 1.5rem 0; color: #28a745; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-check-circle"></i> Aprobar Proyecto
                </h4>

                <form action="{{ route('admin.projects.approve', $proyecto->id) }}" method="POST" id="approveForm">
                    @csrf

                    <x-agromarket.form-group
                        label="Notas de Aprobacion (Opcional)"
                        name="notas_aprobacion"
                        type="textarea"
                        icon="fas fa-comment"
                        placeholder="Comentarios para el agricultor..."
                    ></x-agromarket.form-group>

                    <div style="margin-top: 1.5rem;">
                        <x-agromarket.button
                            variant="primary"
                            icon="fas fa-check"
                            type="button"
                            onclick="confirmarAprobacion()"
                            style="width: 100%;"
                        >
                            Aprobar Proyecto
                        </x-agromarket.button>
                    </div>
                </form>
            </div>

            <!-- Formulario de Rechazo -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 1.5rem 0; color: #dc3545; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-times-circle"></i> Rechazar Proyecto
                </h4>

                <form action="{{ route('admin.projects.reject', $proyecto->id) }}" method="POST" id="rejectForm">
                    @csrf

                    <x-agromarket.form-group
                        label="Motivo de Rechazo (Requerido)"
                        name="motivo_rechazo"
                        type="textarea"
                        icon="fas fa-exclamation-circle"
                        placeholder="Explica por que se rechaza el proyecto..."
                        required
                    ></x-agromarket.form-group>

                    <div style="margin-top: 1.5rem;">
                        <x-agromarket.button
                            variant="outline"
                            icon="fas fa-times"
                            type="button"
                            onclick="confirmarRechazo()"
                            style="width: 100%; border-color: #dc3545; color: #dc3545;"
                        >
                            Rechazar Proyecto
                        </x-agromarket.button>
                    </div>
                </form>
            </div>
            @else
            <!-- Proyecto ya fue revisado -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 1rem 0; color: #6C757D;">
                    <i class="fas fa-info-circle"></i> Estado del Proyecto
                </h4>
                <p style="margin: 0; color: #495057;">
                    Este proyecto ya ha sido revisado y esta en estado:
                    <strong>{{ match($proyecto->estado) {
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'en_recaudacion' => 'En Recaudacion',
                        default => $proyecto->estado
                    } }}</strong>
                </p>

                @if($proyecto->aprobado_por && $proyecto->aprobado_at)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
                    <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">
                        <strong>Aprobado por:</strong> {{ $proyecto->aprobador->name }}<br>
                        <strong>Fecha:</strong> {{ $proyecto->aprobado_at->format('d/m/Y H:i') }}
                    </p>
                    @if($proyecto->notas_aprobacion)
                    <p style="margin: 1rem 0 0 0; font-size: 0.875rem; color: #495057;">
                        <strong>Notas:</strong> {{ $proyecto->notas_aprobacion }}
                    </p>
                    @endif
                </div>
                @endif

                @if($proyecto->estado === 'rechazado' && $proyecto->motivo_rechazo)
                <div style="margin-top: 1.5rem; padding: 1rem; background: #f8d7da; border-radius: 8px; border-left: 4px solid #dc3545;">
                    <p style="margin: 0; font-size: 0.875rem; color: #721c24;">
                        <strong>Motivo de rechazo:</strong><br>
                        {{ $proyecto->motivo_rechazo }}
                    </p>
                </div>
                @endif
            </div>
            @endif

            <!-- Informacion adicional -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #2D5A27; font-size: 1rem;">
                    <i class="fas fa-clock"></i> Informacion de Envio
                </h4>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">
                    <strong>Enviado:</strong> {{ $proyecto->updated_at->format('d/m/Y H:i') }}<br>
                    <strong>Creado:</strong> {{ $proyecto->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // Confirmar aprobacion con SweetAlert
        function confirmarAprobacion() {
            Swal.fire({
                title: 'Aprobar este proyecto?',
                text: 'Pasara automaticamente a estado EN RECAUDACION',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approveForm').submit();
                }
            });
        }

        // Confirmar rechazo con SweetAlert
        function confirmarRechazo() {
            // Validar que se haya ingresado el motivo
            const motivo = document.querySelector('textarea[name="motivo_rechazo"]').value.trim();

            if (!motivo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Debes ingresar el motivo del rechazo antes de continuar.',
                    confirmButtonColor: '#2D5A27'
                });
                return;
            }

            Swal.fire({
                title: 'Rechazar este proyecto?',
                text: 'El agricultor recibira el motivo y podra corregirlo',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, rechazar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectForm').submit();
                }
            });
        }
    </script>
    @endpush

    @push('styles')
    <style>
        /* Layout principal responsive */
        div[style*="grid-template-columns: 2fr 1fr"] {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) !important;
        }

        /* Grid de 3 columnas */
        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }

        /* Grid de 2 columnas */
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
        }

        /* Tabla responsive */
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        @media (max-width: 900px) {
            div[style*="grid-template-columns: 2fr 1fr"],
            div[style*="grid-template-columns: repeat(2, 1fr)"],
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 640px) {
            div[style*="padding: 2rem"] {
                padding: 1rem !important;
            }

            div[style*="padding: 1.5rem"] {
                padding: 1rem !important;
            }

            div[style*="gap: 1.5rem"] {
                gap: 1rem !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
