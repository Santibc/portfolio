@props([
    'type' => 'document', // 'document' o 'image'
    'name' => 'file',
    'label' => null,
    'accept' => null,
    'multiple' => false,
    'maxSize' => null, // en MB
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'preview' => true,
    'showList' => true,
    'uploadUrl' => null,
    'deleteUrl' => null,
    'files' => [], // Archivos existentes
])

@php
    $id = $attributes->get('id') ?? 'upload-' . \Illuminate\Support\Str::random(8);
    $isImage = $type === 'image';
    $defaultAccept = $isImage ? '.jpg,.jpeg,.png,.webp' : '.pdf,.doc,.docx';
    $acceptTypes = $accept ?? $defaultAccept;
    $defaultMaxSize = $isImage ? 2 : 5;
    $maxSizeValue = $maxSize ?? $defaultMaxSize;
    $defaultLabel = $isImage ? 'Subir imágenes' : 'Subir documentos';
    $labelText = $label ?? $defaultLabel;
@endphp

<div class="file-upload-wrapper"
     x-data="fileUploadComponent({
         type: '{{ $type }}',
         uploadUrl: '{{ $uploadUrl }}',
         deleteUrl: '{{ $deleteUrl }}',
         maxSize: {{ $maxSizeValue }},
         multiple: {{ $multiple ? 'true' : 'false' }},
         existingFiles: {{ json_encode($files) }},
         isImage: {{ $isImage ? 'true' : 'false' }}
     })"
     {{ $attributes->merge(['class' => 'file-upload-container']) }}>

    @if($labelText)
        <label class="file-upload-label">{{ $labelText }}</label>
    @endif

    <!-- Dropzone -->
    <div class="file-dropzone {{ $disabled ? 'disabled' : '' }}"
         x-on:dragover.prevent="isDragging = true"
         x-on:dragleave.prevent="isDragging = false"
         x-on:drop.prevent="handleDrop($event)"
         x-bind:class="{ 'dragging': isDragging, 'has-error': error }">

        <input type="file"
               id="{{ $id }}"
               name="{{ $name }}"
               accept="{{ $acceptTypes }}"
               {{ $multiple ? 'multiple' : '' }}
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               x-ref="fileInput"
               x-on:change="handleFileSelect($event)"
               class="file-input-hidden">

        <div class="dropzone-content" x-on:click="$refs.fileInput.click()">
            <div class="dropzone-icon">
                @if($isImage)
                    <i class="fas fa-images"></i>
                @else
                    <i class="fas fa-file-upload"></i>
                @endif
            </div>
            <div class="dropzone-text">
                <span class="dropzone-main-text">
                    Arrastra y suelta {{ $isImage ? 'imágenes' : 'archivos' }} aquí
                </span>
                <span class="dropzone-sub-text">
                    o <span class="dropzone-link">haz clic para seleccionar</span>
                </span>
            </div>
            <div class="dropzone-info">
                @if($isImage)
                    <span>JPG, PNG, WEBP</span>
                @else
                    <span>PDF, DOC, DOCX</span>
                @endif
                <span>•</span>
                <span>Máx. {{ $maxSizeValue }}MB</span>
            </div>
        </div>

        <!-- Loading overlay -->
        <div class="dropzone-loading" x-show="isUploading" x-cloak>
            <div class="spinner"></div>
            <span>Subiendo...</span>
        </div>
    </div>

    @if($hint)
        <p class="file-upload-hint">{{ $hint }}</p>
    @endif

    <!-- Error message -->
    <div class="file-upload-error" x-show="error" x-text="error" x-cloak></div>

    <!-- Files list -->
    @if($showList)
        <div class="uploaded-files-list" x-show="files.length > 0">
            <template x-for="(file, index) in files" :key="file.id || index">
                <div class="uploaded-file-item" x-bind:class="{ 'is-image': isImage, 'is-principal': file.es_principal }">
                    <!-- Thumbnail/Icon -->
                    <div class="file-preview">
                        <template x-if="isImage && file.thumbnail">
                            <img x-bind:src="file.thumbnail" x-bind:alt="file.titulo || file.nombre">
                        </template>
                        <template x-if="!isImage || !file.thumbnail">
                            <div class="file-icon">
                                <i x-bind:class="getFileIcon(file)"></i>
                            </div>
                        </template>
                    </div>

                    <!-- File info -->
                    <div class="file-info">
                        <span class="file-name" x-text="file.nombre || file.titulo"></span>
                        <span class="file-meta">
                            <span x-text="file.tipo_label || file.tipo || ''"></span>
                            <template x-if="file.tamano">
                                <span> • <span x-text="file.tamano"></span></span>
                            </template>
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="file-actions">
                        <!-- Set as principal (images only) -->
                        <template x-if="isImage && !file.es_principal">
                            <button type="button"
                                    class="file-action-btn"
                                    title="Establecer como principal"
                                    x-on:click="setPrincipal(file)">
                                <i class="fas fa-star"></i>
                            </button>
                        </template>
                        <template x-if="isImage && file.es_principal">
                            <span class="principal-badge" title="Imagen principal">
                                <i class="fas fa-star"></i>
                            </span>
                        </template>

                        <!-- Download (documents only) -->
                        <template x-if="!isImage && file.url">
                            <a x-bind:href="file.download_url || file.url"
                               class="file-action-btn"
                               title="Descargar"
                               target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                        </template>

                        <!-- View (images) -->
                        <template x-if="isImage && file.url">
                            <a x-bind:href="file.url"
                               class="file-action-btn"
                               title="Ver imagen"
                               target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </template>

                        <!-- Delete -->
                        <button type="button"
                                class="file-action-btn delete"
                                title="Eliminar"
                                x-on:click="deleteFile(file, index)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    @endif
