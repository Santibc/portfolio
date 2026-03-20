@extends('landing_page.layout')

@push('styles')
<style>
    /* ===== HERO ===== */
    .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .hero-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        will-change: transform;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(13, 31, 13, 0.85) 0%, rgba(27, 67, 50, 0.6) 50%, rgba(13, 31, 13, 0.75) 100%);
        z-index: 2;
    }
    .hero-particles {
        position: absolute;
        inset: 0;
        z-index: 3;
    }
    .hero-content {
        position: relative;
        z-index: 4;
        max-width: 900px;
        padding: 0 40px;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: rgba(57, 255, 20, 0.1);
        border: 1px solid rgba(57, 255, 20, 0.2);
        border-radius: 50px;
        color: var(--manzer-green);
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 30px;
    }
    .hero-title {
        font-size: clamp(2.5rem, 6vw, 5rem);
        font-weight: 900;
        color: var(--manzer-white);
        line-height: 1.05;
        margin-bottom: 24px;
    }
    .hero-title .char {
        display: inline-block;
        will-change: transform;
    }
    .hero-description {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.7;
        margin-bottom: 40px;
        max-width: 650px;
    }
    .hero-buttons {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 60px;
    }
    .hero-stats {
        display: flex;
        gap: 50px;
        padding-top: 40px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .hero-stat-number {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 2.5rem;
        color: var(--manzer-green);
        line-height: 1;
    }
    .hero-stat-label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .hero-scroll {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 4;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.7rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        animation: scrollBounce 2s infinite;
    }
    @keyframes scrollBounce {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(10px); }
    }
    @media (max-width: 768px) {
        .hero-content { padding: 0 20px; }
        .hero-stats { flex-direction: column; gap: 20px; }
        .hero-buttons { flex-direction: column; }
        .hero-buttons .btn-manzer { text-align: center; justify-content: center; }
    }

    /* ===== SERVICES ===== */
    .services-section { background: linear-gradient(180deg, var(--manzer-dark) 0%, var(--manzer-forest) 100%); padding: 120px 0; }
    .services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 60px; }
    .service-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 40px 30px;
        transition: all 0.4s var(--manzer-transition);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        display: block;
        color: inherit;
    }
    .service-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--manzer-green); transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease; }
    .service-card:hover::before { transform: scaleX(1); }
    .service-card:hover { background: rgba(255, 255, 255, 0.06); transform: translateY(-5px); border-color: rgba(57, 255, 20, 0.15); }
    .service-icon { width: 64px; height: 64px; border-radius: 16px; background: rgba(57, 255, 20, 0.1); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--manzer-green); margin-bottom: 24px; transition: all 0.3s ease; }
    .service-card:hover .service-icon { background: var(--manzer-green); color: var(--manzer-dark); }
    .service-card h3 { font-size: 1.25rem; font-weight: 700; color: var(--manzer-white); margin-bottom: 12px; }
    .service-card p { font-size: 0.9rem; color: rgba(255, 255, 255, 0.5); line-height: 1.6; margin-bottom: 20px; }
    .service-link { display: inline-flex; align-items: center; gap: 6px; color: var(--manzer-green); font-weight: 600; font-size: 0.85rem; transition: gap 0.3s ease; }
    .service-card:hover .service-link { gap: 12px; }
    @media (max-width: 991px) { .services-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .services-grid { grid-template-columns: 1fr; } }

    /* ===== ABOUT ===== */
    .about-section { padding: 120px 0; background: var(--manzer-cream); }
    .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .about-image-wrapper { position: relative; }
    .about-image-wrapper img { border-radius: 20px; width: 100%; height: 500px; object-fit: cover; }
    .about-image-accent { position: absolute; bottom: -20px; right: -20px; width: 200px; height: 200px; background: var(--manzer-green); border-radius: 20px; z-index: -1; opacity: 0.2; }
    .about-experience-badge { position: absolute; bottom: 30px; left: -30px; background: var(--manzer-forest); color: var(--manzer-white); padding: 24px 30px; border-radius: 16px; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
    .about-experience-badge .number { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 2.5rem; color: var(--manzer-green); line-height: 1; }
    .about-experience-badge .label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
    .about-text { font-size: 1.05rem; color: var(--manzer-gray); line-height: 1.8; margin-bottom: 16px; }
    .about-stats-row { display: flex; gap: 40px; margin: 40px 0; }
    .about-stat .number { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 2rem; color: var(--manzer-forest); }
    .about-stat .label { font-size: 0.8rem; color: var(--manzer-gray); text-transform: uppercase; letter-spacing: 1px; }
    @media (max-width: 991px) { .about-grid { grid-template-columns: 1fr; gap: 40px; } .about-experience-badge { left: 20px; } }

    /* ===== GALLERY ===== */
    .gallery-section { padding: 120px 0; background: var(--manzer-white); }
    .gallery-filters { display: flex; gap: 10px; flex-wrap: wrap; margin: 40px 0; }
    .gallery-filter-btn { padding: 10px 24px; border: 2px solid #e5e7eb; border-radius: 50px; background: transparent; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease; color: var(--manzer-gray); }
    .gallery-filter-btn.active, .gallery-filter-btn:hover { background: var(--manzer-forest); border-color: var(--manzer-forest); color: var(--manzer-white); }
    .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .gallery-item { border-radius: 12px; overflow: hidden; cursor: pointer; position: relative; aspect-ratio: 1; }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--manzer-transition); }
    .gallery-item:hover img { transform: scale(1.08); }
    .gallery-item-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(13, 31, 13, 0.8) 0%, transparent 60%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end; padding: 20px; }
    .gallery-item:hover .gallery-item-overlay { opacity: 1; }
    .gallery-item-overlay span { color: var(--manzer-white); font-weight: 600; font-size: 0.85rem; }
    @media (max-width: 991px) { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 575px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }

    /* ===== VALUES HORIZONTAL SCROLL ===== */
    .values-section { background: var(--manzer-forest); overflow: hidden; }
    .values-wrapper { min-height: 100vh; display: flex; align-items: center; }
    .values-header { min-width: 400px; padding: 0 60px; }
    .values-track { display: flex; gap: 30px; padding: 40px 60px 40px 0; }
    .value-card { min-width: 350px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 50px 40px; flex-shrink: 0; transition: all 0.4s ease; }
    .value-card:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(57, 255, 20, 0.2); }
    .value-icon { width: 80px; height: 80px; border-radius: 20px; background: rgba(57, 255, 20, 0.1); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--manzer-green); margin-bottom: 30px; }
    .value-card h3 { font-size: 1.4rem; color: var(--manzer-white); margin-bottom: 16px; }
    .value-card p { font-size: 0.95rem; color: rgba(255, 255, 255, 0.5); line-height: 1.7; }
    @media (max-width: 768px) { .values-header { min-width: 100%; padding: 40px 20px; } .values-track { padding: 20px; } .value-card { min-width: 280px; padding: 30px 24px; } }

    /* ===== TESTIMONIALS ===== */
    .testimonials-section { padding: 120px 0; background: var(--manzer-cream); }
    .testimonial-card { background: var(--manzer-white); border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); }
    .testimonial-quote { font-size: 3rem; color: var(--manzer-green); opacity: 0.3; line-height: 1; margin-bottom: 16px; }
    .testimonial-text { font-size: 1.05rem; line-height: 1.8; color: #555; margin-bottom: 24px; font-style: italic; }
    .testimonial-stars { color: #f59e0b; margin-bottom: 16px; }
    .testimonial-author { font-family: 'Montserrat', sans-serif; font-weight: 700; color: var(--manzer-dark); }
    .testimonial-role { font-size: 0.85rem; color: var(--manzer-gray); }

    /* ===== CTA ===== */
    .cta-section { position: relative; padding: 120px 0; overflow: hidden; }
    .cta-bg { position: absolute; inset: 0; }
    .cta-bg img { width: 100%; height: 100%; object-fit: cover; }
    .cta-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(13, 31, 13, 0.92) 0%, rgba(27, 67, 50, 0.88) 100%); }
    .cta-content { position: relative; z-index: 2; text-align: center; max-width: 700px; margin: 0 auto; }
    .cta-phone { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 2.5rem; color: var(--manzer-green); margin: 20px 0 30px; display: block; }
    .cta-phone:hover { color: var(--manzer-white); }

    /* ===== BLOG PREVIEW ===== */
    .blog-section { padding: 120px 0; background: var(--manzer-white); }
    .blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 60px; }
    .blog-card { border-radius: 16px; overflow: hidden; background: var(--manzer-cream); transition: all 0.4s var(--manzer-transition); display: block; color: inherit; }
    .blog-card:hover { transform: translateY(-5px); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1); }
    .blog-card-image { height: 220px; overflow: hidden; }
    .blog-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--manzer-transition); }
    .blog-card:hover .blog-card-image img { transform: scale(1.05); }
    .blog-card-body { padding: 28px; }
    .blog-card-meta { display: flex; align-items: center; gap: 16px; font-size: 0.8rem; color: var(--manzer-gray); margin-bottom: 12px; }
    .blog-card-meta .category { background: rgba(57, 255, 20, 0.1); color: var(--manzer-forest); padding: 4px 12px; border-radius: 20px; font-weight: 600; }
    .blog-card h3 { font-size: 1.15rem; font-weight: 700; margin-bottom: 10px; color: var(--manzer-dark); line-height: 1.4; }
    .blog-card p { font-size: 0.9rem; color: var(--manzer-gray); line-height: 1.6; }
    @media (max-width: 991px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .blog-grid { grid-template-columns: 1fr; } }
    .text-center { text-align: center; }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="hero" id="hero">
    <div class="hero-bg">
        <img src="{{ $homeConfig && $homeConfig->hero_image_path ? asset($homeConfig->hero_image_path) : asset('images/hero-forest.jpg') }}"
             alt="Trabajos forestales Manzer Agroforestal" fetchpriority="high" id="heroBgImg">
    </div>
    <div class="hero-overlay"></div>
    <div id="heroParticles" class="hero-particles"></div>

    <div class="container hero-content">
        <div class="hero-badge" id="heroBadge" style="opacity:0; transform:translateY(20px);">
            <i class="bi bi-tree"></i>
            <span>Expertos Forestales en Lleida</span>
        </div>

        <h1 class="hero-title" id="heroTitle" data-splitting>{{ $homeConfig->hero_title ?? 'Trabajos Forestales de Maxima Seguridad' }}</h1>

        <p class="hero-description" id="heroDesc" style="opacity:0; transform:translateY(30px);">
            {{ $homeConfig->hero_subtitle ?? 'Tala y poda en altura, desbroces, prevencion de incendios y mantenimiento forestal con los mas altos estandares de seguridad y calidad.' }}
        </p>

        <div class="hero-buttons" id="heroButtons" style="opacity:0; transform:translateY(30px);">
            <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary">
                <i class="bi bi-envelope"></i> Solicitar Presupuesto
            </a>
            <a href="{{ route('servicios') }}" class="btn-manzer btn-manzer-outline">
                <i class="bi bi-arrow-right"></i> Nuestros Servicios
            </a>
        </div>

        <div class="hero-stats" id="heroStats" style="opacity:0; transform:translateY(30px);">
            @foreach($heroValues as $value)
            <div>
                <div class="hero-stat-number">{{ $value->title }}</div>
                <div class="hero-stat-label">{{ $value->icon_class }}</div>
            </div>
            @endforeach
            @if($heroValues->isEmpty())
            <div><div class="hero-stat-number">+50</div><div class="hero-stat-label">Proyectos completados</div></div>
            <div><div class="hero-stat-number">+5</div><div class="hero-stat-label">Anos de experiencia</div></div>
            <div><div class="hero-stat-number">100%</div><div class="hero-stat-label">Seguridad garantizada</div></div>
            @endif
        </div>
    </div>

    <div class="hero-scroll"><span>Scroll</span><i class="bi bi-chevron-down"></i></div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="services-section section" id="servicios">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Lo que hacemos</span>
            <h2 class="section-title" style="color: var(--manzer-white);">Nuestros Servicios</h2>
            <p class="section-subtitle" style="margin: 0 auto; color: rgba(255,255,255,0.5);">Soluciones profesionales en trabajos forestales y agroforestales con equipamiento de ultima generacion</p>
        </div>
        <div class="services-grid stagger-children">
            @forelse($services as $service)
            <a href="{{ $service->slug ? route('servicios.detalle', $service->slug) : route('servicios') }}" class="service-card tilt-card stagger-item">
                <div class="service-icon"><i class="{{ $service->icon_class }}"></i></div>
                <h3>{{ $service->title }}</h3>
                <p>{{ $service->short_description ?? $service->description }}</p>
                <span class="service-link">Ver mas <i class="bi bi-arrow-right"></i></span>
            </a>
            @empty
            @php $defaultServices = [
                ['icon' => 'bi bi-tree', 'title' => 'Tala en Altura', 'desc' => 'Sistemas de control de caida para evitar roturas y danos materiales.'],
                ['icon' => 'bi bi-scissors', 'title' => 'Poda en Altura', 'desc' => 'Mediante sistema de trepa donde cestas y elevadoras no pueden acceder.'],
                ['icon' => 'bi bi-hurricane', 'title' => 'Desbroces', 'desc' => 'Desbroces en taludes con sistemas de anclaje y linea de vida.'],
                ['icon' => 'bi bi-fire', 'title' => 'Prevencion de Incendios', 'desc' => 'Limpieza del sotobosque y creacion de cortafuegos.'],
                ['icon' => 'bi bi-signpost-2', 'title' => 'Trabajo en Carreteras', 'desc' => 'Limpieza de carreteras y cunetas para saneamiento y prevencion.'],
                ['icon' => 'bi bi-x-diamond', 'title' => 'Retirada de Arboles', 'desc' => 'Retirada de arboles muertos con riesgo de caida.'],
            ]; @endphp
            @foreach($defaultServices as $ds)
            <div class="service-card tilt-card stagger-item">
                <div class="service-icon"><i class="{{ $ds['icon'] }}"></i></div>
                <h3>{{ $ds['title'] }}</h3>
                <p>{{ $ds['desc'] }}</p>
                <span class="service-link">Ver mas <i class="bi bi-arrow-right"></i></span>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- ===== ABOUT ===== --}}
