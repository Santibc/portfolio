@extends('landing_page.layout')

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "{{ $service->title }}",
    "description": "{{ Str::limit(strip_tags($service->long_description ?? $service->description), 200) }}",
    "provider": {
        "@type": "LocalBusiness",
        "name": "Manzer Agroforestal, S.L.",
        "telephone": "+34698989666",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Menarguens",
            "addressRegion": "Lleida",
            "addressCountry": "ES"
        }
    },
    "areaServed": {
        "@type": "Place",
        "name": "Lleida, Cataluna"
    }
    @if($service->image_path)
    ,"image": "{{ asset($service->image_path) }}"
    @endif
}
</script>
@endpush

@push('styles')
<style>
    /* ===== HERO ===== */
    .sd-hero {
        position: relative;
        min-height: 65vh;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .sd-hero-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .sd-hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        will-change: transform;
    }
    .sd-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(13, 31, 13, 0.95) 0%, rgba(13, 31, 13, 0.4) 40%, rgba(27, 67, 50, 0.3) 100%);
        z-index: 2;
    }
    .sd-hero-content {
        position: relative;
        z-index: 3;
        padding: 60px 0;
        width: 100%;
    }
    .sd-hero-content h1 {
        font-size: clamp(2.2rem, 5vw, 3.8rem);
        color: var(--manzer-white);
        line-height: 1.1;
        margin-bottom: 0;
    }

    /* ===== BREADCRUMBS ===== */
    .sd-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        font-size: 0.85rem;
    }
    .sd-breadcrumbs a {
        color: rgba(255, 255, 255, 0.5);
        transition: color 0.3s ease;
    }
    .sd-breadcrumbs a:hover {
        color: var(--manzer-green);
    }
    .sd-breadcrumbs .separator {
        color: rgba(255, 255, 255, 0.3);
    }
    .sd-breadcrumbs .current {
        color: var(--manzer-green);
        font-weight: 600;
    }

    /* ===== CONTENT ===== */
    .sd-content-section {
        padding: 80px 0;
        background: var(--manzer-cream);
    }
    .sd-content {
        max-width: 900px;
        margin: 0 auto;
    }
    .sd-content h2 {
        font-size: 1.8rem;
        color: var(--manzer-dark);
        margin: 40px 0 16px;
    }
    .sd-content h3 {
        font-size: 1.4rem;
        color: var(--manzer-forest);
        margin: 32px 0 12px;
    }
    .sd-content p {
        font-size: 1.05rem;
        color: #555;
        line-height: 1.85;
        margin-bottom: 20px;
    }
    .sd-content ul, .sd-content ol {
        margin: 16px 0 24px 20px;
        color: #555;
        line-height: 1.85;
    }
    .sd-content li {
        margin-bottom: 8px;
    }
    .sd-content img {
        border-radius: 16px;
        margin: 24px 0;
    }
    .sd-content blockquote {
        border-left: 4px solid var(--manzer-green);
        padding: 20px 24px;
        background: rgba(57, 255, 20, 0.04);
        border-radius: 0 12px 12px 0;
        margin: 24px 0;
        font-style: italic;
        color: var(--manzer-forest);
    }

    /* ===== GALLERY ===== */
    .sd-gallery-section {
        padding: 80px 0;
        background: var(--manzer-white);
    }
    .sd-gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-top: 40px;
    }
    .sd-gallery-item {
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        aspect-ratio: 1;
    }
    .sd-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s var(--manzer-transition);
    }
    .sd-gallery-item:hover img {
        transform: scale(1.08);
    }
    .sd-gallery-item-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(13, 31, 13, 0.7) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.4s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sd-gallery-item:hover .sd-gallery-item-overlay {
        opacity: 1;
    }
    .sd-gallery-item-overlay i {
        color: var(--manzer-white);
        font-size: 1.8rem;
    }

    /* ===== RELATED SERVICES ===== */
    .sd-related-section {
        padding: 100px 0;
        background: linear-gradient(180deg, var(--manzer-dark) 0%, var(--manzer-forest) 100%);
    }
    .sd-related-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
    }
    .sd-related-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 20px;
        padding: 36px 28px;
        transition: all 0.4s var(--manzer-transition);
        display: block;
        color: inherit;
    }
    .sd-related-card:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-6px);
        border-color: rgba(57, 255, 20, 0.2);
    }
    .sd-related-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(57, 255, 20, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: var(--manzer-green);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .sd-related-card:hover .sd-related-card-icon {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }
    .sd-related-card h3 {
        font-size: 1.15rem;
        color: var(--manzer-white);
        margin-bottom: 10px;
    }
    .sd-related-card p {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .sd-related-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--manzer-green);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: gap 0.3s ease;
    }
    .sd-related-card:hover .sd-related-link {
        gap: 12px;
    }

    /* ===== CTA ===== */
    .sd-cta-section {
        position: relative;
        padding: 100px 0;
        background: var(--manzer-forest);
        overflow: hidden;
    }
    .sd-cta-section::before {
        content: '';
        position: absolute;
        bottom: -150px;
        left: -150px;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: rgba(57, 255, 20, 0.04);
        pointer-events: none;
    }
    .sd-cta-content {
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }
    .sd-cta-phone {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: clamp(1.6rem, 3.5vw, 2.2rem);
        color: var(--manzer-green);
        margin: 20px 0 30px;
        display: block;
        transition: color 0.3s ease;
    }
    .sd-cta-phone:hover {
        color: var(--manzer-white);
    }

    @media (max-width: 991px) {
        .sd-gallery-grid { grid-template-columns: repeat(3, 1fr); }
        .sd-related-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575px) {
        .sd-gallery-grid { grid-template-columns: repeat(2, 1fr); }
        .sd-related-grid { grid-template-columns: 1fr; }
        .sd-hero { min-height: 50vh; }
    }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="sd-hero">
    <div class="sd-hero-bg">
        @if($service->image_path)
            <img src="{{ asset($service->image_path) }}" alt="{{ $service->title }}" id="sdHeroBg">
        @else
            <img src="{{ asset('images/hero-forest.jpg') }}" alt="{{ $service->title }}" id="sdHeroBg">
        @endif
    </div>
    <div class="sd-hero-overlay"></div>
    <div class="sd-hero-content">
        <div class="container">
            <nav class="sd-breadcrumbs reveal">
                <a href="{{ route('welcome') }}">Inicio</a>
                <span class="separator"><i class="bi bi-chevron-right"></i></span>
                <a href="{{ route('servicios') }}">Servicios</a>
                <span class="separator"><i class="bi bi-chevron-right"></i></span>
                <span class="current">{{ $service->title }}</span>
            </nav>
            <h1 class="reveal">{{ $service->title }}</h1>
        </div>
    </div>