</div>

<style>
.file-upload-wrapper {
    margin-bottom: 1.5rem;
}

.file-upload-label {
    display: block;
    font-weight: 500;
    color: var(--text-primary, #333);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.file-dropzone {
    border: 2px dashed var(--border-color, #ddd);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: var(--bg-light, #fafafa);
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.file-dropzone:hover {
    border-color: var(--primary-color, #4A7C59);
    background: rgba(74, 124, 89, 0.05);
}

.file-dropzone.dragging {
    border-color: var(--primary-color, #4A7C59);
    background: rgba(74, 124, 89, 0.1);
    transform: scale(1.01);
}

.file-dropzone.has-error {
    border-color: var(--danger-color, #dc3545);
    background: rgba(220, 53, 69, 0.05);
}

.file-dropzone.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.file-input-hidden {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.dropzone-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.dropzone-icon {
    font-size: 2.5rem;
    color: var(--primary-color, #4A7C59);
    opacity: 0.7;
}

.dropzone-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.dropzone-main-text {
    font-size: 1rem;
    color: var(--text-primary, #333);
    font-weight: 500;
}

.dropzone-sub-text {
    font-size: 0.875rem;
    color: var(--text-secondary, #666);
}

.dropzone-link {
    color: var(--primary-color, #4A7C59);
    text-decoration: underline;
    cursor: pointer;
}

.dropzone-info {
    display: flex;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--text-muted, #999);
}

.dropzone-loading {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border-radius: 6px;
}

.spinner {
    width: 30px;
    height: 30px;
    border: 3px solid var(--border-color, #ddd);
    border-top-color: var(--primary-color, #4A7C59);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.file-upload-hint {
    font-size: 0.75rem;
    color: var(--text-muted, #999);
    margin-top: 0.5rem;
}

.file-upload-error {
    font-size: 0.875rem;
    color: var(--danger-color, #dc3545);
    margin-top: 0.5rem;
}

/* Uploaded files list */
.uploaded-files-list {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.uploaded-file-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #fff;
    border: 1px solid var(--border-color, #ddd);
    border-radius: 6px;
    transition: all 0.2s ease;
}

.uploaded-file-item:hover {
    border-color: var(--primary-color, #4A7C59);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.uploaded-file-item.is-principal {
    border-color: var(--secondary-color, #D4AF37);
    background: rgba(212, 175, 55, 0.05);
}

.file-preview {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 4px;
    overflow: hidden;
    background: var(--bg-light, #f5f5f5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    font-size: 1.5rem;
    color: var(--text-muted, #999);
}

.file-info {
    flex: 1;
    min-width: 0;
}

.file-name {
    display: block;
    font-weight: 500;
    color: var(--text-primary, #333);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta {
    font-size: 0.75rem;
    color: var(--text-muted, #999);
}

.file-actions {
    display: flex;
    gap: 0.5rem;
}

.file-action-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--bg-light, #f5f5f5);
    border-radius: 4px;
    color: var(--text-secondary, #666);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    text-decoration: none;
}

.file-action-btn:hover {
    background: var(--primary-color, #4A7C59);
    color: #fff;
}

.file-action-btn.delete:hover {
    background: var(--danger-color, #dc3545);
    color: #fff;
}

.principal-badge {
    width: 32px;
    height: 32px;
    background: var(--secondary-color, #D4AF37);
    border-radius: 4px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Image grid for multiple images */
.uploaded-files-list.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.uploaded-files-list.image-grid .uploaded-file-item {
    flex-direction: column;
    padding: 0.5rem;
}

.uploaded-files-list.image-grid .file-preview {
    width: 100%;
    height: 120px;
}

.uploaded-files-list.image-grid .file-info {
    text-align: center;
}

.uploaded-files-list.image-grid .file-actions {
    justify-content: center;
}

[x-cloak] {
    display: none !important;
}
</style>

<script>
function fileUploadComponent(config) {
    return {
        type: config.type,
        uploadUrl: config.uploadUrl,
        deleteUrl: config.deleteUrl,
        maxSize: config.maxSize,
        multiple: config.multiple,
        isImage: config.isImage,
        files: config.existingFiles || [],
        isDragging: false,
        isUploading: false,
        error: null,

        handleDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            this.processFiles(files);
        },

        handleFileSelect(event) {
            const files = event.target.files;
            this.processFiles(files);
            event.target.value = ''; // Reset input
        },

        async processFiles(fileList) {
            this.error = null;

            for (const file of fileList) {
                // Validate size
                if (file.size > this.maxSize * 1024 * 1024) {
                    this.error = `El archivo "${file.name}" supera el tamaño máximo de ${this.maxSize}MB`;
                    continue;
                }

                // Validate type
                if (!this.validateFileType(file)) {
                    this.error = `El archivo "${file.name}" tiene un tipo no permitido`;
                    continue;
                }

                if (this.uploadUrl) {
                    await this.uploadFile(file);
                } else {
                    // Just add to list (for form submission)
                    this.addFileToList(file);
                }
            }
        },

        validateFileType(file) {
            const allowedTypes = this.isImage
                ? ['image/jpeg', 'image/png', 'image/webp']
                : ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            return allowedTypes.includes(file.type);
        },

        async uploadFile(file) {
            this.isUploading = true;
            this.error = null;

            const formData = new FormData();
            formData.append(this.isImage ? 'imagen' : 'documento', file);

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const response = await fetch(this.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    this.files.push(this.isImage ? data.imagen : data.documento);
                    this.showToast('success', data.message || 'Archivo subido exitosamente');
                } else {
                    this.error = data.message || 'Error al subir el archivo';
                    this.showToast('error', this.error);
                }
            } catch (err) {
                this.error = 'Error de conexión al subir el archivo';
                this.showToast('error', this.error);
            } finally {
                this.isUploading = false;
            }
        },

        async deleteFile(file, index) {
            const result = await Swal.fire({
                title: '¿Eliminar archivo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;

            if (this.deleteUrl && file.id) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = this.deleteUrl.replace(':id', file.id);

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.files.splice(index, 1);
                        this.showToast('success', data.message || 'Archivo eliminado');
                    } else {
                        this.showToast('error', data.message || 'Error al eliminar');
                    }
                } catch (err) {
                    this.showToast('error', 'Error de conexión');
                }
            } else {
                this.files.splice(index, 1);
            }
        },

        async setPrincipal(file) {
            if (!file.id) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const url = `{{ url('agricultor/projects/imagenes') }}/${file.id}/principal`;

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Update all files
                    this.files.forEach(f => f.es_principal = false);
                    file.es_principal = true;
                    this.showToast('success', data.message || 'Imagen principal actualizada');
                } else {
                    this.showToast('error', data.message || 'Error al actualizar');
                }
            } catch (err) {
                this.showToast('error', 'Error de conexión');
            }
        },

        addFileToList(file) {
            // Create preview for images
            if (this.isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files.push({
                        nombre: file.name,
                        titulo: file.name,
                        thumbnail: e.target.result,
                        url: e.target.result,
                        tamano: this.formatBytes(file.size),
                        file: file, // Keep reference for form submission
                    });
                };
                reader.readAsDataURL(file);
            } else {
                this.files.push({
                    nombre: file.name,
                    tamano: this.formatBytes(file.size),
                    file: file,
                });
            }
        },

        getFileIcon(file) {
            if (this.isImage) return 'fas fa-image';

            const extension = (file.nombre || '').split('.').pop()?.toLowerCase();
            switch (extension) {
                case 'pdf': return 'fas fa-file-pdf';
                case 'doc':
                case 'docx': return 'fas fa-file-word';
                default: return 'fas fa-file';
            }
        },

        formatBytes(bytes) {
            if (!bytes) return '';
            const units = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(1) + ' ' + units[i];
        },

        showToast(type, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        }
    };
}
</script>
