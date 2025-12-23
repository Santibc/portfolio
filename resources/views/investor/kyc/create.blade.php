<x-app-layout>
    <x-agromarket.page-header
        title="Subir Documentos KYC"
        subtitle="Verifica tu identidad para poder invertir"
    >
        <a href="{{ route('inversionista.kyc.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </x-agromarket.page-header>

    <div class="dashboard-row">
        {{-- Formulario de upload --}}
        <div class="dashboard-col-8">
            <div class="dashboard-card">
                <div class="card-body">
                    {{-- Mensaje si fue rechazado --}}
                    @if(auth()->user()->kyc_status === 'rechazado')
                        <div class="alert alert-danger mb-4">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-content">
                                <h4 class="alert-title">Documentos Rechazados</h4>
                                <p><strong>Motivo:</strong> {{ auth()->user()->kyc_notas }}</p>
                                <p class="mb-0">Por favor, sube nuevos documentos para poder continuar invirtiendo.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Informacion del proceso --}}
                    <div class="alert alert-info mb-4">
                        <div class="alert-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="alert-content">
                            <h4 class="alert-title">Proceso de Verificacion</h4>
                            <p class="mb-0">Para poder invertir, necesitamos verificar tu identidad. Una vez que subas los documentos, podras <strong>invertir de inmediato</strong> mientras nuestro equipo revisa la informacion.</p>
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('inversionista.kyc.store') }}" enctype="multipart/form-data" id="kyc-form">
                        @csrf

                        {{-- Documento de identidad (frente) --}}
                        <div class="upload-field" data-preview="preview-frente">
                            <label class="upload-label">
                                <i class="fas fa-id-card"></i>
                                Documento de Identidad (Frente) <span class="required">*</span>
                            </label>
                            <div class="upload-dropzone" onclick="document.getElementById('documento_frente').click()">
                                <input
                                    type="file"
                                    id="documento_frente"
                                    name="documento_frente"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="upload-input"
                                    onchange="previewFile(this, 'preview-frente')"
                                >
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Arrastra o haz clic para subir</span>
                                    <span class="upload-hint">JPG, PNG o PDF - Max 5MB</span>
                                </div>
                                <div class="upload-preview" id="preview-frente"></div>
                            </div>
                            @error('documento_frente')
                                <div class="upload-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Documento de identidad (reverso) --}}
                        <div class="upload-field" data-preview="preview-reverso">
                            <label class="upload-label">
                                <i class="fas fa-id-card"></i>
                                Documento de Identidad (Reverso) <span class="required">*</span>
                            </label>
                            <div class="upload-dropzone" onclick="document.getElementById('documento_reverso').click()">
                                <input
                                    type="file"
                                    id="documento_reverso"
                                    name="documento_reverso"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="upload-input"
                                    onchange="previewFile(this, 'preview-reverso')"
                                >
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Arrastra o haz clic para subir</span>
                                    <span class="upload-hint">JPG, PNG o PDF - Max 5MB</span>
                                </div>
                                <div class="upload-preview" id="preview-reverso"></div>
                            </div>
                            @error('documento_reverso')
                                <div class="upload-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Selfie con documento --}}
                        <div class="upload-field" data-preview="preview-selfie">
                            <label class="upload-label">
                                <i class="fas fa-camera"></i>
                                Selfie con Documento <span class="required">*</span>
                            </label>
                            <div class="upload-dropzone" onclick="document.getElementById('selfie').click()">
                                <input
                                    type="file"
                                    id="selfie"
                                    name="selfie"
                                    accept=".jpg,.jpeg,.png"
                                    required
                                    class="upload-input"
                                    onchange="previewFile(this, 'preview-selfie')"
                                >
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Arrastra o haz clic para subir</span>
                                    <span class="upload-hint">Foto tuya sosteniendo tu documento - JPG, PNG - Max 5MB</span>
                                </div>
                                <div class="upload-preview" id="preview-selfie"></div>
                            </div>
                            @error('selfie')
                                <div class="upload-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Comprobante de domicilio --}}
                        <div class="upload-field" data-preview="preview-domicilio">
                            <label class="upload-label">
                                <i class="fas fa-home"></i>
                                Comprobante de Domicilio <span class="required">*</span>
                            </label>
                            <div class="upload-dropzone" onclick="document.getElementById('comprobante_domicilio').click()">
                                <input
                                    type="file"
                                    id="comprobante_domicilio"
                                    name="comprobante_domicilio"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="upload-input"
                                    onchange="previewFile(this, 'preview-domicilio')"
                                >
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Arrastra o haz clic para subir</span>
                                    <span class="upload-hint">Recibo de servicios (max 3 meses) - JPG, PNG o PDF - Max 5MB</span>
                                </div>
                                <div class="upload-preview" id="preview-domicilio"></div>
                            </div>
                            @error('comprobante_domicilio')
                                <div class="upload-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="form-actions mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <i class="fas fa-upload"></i> Enviar Documentos y Comenzar a Invertir
                            </button>
                            <a href="{{ route('inversionista.kyc.index') }}" class="btn btn-outline">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel informativo lateral --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-question-circle"></i> Documentos Requeridos</h3>
                </div>
                <div class="card-body">
                    <div class="requirements-list">
                        <div class="requirement-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>Documento de Identidad</strong>
                                <p>Cedula, DNI o Pasaporte vigente (frente y reverso)</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>Selfie</strong>
                                <p>Foto clara sosteniendo tu documento</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>Comprobante de Domicilio</strong>
                                <p>Recibo de servicios reciente (max. 3 meses)</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Importante:</strong>
                            <p class="mb-0">Asegurate de que todos los documentos sean legibles y esten vigentes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            height: fit-content;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
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

        /* Alert styles */
        .alert {
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            gap: 1rem;
            border: 1px solid;
        }

        .alert-info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .alert-danger {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border-color: #ffecb5;
            color: #856404;
        }

        .alert-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }

        .alert p {
            margin: 0.25rem 0;
        }

        /* Upload field styles */
        .upload-field {
            margin-bottom: 1.5rem;
        }

        .upload-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .upload-label .required {
            color: #dc3545;
        }

        .upload-dropzone {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 140px;
        }

        .upload-dropzone:hover {
            border-color: #4A7C59;
            background: rgba(74, 124, 89, 0.05);
        }

        .upload-dropzone.has-file {
            border-color: #4A7C59;
            border-style: solid;
            background: rgba(74, 124, 89, 0.05);
        }

        .upload-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .upload-icon {
            font-size: 2.5rem;
            color: #4A7C59;
            opacity: 0.7;
        }

        .upload-text {
            font-size: 1rem;
            color: #1f2937;
            font-weight: 500;
        }

        .upload-hint {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .upload-preview {
            display: none;
        }

        .upload-preview.active {
            display: block;
            margin-top: 1rem;
        }

        .upload-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .upload-preview .file-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            max-width: 300px;
            margin: 0 auto;
        }

        .upload-preview .file-icon {
            font-size: 1.5rem;
            color: #dc3545;
        }

        .upload-preview .file-name {
            flex: 1;
            font-size: 0.9rem;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .upload-preview .file-size {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .upload-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        /* Form actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-lg {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }

        /* Requirements list */
        .requirements-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .requirement-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .requirement-item i {
            font-size: 1.25rem;
            margin-top: 0.125rem;
        }

        .requirement-item strong {
            display: block;
            margin-bottom: 0.25rem;
            color: #1f2937;
        }

        .requirement-item p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .text-success {
            color: #28a745;
        }

        @media (max-width: 1024px) {
            .dashboard-col-4,
            .dashboard-col-8 {
                max-width: 100%;
                min-width: 100%;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            const dropzone = input.closest('.upload-dropzone');
            const content = dropzone.querySelector('.upload-content');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);

                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'El archivo no debe superar 5MB',
                    });
                    input.value = '';
                    return;
                }

                dropzone.classList.add('has-file');
                content.style.display = 'none';
                preview.classList.add('active');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = `
                        <div class="file-info">
                            <i class="fas fa-file-pdf file-icon"></i>
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">${fileSize}MB</span>
                        </div>
                    `;
                }
            } else {
                dropzone.classList.remove('has-file');
                content.style.display = 'flex';
                preview.classList.remove('active');
                preview.innerHTML = '';
            }
        }

        // Form submit validation
        document.getElementById('kyc-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo documentos...';
        });

        // Drag and drop support
        document.querySelectorAll('.upload-dropzone').forEach(dropzone => {
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('has-file');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                if (!this.querySelector('input').files.length) {
                    this.classList.remove('has-file');
                }
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                const input = this.querySelector('input');
                const previewId = this.closest('.upload-field').dataset.preview;

                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    previewFile(input, previewId);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
