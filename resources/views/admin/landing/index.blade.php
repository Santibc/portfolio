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
                                    <div class="mb-3">
                                        <label class="form-label">Descripción de la Sección Contacto</label>
                                        <textarea name="description" class="form-control" rows="3" 
                                                  placeholder="Breve descripción que aparece en la sección de contacto">{{ $contactInfo->description ?? 'Estamos aquí para ayudarte. Contáctanos y resolveremos todas tus dudas.' }}</textarea>
                                    </div>
                                    
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
                                                <small class="form-text text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>Recomendado: 1280x854 píxeles (proporción 3:2) para mejor visualización
                                                </small>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h1 class="h3 mb-0">
                                        <i class="bi bi-search me-2 text-primary"></i>
                                        Gestión SEO
                                    </h1>
                                </div>
                            </div>
                        </div>

                        <!-- Card 1: Lista de SEOs Configurados -->
                        @if($seoConfigs->count() > 0)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-list-check me-2 text-success"></i>
                                            SEOs Configurados
                                        </h5>
                                        <span class="badge bg-primary">{{ $seoConfigs->count() }} configuraciones</span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0 fw-bold">
                                                        <i class="bi bi-file-text me-1"></i>Página
                                                    </th>
                                                    <th class="border-0 fw-bold">
                                                        <i class="bi bi-tag me-1"></i>Título SEO
                                                    </th>
                                                    <th class="border-0 fw-bold">
                                                        <i class="bi bi-key me-1"></i>Palabra Clave
                                                    </th>
                                                    <th class="border-0 fw-bold">
                                                        <i class="bi bi-robot me-1"></i>Robots
                                                    </th>
                                                    <th class="border-0 fw-bold text-center">
                                                        <i class="bi bi-toggle-on me-1"></i>Estado
                                                    </th>
                                                    <th class="border-0 fw-bold text-center">
                                                        <i class="bi bi-gear me-1"></i>Acciones
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($seoConfigs as $seoConfig)
                                                    <tr class="align-middle">
                                                        <td class="fw-bold text-primary">
                                                            <i class="bi bi-page-forward me-2"></i>
                                                            {{ $seoConfig->page->name }}
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate" style="max-width: 250px;" title="{{ $seoConfig->meta_title }}">
                                                                {{ $seoConfig->meta_title ?: 'No definido' }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($seoConfig->focus_keyword)
                                                                <span class="badge bg-info">{{ $seoConfig->focus_keyword }}</span>
                                                            @else
                                                                <span class="text-muted">No definida</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <code class="text-dark">{{ $seoConfig->robots }}</code>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($seoConfig->is_active)
                                                                <span class="badge bg-success">
                                                                    <i class="bi bi-check-circle me-1"></i>Activo
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                        onclick="editSeo({{ $seoConfig->page->id }})"
                                                                        title="Editar">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <form action="{{ route('admin.landing.seo.delete', $seoConfig->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                            onclick="return confirm('¿Estás seguro de eliminar esta configuración SEO?')"
                                                                            title="Eliminar">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    No hay configuraciones SEO creadas aún. Utiliza el formulario de abajo para crear la primera configuración.
                                </div>
                            </div>
                        @endif

                        <!-- Card 2: Formulario de Configuración -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Configurar SEO
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.seo.update') }}" method="POST" id="seoForm">
                                    @csrf

                                    <!-- Selector de Página -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <select name="page_id" id="pageSelector" class="form-select" required>
                                                    <option value="">Seleccionar página...</option>
                                                    @foreach($pages as $page)
                                                        <option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>
                                                            {{ $page->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label for="pageSelector">
                                                    <i class="bi bi-file-earmark me-1"></i>Página a Configurar *
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campos del Formulario -->
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-4">
                                                <input type="text" name="meta_title" class="form-control" id="metaTitle" maxlength="150" 
                                                       value="{{ old('meta_title') }}" 
                                                       placeholder="Título optimizado para motores de búsqueda">
                                                <label for="metaTitle">
                                                    <i class="bi bi-tag-fill me-1"></i>Título SEO *
                                                </label>
                                                <div class="form-text">
                                                    <span id="titleCounter">0</span>/150 caracteres
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-4">
                                                <input type="text" name="focus_keyword" class="form-control" id="focusKeyword" maxlength="100"
                                                       value="{{ old('focus_keyword') }}" 
                                                       placeholder="palabra clave principal">
                                                <label for="focusKeyword">
                                                    <i class="bi bi-key-fill me-1"></i>Palabra Clave Principal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-floating mb-4">
                                        <textarea name="meta_description" class="form-control" id="metaDescription" style="height: 120px"
                                                  placeholder="Descripción que aparecerá en los resultados de búsqueda">{{ old('meta_description') }}</textarea>
                                        <label for="metaDescription">
                                            <i class="bi bi-file-text me-1"></i>Meta Descripción
                                        </label>
                                        <div class="form-text">
                                            <span id="descriptionCounter">0</span> caracteres
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-4">
                                                <input type="text" name="meta_keywords" class="form-control" id="metaKeywords" maxlength="500"
                                                       value="{{ old('meta_keywords') }}" 
                                                       placeholder="palabra1, palabra2, palabra3">
                                                <label for="metaKeywords">
                                                    <i class="bi bi-tags me-1"></i>Palabras Clave
                                                </label>
                                                <div class="form-text">Separadas por comas</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-4">
                                                <input type="url" name="canonical_url" class="form-control" id="canonicalUrl" maxlength="500"
                                                       value="{{ old('canonical_url') }}" 
                                                       placeholder="https://ejemplo.com/pagina">
                                                <label for="canonicalUrl">
                                                    <i class="bi bi-link-45deg me-1"></i>URL Canónica
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-4">
                                                <select name="robots" class="form-select" id="robotsSelect">
                                                    <option value="index,follow" {{ old('robots') == 'index,follow' ? 'selected' : '' }}>Index, Follow (Recomendado)</option>
                                                    <option value="noindex,follow" {{ old('robots') == 'noindex,follow' ? 'selected' : '' }}>No Index, Follow</option>
                                                    <option value="index,nofollow" {{ old('robots') == 'index,nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                                                    <option value="noindex,nofollow" {{ old('robots') == 'noindex,nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                                                </select>
                                                <label for="robotsSelect">
                                                    <i class="bi bi-robot me-1"></i>Robots Meta Tag *
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                                       {{ old('is_active', true) ? 'checked' : '' }} id="seoActiveSwitch">
                                                <label class="form-check-label" for="seoActiveSwitch">
                                                  
                                                    <strong>Configuración SEO Activa</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Botón de Guardar fuera de la card -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" form="seoForm" class="btn btn-lg btn-primary shadow">
                                <i class="bi bi-check-circle me-2"></i>
                                Guardar Configuración SEO
                            </button>
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
            const titleInput = document.getElementById('metaTitle');
            const descInput = document.getElementById('metaDescription');
            
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
            const form = document.getElementById('seoForm');
            
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
            const form = document.getElementById('seoForm');
            
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
            form.querySelector('[name="is_active"]').checked = true;

            // Update character counters
            updateCharacterCounters();
        }

        // Update character counters
        function updateCharacterCounters() {
            const titleInput = document.getElementById('metaTitle');
            const descInput = document.getElementById('metaDescription');
            
            if (titleInput) {
                document.getElementById('titleCounter').textContent = titleInput.value.length;
            }
            
            if (descInput) {
                document.getElementById('descriptionCounter').textContent = descInput.value.length;
            }
        }

        // Edit SEO function
        function editSeo(pageId) {
            const pageSelector = document.getElementById('pageSelector');
            if (pageSelector) {
                pageSelector.value = pageId;
                // Trigger change event to load the data
                pageSelector.dispatchEvent(new Event('change'));
                
                // Scroll to form
                document.getElementById('seoForm').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
    </script>
    @endpush
</x-app-layout>