</section>

{{-- ===== CONTENT ===== --}}
<section class="sd-content-section">
    <div class="container">
        <div class="sd-content reveal">
            @if($service->long_description)
                {!! $service->long_description !!}
            @else
                <p>{{ $service->description }}</p>
            @endif
        </div>
    </div>
</section>

{{-- ===== GALLERY ===== --}}
@php
    $galleryImages = $service->gallery_images ?? [];
    if (is_string($galleryImages)) {
        $galleryImages = json_decode($galleryImages, true) ?? [];
    }
@endphp
@if(count($galleryImages) > 0)
<section class="sd-gallery-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Galeria</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Imagenes del servicio</h2>
        </div>
        <div class="sd-gallery-grid stagger-children" id="sdGalleryGrid">
            @foreach($galleryImages as $image)
            <a href="{{ asset($image) }}" class="sd-gallery-item stagger-item" data-src="{{ asset($image) }}">
                <img src="{{ asset($image) }}" alt="{{ $service->title }}" loading="lazy">
                <div class="sd-gallery-item-overlay">
                    <i class="bi bi-zoom-in"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== RELATED SERVICES ===== --}}
@if(isset($otherServices) && $otherServices->count() > 0)
<section class="sd-related-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Mas servicios</span>
            <h2 class="section-title" style="color: var(--manzer-white);">Otros servicios que ofrecemos</h2>
        </div>
        <div class="sd-related-grid stagger-children">
            @foreach($otherServices->take(3) as $other)
            <a href="{{ route('servicios.detalle', $other->slug) }}" class="sd-related-card tilt-card stagger-item">
                <div class="sd-related-card-icon">
                    <i class="{{ $other->icon_class }}"></i>
                </div>
                <h3>{{ $other->title }}</h3>
                <p>{{ $other->short_description ?? $other->description }}</p>
                <span class="sd-related-link">Ver servicio <i class="bi bi-arrow-right"></i></span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== CTA ===== --}}
<section class="sd-cta-section">
    <div class="container">
        <div class="sd-cta-content reveal">
            <span class="section-label">Presupuesto sin compromiso</span>
            <h2 class="section-title" style="color: var(--manzer-white);">Solicitar presupuesto para este servicio</h2>
            <a href="tel:{{ $layoutConfig->footer_phone ?? '+34698989666' }}" class="sd-cta-phone">
                {{ $layoutConfig->footer_phone ?? '+34 698 98 96 66' }}
            </a>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary">
                    <i class="bi bi-envelope"></i> Contactar
                </a>
                <a href="https://wa.me/34698989666" target="_blank" class="btn-manzer btn-manzer-outline">
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
    const heroBg = document.getElementById('sdHeroBg');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 30,
            ease: 'none',
            scrollTrigger: {
                trigger: '.sd-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // LightGallery for service gallery
    const galleryGrid = document.getElementById('sdGalleryGrid');
    if (galleryGrid && typeof lightGallery !== 'undefined') {
        lightGallery(galleryGrid, {
            selector: '.sd-gallery-item[data-src]',
            speed: 500,
            backdropDuration: 300,
            download: false,
            counter: true,
            plugins: [lgZoom]
        });
    }
});
</script>
@endpush
