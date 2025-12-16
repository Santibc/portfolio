<x-app-layout>
    <x-agromarket.page-header
        title="{{ $proyecto->nombre }}"
        description="Codigo: {{ $proyecto->codigo }}"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('farmer.projects.index') }}'"
            >
                Volver
            </x-agromarket.button>

            @if(in_array($proyecto->estado, ['borrador', 'rechazado']))
                <x-agromarket.button
                    variant="primary"
                    icon="fas fa-edit"
                    onclick="window.location.href='{{ route('farmer.projects.edit', $proyecto->id) }}'"
                >
                    Editar
                </x-agromarket.button>
            @endif

            @if($proyecto->estado === 'borrador')
                <form action="{{ route('farmer.projects.submit-review', $proyecto->id) }}" method="POST" id="submitReviewForm" style="display: inline;">
                    @csrf
                    <x-agromarket.button
                        variant="primary"
                        icon="fas fa-paper-plane"
                        type="button"
                        onclick="confirmarEnvioRevision()"
                    >
                        Enviar a Revision
                    </x-agromarket.button>
                </form>
            @endif
        </x-slot>
    </x-agromarket.page-header>


    @if($proyecto->estado === 'rechazado' && $proyecto->motivo_rechazo)
        <div style="background: #f8d7da; color: #721c24; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">
            <h4 style="margin: 0 0 0.5rem 0;"><i class="fas fa-times-circle"></i> Proyecto Rechazado</h4>
            <p style="margin: 0;"><strong>Motivo:</strong> {{ $proyecto->motivo_rechazo }}</p>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">Puedes editar el proyecto y volverlo a enviar a revision.</p>
        </div>
    @endif

    @if($proyecto->notas_aprobacion && $proyecto->estado === 'en_recaudacion')
        <div style="background: #d4edda; color: #155724; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745;">
            <h4 style="margin: 0 0 0.5rem 0;"><i class="fas fa-check-circle"></i> Proyecto Aprobado</h4>
            <p style="margin: 0;"><strong>Notas:</strong> {{ $proyecto->notas_aprobacion }}</p>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                Aprobado por: {{ $proyecto->aprobador->name ?? 'N/A' }} el {{ $proyecto->aprobado_at?->format('d/m/Y H:i') }}
            </p>
        </div>
    @endif

    <!-- Estado y Categoria -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <x-agromarket.stat-card
            icon="fas fa-flag"
            value="{{ match($proyecto->estado) {
                'borrador' => 'Borrador',
                'en_revision' => 'En Revision',
                'rechazado' => 'Rechazado',
                'aprobado' => 'Aprobado',
                'en_recaudacion' => 'En Recaudacion',
                'fondeado' => 'Fondeado',
                'en_ejecucion' => 'En Ejecucion',
                'finalizado' => 'Finalizado',
                default => $proyecto->estado
            } }}"
            title="Estado"
            :color="match($proyecto->estado) {
                'borrador' => 'secondary',
                'en_revision' => 'warning',
                'rechazado' => 'danger',
                'aprobado', 'fondeado', 'finalizado' => 'success',
                'en_recaudacion', 'en_ejecucion' => 'primary',
                default => 'secondary'
            }"
        />

        <x-agromarket.stat-card
            icon="fas fa-folder"
            :value="$proyecto->categoria->nombre ?? 'N/A'"
            title="Categoria"
            color="primary"
        />

        <x-agromarket.stat-card
            icon="fas fa-exclamation-triangle"
            :value="ucfirst($proyecto->nivel_riesgo ?? 'N/A')"
            title="Nivel de Riesgo"
            :color="match($proyecto->nivel_riesgo) {
                'bajo' => 'success',
                'medio' => 'warning',
                'alto' => 'danger',
                default => 'secondary'
            }"
        />

        <x-agromarket.stat-card
            icon="fas fa-calendar-plus"
            :value="$proyecto->created_at->format('d/m/Y')"
            title="Fecha Creacion"
            color="secondary"
        />
    </div>

    {{-- ==================== FASE 1: INFORMACION BASICA ==================== --}}
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
            <i class="fas fa-file-alt"></i> FASE 1: Informacion Basica
        </h3>

        {{-- Descripcion del Proyecto --}}
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                <i class="fas fa-info-circle"></i> Descripcion del Proyecto
            </h4>
            <p style="line-height: 1.6; color: #495057; margin: 0;">{{ $proyecto->descripcion }}</p>

            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
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
        </div>

        {{-- Datos del Cultivo --}}
        @if($proyecto->tipo_cultivo || $proyecto->area_hectareas || $proyecto->etapa_cultivo || $proyecto->ano_inicio_cultivo)
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
        @endif
    </div>

    {{-- ==================== FASE 2: EVALUACION TECNICA ==================== --}}
    @php $perfil = $proyecto->agricultor->perfilAgricultor; @endphp
    @if($perfil)
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
            <i class="fas fa-clipboard-check"></i> FASE 2: Evaluacion Tecnica
        </h3>

        {{-- Tipo de Persona --}}
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

                @if($perfil->direccion_finca)
                <div style="grid-column: span 2;">
                    <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Direccion de la Finca</p>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $perfil->direccion_finca }}</p>
                </div>
                @endif
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
    </div>
    @endif

    {{-- ==================== FASE 3: EVALUACION FINANCIERA ==================== --}}
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #4A7C59; padding-bottom: 0.75rem;">
            <i class="fas fa-chart-line"></i> FASE 3: Evaluacion Financiera
        </h3>

        {{-- Metricas Principales --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
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
            <x-agromarket.stat-card
                icon="fas fa-calendar-alt"
                :value="$proyecto->duracion_meses . ' meses'"
                title="Duracion"
                color="warning"
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
                icon="fas fa-clock"
                :value="$proyecto->periodo_dividendos_dias . ' dias'"
                title="Periodo Dividendos"
                color="primary"
            />
        </div>

        {{-- Fechas --}}
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1rem;">
                <i class="fas fa-calendar"></i> Cronograma
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
                @if($proyecto->fecha_inicio_proyecto)
                <div>
                    <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Inicio Proyecto</p>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_inicio_proyecto->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($proyecto->fecha_fin_proyecto)
                <div>
                    <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Fin Proyecto</p>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_fin_proyecto->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($proyecto->fecha_primer_dividendo)
                <div>
                    <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Primer Dividendo</p>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->fecha_primer_dividendo->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($proyecto->periodo_cosecha_meses)
                <div>
                    <p style="margin: 0; font-size: 0.8rem; color: #6c757d;">Periodo Cosecha</p>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;">{{ $proyecto->periodo_cosecha_meses }} meses</p>
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

    <!-- Galeria de Imagenes -->
    @if($proyecto->imagenes->count() > 0)
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-images"></i> Galeria de Imagenes
            <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->imagenes->count() }} imagenes)</span>
        </h3>

        <div class="project-gallery" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
            @foreach($proyecto->imagenes->sortBy('orden') as $imagen)
                <div class="gallery-item" style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    @if($imagen->es_principal)
                        <span style="position: absolute; top: 8px; left: 8px; background: #D4AF37; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; z-index: 1;">
                            <i class="fas fa-star"></i> Principal
                        </span>
                    @endif
                    <a href="{{ asset($imagen->ruta_imagen) }}" target="_blank" title="{{ $imagen->titulo }}">
                        <img src="{{ asset($imagen->thumbnail ?? $imagen->ruta_imagen) }}"
                             alt="{{ $imagen->titulo }}"
                             style="width: 100%; height: 150px; object-fit: cover; display: block;">
                    </a>
                    @if($imagen->titulo)
                        <div style="padding: 0.5rem; background: #f8f9fa; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $imagen->titulo }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Documentos del Proyecto -->
    @if($proyecto->documentos->count() > 0)
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-file-alt"></i> Documentos
            <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->documentos->count() }} documentos)</span>
        </h3>

        <div class="documents-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
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
            @foreach($proyecto->documentos as $documento)
                <div class="document-item" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                    <div class="doc-icon" style="width: 48px; height: 48px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #6c757d;">
                        @if(str_ends_with($documento->nombre_archivo, '.pdf'))
                            <i class="fas fa-file-pdf" style="color: #dc3545;"></i>
                        @elseif(str_ends_with($documento->nombre_archivo, '.doc') || str_ends_with($documento->nombre_archivo, '.docx'))
                            <i class="fas fa-file-word" style="color: #2b5797;"></i>
                        @else
                            <i class="fas fa-file"></i>
                        @endif
                    </div>
                    <div class="doc-info" style="flex: 1; min-width: 0;">
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
                    </div>
                    <div class="doc-actions" style="display: flex; gap: 0.5rem;">
                        @if($documento->verificado)
                            <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">
                                <i class="fas fa-check"></i> Verificado
                            </span>
                        @endif
                        <a href="{{ route('farmer.projects.documents.download', $documento->id) }}"
                           class="btn-download"
                           style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #4A7C59; color: white; border-radius: 6px; text-decoration: none;"
                           title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Exito!',
                text: '{{ session('success') }}',
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
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // Confirmar envio a revision con SweetAlert
        function confirmarEnvioRevision() {
            Swal.fire({
                title: 'Enviar proyecto a revision?',
                text: 'No podras editarlo mientras este en revision',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2D5A27',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('submitReviewForm').submit();
                }
            });
        }
    </script>
    @endpush

    @push('styles')
    <style>
        /* Hacer grids flexibles */
        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }

        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
        }

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
            div[style*="grid-template-columns: repeat(4, 1fr)"],
            div[style*="grid-template-columns: repeat(3, 1fr)"],
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
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
