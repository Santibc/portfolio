<x-app-layout>
    <x-slot name="header">Configuración Landing Page (Layout)</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Por favor corrija los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.landing.configuracion.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Imágenes --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Imágenes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo Principal</label>
                            @if($config->logo)
                                <div class="mb-2">
                                    <img src="{{ asset($config->logo) }}" alt="Logo" style="max-height: 60px;">
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Se muestra en header y footer</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Favicon</label>
                            @if($config->favicon)
                                <div class="mb-2">
                                    <img src="{{ asset($config->favicon) }}" alt="Favicon" style="max-height: 32px;">
                                </div>
                            @endif
                            <input type="file" name="favicon" class="form-control" accept="image/*">
                            <small class="text-muted">Icono del navegador</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-layout-text-window-reverse"></i> Footer</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto del Footer</label>
                            <input type="text" name="footer_texto" class="form-control"
                                   value="{{ old('footer_texto', $config->footer_texto) }}"
                                   placeholder="Potenciando al talento creativo de Colombia.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">URL Términos y Condiciones</label>
                            <input type="text" name="terminos_url" class="form-control"
                                   value="{{ old('terminos_url', $config->terminos_url) }}"
                                   placeholder="#">
                        </div>
                    </div>
                </div>
            </div>

            {{-- WhatsApp --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-whatsapp"></i> WhatsApp</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de WhatsApp</label>
                            <input type="text" name="whatsapp_numero" class="form-control"
                                   value="{{ old('whatsapp_numero', $config->whatsapp_numero) }}"
                                   placeholder="573001234567">
                            <small class="text-muted">Sin espacios ni guiones, incluir código de país</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto del Botón</label>
                            <input type="text" name="whatsapp_texto" class="form-control"
                                   value="{{ old('whatsapp_texto', $config->whatsapp_texto) }}"
                                   placeholder="Contáctanos vía Whatsapp">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Redes Sociales --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-share"></i> Redes Sociales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-facebook text-primary"></i> Facebook URL</label>
                            <input type="text" name="facebook_url" class="form-control"
                                   value="{{ old('facebook_url', $config->facebook_url) }}"
                                   placeholder="https://facebook.com/...">
                            <small class="text-muted">Dejar vacío para no mostrar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-tiktok"></i> TikTok URL</label>
                            <input type="text" name="tiktok_url" class="form-control"
                                   value="{{ old('tiktok_url', $config->tiktok_url) }}"
                                   placeholder="https://tiktok.com/...">
                            <small class="text-muted">Dejar vacío para no mostrar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-instagram text-danger"></i> Instagram URL</label>
                            <input type="text" name="instagram_url" class="form-control"
                                   value="{{ old('instagram_url', $config->instagram_url) }}"
                                   placeholder="https://instagram.com/...">
                            <small class="text-muted">Dejar vacío para no mostrar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-linkedin text-info"></i> LinkedIn URL</label>
                            <input type="text" name="linkedin_url" class="form-control"
                                   value="{{ old('linkedin_url', $config->linkedin_url) }}"
                                   placeholder="https://linkedin.com/...">
                            <small class="text-muted">Dejar vacío para no mostrar</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contacto --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-envelope"></i> Contacto</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email de Contacto</label>
                            <input type="email" name="contacto_email" class="form-control"
                                   value="{{ old('contacto_email', $config->contacto_email) }}"
                                   placeholder="contacto@empresa.com">
                            <small class="text-muted">Recibirá los formularios de contacto</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
