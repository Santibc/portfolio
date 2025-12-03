<x-app-layout>
    <x-agromarket.page-header
        title="Proyectos en Revisión"
        description="Aprueba o rechaza proyectos enviados por agricultores"
    />


    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <x-agromarket.stat-card
            icon="fas fa-clock"
            :value="$proyectos->count()"
            title="Pendientes de Revisión"
            description="Requieren tu atención"
            color="warning"
        />

        <x-agromarket.stat-card
            icon="fas fa-check-circle"
            :value="$aprobadosHoy"
            title="Aprobados Hoy"
            description="Proyectos validados"
            color="success"
        />

        <x-agromarket.stat-card
            icon="fas fa-times-circle"
            :value="$rechazadosHoy"
            title="Rechazados Hoy"
            description="Requieren correcciones"
            color="danger"
        />
    </div>

    <!-- Tabla de Proyectos -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <x-agromarket.data-table :headers="['Código', 'Nombre', 'Agricultor', 'Categoría', 'Monto', 'ROI', 'Fecha Envío', 'Acciones']">
            @forelse($proyectos as $proyecto)
                <x-agromarket.table-row>
                    <x-agromarket.table-cell>
                        <strong>{{ $proyecto->codigo }}</strong>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        {{ $proyecto->nombre }}
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-circle" style="color: #6C757D;"></i>
                            {{ $proyecto->agricultor->name ?? 'N/A' }}
                        </div>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="primary" type="category">
                            {{ $proyecto->categoria->nombre ?? 'N/A' }}
                        </x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        ${{ number_format($proyecto->monto_objetivo, 0) }}
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        {{ $proyecto->roi_anual }}%
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        {{ $proyecto->updated_at->format('d/m/Y H:i') }}
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.button
                            variant="primary"
                            icon="fas fa-search"
                            size="sm"
                            onclick="window.location.href='{{ route('admin.projects.review.show', $proyecto->id) }}'"
                        >
                            Revisar
                        </x-agromarket.button>
                    </x-agromarket.table-cell>
                </x-agromarket.table-row>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: #6C757D;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; color: #28a745;"></i>
                        <p>No hay proyectos pendientes de revisión.</p>
                    </td>
                </tr>
            @endforelse
        </x-agromarket.data-table>
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
    </script>
    @endpush

    @push('styles')
    <style>
        /* Hacer grids flexibles para adaptarse al zoom y diferentes tamaños */
        /* Grid de 3 columnas - mínimo 220px por columna */
        div[style*="grid-template-columns: repeat(3, 1fr)"] {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
        }

        /* Responsive adicional para pantallas muy pequeñas */
        @media (max-width: 640px) {
            /* Forzar 1 columna en móviles pequeños */
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
                grid-template-columns: 1fr !important;
            }

            /* Ajustar gaps */
            div[style*="gap: 1.5rem"] {
                gap: 1rem !important;
            }

            /* Ajustar márgenes */
            div[style*="margin-bottom: 1.5rem"] {
                margin-bottom: 1rem !important;
            }
        }

        /* Hacer la tabla responsive con scroll horizontal */
        div[style*="background: white"][style*="border-radius: 12px"] {
            overflow-x: auto;
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

        /* Asegurar que la tabla no rompa el layout */
        .data-table {
            min-width: 800px;
        }
    </style>
    @endpush
</x-app-layout>
