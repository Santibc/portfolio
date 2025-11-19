<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Gestión de Landing Page</h1>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
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
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab">
                            <i class="bi bi-house me-1"></i>Home
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services"
                            type="button" role="tab">
                            <i class="bi bi-briefcase me-1"></i>Servicios
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                            type="button" role="tab">
                            <i class="bi bi-envelope me-1"></i>Contacto
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about"
                            type="button" role="tab">
                            <i class="bi bi-info-circle me-1"></i>Nosotros
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout"
                            type="button" role="tab">
                            <i class="bi bi-layout-text-window me-1"></i>Layout
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo"
                            type="button" role="tab">
                            <i class="bi bi-search me-1"></i>SEO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing"
                            type="button" role="tab">
                            <i class="bi bi-calculator me-1"></i>Pricing
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="landingTabsContent">
                    <!-- Home Configuration -->
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración de la Página de Inicio</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.home.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Hero Section -->
                                    <h6 class="border-bottom pb-2 mb-3">Sección Hero</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título Principal</label>
                                                <input type="text" name="hero_title" class="form-control"
                                                    value="{{ $homeConfig->hero_title ?? 'CLEAN ME' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Subtítulo</label>
                                                <input type="text" name="hero_subtitle" class="form-control"
                                                    value="{{ $homeConfig->hero_subtitle ?? 'Top Quality Guaranteed' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descripción Hero</label>
                                        <textarea name="hero_description" class="form-control" rows="3">{{ $homeConfig->hero_description ?? 'At Clean Me, we believe that putting in a lot of hard work ensures the best and fastest service.' }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">URL Botón "Our Services"</label>
                                                <input type="text" name="hero_services_button_url"
                                                    class="form-control"
                                                    value="{{ $homeConfig->hero_services_button_url ?? '/servicios' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">URL Botón "Get Free Estimate"</label>
                                                <input type="text" name="hero_estimate_button_url"
                                                    class="form-control"
                                                    value="{{ $homeConfig->hero_estimate_button_url ?? '#contact' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Imagen Hero</label>
                                        <input type="file" name="hero_image" class="form-control"
                                            accept="image/*">
                                        <small class="form-text text-muted">Imagen principal de la sección hero</small>
                                        @if ($homeConfig && $homeConfig->hero_image_path)
                                            <div class="mt-2">
                                                <img src="{{ asset($homeConfig->hero_image_path) }}" alt="Hero Image"
                                                    style="max-height: 100px;">
                                                <small class="form-text text-muted d-block">Imagen actual</small>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- About Section -->
                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Sección "About" / "Nosotros"</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Título de la Sección</label>
                                        <input type="text" name="about_title" class="form-control"
                                            value="{{ $homeConfig->about_title ?? 'WE ARE CLEAN ME' }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Texto Destacado (Lead)</label>
                                        <textarea name="about_lead" class="form-control" rows="2">{{ $homeConfig->about_lead ?? 'Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.' }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="about_description" class="form-control" rows="3">{{ $homeConfig->about_description ?? 'We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep.' }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Años de Experiencia</label>
                                                <input type="number" name="about_years_experience"
                                                    class="form-control"
                                                    value="{{ $homeConfig->about_years_experience ?? 16 }}"
                                                    min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Clientes Felices</label>
                                                <input type="number" name="about_happy_clients" class="form-control"
                                                    value="{{ $homeConfig->about_happy_clients ?? 500 }}"
                                                    min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Satisfacción del Cliente (%)</label>
                                                <input type="number" name="about_client_satisfaction"
                                                    class="form-control"
                                                    value="{{ $homeConfig->about_client_satisfaction ?? 100 }}"
                                                    min="0" max="100" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Imagen About</label>
                                        <input type="file" name="about_image" class="form-control"
                                            accept="image/*">
                                        <small class="form-text text-muted">Imagen de la sección about</small>
                                        @if ($homeConfig && $homeConfig->about_image_path)
                                            <div class="mt-2">
                                                <img src="{{ asset($homeConfig->about_image_path) }}"
                                                    alt="About Image" style="max-height: 100px;">
                                                <small class="form-text text-muted d-block">Imagen actual</small>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Social Media Links -->
                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Enlaces de Redes Sociales</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Facebook URL</label>
                                                <input type="url" name="facebook_url" class="form-control"
                                                    value="{{ $homeConfig->facebook_url ?? '' }}"
                                                    placeholder="https://www.facebook.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Instagram URL</label>
                                                <input type="url" name="instagram_url" class="form-control"
                                                    value="{{ $homeConfig->instagram_url ?? '' }}"
                                                    placeholder="https://www.instagram.com/...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Indeed URL</label>
                                                <input type="url" name="linkedin_url" class="form-control"
                                                    value="{{ $homeConfig->linkedin_url ?? '' }}"
                                                    placeholder="https://www.indeed.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">TikTok URL</label>
                                                <input type="url" name="youtube_url" class="form-control"
                                                    value="{{ $homeConfig->youtube_url ?? '' }}"
                                                    placeholder="https://www.tiktok.com/...">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Guardar Configuración del Home
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Hero Values Management -->
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Hero Values (Valores/Badges del Hero)</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addHeroValueModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Hero Value
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- Bootstrap Icons Instructions -->
                                <div class="alert alert-info mb-4">
                                    <h6><i class="bi bi-info-circle me-1"></i>Cómo agregar iconos de Bootstrap Icons:
                                    </h6>
                                    <ol class="mb-2">
                                        <li>Visita <a href="https://icons.getbootstrap.com/" target="_blank"
                                                class="fw-bold">https://icons.getbootstrap.com/</a></li>
                                        <li>Busca el icono que deseas usar (ej: "shield", "lightning", "award")</li>
                                        <li>Haz clic en el icono para ver los detalles</li>
                                        <li>Copia la clase del icono que aparece como <code>bi
                                                bi-nombre-del-icono</code></li>
                                        <li>Pega la clase completa en el campo "Icono" del formulario</li>
                                    </ol>
                                    <p class="mb-0"><strong>Ejemplos de iconos populares:</strong></p>
                                    <ul class="mb-0">
                                        <li><code>bi bi-shield-check</code> - Escudo con check (Confianza/Seguridad)
                                        </li>
                                        <li><code>bi bi-lightning-charge</code> - Rayo (Velocidad/Rapidez)</li>
                                        <li><code>bi bi-award</code> - Medalla (Profesionalismo/Calidad)</li>
                                        <li><code>bi bi-tree</code> - Árbol (Ecológico)</li>
                                        <li><code>bi bi-clock</code> - Reloj (Puntualidad/Horarios)</li>
                                        <li><code>bi bi-people</code> - Personas (Equipo)</li>
                                    </ul>
                                </div>

                                @if ($heroValues->count() > 0)
                                    <div class="row">
                                        @foreach ($heroValues as $heroValue)
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <div class="card h-100 text-center">
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <i
                                                                class="{{ $heroValue->icon_class }} fa-3x text-primary"></i>
                                                        </div>
                                                        <h6 class="card-title">{{ $heroValue->title }}</h6>
                                                        <small class="text-muted">Orden:
                                                            {{ $heroValue->order }}</small>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm"
                                                            onclick="editHeroValue({{ $heroValue->id }}, '{{ $heroValue->icon_class }}', '{{ $heroValue->title }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.landing.hero-values.delete', $heroValue->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('¿Estás seguro de eliminar este hero value?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No hay hero values registrados. Agrega el primero usando el
                                        botón de arriba.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Testimonials Management -->
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Testimonios de Clientes</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addTestimonialModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Testimonio
                                </button>
                            </div>
                            <div class="card-body">
                                @if ($testimonials->count() > 0)
                                    <div class="row">
                                        @foreach ($testimonials as $testimonial)
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <h6 class="mb-0">{{ $testimonial->client_name }}</h6>
                                                            @if ($testimonial->client_role)
                                                                <small
                                                                    class="text-muted">{{ $testimonial->client_role }}</small>
                                                            @endif
                                                            <div class="mt-1">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <i
                                                                        class="bi bi-star{{ $i <= $testimonial->rating ? '-fill' : '' }} text-warning"></i>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <p class="card-text">
                                                            {{ Str::limit($testimonial->testimonial, 150) }}</p>
                                                        <small class="text-muted">Orden:
                                                            {{ $testimonial->order }}</small>
                                                    </div>
                                                    <div class="card-footer">
                                                        <button class="btn btn-warning btn-sm"
                                                            onclick="editTestimonial({{ $testimonial->id }}, '{{ $testimonial->client_name }}', '{{ $testimonial->client_role }}', '{{ addslashes($testimonial->testimonial) }}', {{ $testimonial->rating }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.landing.testimonials.delete', $testimonial->id) }}"
                                                            method="POST" class="d-inline">
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
                                    <p class="text-muted">No hay testimonios registrados. Agrega el primero usando el
                                        botón de arriba.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Servicios -->
                    <div class="tab-pane fade" id="services" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Servicios</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addServiceModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Servicio
                                </button>
                            </div>
                            <div class="card-body">
                                @if ($services->count() > 0)
                                    <div class="row">
                                        @foreach ($services as $service)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="text-center mb-3">
                                                            <i
                                                                class="{{ $service->icon_class }} fa-2x text-primary"></i>
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
                                                        <form
                                                            action="{{ route('admin.landing.services.delete', $service->id) }}"
                                                            method="POST" class="d-inline">
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
                                                    value="{{ $contactInfo->address ?? 'A108 Adam Street, New York, NY 535022' }}"
                                                    required>
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
                                                <input type="email" name="receive_messages_email"
                                                    class="form-control"
                                                    value="{{ $contactInfo->receive_messages_email ?? 'admin@example.com' }}"
                                                    required>
                                                <small class="form-text text-muted">Los mensajes del formulario se
                                                    enviarán a este email.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Código Embed de Google Maps</label>

                                        <!-- Instrucciones paso a paso -->
                                        <div class="alert alert-info">
                                            <h6><i class="bi bi-info-circle me-1"></i>Pasos para obtener el código
                                                embed:</h6>
                                            <ol class="mb-0">
                                                <li>Ve a <strong>maps.google.com</strong></li>
                                                <li>Busca tu dirección exacta</li>
                                                <li>Haz clic en <strong>"Compartir"</strong></li>
                                                <li>Selecciona <strong>"Incorporar un mapa"</strong></li>
                                                <li>Elige el tamaño (recomendado: Mediano o Grande)</li>
                                                <li>Copia todo el código <code>&lt;iframe&gt;...&lt;/iframe&gt;</code>
                                                </li>
                                                <li>Pégalo en el campo de abajo</li>
                                            </ol>
                                        </div>

                                        <textarea name="google_maps_embed" class="form-control" rows="4"
                                            placeholder="<iframe src=&quot;https://www.google.com/maps/embed?pb=...&quot; width=&quot;600&quot; height=&quot;450&quot; style=&quot;border:0;&quot; allowfullscreen=&quot;&quot; loading=&quot;lazy&quot; referrerpolicy=&quot;no-referrer-when-downgrade&quot;></iframe>">{{ $contactInfo->google_maps_embed ?? '' }}</textarea>
                                        <small class="form-text text-muted">Pega aquí el código iframe completo de
                                            Google Maps (incluyendo las etiquetas &lt;iframe&gt; de apertura y
                                            cierre).</small>
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
                                <form action="{{ route('admin.landing.about.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título de la Página</label>
                                                <input type="text" name="page_title" class="form-control"
                                                    value="{{ $about->page_title ?? 'Acerca de Nosotros' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Subtítulo de la Página</label>
                                                <input type="text" name="page_subtitle" class="form-control"
                                                    value="{{ $about->page_subtitle ?? 'Learn more about Clean Me' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título de Propósito</label>
                                                <input type="text" name="purpose_title" class="form-control"
                                                    value="{{ $about->purpose_title ?? 'Nuestro Propósito' }}"
                                                    required>
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
                                                <input type="file" name="main_image" class="form-control"
                                                    accept="image/*">
                                                <small class="form-text text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>Recomendado: 1280x854 píxeles
                                                    (proporción 3:2) para mejor visualización
                                                </small>
                                                @if ($about && $about->main_image_path)
                                                    <small class="form-text text-muted mt-1">
                                                        Imagen actual: <a href="{{ asset($about->main_image_path) }}"
                                                            target="_blank">Ver imagen</a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contenido de la Visión</label>
                                        <textarea name="vision_content" class="form-control" rows="4" required>{{ $about->vision_content ?? 'Definir la visión de la empresa...' }}</textarea>
                                    </div>

                                    <!-- Estadísticas -->
                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Estadísticas</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Años de Experiencia</label>
                                                <input type="number" name="stats_years_experience"
                                                    class="form-control"
                                                    value="{{ $about->stats_years_experience ?? 16 }}" min="0"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Clientes Felices</label>
                                                <input type="number" name="stats_happy_clients" class="form-control"
                                                    value="{{ $about->stats_happy_clients ?? 500 }}" min="0"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Satisfacción del Cliente (%)</label>
                                                <input type="number" name="stats_client_satisfaction"
                                                    class="form-control"
                                                    value="{{ $about->stats_client_satisfaction ?? 100 }}"
                                                    min="0" max="100" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Valor 1 -->
                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Valores de la Empresa</h6>
                                    <div class="card mb-3">
                                        <div class="card-header">Valor 1</div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Icono (Bootstrap Icons)</label>
                                                        <input type="text" name="value1_icon" class="form-control"
                                                            value="{{ $about->value1_icon ?? 'bi bi-award' }}"
                                                            required>
                                                        <small class="text-muted">Ejemplo: bi bi-award, bi bi-star,
                                                            etc.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título</label>
                                                        <input type="text" name="value1_title"
                                                            class="form-control"
                                                            value="{{ $about->value1_title ?? 'Quality Assurance' }}"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <textarea name="value1_description" class="form-control" rows="2">{{ $about->value1_description ?? 'We use eco-friendly cleaning products and employ highly trained professionals to deliver exceptional results every time.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Valor 2 -->
                                    <div class="card mb-3">
                                        <div class="card-header">Valor 2</div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Icono (Bootstrap Icons)</label>
                                                        <input type="text" name="value2_icon" class="form-control"
                                                            value="{{ $about->value2_icon ?? 'bi bi-people' }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título</label>
                                                        <input type="text" name="value2_title"
                                                            class="form-control"
                                                            value="{{ $about->value2_title ?? 'Customer Focus' }}"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <textarea name="value2_description" class="form-control" rows="2">{{ $about->value2_description ?? 'Your satisfaction is our priority. We tailor our services to meet your specific needs and exceed your expectations.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Valor 3 -->
                                    <div class="card mb-3">
                                        <div class="card-header">Valor 3</div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Icono (Bootstrap Icons)</label>
                                                        <input type="text" name="value3_icon" class="form-control"
                                                            value="{{ $about->value3_icon ?? 'bi bi-clock-history' }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título</label>
                                                        <input type="text" name="value3_title"
                                                            class="form-control"
                                                            value="{{ $about->value3_title ?? 'Reliability' }}"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <textarea name="value3_description" class="form-control" rows="2">{{ $about->value3_description ?? 'Since 2009, we\'ve built our reputation on consistent, dependable service that you can count on.' }}</textarea>
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

                    <!-- Layout -->
                    <div class="tab-pane fade" id="layout" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración del Layout</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.layout.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Título del Sitio</label>
                                                <input type="text" name="site_title" class="form-control"
                                                    value="{{ $layoutConfig->site_title ?? 'Clean Me' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email del Top Bar</label>
                                                <input type="email" name="topbar_email" class="form-control"
                                                    value="{{ $layoutConfig->topbar_email ?? 'info@example.com' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono del Top Bar</label>
                                                <input type="text" name="topbar_phone" class="form-control"
                                                    value="{{ $layoutConfig->topbar_phone ?? '+1 5589 55488 55' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Empresa para Copyright</label>
                                                <input type="text" name="copyright_company" class="form-control"
                                                    value="{{ $layoutConfig->copyright_company ?? 'Clean Me' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3">Redes Sociales</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">TikTok URL</label>
                                                <input type="url" name="twitter_url" class="form-control"
                                                    value="{{ $layoutConfig->twitter_url ?? '' }}"
                                                    placeholder="https://TikTok.com/usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Facebook URL</label>
                                                <input type="url" name="facebook_url" class="form-control"
                                                    value="{{ $layoutConfig->facebook_url ?? '' }}"
                                                    placeholder="https://facebook.com/usuario">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Instagram URL</label>
                                                <input type="url" name="instagram_url" class="form-control"
                                                    value="{{ $layoutConfig->instagram_url ?? '' }}"
                                                    placeholder="https://instagram.com/usuario">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Indeed URL</label>
                                                <input type="url" name="linkedin_url" class="form-control"
                                                    value="{{ $layoutConfig->linkedin_url ?? '' }}"
                                                    placeholder="https://Indeed.com/in/usuario">
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3">Información del Footer</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Dirección del Footer</label>
                                                <input type="text" name="footer_address" class="form-control"
                                                    value="{{ $layoutConfig->footer_address ?? 'A108 Adam Street' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Ciudad del Footer</label>
                                                <input type="text" name="footer_city" class="form-control"
                                                    value="{{ $layoutConfig->footer_city ?? 'New York, NY 535022' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono del Footer</label>
                                                <input type="text" name="footer_phone" class="form-control"
                                                    value="{{ $layoutConfig->footer_phone ?? '+1 5589 55488 55' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email del Footer</label>
                                                <input type="email" name="footer_email" class="form-control"
                                                    value="{{ $layoutConfig->footer_email ?? 'info@example.com' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descripción del Footer</label>
                                        <textarea name="footer_description" class="form-control" rows="3"
                                            placeholder="Descripción que aparece en el footer del sitio">{{ $layoutConfig->footer_description ?? 'Excellence and professionalism in residential and commercial cleaning services.' }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Logo del Footer</label>
                                        <input type="file" name="footer_logo" class="form-control"
                                            accept="image/*">
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle me-1"></i>Logo que aparece en el footer del
                                            sitio. Si no se especifica, se usa el logo principal.
                                        </small>
                                        @if ($layoutConfig && $layoutConfig->footer_logo_path)
                                            <div class="mt-2">
                                                <img src="{{ asset($layoutConfig->footer_logo_path) }}"
                                                    alt="Footer Logo" style="max-height: 60px;">
                                                <small class="form-text text-muted d-block">Logo actual del
                                                    footer</small>
                                            </div>
                                        @endif
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
                        @if ($seoConfigs->count() > 0)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-list-check me-2 text-success"></i>
                                            SEOs Configurados
                                        </h5>
                                        <span class="badge bg-primary">{{ $seoConfigs->count() }}
                                            configuraciones</span>
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
                                                @foreach ($seoConfigs as $seoConfig)
                                                    <tr class="align-middle">
                                                        <td class="fw-bold text-primary">
                                                            <i class="bi bi-page-forward me-2"></i>
                                                            {{ $seoConfig->page->name }}
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate" style="max-width: 250px;"
                                                                title="{{ $seoConfig->meta_title }}">
                                                                {{ $seoConfig->meta_title ?: 'No definido' }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if ($seoConfig->focus_keyword)
                                                                <span
                                                                    class="badge bg-info">{{ $seoConfig->focus_keyword }}</span>
                                                            @else
                                                                <span class="text-muted">No definida</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <code class="text-dark">{{ $seoConfig->robots }}</code>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($seoConfig->is_active)
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
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm"
                                                                    onclick="editSeo({{ $seoConfig->page->id }})"
                                                                    title="Editar">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <form
                                                                    action="{{ route('admin.landing.seo.delete', $seoConfig->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm"
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
                                    No hay configuraciones SEO creadas aún. Utiliza el formulario de abajo para crear la
                                    primera configuración.
                                </div>
                            </div>
                        @endif

                        <!-- Card 2: Formulario de Configuración -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-gradient"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Configurar SEO
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.seo.update') }}" method="POST"
                                    id="seoForm">
                                    @csrf

                                    <!-- Selector de Página -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <select name="page_id" id="pageSelector" class="form-select"
                                                    required>
                                                    <option value="">Seleccionar página...</option>
                                                    @foreach ($pages as $page)
                                                        <option value="{{ $page->id }}"
                                                            {{ old('page_id') == $page->id ? 'selected' : '' }}>
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
                                                <input type="text" name="meta_title" class="form-control"
                                                    id="metaTitle" maxlength="150" value="{{ old('meta_title') }}"
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
                                                <input type="text" name="focus_keyword" class="form-control"
                                                    id="focusKeyword" maxlength="100"
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
                                                <input type="text" name="meta_keywords" class="form-control"
                                                    id="metaKeywords" maxlength="500"
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
                                                <input type="url" name="canonical_url" class="form-control"
                                                    id="canonicalUrl" maxlength="500"
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
                                                    <option value="index,follow"
                                                        {{ old('robots') == 'index,follow' ? 'selected' : '' }}>Index,
                                                        Follow (Recomendado)</option>
                                                    <option value="noindex,follow"
                                                        {{ old('robots') == 'noindex,follow' ? 'selected' : '' }}>No
                                                        Index, Follow</option>
                                                    <option value="index,nofollow"
                                                        {{ old('robots') == 'index,nofollow' ? 'selected' : '' }}>
                                                        Index, No Follow</option>
                                                    <option value="noindex,nofollow"
                                                        {{ old('robots') == 'noindex,nofollow' ? 'selected' : '' }}>No
                                                        Index, No Follow</option>
                                                </select>
                                                <label for="robotsSelect">
                                                    <i class="bi bi-robot me-1"></i>Robots Meta Tag *
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                                    id="seoActiveSwitch">
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

                        <!-- Base Pricing Configuration -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-calculator me-2"></i>Configuración de
                                    Precios Base</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing.pricing.update-base') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-people-fill me-1"></i>Precio por Limpiador
                                                ($/limpiador)
                                            </label>
                                            <input type="number" step="0.01" name="cleaner_price"
                                                class="form-control"
                                                value="{{ $pricingConfig->cleaner_price ?? 30 }}" min="0"
                                                required>
                                            <small class="text-muted">Precio que se multiplica por el número de
                                                limpiadores</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-clock-fill me-1"></i>Precio por Hora ($/hora)
                                            </label>
                                            <input type="number" step="0.01" name="hour_price"
                                                class="form-control" value="{{ $pricingConfig->hour_price ?? 30 }}"
                                                min="0" required>
                                            <small class="text-muted">Se multiplica también por número de
                                                limpiadores</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-house-check me-1"></i>Precio Servicio Normal ($)
                                            </label>
                                            <input type="number" step="0.01" name="normal_service_price"
                                                class="form-control"
                                                value="{{ $pricingConfig->normal_service_price ?? 0 }}"
                                                min="0" required>
                                            <small class="text-muted">Precio adicional por seleccionar limpieza
                                                normal</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-stars me-1"></i>Precio Servicio Profundo ($)
                                            </label>
                                            <input type="number" step="0.01" name="deep_service_price"
                                                class="form-control"
                                                value="{{ $pricingConfig->deep_service_price ?? 50 }}"
                                                min="0" required>
                                            <small class="text-muted">Precio adicional por seleccionar limpieza
                                                profunda</small>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="bi bi-info-circle me-1"></i>Fórmula de Cálculo:</strong><br>
                                        <code>Precio Total = (Habitaciones) + (Limpiadores × Horas × Precio Base) +
                                            (Tipo de Servicio) + (Extras)</code>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle me-1"></i>Guardar Configuración Base
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Room Type Prices -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-house-door me-2"></i>Precios por Tipo de
                                    Habitación</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo de Habitación</th>
                                                <th>Precio ($)</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roomTypePrices as $roomType)
                                                <tr>
                                                    <form
                                                        action="{{ route('admin.landing.room-type-prices.update', $roomType->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <td>
                                                            @if ($roomType->room_type == 'bathroom')
                                                                <i class="bi bi-water me-2"></i>Baño
                                                            @elseif($roomType->room_type == 'bedroom')
                                                                <i class="bi bi-door-closed me-2"></i>Habitación
                                                            @elseif($roomType->room_type == 'kitchen')
                                                                <i class="bi bi-egg-fried me-2"></i>Cocina
                                                            @else
                                                                <i class="bi bi-plus-circle me-2"></i>Otro
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="price"
                                                                class="form-control form-control-sm"
                                                                value="{{ $roomType->price }}" min="0"
                                                                style="width: 120px;" required>
                                                        </td>
                                                        <td>
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check"></i> Guardar
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

                        <!-- Service Extras -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-plus-square me-2"></i>Servicios Extras
                                </h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addServiceExtraModal">
                                    <i class="bi bi-plus-lg me-1"></i>Agregar Extra
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-info-circle me-1"></i>Iconos de Bootstrap Icons:</h6>
                                    <p class="mb-0">Visita <a href="https://icons.getbootstrap.com/"
                                            target="_blank" class="fw-bold">https://icons.getbootstrap.com/</a> para
                                        buscar iconos. Copia la clase completa (ej: <code>bi bi-thermometer-half</code>)
                                    </p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Icono</th>
                                                <th>Nombre</th>
                                                <th>Precio ($)</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($serviceExtras as $extra)
                                                <tr>
                                                    <td><i class="{{ $extra->icon_class }} fa-2x"></i></td>
                                                    <td>{{ $extra->name }}</td>
                                                    <td>${{ number_format($extra->price, 2) }}</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm"
                                                            onclick="editServiceExtra({{ $extra->id }}, '{{ addslashes($extra->name) }}', '{{ $extra->icon_class }}', {{ $extra->price }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.landing.service-extras.delete', $extra->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('¿Eliminar este extra?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Cleaner Hour Prices - YA NO SE USA (Precio simplificado arriba) -->
                        <!-- <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>Precios por Limpiadores y Horas (Obsoleto)</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    Esta sección ya no se usa. Ahora los precios se configuran en "Configuración de Precios Base" arriba.
                                </div>
                            </div>
                        </div> -->

                    </div>
                    <!-- End Pricing Tab -->


                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    @include('admin.landing.modals.service')

    <!-- Add Hero Value Modal -->
    <div class="modal fade" id="addHeroValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Hero Value</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.hero-values.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (Bootstrap Icons) *</label>
                            <input type="text" name="icon_class" class="form-control" required
                                placeholder="bi bi-shield-check">
                            <small class="form-text text-muted">Visita <a href="https://icons.getbootstrap.com/"
                                    target="_blank">Bootstrap Icons</a> para buscar iconos</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" name="title" class="form-control" required
                                placeholder="Trusted & Insured">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Hero Value Modal -->
    <div class="modal fade" id="editHeroValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Hero Value</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHeroValueForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editHeroValueId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Icono (Bootstrap Icons) *</label>
                            <input type="text" id="editHeroValueIconClass" name="icon_class"
                                class="form-control" required>
                            <small class="form-text text-muted">Visita <a href="https://icons.getbootstrap.com/"
                                    target="_blank">Bootstrap Icons</a> para buscar iconos</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" id="editHeroValueTitle" name="title" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Testimonial Modal -->
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
                            <label class="form-label">Nombre del Cliente *</label>
                            <input type="text" name="client_name" class="form-control" required
                                placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol/Posición</label>
                            <input type="text" name="client_role" class="form-control"
                                placeholder="CEO, Empresa XYZ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonio *</label>
                            <textarea name="testimonial" class="form-control" rows="4" required
                                placeholder="Escriba el testimonio del cliente aquí..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Calificación (estrellas) *</label>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Testimonial Modal -->
    <div class="modal fade" id="editTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Testimonio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editTestimonialForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTestimonialId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Cliente *</label>
                            <input type="text" id="editTestimonialClientName" name="client_name"
                                class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol/Posición</label>
                            <input type="text" id="editTestimonialClientRole" name="client_role"
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonio *</label>
                            <textarea id="editTestimonialText" name="testimonial" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Calificación (estrellas) *</label>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Service Extra Modal -->
    <div class="modal fade" id="addServiceExtraModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Servicio Extra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.landing.service-extras.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Clean Oven">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon_class" class="form-control"
                                placeholder="bi bi-thermometer-half">
                            <small class="form-text text-muted">Opcional. Visita <a
                                    href="https://icons.getbootstrap.com/" target="_blank">Bootstrap
                                    Icons</a></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="price" class="form-control" required
                                placeholder="50.00" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Extra Modal -->
    <div class="modal fade" id="editServiceExtraModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Servicio Extra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editServiceExtraForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editServiceExtraId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" id="editServiceExtraName" name="name" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icono (Bootstrap Icons)</label>
                            <input type="text" id="editServiceExtraIconClass" name="icon_class"
                                class="form-control">
                            <small class="form-text text-muted">Opcional. Visita <a
                                    href="https://icons.getbootstrap.com/" target="_blank">Bootstrap
                                    Icons</a></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" id="editServiceExtraPrice" name="price"
                                class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

            // Hero Values functions
            function editHeroValue(id, iconClass, title) {
                document.getElementById('editHeroValueId').value = id;
                document.getElementById('editHeroValueIconClass').value = iconClass;
                document.getElementById('editHeroValueTitle').value = title;

                const editForm = document.getElementById('editHeroValueForm');
                editForm.action = '{{ url('admin/landing/hero-values') }}/' + id;

                new bootstrap.Modal(document.getElementById('editHeroValueModal')).show();
            }

            // Testimonials functions
            function editTestimonial(id, clientName, clientRole, testimonial, rating) {
                document.getElementById('editTestimonialId').value = id;
                document.getElementById('editTestimonialClientName').value = clientName;
                document.getElementById('editTestimonialClientRole').value = clientRole || '';
                document.getElementById('editTestimonialText').value = testimonial;
                document.getElementById('editTestimonialRating').value = rating;

                const editForm = document.getElementById('editTestimonialForm');
                editForm.action = '{{ url('admin/landing/testimonials') }}/' + id;

                new bootstrap.Modal(document.getElementById('editTestimonialModal')).show();
            }

            // Service Extras functions
            function editServiceExtra(id, name, iconClass, price) {
                document.getElementById('editServiceExtraId').value = id;
                document.getElementById('editServiceExtraName').value = name;
                document.getElementById('editServiceExtraIconClass').value = iconClass || '';
                document.getElementById('editServiceExtraPrice').value = price;

                const editForm = document.getElementById('editServiceExtraForm');
                editForm.action = '{{ url('admin/landing/service-extras') }}/' + id;

                new bootstrap.Modal(document.getElementById('editServiceExtraModal')).show();
            }
        </script>
    @endpush
</x-app-layout>