<section class="about-section section" id="nosotros">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-wrapper reveal-left">
                <img src="{{ $homeConfig && $homeConfig->about_image_path ? asset($homeConfig->about_image_path) : asset('images/about-forest.jpg') }}" alt="Manzer Agroforestal equipo" loading="lazy">
                <div class="about-image-accent"></div>
                <div class="about-experience-badge">
                    <span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $homeConfig->about_years_experience ?? 5 }}" data-purecounter-duration="2">0</span>
                    <div class="label">Anos de<br>Experiencia</div>
                </div>
            </div>
            <div class="about-content reveal-right">
                <span class="section-label">Sobre nosotros</span>
                <h2 class="section-title" style="color: var(--manzer-dark);">{{ $homeConfig->about_title ?? 'Nuestra Historia' }}</h2>
                <p class="about-text">{{ $homeConfig->about_lead ?? 'En Manzer Agroforestal llevamos varios anos realizando proyectos de mantenimiento y control de vegetacion y arbolado, poda y tala en altura, desbroces con sistema de linea, creacion de cortafuegos y todo tipo de trabajos forestales.' }}</p>
                <p class="about-text">{{ $homeConfig->about_description ?? 'Buscamos siempre la mayor seguridad y calidad para el cliente en todas nuestras actividades.' }}</p>
                <div class="about-stats-row">
                    <div class="about-stat">
                        <span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $homeConfig->about_happy_clients ?? 50 }}" data-purecounter-duration="2">0</span>
                        <div class="label">Clientes</div>
                    </div>
                    <div class="about-stat">
                        <span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $homeConfig->about_client_satisfaction ?? 100 }}" data-purecounter-duration="2" data-purecounter-suffix="%">0</span>
                        <div class="label">Satisfaccion</div>
                    </div>
                </div>
                <a href="{{ route('nosotros') }}" class="btn-manzer btn-manzer-dark">Conoce mas sobre nosotros <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

