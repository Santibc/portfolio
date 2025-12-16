@php
    $tiposDocumento = [
        'escritura' => 'Escritura del terreno',
        'certificado_camara' => 'Certificado Cámara de Comercio',
        'cedula_catastral' => 'Cédula Catastral',
        'plan_cultivo' => 'Plan de Cultivo',
        'estudio_suelos' => 'Estudio de Suelos',
        'licencia_ambiental' => 'Licencia Ambiental',
        'poliza_seguro' => 'Póliza de Seguro',
        'contrato_compra' => 'Contrato de Compra',
        'foto_terreno' => 'Fotografía del Terreno',
        'otro' => 'Otro documento',
    ];

    function formatBytes($bytes) {
        if (!$bytes) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
@endphp

<x-app-layout>
    <x-agromarket.page-header
        title="Archivos del Proyecto"
        :description="$proyecto->nombre . ' - Código: ' . $proyecto->codigo"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('farmer.projects.index') }}'"
            >
                Volver a Proyectos
            </x-agromarket.button>

            <x-agromarket.button
                variant="primary"
                icon="fas fa-eye"
                onclick="window.location.href='{{ route('farmer.projects.show', $proyecto->id) }}'"
            >
                Ver Proyecto
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Información del estado -->
    <div style="background: {{ $proyecto->estado === 'borrador' ? '#fff3cd' : '#f8d7da' }}; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid {{ $proyecto->estado === 'borrador' ? '#ffc107' : '#dc3545' }};">
        <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas {{ $proyecto->estado === 'borrador' ? 'fa-edit' : 'fa-exclamation-circle' }}"></i>
            <strong>Estado:</strong>
            {{ $proyecto->estado === 'borrador' ? 'Borrador' : 'Rechazado' }}
            @if($proyecto->estado === 'rechazado' && $proyecto->motivo_rechazo)
                - {{ $proyecto->motivo_rechazo }}
            @endif
        </p>
        <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #666;">
            Sube las imágenes y documentos de tu proyecto. Una vez listos, puedes enviarlo a revisión.
        </p>
    </div>

    <!-- Galería de Imágenes -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-images"></i> Galería de Imágenes
            <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->imagenes->count() }} imágenes)</span>
        </h3>

        <x-agromarket.file-upload
            type="image"
            name="imagenes"
            label=""
            :multiple="true"
            :max-size="2"
            :upload-url="route('farmer.projects.images.store', $proyecto->id)"
            :delete-url="url('agricultor/projects/imagenes/:id')"
            :files="$proyecto->imagenes->map(fn($img) => [
                'id' => $img->id,
                'titulo' => $img->titulo,
                'descripcion' => $img->descripcion,
                'url' => asset($img->ruta_imagen),
                'thumbnail' => asset($img->thumbnail ?? $img->ruta_imagen),
                'es_principal' => $img->es_principal,
                'orden' => $img->orden,
            ])->toArray()"
            hint="Sube imágenes de tu proyecto: terreno, cultivos, instalaciones, etc. La imagen principal se mostrará en el catálogo. Formatos: JPG, PNG, WEBP. Máximo 2MB."
        />
    </div>

    <!-- Documentos del Proyecto -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-file-alt"></i> Documentos del Proyecto
            <span style="font-size: 0.875rem; font-weight: normal; color: #6c757d;">({{ $proyecto->documentos->count() }} documentos)</span>
        </h3>

        <!-- Selector de tipo de documento -->
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: #333;">
                Tipo de documento a subir
            </label>
            <select id="tipo_documento_select" style="width: 100%; max-width: 400px; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                <option value="escritura">Escritura del terreno</option>
                <option value="certificado_camara">Certificado Cámara de Comercio</option>
                <option value="cedula_catastral">Cédula Catastral</option>
                <option value="plan_cultivo">Plan de Cultivo</option>
                <option value="estudio_suelos">Estudio de Suelos</option>
                <option value="licencia_ambiental">Licencia Ambiental</option>
                <option value="poliza_seguro">Póliza de Seguro</option>
                <option value="contrato_compra">Contrato de Compra</option>
                <option value="foto_terreno">Fotografía del Terreno</option>
                <option value="otro">Otro documento</option>
            </select>
        </div>

        <x-agromarket.file-upload
            type="document"
            name="documentos"
            label=""
            :multiple="false"
            :max-size="5"
            :upload-url="route('farmer.projects.documents.store', $proyecto->id)"
            :delete-url="url('agricultor/projects/documentos/:id')"
            :files="$proyecto->documentos->map(fn($doc) => [
                'id' => $doc->id,
                'nombre' => $doc->nombre_archivo,
                'tipo' => $doc->tipo_documento,
                'tipo_label' => $tiposDocumento[$doc->tipo_documento] ?? $doc->tipo_documento,
                'tamano' => formatBytes($doc->tamano_bytes),
                'url' => asset($doc->ruta_archivo),
                'download_url' => route('farmer.projects.documents.download', $doc->id),
            ])->toArray()"
            hint="Sube documentos legales y técnicos de tu proyecto. Formatos: PDF, DOC, DOCX. Máximo 5MB."
        />
    </div>

    <!-- Acciones finales -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="margin: 0; font-size: 0.875rem; color: #666;">
                <i class="fas fa-info-circle"></i>
                Cuando hayas terminado de subir tus archivos, puedes enviar el proyecto a revisión.
            </p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-edit"
                onclick="window.location.href='{{ route('farmer.projects.edit', $proyecto->id) }}'"
            >
                Editar Datos
            </x-agromarket.button>

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
        </div>
    </div>

    @push('scripts')
    <script>
        // Interceptar fetch para agregar tipo_documento
        document.addEventListener('DOMContentLoaded', function() {
            const originalFetch = window.fetch;
            window.fetch = function(url, options) {
                if (url && url.includes('/documentos') && options && options.body instanceof FormData) {
                    const tipoDocumento = document.getElementById('tipo_documento_select')?.value || 'otro';
                    options.body.append('tipo_documento', tipoDocumento);
                }
                return originalFetch.apply(this, arguments);
            };
        });

        // Confirmar envío a revisión
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
    </script>
    @endpush
</x-app-layout>
