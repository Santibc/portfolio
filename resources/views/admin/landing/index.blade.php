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
                        <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                            <i class="bi bi-star me-1"></i>Sección Principal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                            <i class="bi bi-share me-1"></i>Redes Sociales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="home-about-tab" data-bs-toggle="tab" data-bs-target="#home-about" type="button" role="tab">
                            <i class="bi bi-house-door me-1"></i> Acerca de (Home)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="home-services-tab" data-bs-toggle="tab" data-bs-target="#home-services" type="button" role="tab">
                            <i class="bi bi-briefcase me-1"></i>Servicios (Home)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="testimonials-tab" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab">
                            <i class="bi bi-chat-quote me-1"></i>Testimonios
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
                        <button class="nav-link" id="about-values-tab" data-bs-toggle="tab" data-bs-target="#about-values" type="button" role="tab">
                            <i class="bi bi-award me-1"></i>Valores (Nosotros)
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                            <i class="bi bi-calculator me-1"></i>Pricing
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
                                                       value="{{ $config->company_name ?? 'GUILLEN ' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción de la Empresa</label>
                                        <textarea name="company_description" class="form-control" rows="5" required>{{ $config->company_description ?? 'Somos un despacho orientado a resultados...' }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Principal -->
                    <div class="tab-pane fade" id="hero" role="tabpanel">
                        <!-- Hero Configuration -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración de la Sección Principal</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.hero.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Subtítulo de la Página Principal</label>
                                        <input type="text" name="subtitle" class="form-control"
                                               value="{{ $heroSection->subtitle ?? '' }}" placeholder="Texto del subtítulo">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Imagen de Fondo Principal</label>
                                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                                        @if($heroSection && $heroSection->hero_image_path)
                                            <small class="form-text text-muted">
                                                Imagen actual: <a href="{{ asset($heroSection->hero_image_path) }}" target="_blank">Ver imagen</a>
                                            </small>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Hero
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Hero Values -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Valores de la Empresa</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHeroValueModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Valor
                                </button>
                            </div>
                            <div class="card-body">
                                @if($heroValues->count() > 0)
                                    <div class="row">
                                        @foreach($heroValues as $value)
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="text-center mb-3">
                                                            <i class="{{ $value->icon_class }} fa-2x text-primary"></i>
                                                        </div>
                                                        <h6 class="card-title text-center">{{ $value->title }}</h6>
                                                        <p class="card-text text-center"><small class="text-muted">Orden: {{ $value->order }}</small></p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm"
                                                                onclick="editHeroValue({{ $value->id }}, '{{ $value->icon_class }}', '{{ addslashes($value->title) }}', {{ $value->order }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.hero-values.delete', $value->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('¿Estás seguro de eliminar este valor?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay valores del hero registrados. Agrega el primer valor.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Redes Sociales -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Redes Sociales</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.social-media.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Facebook URL</label>
                                                <input type="url" name="facebook_url" class="form-control"
                                                       value="{{ $socialMedia->facebook_url ?? '' }}" placeholder="https://facebook.com/usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Instagram URL</label>
                                                <input type="url" name="instagram_url" class="form-control"
                                                       value="{{ $socialMedia->instagram_url ?? '' }}" placeholder="https://instagram.com/usuario">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Google URL</label>
                                                <input type="url" name="linkedin_url" class="form-control"
                                                       value="{{ $socialMedia->linkedin_url ?? '' }}" placeholder="https://google.com/">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">TikTok URL</label>
                                                <input type="url" name="youtube_url" class="form-control"
                                                       value="{{ $socialMedia->youtube_url ?? '' }}" placeholder="https://tiktok.com/@usuario">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Redes Sociales
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Secci00f3n Acerca de (Home) -->
                    <div class="tab-pane fade" id="home-about" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Sección About en Home</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.home-about.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Imagen</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                @if($homeAbout && $homeAbout->image_path)
                                                    <small class="form-text text-muted">
                                                        Imagen actual: <a href="{{ asset($homeAbout->image_path) }}" target="_blank">Ver imagen</a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título</label>
                                                <input type="text" name="title" class="form-control"
                                                       value="{{ $homeAbout->title ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Lead Text</label>
                                        <input type="text" name="lead_text" class="form-control"
                                               value="{{ $homeAbout->lead_text ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="description" class="form-control" rows="4" required>{{ $homeAbout->description ?? '' }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Años de Experiencia</label>
                                                <input type="number" name="years_experience" class="form-control"
                                                       value="{{ $homeAbout->years_experience ?? 0 }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Clientes Felices</label>
                                                <input type="number" name="happy_clients" class="form-control"
                                                       value="{{ $homeAbout->happy_clients ?? 0 }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Satisfacción del Cliente (%)</label>
                                                <input type="number" name="client_satisfaction" class="form-control"
                                                       value="{{ $homeAbout->client_satisfaction ?? 0 }}" min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Texto del Botón CTA</label>
                                                <input type="text" name="cta_button_text" class="form-control"
                                                       value="{{ $homeAbout->cta_button_text ?? 'Más Información' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">URL del Botón CTA</label>
                                                <input type="text" name="cta_button_url" class="form-control"
                                                       value="{{ $homeAbout->cta_button_url ?? '#' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar  Acerca de (Home)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Servicios (Home) -->
                    <div class="tab-pane fade" id="home-services" role="tabpanel">
                        <!-- Service Configuration -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración de Servicios</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.home-services.update') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Título de la Sección</label>
                                        <input type="text" name="section_title" class="form-control"
                                               value="{{ $homeServicesConfig->section_title ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción de la Sección</label>
                                        <textarea name="section_description" class="form-control" rows="3">{{ $homeServicesConfig->section_description ?? '' }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título Comercial</label>
                                                <input type="text" name="commercial_title" class="form-control"
                                                       value="{{ $homeServicesConfig->commercial_title ?? 'Comercial' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título Residencial</label>
                                                <input type="text" name="residential_title" class="form-control"
                                                       value="{{ $homeServicesConfig->residential_title ?? 'Residencial' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nota Eco-Friendly</label>
                                        <input type="text" name="eco_friendly_note" class="form-control"
                                               value="{{ $homeServicesConfig->eco_friendly_note ?? '' }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Service Items -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Items de Servicio</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceItemModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Item
                                </button>
                            </div>
                            <div class="card-body">
                                @if($serviceItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Icono</th>
                                                    <th>Título</th>
                                                    <th>Descripción</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($serviceItems as $item)
                                                    <tr>
                                                        <td><span class="badge bg-{{ $item->type == 'commercial' ? 'primary' : 'success' }}">{{ $item->type }}</span></td>
                                                        <td><i class="{{ $item->icon_class }}"></i></td>
                                                        <td>{{ $item->title }}</td>
                                                        <td>{{ Str::limit($item->description, 50) }}</td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm"
                                                                    onclick="editServiceItem({{ $item->id }}, '{{ $item->type }}', '{{ $item->icon_class }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->description) }}')">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <form action="{{ route('admin.landing.service-items.delete', $item->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('¿Estás seguro de eliminar este item?')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No hay items de servicio registrados.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Service Images -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Imágenes de Servicios</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceImageModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Imagen
                                </button>
                            </div>
                            <div class="card-body">
                                @if($serviceImages->count() > 0)
                                    <div class="row">
                                        @foreach($serviceImages as $image)
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <img src="{{ asset($image->image_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                            <span class="badge bg-{{ $image->type == 'commercial' ? 'primary' : 'success' }}">{{ $image->type }}</span>
                                                        </p>
                                                        <p class="card-text"><small>{{ $image->alt_text }}</small></p>
                                                        <form action="{{ route('admin.landing.service-images.delete', $image->id) }}" method="POST">
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
                                    <p class="text-muted">No hay imágenes de servicio registradas.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Testimonios de Clientes -->
                    <div class="tab-pane fade" id="testimonials" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Testimonios</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Testimonio
                                </button>
                            </div>
                            <div class="card-body">
                                @if($testimonials->count() > 0)
                                    <div class="row">
                                        @foreach($testimonials as $testimonial)
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <h6 class="card-title mb-0">{{ $testimonial->client_name }}</h6>
                                                            <div>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill' : '' }} text-warning"></i>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <p class="card-text"><small class="text-muted">{{ $testimonial->client_location }}</small></p>
                                                        <p class="card-text">{{ $testimonial->testimonial_text }}</p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm"
                                                                onclick="editTestimonial({{ $testimonial->id }}, '{{ addslashes($testimonial->client_name) }}', '{{ addslashes($testimonial->client_location) }}', '{{ addslashes($testimonial->testimonial_text) }}', {{ $testimonial->rating }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.testimonials.delete', $testimonial->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('¿Estás seguro de eliminar este testimonio?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay testimonios registrados. Agrega el primer testimonio.</p>
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
                                        <label class="form-label">Título del Hero de Contacto</label>
                                        <input type="text" name="contact_hero_title" class="form-control"
                                               value="{{ $contactInfo->contact_hero_title ?? 'Contáctanos' }}" placeholder="Título del hero">
                                    </div>
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

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Años de Experiencia</label>
                                                <input type="number" name="years_experience" class="form-control"
                                                       value="{{ $about->years_experience ?? 16 }}" min="0" required>
                                                <small class="text-muted">Ejemplo: 16 (se mostrará como "16+")</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Clientes Felices</label>
                                                <input type="number" name="happy_clients" class="form-control"
                                                       value="{{ $about->happy_clients ?? 500 }}" min="0" required>
                                                <small class="text-muted">Ejemplo: 500 (se mostrará como "500+")</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Satisfacción del Cliente (%)</label>
                                                <input type="number" name="client_satisfaction" class="form-control"
                                                       value="{{ $about->client_satisfaction ?? 100 }}" min="0" max="100" required>
                                                <small class="text-muted">Ejemplo: 100 (se mostrará como "100%")</small>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Página Nosotros
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Valores ( Nosotros) -->
                    <div class="tab-pane fade" id="about-values" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Valores de Nosotros</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAboutValueModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Valor
                                </button>
                            </div>
                            <div class="card-body">
                                @if($aboutValues->count() > 0)
                                    <div class="row">
                                        @foreach($aboutValues as $value)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="text-center mb-3">
                                                            <i class="{{ $value->icon_class }} fa-2x text-primary"></i>
                                                        </div>
                                                        <h6 class="card-title">{{ $value->title }}</h6>
                                                        <p class="card-text">{{ $value->description }}</p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm"
                                                                onclick="editAboutValue({{ $value->id }}, '{{ $value->icon_class }}', '{{ addslashes($value->title) }}', '{{ addslashes($value->description) }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.landing.about-values.delete', $value->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('¿Estás seguro de eliminar este valor?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay valores registrados. Agrega el primer valor.</p>
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
                                                       value="{{ $layoutConfig->site_title ?? 'GUILLEN' }}" required>
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

                                    <div class="mb-3">
                                        <label class="form-label">Descripción del Footer</label>
                                        <textarea name="footer_description" class="form-control" rows="3"
                                                  placeholder="Descripción de la empresa que aparece en el footer">{{ $layoutConfig->footer_description ?? 'Excellence and professionalism in residential and commercial cleaning services. Trusted by Wisconsin since 2009.' }}</textarea>
                                        <small class="text-muted">Aparece debajo del nombre de la empresa en el footer</small>
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
                                                       value="{{ $layoutConfig->copyright_company ?? 'GUILLEN' }}" required>
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

                    <!-- Pricing Calculator Tab -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-calculator me-2"></i>Pricing Calculator Configuration</h5>
                            </div>
                            <div class="card-body">

                                <!-- General Configuration -->
                                <h6 class="border-bottom pb-2 mb-3">General Configuration</h6>
                                <form action="{{ route('admin.landing.pricing.config.update') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">WhatsApp Number (with country code)</label>
                                                <input type="text" name="whatsapp_number" class="form-control"
                                                       value="{{ $pricingConfig->whatsapp_number ?? '573202230467' }}"
                                                       placeholder="573001234567" required>
                                                <small class="text-muted">Example: 573001234567 (57 = Colombia, 1 = USA)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Weekly Discount (%)</label>
                                                <input type="number" name="recurring_weekly_discount" class="form-control"
                                                       value="{{ $pricingConfig->recurring_weekly_discount ?? 20 }}" min="0" max="100" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Bi-Weekly Discount (%)</label>
                                                <input type="number" name="recurring_biweekly_discount" class="form-control"
                                                       value="{{ $pricingConfig->recurring_biweekly_discount ?? 15 }}" min="0" max="100" required>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Extra Services Pricing</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Extra Heavy Duty ($)</label>
                                                <input type="number" step="0.01" name="extra_heavy_duty" class="form-control"
                                                       value="{{ $pricingConfig->extra_heavy_duty ?? 150 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Inside Fridge ($)</label>
                                                <input type="number" step="0.01" name="inside_fridge_ea" class="form-control"
                                                       value="{{ $pricingConfig->inside_fridge_ea ?? 50 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Inside Oven ($)</label>
                                                <input type="number" step="0.01" name="inside_oven_ea" class="form-control"
                                                       value="{{ $pricingConfig->inside_oven_ea ?? 50 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Post-Const Government ($/sqft)</label>
                                                <input type="number" step="0.01" name="post_construction_government" class="form-control"
                                                       value="{{ $pricingConfig->post_construction_government ?? 0.90 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Post-Const Private ($/sqft)</label>
                                                <input type="number" step="0.01" name="post_construction_private" class="form-control"
                                                       value="{{ $pricingConfig->post_construction_private ?? 0.60 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Window Interior ($/pane)</label>
                                                <input type="number" step="0.01" name="window_clean_interior" class="form-control"
                                                       value="{{ $pricingConfig->window_clean_interior ?? 8 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Window Exterior ($/pane)</label>
                                                <input type="number" step="0.01" name="window_clean_exterior" class="form-control"
                                                       value="{{ $pricingConfig->window_clean_exterior ?? 10 }}" min="0" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Save Configuration
                                        </button>
                                    </div>
                                </form>

                                <!-- Pricing Ranges -->
                                <h6 class="border-bottom pb-2 mb-3 mt-5">Pricing Ranges by Square Footage</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Range (sq ft)</th>
                                                <th>Initial Clean</th>
                                                <th>Weekly</th>
                                                <th>Bi-Weekly</th>
                                                <th>Monthly</th>
                                                <th>Deep Clean</th>
                                                <th>Move Out</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pricingRanges as $range)
                                            <tr>
                                                <form action="{{ route('admin.landing.pricing.range.update', $range->id) }}" method="POST" class="range-form-{{ $range->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <input type="number" name="sq_ft_min" class="form-control form-control-sm" style="width: 70px;"
                                                                   value="{{ $range->sq_ft_min }}" required>
                                                            <span class="align-self-center">-</span>
                                                            <input type="number" name="sq_ft_max" class="form-control form-control-sm" style="width: 70px;"
                                                                   value="{{ $range->sq_ft_max }}" required>
                                                        </div>
                                                    </td>
                                                    <td><input type="number" step="0.01" name="initial_clean" class="form-control form-control-sm" value="{{ $range->initial_clean }}" required></td>
                                                    <td><input type="number" step="0.01" name="weekly" class="form-control form-control-sm" value="{{ $range->weekly }}" required></td>
                                                    <td><input type="number" step="0.01" name="biweekly" class="form-control form-control-sm" value="{{ $range->biweekly }}" required></td>
                                                    <td><input type="number" step="0.01" name="monthly" class="form-control form-control-sm" value="{{ $range->monthly }}" required></td>
                                                    <td><input type="number" step="0.01" name="deep_clean" class="form-control form-control-sm" value="{{ $range->deep_clean }}" required></td>
                                                    <td><input type="number" step="0.01" name="move_out_clean" class="form-control form-control-sm" value="{{ $range->move_out_clean }}" required></td>
                                                    <td>
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    </td>
                                                </form>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- End Pricing Tab -->

                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Hero Value Add Modal -->
    <div class="modal fade" id="addHeroValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Valor del Hero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.hero-values.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" name="icon_class" class="form-control" required placeholder="bi bi-check-circle">
                            <div class="alert alert-info mt-2"><strong><i class="bi bi-info-circle me-1"></i>Cómo encontrar iconos:</strong><ol class="mb-0 mt-2"><li><strong>Bootstrap Icons:</strong> Ve a <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></li><li>Busca el icono que necesitas (ejemplo: "check", "star", "home")</li><li>Haz clic en el icono para ver su nombre</li><li>Copia la clase completa (ejemplo: <code>bi bi-check-circle</code>)</li><li>Pégala en el campo de arriba</li></ol><p class="mb-0 mt-2"><strong>Alternativa Font Awesome:</strong> <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a> (ejemplo: <code>fas fa-star</code>)</p></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required placeholder="Ejemplo: Trusted & Insured">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hero Value Edit Modal -->
    <div class="modal fade" id="editHeroValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Valor del Hero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHeroValueForm" action="{{ route('admin.landing.hero-values.update', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editHeroValueId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" id="editHeroValueIcon" name="icon_class" class="form-control" required>
                            <div class="alert alert-info mt-2"><strong><i class="bi bi-info-circle me-1"></i>Cómo encontrar iconos:</strong><ol class="mb-0 mt-2"><li><strong>Bootstrap Icons:</strong> Ve a <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></li><li>Busca el icono que necesitas (ejemplo: "check", "star", "home")</li><li>Haz clic en el icono para ver su nombre</li><li>Copia la clase completa (ejemplo: <code>bi bi-check-circle</code>)</li><li>Pégala en el campo de arriba</li></ol><p class="mb-0 mt-2"><strong>Alternativa Font Awesome:</strong> <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a> (ejemplo: <code>fas fa-star</code>)</p></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" id="editHeroValueTitle" name="title" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Item Add Modal -->
    <div class="modal fade" id="addServiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Item de Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.service-items.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="commercial">Comercial</option>
                                <option value="residential">Residencial</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" name="icon_class" class="form-control" required placeholder="bi bi-check">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Item Edit Modal -->
    <div class="modal fade" id="editServiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Item de Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editServiceItemForm" action="{{ route('admin.landing.service-items.update', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceItemId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select id="editServiceItemType" name="type" class="form-select" required>
                                <option value="commercial">Comercial</option>
                                <option value="residential">Residencial</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" id="editServiceItemIcon" name="icon_class" class="form-control" required>
                            <div class="alert alert-info mt-2"><strong><i class="bi bi-info-circle me-1"></i>Cómo encontrar iconos:</strong><ol class="mb-0 mt-2"><li><strong>Bootstrap Icons:</strong> Ve a <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></li><li>Busca el icono que necesitas (ejemplo: "check", "star", "home")</li><li>Haz clic en el icono para ver su nombre</li><li>Copia la clase completa (ejemplo: <code>bi bi-check-circle</code>)</li><li>Pégala en el campo de arriba</li></ol><p class="mb-0 mt-2"><strong>Alternativa Font Awesome:</strong> <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a> (ejemplo: <code>fas fa-star</code>)</p></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" id="editServiceItemTitle" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="editServiceItemDescription" name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Image Add Modal -->
    <div class="modal fade" id="addServiceImageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Imagen de Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.service-images.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="commercial">Comercial</option>
                                <option value="residential">Residencial</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Texto Alternativo</label>
                            <input type="text" name="alt_text" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Testimonial Add Modal -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Testimonio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.testimonials.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Cliente</label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="client_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonio</label>
                            <textarea name="testimonial_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Calificación (1-5)</label>
                            <select name="rating" class="form-select" required>
                                <option value="5" selected>5 Estrellas</option>
                                <option value="4">4 Estrellas</option>
                                <option value="3">3 Estrellas</option>
                                <option value="2">2 Estrellas</option>
                                <option value="1">1 Estrella</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Testimonial Edit Modal -->
    <div class="modal fade" id="editTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Testimonio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editTestimonialForm" action="{{ route('admin.landing.testimonials.update', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTestimonialId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Cliente</label>
                            <input type="text" id="editTestimonialClientName" name="client_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" id="editTestimonialLocation" name="client_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonio</label>
                            <textarea id="editTestimonialText" name="testimonial_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Calificación (1-5)</label>
                            <select id="editTestimonialRating" name="rating" class="form-select" required>
                                <option value="5">5 Estrellas</option>
                                <option value="4">4 Estrellas</option>
                                <option value="3">3 Estrellas</option>
                                <option value="2">2 Estrellas</option>
                                <option value="1">1 Estrella</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- About Value Add Modal -->
    <div class="modal fade" id="addAboutValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Valor de Nosotros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.about-values.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" name="icon_class" class="form-control" required placeholder="bi bi-award">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- About Value Edit Modal -->
    <div class="modal fade" id="editAboutValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Valor de Nosotros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAboutValueForm" action="{{ route('admin.landing.about-values.update', 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editAboutValueId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (clase)</label>
                            <input type="text" id="editAboutValueIcon" name="icon_class" class="form-control" required>
                            <div class="alert alert-info mt-2"><strong><i class="bi bi-info-circle me-1"></i>Cómo encontrar iconos:</strong><ol class="mb-0 mt-2"><li><strong>Bootstrap Icons:</strong> Ve a <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></li><li>Busca el icono que necesitas (ejemplo: "check", "star", "home")</li><li>Haz clic en el icono para ver su nombre</li><li>Copia la clase completa (ejemplo: <code>bi bi-check-circle</code>)</li><li>Pégala en el campo de arriba</li></ol><p class="mb-0 mt-2"><strong>Alternativa Font Awesome:</strong> <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a> (ejemplo: <code>fas fa-star</code>)</p></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" id="editAboutValueTitle" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="editAboutValueDescription" name="description" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Esperar a que Bootstrap esté completamente cargado
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado');
        }

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            // Guardar y restaurar el tab activo
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

        // Hero Value Edit
        function editHeroValue(id, icon, title, order) {
            document.getElementById('editHeroValueId').value = id;
            document.getElementById('editHeroValueIcon').value = icon;
            document.getElementById('editHeroValueTitle').value = title;

            const editForm = document.getElementById('editHeroValueForm');
            editForm.action = editForm.action.replace('/0', '/' + id);

            new bootstrap.Modal(document.getElementById('editHeroValueModal')).show();
        }

        // Service Item Edit
        function editServiceItem(id, type, icon, title, description) {
            document.getElementById('editServiceItemId').value = id;
            document.getElementById('editServiceItemType').value = type;
            document.getElementById('editServiceItemIcon').value = icon;
            document.getElementById('editServiceItemTitle').value = title;
            document.getElementById('editServiceItemDescription').value = description;

            const editForm = document.getElementById('editServiceItemForm');
            editForm.action = editForm.action.replace('/0', '/' + id);

            new bootstrap.Modal(document.getElementById('editServiceItemModal')).show();
        }

        // Testimonial Edit
        function editTestimonial(id, clientName, location, text, rating) {
            document.getElementById('editTestimonialId').value = id;
            document.getElementById('editTestimonialClientName').value = clientName;
            document.getElementById('editTestimonialLocation').value = location;
            document.getElementById('editTestimonialText').value = text;
            document.getElementById('editTestimonialRating').value = rating;

            const editForm = document.getElementById('editTestimonialForm');
            editForm.action = editForm.action.replace('/0', '/' + id);

            new bootstrap.Modal(document.getElementById('editTestimonialModal')).show();
        }

        // About Value Edit
        function editAboutValue(id, icon, title, description) {
            document.getElementById('editAboutValueId').value = id;
            document.getElementById('editAboutValueIcon').value = icon;
            document.getElementById('editAboutValueTitle').value = title;
            document.getElementById('editAboutValueDescription').value = description;

            const editForm = document.getElementById('editAboutValueForm');
            editForm.action = editForm.action.replace('/0', '/' + id);

            new bootstrap.Modal(document.getElementById('editAboutValueModal')).show();
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
