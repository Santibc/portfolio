@extends('landing_page.layout')

@push('styles')
<style>
    /* ===== HERO BANNER ===== */
    .services-hero {
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .services-hero-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .services-hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        will-change: transform;
    }
    .services-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(13, 31, 13, 0.88) 0%, rgba(27, 67, 50, 0.7) 50%, rgba(13, 31, 13, 0.92) 100%);
        z-index: 2;
    }
    .services-hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        padding: 0 20px;
    }
    .services-hero-content .section-label {
        margin-bottom: 20px;
    }
    .services-hero-content h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: var(--manzer-white);
        margin-bottom: 20px;
        line-height: 1.1;
    }
    .services-hero-content p {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.6);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ===== SERVICES GRID ===== */
    .services-page-section {
        padding: 100px 0;
        background: linear-gradient(180deg, var(--manzer-dark) 0%, var(--manzer-forest) 50%, var(--manzer-dark) 100%);
    }
    .services-page-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 36px;
        margin-top: 20px;
    }
    .sp-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 24px;
        overflow: hidden;
        transition: all 0.5s var(--manzer-transition);
        position: relative;
        display: flex;
        flex-direction: column;
        color: inherit;
    }
    .sp-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--manzer-green);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s ease;
        z-index: 2;
    }
    .sp-card:hover::before {
        transform: scaleX(1);
    }
    .sp-card:hover {
        transform: translateY(-8px);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(57, 255, 20, 0.2);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(57, 255, 20, 0.05);
    }
    .sp-card-image {
        height: 220px;
        overflow: hidden;
        position: relative;
    }
    .sp-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s var(--manzer-transition);
    }
    .sp-card:hover .sp-card-image img {
        transform: scale(1.08);
    }
    .sp-card-image-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(13, 31, 13, 0.6) 100%);
    }
    .sp-card-body {
        padding: 36px 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .sp-card-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: rgba(57, 255, 20, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: var(--manzer-green);
        margin-bottom: 24px;
        transition: all 0.4s ease;
    }
    .sp-card:hover .sp-card-icon {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }
    .sp-card h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--manzer-white);
        margin-bottom: 14px;
    }
    .sp-card p {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.5);
        line-height: 1.7;
        margin-bottom: 24px;
        flex: 1;
    }
    .sp-card-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--manzer-green);
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        transition: gap 0.3s ease;
    }
    .sp-card:hover .sp-card-link {
        gap: 14px;
    }

    /* ===== CTA SECTION ===== */
    .services-cta {
        position: relative;
        padding: 120px 0;
        background: var(--manzer-forest);
        overflow: hidden;
    }
    .services-cta::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: rgba(57, 255, 20, 0.04);
        pointer-events: none;
    }
    .services-cta-content {
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }
    .services-cta-phone {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        color: var(--manzer-green);
        margin: 24px 0 36px;
        display: block;
        transition: color 0.3s ease;
    }
    .services-cta-phone:hover {
        color: var(--manzer-white);
    }

    @media (max-width: 991px) {
        .services-page-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575px) {
        .services-page-grid { grid-template-columns: 1fr; }
        .services-hero { min-height: 50vh; }
    }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="services-hero">
    <div class="services-hero-bg">
        <img src="{{ asset('images/hero-forest.jpg') }}" alt="Servicios forestales" id="servicesHeroBg">
    </div>
    <div class="services-hero-overlay"></div>
    <div class="services-hero-content">
        <span class="section-label reveal">Lo que hacemos</span>
        <h1 class="reveal">Nuestros Servicios</h1>
        <p class="reveal">Soluciones profesionales en trabajos forestales y agroforestales con los mas altos estandares de seguridad y calidad</p>
    </div>
</section>

{{-- ===== SERVICES GRID ===== --}}
<section class="services-page-section section">
    <div class="container">
        @if($services && $services->count() > 0)
        <div class="services-page-grid stagger-children">
            @foreach($services as $service)
            <a href="{{ $service->slug ? route('servicios.detalle', $service->slug) : '#' }}" class="sp-card tilt-card stagger-item">
                @if($service->image_path)
                <div class="sp-card-image">
                    <img src="{{ asset($service->image_path) }}" alt="{{ $service->title }}" loading="lazy">
                    <div class="sp-card-image-overlay"></div>
                </div>
                @endif
                <div class="sp-card-body">
                    <div class="sp-card-icon">
                        <i class="{{ $service->icon_class }}"></i>
                    </div>
                    <h3>{{ $service->title }}</h3>
                    <p>{{ $service->short_description ?? $service->description }}</p>
                    <span class="sp-card-link">Ver detalles <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center reveal" style="padding: 60px 0;">
            <p style="color: rgba(255,255,255,0.6); font-size: 1.1rem;">No hay servicios disponibles en este momento.</p>
            <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary" style="margin-top: 20px;">
                <i class="bi bi-envelope"></i> Contactar
            </a>
        </div>
        @endif
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="services-cta">
    <div class="container">
        <div class="services-cta-content reveal">
            <span class="section-label">Presupuesto sin compromiso</span>
            <h2 class="section-title" style="color: var(--manzer-white);">Solicite presupuesto para su proyecto</h2>
            <a href="tel:{{ $layoutConfig->footer_phone ?? '+34698989666' }}" class="services-cta-phone"
               onclick="return gtag_report_conversion('AW-17792196133/e7vtCMTc2NUbEKW8_aNC', this.href);">
                {{ $layoutConfig->footer_phone ?? '+34 698 98 96 66' }}
            </a>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary">
                    <i class="bi bi-envelope"></i> Contactar
                </a>
                <a href="https://wa.me/34698989666" target="_blank" class="btn-manzer btn-manzer-outline"
                   onclick="return gtag_report_conversion('AW-17792196133/6sYcCO3By-4bEKW8_aNC', this.href);">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Hero parallax
    const heroBg = document.getElementById('servicesHeroBg');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 25,
            ease: 'none',
            scrollTrigger: {
                trigger: '.services-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }
});
</script>
@endpush
