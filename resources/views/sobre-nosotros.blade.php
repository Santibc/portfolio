@extends('layouts.public')

@section('title', 'Sobre Nosotros - Betogether')
@section('title-suffix', ' | Sobre Nosotros')
@section('body-id', 'sobre-nosotros-body')

@section('content')
{{-- Hero Section --}}
<section class="about-hero">
    <div class="about-hero-content">
        <p class="about-subtitle">{{ $pagina->contenido['hero_subtitle'] ?? 'NUESTRA HISTORIA' }}</p>
        <h1 class="about-title">{{ $pagina->contenido['hero_title'] ?? 'Sobre Nosotros' }}</h1>
        <p class="about-description">
            {!! $pagina->contenido['hero_description'] ?? 'Conoce la historia detrás de <strong>Better Together</strong> y nuestra misión de transformar el emprendimiento en Colombia.' !!}
        </p>
    </div>
</section>

{{-- Mission Section --}}
<section class="about-section">
    <div class="about-container">
        <div class="about-grid">
            <div class="about-content">
                <h2><i class="bi bi-bullseye"></i> {{ $pagina->contenido['mision_titulo'] ?? 'Nuestra Misión' }}</h2>
                {!! $pagina->contenido['mision_texto'] ?? '<p>En <strong>Better Together</strong>, creemos que emprender no debe ser imposible, debe ser accesible. Nuestra misión es proporcionar a emprendedores y fundaciones las herramientas necesarias para crecer y prosperar en el mercado colombiano.</p><p>Nacimos con la convicción de que juntos somos más fuertes. Por eso, hemos creado un ecosistema completo que combina tecnología, comunidad y oportunidades de negocio para que nuestros miembros puedan enfocarse en lo que mejor saben hacer: vender.</p>' !!}
            </div>
            <div class="about-image">
                <img src="{{ asset($pagina->contenido['mision_imagen'] ?? 'images/imagen1.png') }}" alt="Nuestra Misión">
            </div>
        </div>
    </div>
</section>

{{-- Vision Section --}}
<section class="about-section about-section-alt">
    <div class="about-container">
        <div class="about-grid about-grid-reverse">
            <div class="about-content">
                <h2><i class="bi bi-eye"></i> {{ $pagina->contenido['vision_titulo'] ?? 'Nuestra Visión' }}</h2>
                {!! $pagina->contenido['vision_texto'] ?? '<p>Ser la plataforma líder en Colombia para el desarrollo y crecimiento de emprendimientos y fundaciones, creando un impacto positivo en la economía local y en la vida de miles de familias colombianas.</p><p>Visualizamos un futuro donde cada emprendedor tenga acceso a las mismas oportunidades que las grandes empresas: tecnología de punta, visibilidad en eventos comerciales y una comunidad que los respalde.</p>' !!}
            </div>
            <div class="about-image">
                <img src="{{ asset($pagina->contenido['vision_imagen'] ?? 'images/imagen2.png') }}" alt="Nuestra Visión">
            </div>
        </div>
    </div>
</section>

{{-- Values Section --}}
@if($valores->count() > 0)
<section class="about-values">
    <div class="about-container">
        <h2 class="values-title">{{ $pagina->contenido['valores_titulo'] ?? 'Nuestros Valores' }}</h2>
        <div class="values-grid">
            @foreach($valores as $valor)
            <div class="value-card">
                <div class="value-icon">
                    <i class="{{ $valor->icono }}"></i>
                </div>
                <h3>{{ $valor->titulo }}</h3>
                <p>{{ $valor->descripcion }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Stats Section --}}
@if($estadisticas->count() > 0 && ($pagina->contenido['estadisticas_visible'] ?? true))
<section class="about-stats">
    <div class="about-container">
        <div class="stats-grid" style="grid-template-columns: repeat({{ $estadisticas->count() }}, 1fr);">
            @foreach($estadisticas as $estadistica)
            <div class="stat-item">
                <span class="stat-number">{{ $estadistica->numero }}</span>
                <span class="stat-label">{{ $estadistica->etiqueta }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="about-cta">
    <div class="about-cta-content">
        <h2>{{ $pagina->contenido['cta_titulo'] ?? '¿Listo para ser parte de nuestra comunidad?' }}</h2>
        <p>{{ $pagina->contenido['cta_descripcion'] ?? 'Únete a miles de emprendedores que ya están creciendo con Better Together' }}</p>
        <div class="about-cta-buttons">
            <a href="{{ route('planes') }}" class="btn-primary">
                <i class="bi bi-rocket-takeoff"></i> {{ $pagina->contenido['cta_btn1_texto'] ?? 'Ver Planes' }}
            </a>
            <a href="{{ route('contacto') }}" class="btn-secondary">
                <i class="bi bi-chat-dots"></i> {{ $pagina->contenido['cta_btn2_texto'] ?? 'Contáctanos' }}
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* About Hero */
.about-hero {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 50%, #1c1c2e 100%);
    padding: 100px 20px 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.about-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.about-subtitle {
    color: #ff00c8;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 3px;
    margin-bottom: 15px;
    text-transform: uppercase;
}

.about-title {
    color: white;
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.about-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.about-description strong {
    color: white;
}

/* About Sections */
.about-section {
    padding: 80px 20px;
    background: #f8f9fa;
}

.about-section-alt {
    background: white;
}

.about-container {
    max-width: 1200px;
    margin: 0 auto;
}

.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.about-grid-reverse {
    direction: rtl;
}

.about-grid-reverse > * {
    direction: ltr;
}

.about-content h2 {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1c1c2e;
    margin-bottom: 20px;
}

.about-content h2 i {
    color: #ff00c8;
    font-size: 1.5rem;
}

.about-content p {
    color: #555;
    font-size: 1.05rem;
    line-height: 1.8;
    margin-bottom: 15px;
}

.about-image {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
}

.about-image img {
    width: 100%;
    height: auto;
    display: block;
}

/* Values Section */
.about-values {
    padding: 80px 20px;
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 100%);
}

.values-title {
    text-align: center;
    color: white;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 50px;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.value-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 35px 25px;
    text-align: center;
    transition: all 0.3s ease;
}

.value-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-10px);
}

.value-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.value-icon i {
    font-size: 1.8rem;
    color: white;
}

.value-card h3 {
    color: white;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.value-card p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Stats Section */
.about-stats {
    padding: 60px 20px;
    background: #f8f9fa;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    text-align: center;
}

.stat-item {
    padding: 30px;
}

.stat-number {
    display: block;
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
}

.stat-label {
    color: #666;
    font-size: 1rem;
    font-weight: 500;
}

/* CTA Section */
.about-cta {
    padding: 80px 20px;
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 100%);
    text-align: center;
}

.about-cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.about-cta-content h2 {
    color: white;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.about-cta-content p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.about-cta-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 200, 0.4);
    color: white;
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    color: white;
}

/* Responsive */
@media (max-width: 968px) {
    .about-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .about-grid-reverse {
        direction: ltr;
    }

    .about-title {
        font-size: 2rem;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .about-hero {
        padding: 80px 20px 60px;
    }

    .about-title {
        font-size: 1.8rem;
    }

    .about-content h2 {
        font-size: 1.5rem;
    }

    .values-grid {
        grid-template-columns: 1fr;
    }

    .stat-number {
        font-size: 2.2rem;
    }

    .about-cta-buttons {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush
