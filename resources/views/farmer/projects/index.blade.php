<x-app-layout>
    <x-agromarket.page-header
        title="Mis Proyectos"
        description="Gestiona tus proyectos agricolas"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="primary"
                icon="fas fa-plus"
                onclick="window.location.href='{{ route('farmer.projects.create') }}'"
            >
                Nuevo Proyecto
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Estadisticas -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <x-agromarket.stat-card
            icon="fas fa-folder"
            :value="$proyectos->count()"
            title="Total Proyectos"
            color="primary"
        />
        <x-agromarket.stat-card
            icon="fas fa-edit"
            :value="$proyectos->where('estado', 'borrador')->count()"
            title="En Borrador"
            color="secondary"
        />
        <x-agromarket.stat-card
            icon="fas fa-clock"
            :value="$proyectos->where('estado', 'en_revision')->count()"
            title="En Revision"
            color="warning"
        />
        <x-agromarket.stat-card
            icon="fas fa-check-circle"
            :value="$proyectos->whereIn('estado', ['aprobado', 'en_recaudacion', 'fondeado'])->count()"
            title="Aprobados"
            color="success"
        />
    </div>

    <!-- Lista de Proyectos -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($proyectos->count() > 0)
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Codigo</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Proyecto</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Categoria</th>
                        <th style="text-align: right; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Monto</th>
                        <th style="text-align: center; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Fase</th>
                        <th style="text-align: center; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Estado</th>
                        <th style="text-align: right; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proyectos as $proyecto)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 1rem;">
                                <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                                    {{ $proyecto->codigo }}
                                </code>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 500; color: #333;">{{ $proyecto->nombre }}</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">{{ Str::limit($proyecto->tipo_cultivo ?? $proyecto->descripcion, 30) }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <x-agromarket.badge variant="primary" type="category">
                                    {{ $proyecto->categoria->nombre ?? 'N/A' }}
                                </x-agromarket.badge>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <span style="font-weight: 600; color: #2D5A27;">${{ number_format($proyecto->monto_objetivo, 0) }}</span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @php
                                    $faseActual = $proyecto->current_phase ?? 1;
                                @endphp
                                <div style="display: flex; justify-content: center; gap: 4px;">
                                    @for($i = 1; $i <= 3; $i++)
                                        <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;
                                            {{ $i < $faseActual ? 'background: #28a745; color: white;' : ($i == $faseActual ? 'background: #ffc107; color: #333;' : 'background: #e9ecef; color: #6c757d;') }}">
                                            @if($i < $faseActual)
                                                <i class="fas fa-check" style="font-size: 0.6rem;"></i>
                                            @else
                                                {{ $i }}
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @php
                                    $estadoConfig = match($proyecto->estado) {
                                        'borrador' => ['variant' => 'secondary', 'text' => 'Borrador'],
                                        'en_revision' => ['variant' => 'warning', 'text' => 'En Revision'],
                                        'aprobado', 'en_recaudacion' => ['variant' => 'success', 'text' => 'Aprobado'],
                                        'rechazado' => ['variant' => 'danger', 'text' => 'Rechazado'],
                                        'fondeado' => ['variant' => 'success', 'text' => 'Fondeado'],
                                        'en_ejecucion' => ['variant' => 'primary', 'text' => 'En Ejecucion'],
                                        'finalizado' => ['variant' => 'success', 'text' => 'Finalizado'],
                                        default => ['variant' => 'secondary', 'text' => ucfirst($proyecto->estado)]
                                    };
                                @endphp
                                <x-agromarket.badge :variant="$estadoConfig['variant']">
                                    {{ $estadoConfig['text'] }}
                                </x-agromarket.badge>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    {{-- Ver detalles --}}
                                    <a href="{{ route('farmer.projects.show', $proyecto) }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e9ecef; color: #495057; border-radius: 6px; text-decoration: none;"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(in_array($proyecto->estado, ['borrador', 'rechazado']))
                                        {{-- Continuar a siguiente fase --}}
                                        @if($faseActual == 1)
                                            <a href="{{ route('farmer.projects.phase2', $proyecto) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #ffc107; color: #333; border-radius: 6px; text-decoration: none;"
                                               title="Continuar Fase 2">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @elseif($faseActual == 2)
                                            <a href="{{ route('farmer.projects.phase3', $proyecto) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #ffc107; color: #333; border-radius: 6px; text-decoration: none;"
                                               title="Continuar Fase 3">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @elseif($faseActual >= 3)
                                            <a href="{{ route('farmer.projects.files', $proyecto) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #17a2b8; color: white; border-radius: 6px; text-decoration: none;"
                                               title="Gestionar archivos">
                                                <i class="fas fa-images"></i>
                                            </a>
                                        @endif

                                        {{-- Editar Fase 1 --}}
                                        <a href="{{ route('farmer.projects.edit', $proyecto) }}"
                                           style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none;"
                                           title="Editar datos basicos">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Enviar a revision (solo si completo y en borrador) --}}
                                    @if($proyecto->estado === 'borrador' && ($proyecto->is_complete ?? false))
                                        <form action="{{ route('farmer.projects.submit-review', $proyecto->id) }}" method="POST" class="submit-review-form" style="display: inline;">
                                            @csrf
                                            <button type="button"
                                                    onclick="confirmarEnvioRevision(this.closest('form'))"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #28a745; color: white; border-radius: 6px; border: none; cursor: pointer;"
                                                    title="Enviar a revision">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #dee2e6; margin-bottom: 1rem;"></i>
                <p style="color: #6c757d; margin: 0;">No tienes proyectos aun.</p>
                <a href="{{ route('farmer.projects.create') }}"
                   style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #4A7C59; color: white; border-radius: 8px; text-decoration: none;">
                    <i class="fas fa-plus"></i> Crear mi primer proyecto
                </a>
            </div>
        @endif
    </div>

    <!-- Leyenda de Estados -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 1.5rem;">
        <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> Leyenda de Fases
        </h4>
        <div style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #ffc107; color: #333; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">1</div>
                <span>Fase 1: Datos Basicos</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #ffc107; color: #333; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">2</div>
                <span>Fase 2: Evaluacion Tecnica</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #ffc107; color: #333; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">3</div>
                <span>Fase 3: Evaluacion Financiera</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;"><i class="fas fa-check"></i></div>
                <span>Fase Completada</span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Excelente',
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

        // Confirmar envio a revision
        function confirmarEnvioRevision(form) {
            Swal.fire({
                title: 'Enviar proyecto a revision?',
                text: 'No podras editarlo mientras este en revision',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    @endpush

    @push('styles')
    <style>
        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important;
        }

        @media (max-width: 1200px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
    @endpush
</x-app-layout>
