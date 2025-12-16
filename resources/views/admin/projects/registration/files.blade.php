<x-app-layout>
    <x-agromarket.page-header
        title="Archivos del Proyecto"
        description="{{ $proyecto->nombre }} - {{ $proyecto->codigo }}"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.projects.registration.show', $proyecto) }}'"
            >
                Volver al Proyecto
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Sección de Imágenes -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-images"></i> Imágenes del Proyecto
            </h3>

            <!-- Formulario para subir imagen -->
            <form id="formSubirImagen" enctype="multipart/form-data" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">
                        Seleccionar Imagen <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="file" name="imagen" id="inputImagen" accept="image/jpeg,image/png,image/webp" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                    <small style="color: #6c757d;">JPG, PNG o WEBP. Máximo 2MB.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">Título</label>
                        <input type="text" name="titulo" id="inputTituloImagen" maxlength="200"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                    </div>
                    <div style="display: flex; align-items: center; padding-top: 1.5rem;">
                        <input type="checkbox" name="es_principal" id="inputEsPrincipal" value="1" style="margin-right: 0.5rem;">
                        <label for="inputEsPrincipal" style="color: #495057;">Imagen Principal</label>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">Descripción</label>
                    <textarea name="descripcion" id="inputDescripcionImagen" maxlength="500" rows="2"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <button type="submit" id="btnSubirImagen" style="width: 100%; padding: 0.75rem; background: #4A7C59; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-upload"></i> Subir Imagen
                </button>
            </form>

            <!-- Lista de imágenes -->
            <div id="listaImagenes">
                @if($proyecto->imagenes->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem;">
                        @foreach($proyecto->imagenes->sortBy('orden') as $imagen)
                            <div class="imagen-item" data-id="{{ $imagen->id }}" style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <img src="{{ asset($imagen->thumbnail ?? $imagen->ruta_imagen) }}" alt="{{ $imagen->titulo }}"
                                    style="width: 100%; height: 100px; object-fit: cover; cursor: pointer;"
                                    onclick="window.open('{{ asset($imagen->ruta_imagen) }}', '_blank')">

                                @if($imagen->es_principal)
                                    <span style="position: absolute; top: 4px; left: 4px; background: #ffc107; color: #333; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                                        <i class="fas fa-star"></i> Principal
                                    </span>
                                @endif

                                <div style="padding: 0.5rem; background: white;">
                                    <p style="margin: 0; font-size: 0.8rem; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $imagen->titulo ?: 'Sin título' }}
                                    </p>
                                    <div style="display: flex; gap: 0.25rem; margin-top: 0.5rem;">
                                        @if(!$imagen->es_principal)
                                            <button onclick="setPrincipal({{ $imagen->id }})" title="Establecer como principal"
                                                style="flex: 1; padding: 0.25rem; background: #ffc107; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 0.7rem;">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endif
                                        <button onclick="eliminarImagen({{ $imagen->id }})" title="Eliminar"
                                            style="flex: 1; padding: 0.25rem; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.7rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #6c757d;">
                        <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p style="margin: 0;">No hay imágenes cargadas</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sección de Documentos -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                <i class="fas fa-file-alt"></i> Documentos del Proyecto
            </h3>

            <!-- Formulario para subir documento -->
            <form id="formSubirDocumento" enctype="multipart/form-data" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">
                        Tipo de Documento <span style="color: #dc3545;">*</span>
                    </label>
                    <select name="tipo_documento" id="inputTipoDocumento" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px; background: white;">
                        <option value="">Seleccione...</option>
                        @foreach($tiposDocumento as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">
                        Seleccionar Archivo <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="file" name="documento" id="inputDocumento" accept=".pdf,.doc,.docx" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px;">
                    <small style="color: #6c757d;">PDF, DOC o DOCX. Máximo 5MB.</small>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #495057;">Descripción</label>
                    <textarea name="descripcion" id="inputDescripcionDocumento" maxlength="500" rows="2"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <button type="submit" id="btnSubirDocumento" style="width: 100%; padding: 0.75rem; background: #4A7C59; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-upload"></i> Subir Documento
                </button>
            </form>

            <!-- Lista de documentos -->
            <div id="listaDocumentos">
                @if($proyecto->documentos->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($proyecto->documentos as $documento)
                            <div class="documento-item" data-id="{{ $documento->id }}"
                                style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #4A7C59;">
                                <div style="flex-shrink: 0;">
                                    @php
                                        $iconColor = match(true) {
                                            str_contains($documento->tipo_mime, 'pdf') => '#dc3545',
                                            str_contains($documento->tipo_mime, 'word') || str_contains($documento->tipo_mime, 'document') => '#0d6efd',
                                            default => '#6c757d'
                                        };
                                        $icon = match(true) {
                                            str_contains($documento->tipo_mime, 'pdf') => 'fa-file-pdf',
                                            str_contains($documento->tipo_mime, 'word') || str_contains($documento->tipo_mime, 'document') => 'fa-file-word',
                                            default => 'fa-file'
                                        };
                                    @endphp
                                    <i class="fas {{ $icon }}" style="font-size: 2rem; color: {{ $iconColor }};"></i>
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0; font-weight: 500; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $documento->nombre_archivo }}
                                    </p>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #6c757d;">
                                        {{ $tiposDocumento[$documento->tipo_documento] ?? $documento->tipo_documento }}
                                        &bull; {{ number_format($documento->tamano_bytes / 1024, 1) }} KB
                                        &bull; {{ $documento->created_at->format('d/m/Y') }}
                                    </p>
                                    @if($documento->descripcion)
                                        <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #495057;">
                                            {{ Str::limit($documento->descripcion, 60) }}
                                        </p>
                                    @endif
                                </div>

                                <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                                    <a href="{{ route('admin.projects.registration.documents.download', $documento) }}"
                                        style="padding: 0.5rem 0.75rem; background: #17a2b8; color: white; border-radius: 6px; text-decoration: none; font-size: 0.8rem;"
                                        title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button onclick="eliminarDocumento({{ $documento->id }})"
                                        style="padding: 0.5rem 0.75rem; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.8rem;"
                                        title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #6c757d;">
                        <i class="fas fa-file-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p style="margin: 0;">No hay documentos cargados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Documentos Requeridos según PDF -->
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 2rem;">
        <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
            <i class="fas fa-clipboard-check"></i> Documentos Requeridos
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            @php
                $documentosRequeridos = [
                    'escritura' => 'Escritura del terreno',
                    'cedula_catastral' => 'Cédula Catastral',
                    'plan_cultivo' => 'Plan de Cultivo',
                    'estudio_suelos' => 'Estudio de Suelos',
                    'poliza_seguro' => 'Póliza de Seguro',
                    'foto_terreno' => 'Fotografías del Terreno',
                ];
                $documentosSubidos = $proyecto->documentos->pluck('tipo_documento')->toArray();
            @endphp

            @foreach($documentosRequeridos as $tipo => $nombre)
                @php
                    $subido = in_array($tipo, $documentosSubidos);
                @endphp
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: {{ $subido ? '#d4edda' : '#f8f9fa' }}; border-radius: 6px;">
                    <i class="fas {{ $subido ? 'fa-check-circle' : 'fa-circle' }}"
                        style="color: {{ $subido ? '#28a745' : '#adb5bd' }}; font-size: 1.25rem;"></i>
                    <span style="color: {{ $subido ? '#155724' : '#495057' }};">{{ $nombre }}</span>
                </div>
            @endforeach
        </div>

        <p style="margin: 1.5rem 0 0 0; font-size: 0.875rem; color: #6c757d;">
            <i class="fas fa-info-circle"></i>
            Estos documentos son necesarios para completar el registro del proyecto.
        </p>
    </div>

    @push('scripts')
    <script>
        // Subir Imagen
        document.getElementById('formSubirImagen').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubirImagen');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route("admin.projects.registration.images.store", $proyecto) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Imagen subida',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    location.reload();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al subir la imagen'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        // Subir Documento
        document.getElementById('formSubirDocumento').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubirDocumento');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route("admin.projects.registration.documents.store", $proyecto) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Documento subido',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    location.reload();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al subir el documento'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        // Eliminar Imagen
        async function eliminarImagen(id) {
            const result = await Swal.fire({
                title: 'Eliminar imagen',
                text: '¿Está seguro de eliminar esta imagen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('admin/proyectos/registro/imagenes') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al eliminar la imagen'
                    });
                }
            }
        }

        // Eliminar Documento
        async function eliminarDocumento(id) {
            const result = await Swal.fire({
                title: 'Eliminar documento',
                text: '¿Está seguro de eliminar este documento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('admin/proyectos/registro/documentos') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al eliminar el documento'
                    });
                }
            }
        }

        // Establecer imagen como principal
        async function setPrincipal(id) {
            try {
                const response = await fetch(`{{ url('admin/proyectos/registro/imagenes') }}/${id}/principal`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    location.reload();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al establecer imagen como principal'
                });
            }
        }

        // Alertas de sesión
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Excelente',
                text: @json(session('success')),
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
                text: @json(session('error')),
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
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }

        .imagen-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }

        .documento-item:hover {
            background: #e9ecef !important;
            transition: all 0.2s ease;
        }
    </style>
    @endpush
</x-app-layout>
