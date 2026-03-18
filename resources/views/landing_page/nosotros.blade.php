@extends('landing_page.layout')

@push('styles')
<style>
    /* ===== HERO ===== */
    .about-hero {
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .about-hero-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .about-hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        will-change: transform;
    }
    .about-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(13, 31, 13, 0.88) 0%, rgba(27, 67, 50, 0.7) 50%, rgba(13, 31, 13, 0.92) 100%);
        z-index: 2;
    }
    .about-hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        padding: 0 20px;
    }
    .about-hero-content h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: var(--manzer-white);
        margin-bottom: 16px;
        line-height: 1.1;
    }
    .about-hero-content p {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.6);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ===== ABOUT CONTENT ===== */
    .about-content-section {
        padding: 100px 0;
        background: var(--manzer-cream);
    }
    .about-content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }
    .about-image-wrap {
        position: relative;
    }
    .about-image-wrap img {
        border-radius: 24px;
        width: 100%;
        height: 520px;
        object-fit: cover;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12);
    }
    .about-image-accent {
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 180px;
        height: 180px;
        background: var(--manzer-green);
        border-radius: 24px;
        z-index: -1;
        opacity: 0.15;
    }
    .about-text-content .section-label {
        margin-bottom: 16px;
    }
    .about-text-content h2 {
        font-size: clamp(2rem, 4vw, 2.8rem);
        color: var(--manzer-dark);
        margin-bottom: 24px;
        line-height: 1.15;
    }
    .about-text-content p {
        font-size: 1.05rem;
        color: #555;
        line-height: 1.85;
        margin-bottom: 16px;
    }

    /* ===== MISSION & VISION ===== */
    .mv-section {
        padding: 100px 0;
        background: var(--manzer-white);
    }
    .mv-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin-top: 50px;
    }
    .mv-card {
        background: var(--manzer-cream);
        border-radius: 24px;
        padding: 50px 40px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s var(--manzer-transition);
    }
    .mv-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }
    .mv-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--manzer-green);
    }
    .mv-card-icon {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: rgba(57, 255, 20, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--manzer-forest);
        margin-bottom: 24px;
    }
    .mv-card h3 {
        font-size: 1.5rem;
        color: var(--manzer-dark);
        margin-bottom: 16px;
    }
    .mv-card p {
        font-size: 1rem;
        color: #555;
        line-height: 1.8;
    }

    /* ===== VALUES ===== */
    .values-cards-section {
        padding: 100px 0;
        background: var(--manzer-cream);
    }
    .values-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
    }
    .val-card {
        background: var(--manzer-white);
        border-radius: 24px;
        padding: 44px 32px;
        text-align: center;
        transition: all 0.4s var(--manzer-transition);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    .val-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        border-color: rgba(57, 255, 20, 0.15);
    }
    .val-card-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: rgba(57, 255, 20, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--manzer-forest);
        margin: 0 auto 24px;
        transition: all 0.4s ease;
    }
    .val-card:hover .val-card-icon {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }
    .val-card h3 {
        font-size: 1.25rem;
        color: var(--manzer-dark);
        margin-bottom: 12px;
    }
    .val-card p {
        font-size: 0.95rem;
        color: #666;
        line-height: 1.7;
    }

    /* ===== STATS ===== */
    .stats-section {
        padding: 100px 0;
        background: linear-gradient(135deg, var(--manzer-dark) 0%, var(--manzer-forest) 100%);
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
    }
    .stat-block {
        text-align: center;
        padding: 40px 20px;
    }
    .stat-block .number {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: var(--manzer-green);
        line-height: 1;
    }
    .stat-block .label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-top: 12px;
        font-weight: 600;
    }

    /* ===== TEAM ===== */
    .team-section {
        padding: 100px 0;
        background: var(--manzer-white);
    }
    .team-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 36px;
        margin-top: 50px;
    }
    .team-card {
        border-radius: 24px;
        overflow: hidden;
        background: var(--manzer-cream);
        transition: all 0.4s var(--manzer-transition);
    }
    .team-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }
    .team-card-image {
        height: 320px;
        overflow: hidden;
        position: relative;
    }
    .team-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s var(--manzer-transition);
    }
    .team-card:hover .team-card-image img {
        transform: scale(1.05);
    }
    .team-card-social {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
        background: linear-gradient(to top, rgba(13, 31, 13, 0.9) 0%, transparent 100%);
        display: flex;
        justify-content: center;
        gap: 10px;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.4s ease;
    }
    .team-card:hover .team-card-social {
        opacity: 1;
        transform: translateY(0);
    }
    .team-card-social a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--manzer-white);
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .team-card-social a:hover {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }
    .team-card-body {
        padding: 28px;
        text-align: center;
    }
    .team-card-body h3 {
        font-size: 1.2rem;
        color: var(--manzer-dark);
        margin-bottom: 4px;
    }
    .team-card-body .position {
        font-size: 0.85rem;
        color: var(--manzer-green-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }
    .team-card-body p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.6;
    }

    /* ===== TIMELINE ===== */
    .timeline-section {
        padding: 100px 0;
        background: var(--manzer-cream);
    }
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 60px auto 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--manzer-green), var(--manzer-forest));
        transform: translateX(-50%);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 60px;
        display: flex;
        align-items: flex-start;
    }
    .timeline-item:nth-child(odd) {
        flex-direction: row;
        padding-right: calc(50% + 40px);
    }
    .timeline-item:nth-child(even) {
        flex-direction: row-reverse;
        padding-left: calc(50% + 40px);
    }
    .timeline-dot {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--manzer-green);
        border: 4px solid var(--manzer-cream);
        z-index: 2;
        box-shadow: 0 0 20px rgba(57, 255, 20, 0.3);
    }
    .timeline-content {
        background: var(--manzer-white);
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        width: 100%;
    }
    .timeline-content .year {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 0.8rem;
        color: var(--manzer-green-muted);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }
    .timeline-content h4 {
        font-size: 1.1rem;
        color: var(--manzer-dark);
        margin-bottom: 8px;
    }
    .timeline-content p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.6;
    }

    /* ===== CTA ===== */
    .about-cta-section {
        position: relative;
        padding: 120px 0;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .about-cta-bg {
        position: absolute;
        inset: 0;
    }
    .about-cta-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .about-cta-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(13, 31, 13, 0.92) 0%, rgba(27, 67, 50, 0.88) 100%);
    }
    .about-cta-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
    }
    .about-cta-phone {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 2.5rem;
        color: var(--manzer-green);
        margin: 20px 0 30px;
        display: block;
        transition: color 0.3s ease;
    }
    .about-cta-phone:hover {
        color: var(--manzer-white);
    }

    .text-center { text-align: center; }

    @media (max-width: 991px) {
        .about-content-grid { grid-template-columns: 1fr; gap: 40px; }
        .mv-grid { grid-template-columns: 1fr; }
        .values-cards-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr; }
        .team-grid { grid-template-columns: repeat(2, 1fr); }
        .timeline-item:nth-child(odd),
        .timeline-item:nth-child(even) {
            padding-left: 60px;
            padding-right: 0;
            flex-direction: row;
        }
        .timeline::before { left: 20px; }
        .timeline-dot { left: 20px; }
    }
    @media (max-width: 575px) {
        .about-hero { min-height: 50vh; }
        .team-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="about-hero">
    <div class="about-hero-bg">
        <img src="{{ $about && $about->main_image_path ? asset($about->main_image_path) : asset('images/hero-forest.jpg') }}" alt="Sobre nosotros" id="aboutHeroBg">
    </div>
    <div class="about-hero-overlay"></div>
    <div class="about-hero-content">
        <span class="section-label reveal">Quienes somos</span>
        <h1 class="reveal">{{ $about->page_title ?? 'Nuestra Historia' }}</h1>
        <p class="reveal">Compromiso con la naturaleza, seguridad y excelencia profesional</p>
    </div>
</section>

{{-- ===== ABOUT CONTENT ===== --}}
<section class="about-content-section section">
    <div class="container">
        <div class="about-content-grid">
            <div class="about-image-wrap reveal-left">
                <img src="{{ $about && $about->main_image_path ? asset($about->main_image_path) : asset('images/about-forest.jpg') }}" alt="Manzer Agroforestal">
                <div class="about-image-accent"></div>
            </div>
            <div class="about-text-content reveal-right">
                <span class="section-label">Sobre nosotros</span>
                <h2>{{ $about->purpose_title ?? 'Nuestro Proposito' }}</h2>
                <p>{{ $about->purpose_content ?? 'En Manzer Agroforestal llevamos anos realizando proyectos de mantenimiento y control de vegetacion y arbolado, con los mas altos estandares de seguridad y calidad.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== MISSION & VISION ===== --}}
<section class="mv-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Nuestra esencia</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Mision y Vision</h2>
        </div>
        <div class="mv-grid">
            <div class="mv-card reveal-left">
                <div class="mv-card-icon">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h3>{{ $about->mission_title ?? 'Nuestra Mision' }}</h3>
                <p>{{ $about->mission_content ?? 'Ofrecer servicios forestales y agroforestales de maxima calidad, con los mejores estandares de seguridad y compromiso medioambiental, contribuyendo al cuidado y mantenimiento de nuestros bosques.' }}</p>
            </div>
            <div class="mv-card reveal-right">
                <div class="mv-card-icon">
                    <i class="bi bi-eye"></i>
                </div>
                <h3>{{ $about->vision_title ?? 'Nuestra Vision' }}</h3>
                <p>{{ $about->vision_content ?? 'Ser la empresa de referencia en trabajos forestales y agroforestales en Lleida y alrededores, reconocida por la excelencia en nuestros servicios y nuestro compromiso con el medio ambiente.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== VALUES ===== --}}
<section class="values-cards-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Lo que nos define</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Nuestros Valores</h2>
        </div>
        <div class="values-cards-grid stagger-children">
            <div class="val-card tilt-card stagger-item">
                <div class="val-card-icon">
                    <i class="{{ $about->value1_icon ?? 'bi bi-shield-check' }}"></i>
                </div>
                <h3>{{ $about->value1_title ?? 'Seguridad' }}</h3>
                <p>{{ $about->value1_description ?? 'Utilizamos los mejores sistemas de seguridad y control. La proteccion de nuestro equipo y del entorno es nuestra prioridad.' }}</p>
            </div>
            <div class="val-card tilt-card stagger-item">
                <div class="val-card-icon">
                    <i class="{{ $about->value2_icon ?? 'bi bi-award' }}"></i>
                </div>
                <h3>{{ $about->value2_title ?? 'Calidad' }}</h3>
                <p>{{ $about->value2_description ?? 'Cada proyecto es ejecutado con los mas altos estandares de calidad y profesionalismo.' }}</p>
            </div>
            <div class="val-card tilt-card stagger-item">
                <div class="val-card-icon">
                    <i class="{{ $about->value3_icon ?? 'bi bi-globe-americas' }}"></i>
                </div>
                <h3>{{ $about->value3_title ?? 'Medio Ambiente' }}</h3>
                <p>{{ $about->value3_description ?? 'Compromiso total con la sostenibilidad y la proteccion de la biodiversidad en cada trabajo.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== STATS ===== --}}
<section class="stats-section section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-block reveal">
                <div class="number" data-purecounter-start="0" data-purecounter-end="{{ $about->stats_years_experience ?? 5 }}" data-purecounter-duration="2">0</div>
                <div class="label">Anos de experiencia</div>
            </div>
            <div class="stat-block reveal">
                <div class="number" data-purecounter-start="0" data-purecounter-end="{{ $about->stats_happy_clients ?? 50 }}" data-purecounter-duration="2">0</div>
                <div class="label">Clientes satisfechos</div>
            </div>
            <div class="stat-block reveal">
                <div class="number" data-purecounter-start="0" data-purecounter-end="{{ $about->stats_client_satisfaction ?? 100 }}" data-purecounter-duration="2" data-purecounter-suffix="%">0</div>
                <div class="label">Satisfaccion cliente</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== TEAM ===== --}}
@if(isset($teamMembers) && $teamMembers->count() > 0)
<section class="team-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Nuestro equipo</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Las personas detras de Manzer</h2>
        </div>
        <div class="team-grid stagger-children">
            @foreach($teamMembers as $member)
            <div class="team-card stagger-item">
                <div class="team-card-image">
                    @if($member->image_path)
                    <img src="{{ asset($member->image_path) }}" alt="{{ $member->name }}" loading="lazy">
                    @else
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--manzer-forest),var(--manzer-dark));display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person" style="font-size:4rem;color:var(--manzer-green);opacity:0.3;"></i>
                    </div>
                    @endif
                    <div class="team-card-social">
                        @if($member->linkedin_url)
                        <a href="{{ $member->linkedin_url }}" target="_blank" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        @endif
                        @if($member->instagram_url)
                        <a href="{{ $member->instagram_url }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if($member->twitter_url)
                        <a href="{{ $member->twitter_url }}" target="_blank" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($member->email)
                        <a href="mailto:{{ $member->email }}" aria-label="Email"><i class="bi bi-envelope"></i></a>
                        @endif
                    </div>
                </div>
                <div class="team-card-body">
                    <h3>{{ $member->name }}</h3>
                    <div class="position">{{ $member->position }}</div>
                    @if($member->description)
                    <p>{{ $member->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== TIMELINE ===== --}}
<section class="timeline-section section">
    <div class="container">
        <div class="text-center reveal">
            <span class="section-label">Nuestra trayectoria</span>
            <h2 class="section-title" style="color: var(--manzer-dark);">Hitos de la empresa</h2>
        </div>
        <div class="timeline" id="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="year">Fundacion</div>
                    <h4>Nace Manzer Agroforestal</h4>
                    <p>Fundamos la empresa con la vision de ofrecer servicios forestales profesionales en Lleida y alrededores.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="year">Crecimiento</div>
                    <h4>Expansion de servicios</h4>
                    <p>Ampliamos nuestra oferta incluyendo trabajos en altura, desbroces y prevencion de incendios forestales.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="year">Consolidacion</div>
                    <h4>Referentes en la zona</h4>
                    <p>Nos convertimos en empresa de referencia para trabajos forestales y agroforestales en toda la provincia de Lleida.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="year">Actualidad</div>
                    <h4>Innovacion continua</h4>
                    <p>Seguimos invirtiendo en formacion, equipamiento de ultima generacion y nuevas tecnicas de trabajo seguro.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="about-cta-section">
    <div class="about-cta-bg">
        <img src="{{ asset('images/hero-forest.jpg') }}" alt="Contacto" loading="lazy" id="aboutCtaBg">
    </div>
    <div class="about-cta-overlay"></div>
    <div class="about-cta-content reveal">
        <span class="section-label">Contactanos</span>
        <h2 class="section-title" style="color: var(--manzer-white);">Trabajemos juntos en su proximo proyecto</h2>
        <a href="tel:{{ $layoutConfig->footer_phone ?? '+34698989666' }}" class="about-cta-phone">
            {{ $layoutConfig->footer_phone ?? '+34 698 98 96 66' }}
        </a>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('contacto') }}" class="btn-manzer btn-manzer-primary"><i class="bi bi-envelope"></i> Contactar</a>
            <a href="https://wa.me/34698989666" target="_blank" class="btn-manzer btn-manzer-outline"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/purecounterjs@1.5.0/dist/purecounter_vanilla.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Hero parallax
    const heroBg = document.getElementById('aboutHeroBg');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 25,
            ease: 'none',
            scrollTrigger: {
                trigger: '.about-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // About image parallax
    const aboutImg = document.querySelector('.about-image-wrap img');
    if (aboutImg) {
        gsap.to(aboutImg, {
            yPercent: 8,
            ease: 'none',
            scrollTrigger: {
                trigger: '.about-content-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // CTA parallax
    const ctaBg = document.getElementById('aboutCtaBg');
    if (ctaBg) {
        gsap.to(ctaBg, {
            yPercent: 20,
            ease: 'none',
            scrollTrigger: {
                trigger: '.about-cta-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // Timeline scroll animation
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        const content = item.querySelector('.timeline-content');
        const dot = item.querySelector('.timeline-dot');

        gsap.fromTo(dot,
            { scale: 0 },
            {
                scale: 1,
                duration: 0.5,
                ease: 'back.out(2)',
                scrollTrigger: {
                    trigger: item,
                    start: 'top 80%',
                    once: true
                }
            }
        );

        gsap.fromTo(content,
            { opacity: 0, x: index % 2 === 0 ? -40 : 40, y: 20 },
            {
                opacity: 1,
                x: 0,
                y: 0,
                duration: 0.8,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: item,
                    start: 'top 80%',
                    once: true
                }
            }
        );
    });

    // Timeline line grow animation
    const timeline = document.querySelector('.timeline::before');
    gsap.fromTo('.timeline::before',
        { scaleY: 0 },
        {
            scaleY: 1,
            ease: 'none',
            scrollTrigger: {
                trigger: '.timeline',
                start: 'top 80%',
                end: 'bottom 60%',
                scrub: true
            }
        }
    );

    // PureCounter
    if (typeof PureCounter !== 'undefined') {
        new PureCounter({
            selector: '[data-purecounter-start]',
            start: 0,
            once: true,
            pulse: false
        });
    }
});
</script>
@endpush
