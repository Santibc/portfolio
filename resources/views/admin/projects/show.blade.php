<x-app-layout>
    <x-agromarket.page-header
        title="Revisar Proyecto"
        description="Código: {{ $proyecto->codigo }}"
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


    <!-- Información del Proyecto -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Columna izquierda - Detalles del Proyecto -->
        <div>
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; color: #2D5A27;">{{ $proyecto->nombre }}</h3>

                <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <x-agromarket.badge variant="primary" type="category">
                        {{ $proyecto->categoria->nombre }}
                    </x-agromarket.badge>
                    <x-agromarket.badge variant="{{ match($proyecto->nivel_riesgo) {
                        'bajo' => 'success',
                        'medio' => 'warning',
                        'alto' => 'danger',
                        default => 'secondary'
                    } }}">
                        Riesgo: {{ ucfirst($proyecto->nivel_riesgo) }}
                    </x-agromarket.badge>
                </div>

                <p style="line-height: 1.6; color: #495057; margin: 0 0 1.5rem 0;">{{ $proyecto->descripcion }}</p>

                <div style="padding-top: 1.5rem; border-top: 1px solid #f0f0f0;">
                    <p style="margin: 0; color: #6C757D;">
                        <i class="fas fa-map-marker-alt"></i> <strong>Ubicación:</strong> {{ $proyecto->ubicacion }}
                        @if($proyecto->coordenadas)
                            <br><i class="fas fa-globe"></i> <strong>Coordenadas:</strong> {{ $proyecto->coordenadas }}
                        @endif
                    </p>
                    <p style="margin: 0.5rem 0 0 0; color: #6C757D;">
                        <i class="fas fa-user"></i> <strong>Agricultor:</strong> {{ $proyecto->agricultor->name }}
                    </p>
                </div>
            </div>

            <!-- Métricas Financieras -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
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
                    icon="fas fa-calendar-alt"
                    :value="$proyecto->duracion_meses . ' meses'"
                    title="Duración"
                    color="warning"
                />
            </div>

            <!-- Cronograma -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-calendar"></i> Fechas Importantes
                </h4>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Inicio Recaudación</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                            {{ $proyecto->fecha_inicio_recaudacion?->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Cierre Recaudación</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                            {{ $proyecto->fecha_cierre_recaudacion?->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Período Dividendos</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                            {{ $proyecto->periodo_dividendos_dias }} días
                        </p>
                    </div>
                    @if($proyecto->periodo_cosecha_meses)
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #6C757D;">Período Cosecha</p>
                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #2D5A27;">
                            {{ $proyecto->periodo_cosecha_meses }} meses
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna derecha - Acciones de Revisión -->
        <div>
            @if($proyecto->estado === 'en_revision')
            <!-- Formulario de Aprobación -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 1.5rem 0; color: #28a745; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                    <i class="fas fa-check-circle"></i> Aprobar Proyecto
                </h4>

                <form action="{{ route('admin.projects.approve', $proyecto->id) }}" method="POST" id="approveForm">
                    @csrf

                    <x-agromarket.form-group
                        label="Notas de Aprobación (Opcional)"
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
                        placeholder="Explica por qué se rechaza el proyecto..."
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
                    Este proyecto ya ha sido revisado y está en estado:
                    <strong>{{ match($proyecto->estado) {
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'en_recaudacion' => 'En Recaudación',
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

            <!-- Información adicional -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #2D5A27; font-size: 1rem;">
                    <i class="fas fa-clock"></i> Información de Envío
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

        // Confirmar aprobación con SweetAlert
        function confirmarAprobacion() {
            Swal.fire({
                title: '¿Aprobar este proyecto?',
                text: 'Pasará automáticamente a estado EN RECAUDACIÓN',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar',
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
                title: '¿Rechazar este proyecto?',
                text: 'El agricultor recibirá el motivo y podrá corregirlo',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, rechazar',
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
        /* Hacer grids flexibles para adaptarse al zoom y diferentes tamaños */

        /* Layout principal de 2 columnas - se adapta automáticamente */
        div[style*="grid-template-columns: 2fr 1fr"] {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) !important;
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
            /* Forzar 1 columna en móviles pequeños */
            div[style*="grid-template-columns: 2fr 1fr"],
            div[style*="grid-template-columns: repeat(2, 1fr)"],
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
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

        /* Hacer que los formularios sean flexibles */
        form {
            max-width: 100%;
        }
    </style>
    @endpush
</x-app-layout>
