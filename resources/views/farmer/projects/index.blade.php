<x-app-layout>
    <x-agromarket.page-header
        title="Mis Proyectos"
        description="Gestiona tus proyectos agrícolas"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="primary"
                icon="fas fa-plus"
                onclick="window.location.href='{{ route('farmer.projects.create') }}'"
            >
                Crear Proyecto
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>


    <!-- Filtros -->
    <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.875rem; color: #6C757D; margin-bottom: 0.5rem;">Filtrar por Estado</label>
                <select id="estadoFilter" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="">Todos</option>
                    <option value="borrador">Borrador</option>
                    <option value="en_revision">En Revisión</option>
                    <option value="rechazado">Rechazado</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="en_recaudacion">En Recaudación</option>
                    <option value="fondeado">Fondeado</option>
                    <option value="en_ejecucion">En Ejecución</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.875rem; color: #6C757D; margin-bottom: 0.5rem;">Buscar</label>
                <input type="text" id="searchInput" placeholder="Buscar por nombre o código..." style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
            </div>
        </div>
    </div>

    <!-- Tabla de Proyectos -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <x-agromarket.data-table :headers="['Código', 'Nombre', 'Categoría', 'Monto Objetivo', 'Estado', 'Fecha Creación', 'Acciones']">
            @forelse($proyectos as $proyecto)
                <x-agromarket.table-row>
                    <x-agromarket.table-cell>
                        <strong>{{ $proyecto->codigo }}</strong>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        {{ $proyecto->nombre }}
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
                        <x-agromarket.badge
                            variant="{{ match($proyecto->estado) {
                                'borrador' => 'secondary',
                                'en_revision' => 'warning',
                                'rechazado' => 'danger',
                                'aprobado' => 'success',
                                'en_recaudacion' => 'primary',
                                'fondeado' => 'success',
                                'en_ejecucion' => 'primary',
                                'finalizado' => 'success',
                                default => 'secondary'
                            } }}"
                            type="status"
                        >
                            {{ match($proyecto->estado) {
                                'borrador' => 'Borrador',
                                'en_revision' => 'En Revisión',
                                'rechazado' => 'Rechazado',
                                'aprobado' => 'Aprobado',
                                'en_recaudacion' => 'En Recaudación',
                                'fondeado' => 'Fondeado',
                                'en_ejecucion' => 'En Ejecución',
                                'finalizado' => 'Finalizado',
                                default => $proyecto->estado
                            } }}
                        </x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        {{ $proyecto->created_at->format('d/m/Y') }}
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div style="display: flex; gap: 0.5rem;">
                            <x-agromarket.button
                                variant="icon"
                                icon="fas fa-eye"
                                size="sm"
                                onclick="window.location.href='{{ route('farmer.projects.show', $proyecto->id) }}'"
                                title="Ver detalles"
                            >
                            </x-agromarket.button>

                            @if(in_array($proyecto->estado, ['borrador', 'rechazado']))
                                <x-agromarket.button
                                    variant="icon"
                                    icon="fas fa-edit"
                                    size="sm"
                                    onclick="window.location.href='{{ route('farmer.projects.edit', $proyecto->id) }}'"
                                    title="Editar datos"
                                >
                                </x-agromarket.button>

                                <x-agromarket.button
                                    variant="icon"
                                    icon="fas fa-images"
                                    size="sm"
                                    onclick="window.location.href='{{ route('farmer.projects.files', $proyecto->id) }}'"
                                    title="Gestionar archivos"
                                >
                                </x-agromarket.button>
                            @endif

                            @if($proyecto->estado === 'borrador')
                                <form action="{{ route('farmer.projects.submit-review', $proyecto->id) }}" method="POST" class="submit-review-form" style="display: inline;">
                                    @csrf
                                    <x-agromarket.button
                                        variant="icon"
                                        icon="fas fa-paper-plane"
                                        size="sm"
                                        type="button"
                                        title="Enviar a revisión"
                                        onclick="confirmarEnvioRevisionIndex(this.closest('form'))"
                                    >
                                    </x-agromarket.button>
                                </form>
                            @endif
                        </div>
                    </x-agromarket.table-cell>
                </x-agromarket.table-row>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #6C757D;">
                        <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>No tienes proyectos aún. Crea tu primer proyecto.</p>
                    </td>
                </tr>
            @endforelse
        </x-agromarket.data-table>
    </div>

    @push('scripts')
    <script>
        // Filtro por estado
        document.getElementById('estadoFilter').addEventListener('change', function() {
            filterTable();
        });

        // Búsqueda
        document.getElementById('searchInput').addEventListener('keyup', function() {
            filterTable();
        });

        function filterTable() {
            const estadoFilter = document.getElementById('estadoFilter').value.toLowerCase();
            const searchFilter = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.table-row');

            rows.forEach(row => {
                const estado = row.querySelector('.status-badge')?.textContent.toLowerCase() || '';
                const text = row.textContent.toLowerCase();

                const matchEstado = !estadoFilter || estado.includes(estadoFilter);
                const matchSearch = !searchFilter || text.includes(searchFilter);

                row.style.display = matchEstado && matchSearch ? '' : 'none';
            });
        }

        // SweetAlert para mensajes de sesión
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
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
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        // Confirmar envío a revisión desde la lista con SweetAlert
        function confirmarEnvioRevisionIndex(form) {
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
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