{{-- ===== GALLERY ===== --}}
<section class="gallery-section section" id="galeria">
    <div class="container">
        <div class="reveal">
            <span class="section-label">Nuestro trabajo</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Portfolio de Proyectos</h2>
        </div>
        <div class="gallery-filters reveal">
            <button class="gallery-filter-btn active" data-filter="all">Todos</button>
            <button class="gallery-filter-btn" data-filter="tala">Tala</button>
            <button class="gallery-filter-btn" data-filter="poda">Poda</button>
            <button class="gallery-filter-btn" data-filter="desbroce">Desbroces</button>
            <button class="gallery-filter-btn" data-filter="carreteras">Carreteras</button>
        </div>
        <div class="gallery-grid stagger-children" id="galleryGrid">
            @forelse($galleryImages as $image)
            <a href="{{ asset($image->image_path) }}" class="gallery-item stagger-item" data-category="{{ $image->category ?? 'all' }}" data-src="{{ asset($image->image_path) }}">
                <img src="{{ asset($image->image_path) }}" alt="{{ $image->alt_text ?? 'Proyecto forestal' }}" loading="lazy">
                <div class="gallery-item-overlay"><span>{{ $image->caption ?? $image->alt_text ?? '' }}</span></div>
            </a>
            @empty
            @for ($i = 1; $i <= 8; $i++)
            <div class="gallery-item stagger-item" data-category="all">
                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--manzer-forest),var(--manzer-dark));display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-tree" style="font-size:2rem;color:var(--manzer-green);opacity:0.3;"></i>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
