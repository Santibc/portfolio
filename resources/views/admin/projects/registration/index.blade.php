<x-app-layout>
    <x-agromarket.page-header
        title="Registro de Proyectos"
        description="Proyectos creados por administradores"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="primary"
                icon="fas fa-plus"
                onclick="window.location.href='{{ route('admin.projects.registration.create') }}'"
            >
                Nuevo Proyecto
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <x-agromarket.stat-card
            icon="fas fa-folder"
            :value="$totalRegistrados"
            title="Total Registrados"
            color="primary"
        />
        <x-agromarket.stat-card
            icon="fas fa-edit"
            :value="$enBorrador"
            title="En Borrador"
            color="secondary"
        />
        <x-agromarket.stat-card
            icon="fas fa-clock"
            :value="$enRevision"
            title="En Revisión"
            color="warning"
        />
        <x-agromarket.stat-card
            icon="fas fa-check-circle"
            :value="$aprobados"
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
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Código</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Proyecto</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Categoría</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6c757d;">Agricultor</th>
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
                                <div style="font-size: 0.8rem; color: #6c757d;">{{ Str::limit($proyecto->tipo_cultivo, 30) }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <x-agromarket.badge variant="primary" type="category">
                                    {{ $proyecto->categoria->nombre }}
                                </x-agromarket.badge>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 500; color: #333;">{{ $proyecto->agricultor->name }}</div>
                                <div style="font-size: 0.8rem; color: #6c757d;">{{ $proyecto->agricultor->email }}</div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @php
                                    $faseActual = app(\App\Services\Project\ProjectFormService::class)->getCurrentPhase($proyecto);
                                @endphp
                                <div style="display: flex; justify-content: center; gap: 4px;">
                                    @for($i = 1; $i <= 3; $i++)
                                        <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;
                                            {{ $i < $faseActual ? 'background: #28a745; color: white;' : ($i == $faseActual ? 'background: #ffc107; color: #333;' : 'background: #e9ecef; color: #6c757d;') }}">
                                            {{ $i }}
                                        </div>
                                    @endfor
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @php
                                    $estadoConfig = match($proyecto->estado) {
                                        'borrador' => ['variant' => 'secondary', 'text' => 'Borrador'],
                                        'en_revision' => ['variant' => 'warning', 'text' => 'En Revisión'],
                                        'aprobado', 'en_recaudacion' => ['variant' => 'success', 'text' => 'Aprobado'],
                                        'rechazado' => ['variant' => 'danger', 'text' => 'Rechazado'],
                                        default => ['variant' => 'secondary', 'text' => ucfirst($proyecto->estado)]
                                    };
                                @endphp
                                <x-agromarket.badge :variant="$estadoConfig['variant']">
                                    {{ $estadoConfig['text'] }}
                                </x-agromarket.badge>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    <a href="{{ route('admin.projects.registration.show', $proyecto) }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e9ecef; color: #495057; border-radius: 6px; text-decoration: none;"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($proyecto->estado === 'borrador' || $proyecto->estado === 'rechazado')
                                        @if($faseActual == 1)
                                            <a href="{{ route('admin.projects.registration.phase2', $proyecto) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #ffc107; color: #333; border-radius: 6px; text-decoration: none;"
                                               title="Continuar Fase 2">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @elseif($faseActual == 2)
                                            <a href="{{ route('admin.projects.registration.phase3', $proyecto) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #ffc107; color: #333; border-radius: 6px; text-decoration: none;"
                                               title="Continuar Fase 3">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.projects.registration.edit', $proyecto) }}"
                                           style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #17a2b8; color: white; border-radius: 6px; text-decoration: none;"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $proyectos->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #dee2e6; margin-bottom: 1rem;"></i>
                <p style="color: #6c757d; margin: 0;">No hay proyectos registrados aún.</p>
                <a href="{{ route('admin.projects.registration.create') }}"
                   style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #4A7C59; color: white; border-radius: 8px; text-decoration: none;">
                    <i class="fas fa-plus"></i> Registrar primer proyecto
                </a>
            </div>
        @endif
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
    </script>
    @endpush

    @push('styles')
    <style>
        div[style*="grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        }
    </style>
    @endpush
</x-app-layout>
