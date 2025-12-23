<x-app-layout>
    <x-agromarket.page-header
        title="Revisar KYC - {{ $user->name }}"
        subtitle="Verificación de documentos de identidad"
    >
        <a href="{{ route('admin.kyc.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
    </x-agromarket.page-header>

    <div class="dashboard-row">
        {{-- Información del Usuario --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> Información del Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Nombre:</strong>
                            <p>{{ $user->name }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <p>{{ $user->email }}</p>
                        </div>
                        @if($user->telefono)
                            <div class="info-item">
                                <strong>Teléfono:</strong>
                                <p>{{ $user->telefono }}</p>
                            </div>
                        @endif
                        @if($user->documento_identidad)
                            <div class="info-item">
                                <strong>Documento:</strong>
                                <p>{{ $user->tipo_documento }}: {{ $user->documento_identidad }}</p>
                            </div>
                        @endif
                        @if($user->pais)
                            <div class="info-item">
                                <strong>País:</strong>
                                <p>{{ $user->pais }}</p>
                            </div>
                        @endif
                        <div class="info-item">
                            <strong>Estado KYC:</strong>
                            <p>
                                @if($user->kyc_status === 'en_revision')
                                    <x-agromarket.badge variant="warning" type="status">En Revisión</x-agromarket.badge>
                                @elseif($user->kyc_status === 'aprobado')
                                    <x-agromarket.badge variant="active" type="status">Aprobado</x-agromarket.badge>
                                @elseif($user->kyc_status === 'rechazado')
                                    <x-agromarket.badge variant="danger" type="status">Rechazado</x-agromarket.badge>
                                @else
                                    <x-agromarket.badge variant="info" type="status">Pendiente</x-agromarket.badge>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="dashboard-card actions-card mt-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks"></i> Acciones</h3>
                </div>
                <div class="card-body">
                    {{-- Aprobar KYC --}}
                    <form method="POST" action="{{ route('admin.kyc.approve', $user) }}" id="form-aprobar-kyc" class="mb-3">
                        @csrf
                        <button type="button" class="btn btn-success btn-block" onclick="confirmarAprobacion()">
                            <i class="fas fa-check-circle"></i> Aprobar KYC
                        </button>
                    </form>

                    {{-- Rechazar KYC --}}
                    <button type="button" class="btn btn-danger btn-block" onclick="mostrarModalRechazo()">
                        <i class="fas fa-times-circle"></i> Rechazar KYC
                    </button>
                </div>
            </div>
        </div>

        {{-- Documentos Subidos --}}
        <div class="dashboard-col-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt"></i> Documentos Subidos</h3>
                    <span class="text-muted">{{ $documentos->count() }} documento(s)</span>
                </div>
                <div class="card-body">
                    @if($documentos->count() > 0)
                        <div class="documents-grid">
                            @foreach($documentos as $doc)
                                <div class="document-card">
                                    <div class="document-header">
                                        <h4>{{ ucfirst(str_replace('_', ' ', $doc->tipo_documento)) }}</h4>
                                        @if($doc->estado === 'aprobado')
                                            <x-agromarket.badge variant="active" type="status">Aprobado</x-agromarket.badge>
                                        @elseif($doc->estado === 'rechazado')
                                            <x-agromarket.badge variant="danger" type="status">Rechazado</x-agromarket.badge>
                                        @else
                                            <x-agromarket.badge variant="warning" type="status">Pendiente</x-agromarket.badge>
                                        @endif
                                    </div>
                                    <div class="document-preview">
                                        @if(in_array($doc->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']))
                                            <img src="{{ asset($doc->ruta_archivo) }}"
                                                 alt="{{ $doc->tipo_documento }}"
                                                 class="preview-image"
                                                 onclick="window.open('{{ asset($doc->ruta_archivo) }}', '_blank')">
                                        @else
                                            <div class="pdf-preview">
                                                <i class="fas fa-file-pdf fa-3x"></i>
                                                <p>{{ $doc->nombre_archivo }}</p>
                                                <a href="{{ asset($doc->ruta_archivo) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-primary mt-2">
                                                    <i class="fas fa-download"></i> Ver PDF
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="document-info">
                                        <small class="text-muted">
                                            Subido: {{ $doc->created_at->format('d/m/Y H:i') }}<br>
                                            Tamano: {{ number_format($doc->tamanio_kb, 0) }} KB
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>No se encontraron documentos</h4>
                            <p class="text-muted">Este usuario no ha subido documentos KYC</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Form oculto para Rechazar KYC --}}
    <form method="POST" action="{{ route('admin.kyc.reject', $user) }}" id="form-rechazar-kyc">
        @csrf
        <input type="hidden" name="motivo" id="motivo-rechazo">
    </form>

    @push('styles')
    <style>
        .dashboard-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dashboard-col-4 {
            flex: 1;
            min-width: 300px;
            max-width: 33.333%;
        }

        .dashboard-col-8 {
            flex: 2;
            min-width: 500px;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .actions-card {
            height: auto;
        }

        .actions-card .card-body {
            padding: 1.25rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .info-item {
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item strong {
            display: block;
            margin-bottom: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .info-item p {
            margin: 0;
            color: #1f2937;
            font-size: 1rem;
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1abc9c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #c0392b 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4A7C59 0%, #6B9B7A 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(74, 124, 89, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3d6b4a 0%, #5a8a69 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 124, 89, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #d1d5db;
            color: #6b7280;
        }

        .btn-outline:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .btn-sm {
            padding: 0.5rem 0.875rem;
            font-size: 0.85rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .document-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            background: #f9fafb;
            transition: all 0.2s;
        }

        .document-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .document-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .document-preview {
            text-align: center;
            margin-bottom: 1rem;
            background: white;
            border-radius: 8px;
            padding: 1rem;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-image {
            max-height: 300px;
            max-width: 100%;
            cursor: pointer;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .preview-image:hover {
            transform: scale(1.02);
        }

        .pdf-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            color: #dc3545;
        }

        .pdf-preview p {
            margin: 0;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .document-info {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .empty-state i.text-warning {
            color: #ffc107;
        }

        .text-muted {
            color: #6b7280;
        }

        @media (max-width: 1024px) {
            .dashboard-col-4,
            .dashboard-col-8 {
                max-width: 100%;
                min-width: 100%;
            }

            .documents-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function confirmarAprobacion() {
            Swal.fire({
                title: 'Aprobar KYC',
                text: '¿Estas seguro de aprobar el KYC de este usuario?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Si, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-aprobar-kyc').submit();
                }
            });
        }

        function mostrarModalRechazo() {
            Swal.fire({
                title: 'Rechazar KYC',
                html: '<p class="mb-3">Indica el motivo del rechazo para que el usuario pueda corregir sus documentos.</p>',
                input: 'textarea',
                inputPlaceholder: 'Escribe el motivo del rechazo...',
                inputAttributes: {
                    'aria-label': 'Motivo del rechazo'
                },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times"></i> Rechazar KYC',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Debes indicar un motivo para el rechazo';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('motivo-rechazo').value = result.value;
                    document.getElementById('form-rechazar-kyc').submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