</section>

{{-- ===== VALUES HORIZONTAL SCROLL ===== --}}
<section class="values-section" id="valores">
    <div class="values-wrapper" id="valuesWrapper">
        <div class="values-header">
            <span class="section-label">Por que elegirnos</span>
            <h2 class="section-title" style="color: var(--manzer-white); font-size: 2.5rem;">Nuestros<br>Valores</h2>
            <p style="color: rgba(255,255,255,0.5); margin-top: 16px; line-height: 1.7;">Cada proyecto es una oportunidad para demostrar nuestro compromiso con la excelencia y el medio ambiente.</p>
        </div>
        <div class="values-track" id="valuesTrack">
            <div class="value-card"><div class="value-icon"><i class="bi bi-shield-check"></i></div><h3>Seguridad</h3><p>Utilizamos los mejores sistemas de seguridad y control de caida. La proteccion de nuestro equipo y del entorno es nuestra prioridad absoluta.</p></div>
            <div class="value-card"><div class="value-icon"><i class="bi bi-award"></i></div><h3>Experiencia</h3><p>Anos de trayectoria avalan nuestro trabajo. Cada proyecto suma a nuestra experiencia para ofrecer soluciones cada vez mas eficientes.</p></div>
            <div class="value-card"><div class="value-icon"><i class="bi bi-tools"></i></div><h3>Equipamiento</h3><p>Contamos con maquinaria y equipos de ultima generacion. La tecnologia al servicio de resultados impecables.</p></div>
            <div class="value-card"><div class="value-icon"><i class="bi bi-globe-americas"></i></div><h3>Medio Ambiente</h3><p>Compromiso total con la sostenibilidad. Realizamos cada trabajo minimizando el impacto ambiental y protegiendo la biodiversidad.</p></div>
        </div>
    </div>
