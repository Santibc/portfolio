<x-app-layout>
    <x-agromarket.page-header
        title="{{ $proyecto->nombre }}"
        description="Código: {{ $proyecto->codigo }}"
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
                        Enviar a Revisión
                    </x-agromarket.button>
                </form>
            @endif
        </x-slot>
    </x-agromarket.page-header>


    @if($proyecto->estado === 'rechazado' && $proyecto->motivo_rechazo)
        <div style="background: #f8d7da; color: #721c24; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">
            <h4 style="margin: 0 0 0.5rem 0;"><i class="fas fa-times-circle"></i> Proyecto Rechazado</h4>
            <p style="margin: 0;"><strong>Motivo:</strong> {{ $proyecto->motivo_rechazo }}</p>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">Puedes editar el proyecto y volverlo a enviar a revisión.</p>
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

    <!-- Estado y Categoría -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <x-agromarket.stat-card
            icon="fas fa-flag"
            value="{{ match($proyecto->estado) {
                'borrador' => 'Borrador',
                'en_revision' => 'En Revisión',
                'rechazado' => 'Rechazado',
                'aprobado' => 'Aprobado',
                'en_recaudacion' => 'En Recaudación',
                'fondeado' => 'Fondeado',
                'en_ejecucion' => 'En Ejecución',
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
            title="Categoría"
            color="primary"
        />

        <x-agromarket.stat-card
            icon="fas fa-exclamation-triangle"
            :value="ucfirst($proyecto->nivel_riesgo)"
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
            title="Fecha Creación"
            color="secondary"
        />
    </div>

    <!-- Información del Proyecto -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-info-circle"></i> Descripción
        </h3>
        <p style="line-height: 1.6; color: #495057; margin: 0;">{{ $proyecto->descripcion }}</p>

        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
            <p style="margin: 0; color: #6C757D;">
                <i class="fas fa-map-marker-alt"></i> <strong>Ubicación:</strong> {{ $proyecto->ubicacion }}
                @if($proyecto->coordenadas)
                    <span style="margin-left: 1rem;"><i class="fas fa-globe"></i> {{ $proyecto->coordenadas }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Información Financiera -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <x-agromarket.stat-card
            icon="fas fa-money-bill-wave"
            :value="'$' . number_format($proyecto->monto_objetivo, 0)"
            title="Monto Objetivo"
            description="Meta de recaudación"
            color="primary"
        />

        <x-agromarket.stat-card
            icon="fas fa-percentage"
            :value="$proyecto->roi_anual . '%'"
            title="ROI Anual"
            description="Retorno esperado"
            color="success"
        />

        <x-agromarket.stat-card
            icon="fas fa-calendar-alt"
            :value="$proyecto->duracion_meses . ' meses'"
            title="Duración"
            description="Plazo del proyecto"
            color="warning"
        />
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <x-agromarket.stat-card
            icon="fas fa-coins"
            :value="'$' . number_format($proyecto->inversion_minima, 0)"
            title="Inversión Mínima"
            color="secondary"
        />

        @if($proyecto->inversion_maxima)
        <x-agromarket.stat-card
            icon="fas fa-coins"
            :value="'$' . number_format($proyecto->inversion_maxima, 0)"
            title="Inversión Máxima"
            color="secondary"
        />
        @endif

        <x-agromarket.stat-card
            icon="fas fa-clock"
            :value="$proyecto->periodo_dividendos_dias . ' días'"
            title="Período Dividendos"
            description="Frecuencia de pago"
            color="primary"
        />
    </div>

    <!-- Fechas -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-calendar"></i> Cronograma
        </h3>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Inicio Recaudación</p>
                <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                    {{ $proyecto->fecha_inicio_recaudacion?->format('d/m/Y') ?? 'No definida' }}
                </p>
            </div>
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Cierre Recaudación</p>
                <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                    {{ $proyecto->fecha_cierre_recaudacion?->format('d/m/Y') ?? 'No definida' }}
                </p>
            </div>

            @if($proyecto->fecha_inicio_proyecto)
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Inicio Proyecto</p>
                <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                    {{ $proyecto->fecha_inicio_proyecto->format('d/m/Y') }}
                </p>
            </div>
            @endif

            @if($proyecto->fecha_fin_proyecto)
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Fin Proyecto</p>
                <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                    {{ $proyecto->fecha_fin_proyecto->format('d/m/Y') }}
                </p>
            </div>
            @endif

            @if($proyecto->fecha_primer_dividendo)
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Primer Dividendo</p>
                <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                    {{ $proyecto->fecha_primer_dividendo->format('d/m/Y') }}
                </p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
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

        // Confirmar envío a revisión con SweetAlert
        function confirmarEnvioRevision() {
            Swal.fire({
                title: '¿Enviar proyecto a revisión?',
                text: 'No podrás editarlo mientras esté en revisión',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2D5A27',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, enviar',
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
        /* Hacer grids flexibles para adaptarse al zoom y diferentes tamaños */
        /* Grid de 4 columnas - mínimo 200px por columna */
        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }

        /* Grid de 3 columnas - mínimo 220px por columna */
        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
        }

        /* Grid de 2 columnas - mínimo 280px por columna */
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
        }

        /* Ajustes para cards en tamaños intermedios */
        @media (max-width: 1200px) {
            .summary-card {
                padding: 1.25rem !important;
            }

            .summary-card .card-value {
                font-size: 1.5rem !important;
            }

            .summary-card .card-icon {
                width: 50px !important;
                height: 50px !important;
                font-size: 1.25rem !important;
            }

            .summary-card .card-label,
            .summary-card .card-description {
                font-size: 0.8rem !important;
            }
        }

        @media (max-width: 900px) {
            .summary-card {
                padding: 1rem !important;
            }

            .summary-card .card-value {
                font-size: 1.25rem !important;
                word-break: break-word;
            }

            .summary-card .card-icon {
                width: 45px !important;
                height: 45px !important;
                font-size: 1.1rem !important;
            }

            .summary-card .card-label {
                font-size: 0.75rem !important;
            }
        }

        /* Responsive adicional para pantallas muy pequeñas */
        @media (max-width: 640px) {
            /* Ajustar tamaños mínimos en móviles */
            div[style*="grid-template-columns: repeat(4, 1fr)"],
            div[style*="grid-template-columns: repeat(3, 1fr)"],
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }

            .summary-card {
                padding: 1.5rem !important;
            }

            .summary-card .card-value {
                font-size: 1.75rem !important;
            }

            .summary-card .card-icon {
                width: 55px !important;
                height: 55px !important;
                font-size: 1.3rem !important;
            }

            /* Ajustar padding en móviles pequeños */
            div[style*="padding: 2rem"] {
                padding: 1rem !important;
            }

            /* Ajustar gaps */
            div[style*="gap: 1.5rem"] {
                gap: 1rem !important;
            }
        }

        /* Prevenir overflow horizontal en zoom */
        body {
            overflow-x: hidden;
        }

        /* Hacer que las cards sean flexibles */
        .summary-card {
            min-width: 0;
            overflow: hidden;
        }

        /* Asegurar que el texto no se corte */
        .summary-card .card-value,
        .summary-card .card-label,
        .summary-card .card-description {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Permitir wrap en valores monetarios largos */
        .summary-card .card-value {
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
    @endpush
</x-app-layout>
