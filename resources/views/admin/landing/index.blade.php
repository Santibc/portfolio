<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Gestión de Landing Page</h1>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="landingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button" role="tab">
                            <i class="bi bi-gear me-1"></i>Configuración General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="carousel-tab" data-bs-toggle="tab" data-bs-target="#carousel" type="button" role="tab">
                            <i class="bi bi-images me-1"></i>Carrusel
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                            <i class="bi bi-briefcase me-1"></i>Servicios
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="steps-tab" data-bs-toggle="tab" data-bs-target="#steps" type="button" role="tab">
                            <i class="bi bi-list-ol me-1"></i>Pasos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="bi bi-envelope me-1"></i>Contacto
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">
                            <i class="bi bi-info-circle me-1"></i>Nosotros
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
                            <i class="bi bi-people me-1"></i>Equipo
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout" type="button" role="tab">
                            <i class="bi bi-layout-text-window me-1"></i>Layout
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                            <i class="bi bi-search me-1"></i>SEO
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="landingTabsContent">
                    <!-- Configuración General -->
                    <div class="tab-pane fade show active" id="config" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Información General</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.config.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre de la Empresa</label>
                                                <input type="text" name="company_name" class="form-control" 
                                                       value="{{ $config->company_name ?? 'Montano & Co.' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email del Botón Contacto</label>
                                                <input type="email" name="contact_email" class="form-control" 
                                                       value="{{ $config->contact_email ?? '' }}" placeholder="contacto@ejemplo.com">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción de la Empresa</label>
                                        <textarea name="company_description" class="form-control" rows="5" required>{{ $config->company_description ?? 'Somos un despacho orientado a resultados...' }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">URL del Botón "Nuestros Servicios" (opcional)</label>
                                        <input type="text" name="services_button_url" class="form-control" 
                                               value="{{ $config->services_button_url ?? '#services' }}" 
                                               placeholder="#services o URL externa">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Carrusel -->
                    <div class="tab-pane fade" id="carousel" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Imágenes del Carrusel</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCarouselModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Imagen
                                </button>
                            </div>
                            <div class="card-body">
                                @if($carouselImages->count() > 0)
                                    <div class="row">
                                        @foreach($carouselImages as $image)
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <img src="{{ asset($image->image_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body">
                                                        <p class="card-text">{{ $image->alt_text ?: 'Sin descripción' }}</p>
                                                        <p class="card-text"><small class="text-muted">Orden: {{ $image->order }}</small></p>
                                                        <form action="{{ route('admin.landing.carousel.delete', $image->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay imágenes en el carrusel. Agrega la primera imagen.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Servicios -->
                    <div class="tab-pane fade" id="services" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Servicios</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Servicio
                                </button>
                            </div>
                            <div class="card-body">
                                @if($services->count() > 0)
                                    <div class="row">
                                        @foreach($services as $service)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="text-center mb-3">
                                                            <i class="{{ $service->icon_class }} fa-2x text-primary"></i>
                                                        </div>
                                                        <h6 class="card-title">{{ $service->title }}</h6>
                                                        <p class="card-text">{{ $service->description }}</p>
                                                        <small class="text-muted">Orden: {{ $service->order }}</small>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm" 
                                                                onclick="editService({{ $service->id }}, '{{ $service->icon_class }}', '{{ $service->title }}', '{{ addslashes($service->description) }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.services.delete', $service->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('¿Estás seguro de eliminar este servicio?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay servicios registrados. Agrega el primer servicio.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pasos -->
                    <div class="tab-pane fade" id="steps" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Pasos del Proceso</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStepModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Paso
                                </button>
                            </div>
                            <div class="card-body">
                                @if($steps->count() > 0)
                                    <div class="row">
                                        @foreach($steps as $step)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="text-center mb-3">
                                                            <span class="badge bg-primary fs-4">{{ $step->step_number }}</span>
                                                        </div>
                                                        <h6 class="card-title">{{ $step->title }}</h6>
                                                        <p class="card-text">{{ $step->description }}</p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm" 
                                                                onclick="editStep({{ $step->id }}, '{{ $step->title }}', '{{ addslashes($step->description) }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.steps.delete', $step->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('¿Estás seguro de eliminar este paso?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay pasos registrados. Agrega el primer paso.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Información de Contacto</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.contact.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Dirección</label>
                                                <input type="text" name="address" class="form-control" 
                                                       value="{{ $contactInfo->address ?? 'A108 Adam Street, New York, NY 535022' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="text" name="phone" class="form-control" 
                                                       value="{{ $contactInfo->phone ?? '+1 5589 55488 55' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email (mostrar en contacto)</label>
                                                <input type="email" name="email" class="form-control" 
                                                       value="{{ $contactInfo->email ?? 'info@example.com' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email para recibir mensajes</label>
                                                <input type="email" name="receive_messages_email" class="form-control" 
                                                       value="{{ $contactInfo->receive_messages_email ?? 'admin@example.com' }}" required>
                                                <small class="form-text text-muted">Los mensajes del formulario se enviarán a este email.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Código Embed de Google Maps</label>
                                        
                                        <!-- Instrucciones paso a paso -->
                                        <div class="alert alert-info">
                                            <h6><i class="bi bi-info-circle me-1"></i>Pasos para obtener el código embed:</h6>
                                            <ol class="mb-0">
                                                <li>Ve a <strong>maps.google.com</strong></li>
                                                <li>Busca tu dirección exacta</li>
                                                <li>Haz clic en <strong>"Compartir"</strong></li>
                                                <li>Selecciona <strong>"Incorporar un mapa"</strong></li>
                                                <li>Elige el tamaño (recomendado: Mediano o Grande)</li>
                                                <li>Copia todo el código <code>&lt;iframe&gt;...&lt;/iframe&gt;</code></li>
                                                <li>Pégalo en el campo de abajo</li>
                                            </ol>
                                        </div>
                                        
                                        <textarea name="google_maps_embed" class="form-control" rows="4" 
                                                  placeholder="<iframe src=&quot;https://www.google.com/maps/embed?pb=...&quot; width=&quot;600&quot; height=&quot;450&quot; style=&quot;border:0;&quot; allowfullscreen=&quot;&quot; loading=&quot;lazy&quot; referrerpolicy=&quot;no-referrer-when-downgrade&quot;></iframe>">{{ $contactInfo->google_maps_embed ?? '' }}</textarea>
                                        <small class="form-text text-muted">Pega aquí el código iframe completo de Google Maps (incluyendo las etiquetas &lt;iframe&gt; de apertura y cierre).</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Información de Contacto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Nosotros -->
                    <div class="tab-pane fade" id="about" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Página Nosotros</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.about.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Título de la Página</label>
                                        <input type="text" name="page_title" class="form-control" 
                                               value="{{ $about->page_title ?? 'Acerca de Nosotros' }}" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título de Propósito</label>
                                                <input type="text" name="purpose_title" class="form-control" 
                                                       value="{{ $about->purpose_title ?? 'Nuestro Propósito' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título de Misión</label>
                                                <input type="text" name="mission_title" class="form-control" 
                                                       value="{{ $about->mission_title ?? 'Nuestra Misión' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido del Propósito</label>
                                        <textarea name="purpose_content" class="form-control" rows="4" required>{{ $about->purpose_content ?? 'Definir el propósito de la empresa...' }}</textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido de la Misión</label>
                                        <textarea name="mission_content" class="form-control" rows="4" required>{{ $about->mission_content ?? 'Definir la misión de la empresa...' }}</textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título de Visión</label>
                                                <input type="text" name="vision_title" class="form-control" 
                                                       value="{{ $about->vision_title ?? 'Nuestra Visión' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Imagen Principal</label>
                                                <input type="file" name="main_image" class="form-control" accept="image/*">
                                                @if($about && $about->main_image_path)
                                                    <small class="form-text text-muted mt-1">
                                                        Imagen actual: <a href="{{ asset($about->main_image_path) }}" target="_blank">Ver imagen</a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Contenido de la Visión</label>
                                        <textarea name="vision_content" class="form-control" rows="4" required>{{ $about->vision_content ?? 'Definir la visión de la empresa...' }}</textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Página Nosotros
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Equipo -->
                    <div class="tab-pane fade" id="team" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Equipo de Trabajo</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTeamMemberModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Miembro
                                </button>
                            </div>
                            <div class="card-body">
                                @if($teamMembers->count() > 0)
                                    <div class="row">
                                        @foreach($teamMembers as $member)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    @if($member->image_path)
                                                        <img src="{{ asset($member->image_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                    @else
                                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                            <i class="bi bi-person-circle fs-1 text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $member->name }}</h6>
                                                        <p class="card-text"><strong>{{ $member->position }}</strong></p>
                                                        <p class="card-text">{{ $member->description }}</p>
                                                        
                                                        @if($member->twitter_url || $member->facebook_url || $member->instagram_url || $member->linkedin_url)
                                                            <div class="d-flex gap-2">
                                                                @if($member->twitter_url)
                                                                    <a href="{{ $member->twitter_url }}" target="_blank" class="text-primary">
                                                                        <i class="bi bi-twitter"></i>
                                                                    </a>
                                                                @endif
                                                                @if($member->facebook_url)
                                                                    <a href="{{ $member->facebook_url }}" target="_blank" class="text-primary">
                                                                        <i class="bi bi-facebook"></i>
                                                                    </a>
                                                                @endif
                                                                @if($member->instagram_url)
                                                                    <a href="{{ $member->instagram_url }}" target="_blank" class="text-primary">
                                                                        <i class="bi bi-instagram"></i>
                                                                    </a>
                                                                @endif
                                                                @if($member->linkedin_url)
                                                                    <a href="{{ $member->linkedin_url }}" target="_blank" class="text-primary">
                                                                        <i class="bi bi-linkedin"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm" 
                                                                onclick="editTeamMember({{ $member->id }}, '{{ $member->name }}', '{{ $member->position }}', '{{ addslashes($member->description) }}', '{{ $member->twitter_url }}', '{{ $member->facebook_url }}', '{{ $member->instagram_url }}', '{{ $member->linkedin_url }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.team.delete', $member->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('¿Estás seguro de eliminar este miembro del equipo?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay miembros del equipo registrados. Agrega el primer miembro.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Layout -->
                    <div class="tab-pane fade" id="layout" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración del Layout</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.layout.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título del Sitio</label>
                                                <input type="text" name="site_title" class="form-control" 
                                                       value="{{ $layoutConfig->site_title ?? 'Montano & Co.' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email del Top Bar</label>
                                                <input type="email" name="topbar_email" class="form-control" 
                                                       value="{{ $layoutConfig->topbar_email ?? 'info@example.com' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono del Top Bar</label>
                                                <input type="text" name="topbar_phone" class="form-control" 
                                                       value="{{ $layoutConfig->topbar_phone ?? '+1 5589 55488 55' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Empresa para Copyright</label>
                                                <input type="text" name="copyright_company" class="form-control" 
                                                       value="{{ $layoutConfig->copyright_company ?? 'Montano & Co.' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3">Redes Sociales</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Twitter URL</label>
                                                <input type="url" name="twitter_url" class="form-control" 
                                                       value="{{ $layoutConfig->twitter_url ?? '' }}" placeholder="https://twitter.com/usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Facebook URL</label>
                                                <input type="url" name="facebook_url" class="form-control" 
                                                       value="{{ $layoutConfig->facebook_url ?? '' }}" placeholder="https://facebook.com/usuario">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Instagram URL</label>
                                                <input type="url" name="instagram_url" class="form-control" 
                                                       value="{{ $layoutConfig->instagram_url ?? '' }}" placeholder="https://instagram.com/usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">LinkedIn URL</label>
                                                <input type="url" name="linkedin_url" class="form-control" 
                                                       value="{{ $layoutConfig->linkedin_url ?? '' }}" placeholder="https://linkedin.com/in/usuario">
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3">Información del Footer</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Dirección del Footer</label>
                                                <input type="text" name="footer_address" class="form-control" 
                                                       value="{{ $layoutConfig->footer_address ?? 'A108 Adam Street' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Ciudad del Footer</label>
                                                <input type="text" name="footer_city" class="form-control" 
                                                       value="{{ $layoutConfig->footer_city ?? 'New York, NY 535022' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono del Footer</label>
                                                <input type="text" name="footer_phone" class="form-control" 
                                                       value="{{ $layoutConfig->footer_phone ?? '+1 5589 55488 55' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email del Footer</label>
                                                <input type="email" name="footer_email" class="form-control" 
                                                       value="{{ $layoutConfig->footer_email ?? 'info@example.com' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración del Layout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración SEO</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.seo.update') }}" method="POST">
                                    @csrf

                                    <!-- Selector de Página -->
                                    <div class="mb-4">
                                        <label class="form-label">Página a Configurar</label>
                                        <select name="page_id" id="pageSelector" class="form-control" required>
                                            <option value="">Seleccionar página...</option>
                                            @foreach($pages as $page)
                                                <option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>
                                                    {{ $page->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Meta Tags Básicos -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">Meta Tags Básicos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título SEO <small>(máx. 150 caracteres)</small></label>
                                                        <input type="text" name="meta_title" class="form-control" maxlength="150" 
                                                               value="{{ old('meta_title') }}" 
                                                               placeholder="Título optimizado para motores de búsqueda">
                                                        <div class="form-text">
                                                            <span id="titleCounter">0</span>/150 caracteres
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Palabra Clave Principal</label>
                                                        <input type="text" name="focus_keyword" class="form-control" maxlength="100"
                                                               value="{{ old('focus_keyword') }}" 
                                                               placeholder="palabra clave principal">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Meta Descripción <small>(máx. 300 caracteres)</small></label>
                                                <textarea name="meta_description" class="form-control" rows="3" maxlength="300"
                                                          placeholder="Descripción atractiva que aparecerá en los resultados de búsqueda">{{ old('meta_description') }}</textarea>
                                                <div class="form-text">
                                                    <span id="descriptionCounter">0</span>/300 caracteres
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Palabras Clave (separadas por comas)</label>
                                                        <input type="text" name="meta_keywords" class="form-control" maxlength="500"
                                                               value="{{ old('meta_keywords') }}" 
                                                               placeholder="palabra1, palabra2, palabra3">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">URL Canónica</label>
                                                        <input type="url" name="canonical_url" class="form-control" maxlength="500"
                                                               value="{{ old('canonical_url') }}" 
                                                               placeholder="https://ejemplo.com/pagina">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Robots</label>
                                                <select name="robots" class="form-control">
                                                    <option value="index,follow" {{ old('robots') == 'index,follow' ? 'selected' : '' }}>Index, Follow (Recomendado)</option>
                                                    <option value="noindex,follow" {{ old('robots') == 'noindex,follow' ? 'selected' : '' }}>No Index, Follow</option>
                                                    <option value="index,nofollow" {{ old('robots') == 'index,nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                                                    <option value="noindex,nofollow" {{ old('robots') == 'noindex,nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Open Graph (Facebook) -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">Open Graph (Facebook/LinkedIn)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">OG Título</label>
                                                        <input type="text" name="og_title" class="form-control" maxlength="150"
                                                               value="{{ old('og_title') }}" 
                                                               placeholder="Título para compartir en redes sociales">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">OG Tipo</label>
                                                        <select name="og_type" class="form-control">
                                                            <option value="website" {{ old('og_type') == 'website' ? 'selected' : '' }}>Website</option>
                                                            <option value="article" {{ old('og_type') == 'article' ? 'selected' : '' }}>Article</option>
                                                            <option value="product" {{ old('og_type') == 'product' ? 'selected' : '' }}>Product</option>
                                                            <option value="business.business" {{ old('og_type') == 'business.business' ? 'selected' : '' }}>Business</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">OG Descripción</label>
                                                <textarea name="og_description" class="form-control" rows="3"
                                                          placeholder="Descripción para compartir en redes sociales">{{ old('og_description') }}</textarea>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">OG Imagen (URL)</label>
                                                        <input type="url" name="og_image" class="form-control" maxlength="500"
                                                               value="{{ old('og_image') }}" 
                                                               placeholder="https://ejemplo.com/imagen.jpg">
                                                        <div class="form-text">Tamaño recomendado: 1200x630 px</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">OG Nombre del Sitio</label>
                                                        <input type="text" name="og_site_name" class="form-control" maxlength="100"
                                                               value="{{ old('og_site_name') }}" 
                                                               placeholder="Nombre de tu sitio web">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">OG URL</label>
                                                <input type="url" name="og_url" class="form-control" maxlength="500"
                                                       value="{{ old('og_url') }}" 
                                                       placeholder="https://ejemplo.com/pagina">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Twitter Cards -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">Twitter Cards</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Twitter Card</label>
                                                        <select name="twitter_card" class="form-control">
                                                            <option value="summary_large_image" {{ old('twitter_card') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image (Recomendado)</option>
                                                            <option value="summary" {{ old('twitter_card') == 'summary' ? 'selected' : '' }}>Summary</option>
                                                            <option value="app" {{ old('twitter_card') == 'app' ? 'selected' : '' }}>App</option>
                                                            <option value="player" {{ old('twitter_card') == 'player' ? 'selected' : '' }}>Player</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Twitter Título</label>
                                                        <input type="text" name="twitter_title" class="form-control" maxlength="150"
                                                               value="{{ old('twitter_title') }}" 
                                                               placeholder="Título para Twitter">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Twitter Descripción</label>
                                                <textarea name="twitter_description" class="form-control" rows="3"
                                                          placeholder="Descripción para Twitter">{{ old('twitter_description') }}</textarea>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Twitter Imagen (URL)</label>
                                                        <input type="url" name="twitter_image" class="form-control" maxlength="500"
                                                               value="{{ old('twitter_image') }}" 
                                                               placeholder="https://ejemplo.com/imagen.jpg">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Twitter Site (@usuario)</label>
                                                        <input type="text" name="twitter_site" class="form-control" maxlength="50"
                                                               value="{{ old('twitter_site') }}" 
                                                               placeholder="@usuario">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Twitter Creator (@usuario)</label>
                                                <input type="text" name="twitter_creator" class="form-control" maxlength="50"
                                                       value="{{ old('twitter_creator') }}" 
                                                       placeholder="@autor">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Configuración de Sitemap -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">Configuración de Sitemap</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Incluir en Sitemap</label>
                                                        <select name="sitemap_include" class="form-control">
                                                            <option value="1" {{ old('sitemap_include') == '1' ? 'selected' : '' }}>Sí</option>
                                                            <option value="0" {{ old('sitemap_include') == '0' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Prioridad (0.0 - 1.0)</label>
                                                        <select name="sitemap_priority" class="form-control">
                                                            <option value="1.0" {{ old('sitemap_priority') == '1.0' ? 'selected' : '' }}>1.0 (Muy alta)</option>
                                                            <option value="0.8" {{ old('sitemap_priority') == '0.8' ? 'selected' : '' }}>0.8 (Alta)</option>
                                                            <option value="0.6" {{ old('sitemap_priority') == '0.6' ? 'selected' : '' }}>0.6 (Media)</option>
                                                            <option value="0.4" {{ old('sitemap_priority') == '0.4' ? 'selected' : '' }}>0.4 (Baja)</option>
                                                            <option value="0.2" {{ old('sitemap_priority') == '0.2' ? 'selected' : '' }}>0.2 (Muy baja)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Frecuencia de Cambio</label>
                                                        <select name="sitemap_changefreq" class="form-control">
                                                            <option value="daily" {{ old('sitemap_changefreq') == 'daily' ? 'selected' : '' }}>Diariamente</option>
                                                            <option value="weekly" {{ old('sitemap_changefreq') == 'weekly' ? 'selected' : '' }}>Semanalmente</option>
                                                            <option value="monthly" {{ old('sitemap_changefreq') == 'monthly' ? 'selected' : '' }}>Mensualmente</option>
                                                            <option value="yearly" {{ old('sitemap_changefreq') == 'yearly' ? 'selected' : '' }}>Anualmente</option>
                                                            <option value="never" {{ old('sitemap_changefreq') == 'never' ? 'selected' : '' }}>Nunca</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Título para Breadcrumbs</label>
                                                <input type="text" name="breadcrumb_title" class="form-control"
                                                       value="{{ old('breadcrumb_title') }}" 
                                                       placeholder="Título alternativo para navegación">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Schema.org -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">Schema.org JSON-LD (Opcional)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Marcado Estructurado JSON-LD</label>
                                                <textarea name="schema_markup" class="form-control" rows="8"
                                                          placeholder='{"@context": "https://schema.org", "@type": "Organization", "name": "Empresa"}'>{{ old('schema_markup') }}</textarea>
                                                <div class="form-text">
                                                    Código JSON-LD para datos estructurados. 
                                                    <a href="https://schema.org" target="_blank">Más información</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                                       {{ old('is_active', true) ? 'checked' : '' }} id="seoActive">
                                                <label class="form-check-label" for="seoActive">
                                                    Configuración SEO Activa
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración SEO
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    @include('admin.landing.modals.carousel')
    @include('admin.landing.modals.service')
    @include('admin.landing.modals.step')
    @include('admin.landing.modals.team')

    @push('scripts')
    <script>
        // Guardar y restaurar el tab activo
        document.addEventListener('DOMContentLoaded', function() {
            // Restaurar el tab activo al cargar la página
            const activeTab = localStorage.getItem('activeLandingTab');
            if (activeTab) {
                // Remover clase active de todos los tabs
                document.querySelectorAll('#landingTabs .nav-link').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('#landingTabsContent .tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Activar el tab guardado
                const tabButton = document.querySelector(`#landingTabs .nav-link[data-bs-target="${activeTab}"]`);
                const tabPane = document.querySelector(activeTab);
                
                if (tabButton && tabPane) {
                    tabButton.classList.add('active');
                    tabPane.classList.add('show', 'active');
                }
            }
            
            // Guardar el tab activo cuando se hace clic
            document.querySelectorAll('#landingTabs .nav-link').forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    localStorage.setItem('activeLandingTab', target);
                });
            });
        });

        function editService(id, iconClass, title, description) {
            document.getElementById('editServiceId').value = id;
            document.getElementById('editServiceIconClass').value = iconClass;
            document.getElementById('editServiceTitle').value = title;
            document.getElementById('editServiceDescription').value = description;
            
            const editForm = document.getElementById('editServiceForm');
            editForm.action = editForm.action.replace('/0', '/' + id);
            
            new bootstrap.Modal(document.getElementById('editServiceModal')).show();
        }

        function editStep(id, title, description) {
            document.getElementById('editStepId').value = id;
            document.getElementById('editStepTitle').value = title;
            document.getElementById('editStepDescription').value = description;
            
            const editForm = document.getElementById('editStepForm');
            editForm.action = editForm.action.replace('/0', '/' + id);
            
            new bootstrap.Modal(document.getElementById('editStepModal')).show();
        }

        function editTeamMember(id, name, position, description, twitterUrl, facebookUrl, instagramUrl, linkedinUrl) {
            document.getElementById('editTeamMemberId').value = id;
            document.getElementById('editTeamMemberName').value = name;
            document.getElementById('editTeamMemberPosition').value = position;
            document.getElementById('editTeamMemberDescription').value = description;
            document.getElementById('editTeamMemberTwitterUrl').value = twitterUrl || '';
            document.getElementById('editTeamMemberFacebookUrl').value = facebookUrl || '';
            document.getElementById('editTeamMemberInstagramUrl').value = instagramUrl || '';
            document.getElementById('editTeamMemberLinkedinUrl').value = linkedinUrl || '';
            
            const editForm = document.getElementById('editTeamMemberForm');
            editForm.action = editForm.action.replace('/0', '/' + id);
            
            new bootstrap.Modal(document.getElementById('editTeamMemberModal')).show();
        }

        // Character counters for SEO fields
        function updateCharacterCount(inputId, counterId) {
            const input = document.getElementById(inputId) || document.querySelector(`[name="${inputId}"]`);
            const counter = document.getElementById(counterId);
            
            if (input && counter) {
                input.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                });
                
                // Update on load
                counter.textContent = input.value.length;
            }
        }

        // Initialize character counters when SEO tab is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Setup character counters
            const titleInput = document.querySelector('[name="meta_title"]');
            const descInput = document.querySelector('[name="meta_description"]');
            
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    document.getElementById('titleCounter').textContent = this.value.length;
                });
            }
            
            if (descInput) {
                descInput.addEventListener('input', function() {
                    document.getElementById('descriptionCounter').textContent = this.value.length;
                });
            }

            // SEO page selector functionality
            const pageSelector = document.getElementById('pageSelector');
            if (pageSelector) {
                pageSelector.addEventListener('change', function() {
                    const pageId = this.value;
                    if (pageId) {
                        loadSeoData(pageId);
                    } else {
                        clearSeoForm();
                    }
                });
            }
        });

        // Load SEO data for selected page
        function loadSeoData(pageId) {
            fetch(`{{ url('admin/landing/seo') }}/${pageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        populateSeoForm(data);
                    } else {
                        clearSeoForm();
                    }
                })
                .catch(error => {
                    console.error('Error loading SEO data:', error);
                    clearSeoForm();
                });
        }

        // Populate SEO form with data
        function populateSeoForm(data) {
            const form = document.querySelector('[action="{{ route('admin.landing.seo.update') }}"]');
            
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'checkbox') {
                        input.checked = Boolean(data[key]);
                    } else if (input.tagName === 'TEXTAREA') {
                        input.value = data[key] || '';
                    } else {
                        input.value = data[key] || '';
                    }
                }
            });

            // Update character counters
            updateCharacterCounters();
        }

        // Clear SEO form
        function clearSeoForm() {
            const form = document.querySelector('[action="{{ route('admin.landing.seo.update') }}"]');
            
            // Reset all inputs except page_id
            form.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name !== 'page_id') {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                }
            });

            // Set defaults
            form.querySelector('[name="robots"]').value = 'index,follow';
            form.querySelector('[name="og_type"]').value = 'website';
            form.querySelector('[name="twitter_card"]').value = 'summary_large_image';
            form.querySelector('[name="sitemap_include"]').value = '1';
            form.querySelector('[name="sitemap_priority"]').value = '0.8';
            form.querySelector('[name="sitemap_changefreq"]').value = 'weekly';
            form.querySelector('[name="is_active"]').checked = true;

            // Update character counters
            updateCharacterCounters();
        }

        // Update character counters
        function updateCharacterCounters() {
            const titleInput = document.querySelector('[name="meta_title"]');
            const descInput = document.querySelector('[name="meta_description"]');
            
            if (titleInput) {
                document.getElementById('titleCounter').textContent = titleInput.value.length;
            }
            
            if (descInput) {
                document.getElementById('descriptionCounter').textContent = descInput.value.length;
            }
        }
    </script>
    @endpush
</x-app-layout>