</section>

{{-- ===== TESTIMONIALS ===== --}}
@if($testimonials->count() > 0)
<section class="testimonials-section section" id="testimonios">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Testimonios</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Lo que dicen nuestros clientes</h2>
        </div>
        <div class="swiper testimonials-swiper reveal" style="margin-top: 60px; padding-bottom: 60px;">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"</div>
                        <div class="testimonial-stars">@for($i = 0; $i < $testimonial->rating; $i++)<i class="bi bi-star-fill"></i>@endfor</div>
                        <p class="testimonial-text">{{ $testimonial->testimonial }}</p>
                        <div class="testimonial-author">{{ $testimonial->client_name }}</div>
                        <div class="testimonial-role">{{ $testimonial->client_role }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif

{{-- ===== CTA ===== --}}
<section class="cta-section">
    <div class="cta-bg">
        <img src="{{ $homeConfig && $homeConfig->hero_image_path ? asset($homeConfig->hero_image_path) : asset('images/cta-forest.jpg') }}" alt="Contacto" loading="lazy" id="ctaBgImg">
    </div>
    <div class="cta-overlay"></div>
    <div class="cta-content reveal">
        <span class="section-label">Contactanos</span>
        <h2 class="section-title" style="color: var(--manzer-white);">Solicite su presupuesto sin compromiso</h2>
        <a href="tel:+34698989666" class="cta-phone">+34 698 98 96 66</a>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary"><i class="bi bi-envelope"></i> Escribenos</a>
            <a href="https://wa.me/34698989666" target="_blank" class="btn-manzer btn-manzer-outline"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</section>

{{-- ===== BLOG PREVIEW ===== --}}
@if($blogPosts->count() > 0)
<section class="blog-section section" id="blog">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Blog</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Ultimas Novedades</h2>
        </div>
        <div class="blog-grid stagger-children">
            @foreach($blogPosts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card stagger-item">
                @if($post->featured_image)
                <div class="blog-card-image"><img src="{{ asset($post->featured_image) }}" alt="{{ $post->featured_image_alt ?? $post->title }}" loading="lazy"></div>
                @endif
                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        @if($post->category)<span class="category">{{ $post->category->name }}</span>@endif
                        <span>{{ $post->formatted_date }}</span>
                        @if($post->reading_time)<span>{{ $post->reading_time }} min</span>@endif
                    </div>
                    <h3>{{ $post->title }}</h3>
                    <p>{{ Str::limit($post->excerpt ?? strip_tags($post->body), 120) }}</p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center reveal" style="margin-top: 40px;">
            <a href="{{ route('blog.index') }}" class="btn-manzer btn-manzer-dark">Ver todos los articulos <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tsparticles-slim@2.12.0/tsparticles.slim.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== SPLITTING.JS =====
    Splitting({ target: '#heroTitle', by: 'chars' });
    const heroChars = document.querySelectorAll('#heroTitle .char');
    gsap.fromTo(heroChars, { opacity: 0, y: 80, rotateX: -90 }, { opacity: 1, y: 0, rotateX: 0, duration: 0.8, stagger: 0.03, ease: 'back.out(1.7)', delay: 0.8 });

    // Hero sequence
    gsap.to('#heroBadge', { opacity: 1, y: 0, duration: 0.8, delay: 0.4, ease: 'power3.out' });
    gsap.to('#heroDesc', { opacity: 1, y: 0, duration: 0.8, delay: 1.6, ease: 'power3.out' });
    gsap.to('#heroButtons', { opacity: 1, y: 0, duration: 0.8, delay: 1.8, ease: 'power3.out' });
    gsap.to('#heroStats', { opacity: 1, y: 0, duration: 0.8, delay: 2.0, ease: 'power3.out' });

    // ===== HERO PARALLAX =====
    gsap.to('#heroBgImg', { yPercent: 30, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });

    // ===== TSPARTICLES =====
    if (typeof tsParticles !== 'undefined') {
        tsParticles.load('heroParticles', {
            fullScreen: { enable: false },
            particles: {
                number: { value: 15 },
                color: { value: ['#39ff14', '#2d8a2d', '#1b4332'] },
                shape: { type: 'circle' },
                opacity: { value: { min: 0.1, max: 0.4 } },
                size: { value: { min: 3, max: 8 } },
                move: { enable: true, speed: { min: 0.5, max: 1.5 }, direction: 'bottom', straight: false, outModes: { default: 'out' }, random: true },
                wobble: { enable: true, distance: 20, speed: 10 }
            },
            detectRetina: true,
        });
    }

    // ===== CTA PARALLAX =====
    const ctaImg = document.getElementById('ctaBgImg');
    if (ctaImg) {
        gsap.to(ctaImg, { yPercent: 20, ease: 'none', scrollTrigger: { trigger: '.cta-section', start: 'top bottom', end: 'bottom top', scrub: true } });
    }

    // ===== VALUES HORIZONTAL SCROLL =====
    const valuesTrack = document.getElementById('valuesTrack');
    if (valuesTrack && window.innerWidth > 768) {
        const scrollWidth = valuesTrack.scrollWidth;
        gsap.to(valuesTrack, {
            x: () => -(scrollWidth - window.innerWidth + 200),
            ease: 'none',
            scrollTrigger: { trigger: '.values-section', start: 'top top', end: () => `+=${scrollWidth}`, scrub: 1, pin: true, anticipatePin: 1, invalidateOnRefresh: true }
        });
    }

    // ===== TESTIMONIALS SWIPER =====
    new Swiper('.testimonials-swiper', {
        slidesPerView: 1, spaceBetween: 30,
        pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } },
        autoplay: { delay: 5000, disableOnInteraction: false }
    });

    // ===== GALLERY FILTERS =====
    document.querySelectorAll('.gallery-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.gallery-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.dataset.filter;
            document.querySelectorAll('.gallery-item').forEach(item => {
                const cat = item.dataset.category;
                if (filter === 'all' || cat === filter) {
                    gsap.to(item, { opacity: 1, scale: 1, duration: 0.4, display: 'block' });
                } else {
                    gsap.to(item, { opacity: 0, scale: 0.8, duration: 0.4, display: 'none' });
                }
            });
        });
    });

    // ===== LIGHTGALLERY =====
    const galleryGrid = document.getElementById('galleryGrid');
    if (galleryGrid && typeof lightGallery !== 'undefined') {
        lightGallery(galleryGrid, { selector: '.gallery-item[data-src]', speed: 500, backdropDuration: 300, download: false, counter: true, plugins: [lgZoom] });
    }

    // ===== ANIMATED COUNTERS =====
    function animateCounter(el) {
        const end = parseInt(el.getAttribute('data-purecounter-end')) || 0;
        const duration = (parseFloat(el.getAttribute('data-purecounter-duration')) || 2) * 1000;
        const suffix = el.getAttribute('data-purecounter-suffix') || '';
        const start = 0;
        const startTime = performance.now();
        function update(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(start + (end - start) * eased);
            el.textContent = current + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.purecounter').forEach(el => counterObserver.observe(el));
});
</script>
@endpush
