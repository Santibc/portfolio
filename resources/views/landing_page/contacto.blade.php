@extends('landing_page.layout')

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Manzer Agroforestal, S.L.",
    "description": "{{ $contactInfo->description ?? 'Empresa de servicios forestales y agroforestales en Lleida' }}",
    "url": "{{ url('/') }}",
    "telephone": "{{ $contactInfo->phone ?? '+34698989666' }}",
    "email": "{{ $contactInfo->email ?? 'contacto@manzeragroforestal.es' }}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $contactInfo->address ?? 'C/ Major, 54' }}",
        "addressLocality": "Menarguens",
        "addressRegion": "Lleida",
        "postalCode": "25139",
        "addressCountry": "ES"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 41.7442,
        "longitude": 0.7228
    },
    "openingHours": "Mo-Fr 08:00-18:00",
    "sameAs": [
        "https://www.instagram.com/manzer_agroforestal"
    ]
}
</script>
@endpush

@push('styles')
<style>
    /* ===== HERO ===== */
    .contact-hero {
        position: relative;
        min-height: 55vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: var(--manzer-dark);
    }
    .contact-hero-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .contact-hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        will-change: transform;
    }
    .contact-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(13, 31, 13, 0.9) 0%, rgba(27, 67, 50, 0.7) 50%, rgba(13, 31, 13, 0.92) 100%);
        z-index: 2;
    }
    .contact-hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        padding: 0 20px;
    }
    .contact-hero-content h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        color: var(--manzer-white);
        margin-bottom: 16px;
        line-height: 1.1;
    }
    .contact-hero-content p {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.6);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ===== CONTACT SECTION ===== */
    .contact-section {
        padding: 100px 0;
        background: var(--manzer-cream);
    }
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 60px;
        align-items: start;
    }

    /* ===== INFO COLUMN ===== */
    .contact-info-card {
        background: var(--manzer-dark);
        border-radius: 24px;
        padding: 48px 40px;
        color: var(--manzer-white);
        position: relative;
        overflow: hidden;
    }
    .contact-info-card::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(57, 255, 20, 0.05);
        pointer-events: none;
    }
    .contact-info-card::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(57, 255, 20, 0.03);
        pointer-events: none;
    }
    .contact-info-card > h3 {
        font-size: 1.5rem;
        margin-bottom: 8px;
        color: var(--manzer-white);
    }
    .contact-info-card > .info-subtitle {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 36px;
        line-height: 1.7;
    }
    .contact-info-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 28px;
        position: relative;
        z-index: 2;
    }
    .contact-info-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        background: rgba(57, 255, 20, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--manzer-green);
        transition: all 0.3s ease;
    }
    .contact-info-item:hover .contact-info-icon {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }
    .contact-info-item h4 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 4px;
    }
    .contact-info-item a,
    .contact-info-item p {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.5;
        margin: 0;
        transition: color 0.3s ease;
    }
    .contact-info-item a:hover {
        color: var(--manzer-green);
    }
    .contact-info-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.08);
        margin: 32px 0;
        position: relative;
        z-index: 2;
    }
    .contact-social {
        display: flex;
        gap: 12px;
        position: relative;
        z-index: 2;
    }
    .contact-social a {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    .contact-social a:hover {
        background: var(--manzer-green);
        color: var(--manzer-dark);
        transform: translateY(-3px);
    }

    /* ===== FORM COLUMN ===== */
    .contact-form-card {
        background: var(--manzer-white);
        border-radius: 24px;
        padding: 48px 40px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.04);
    }
    .contact-form-card h3 {
        font-size: 1.5rem;
        color: var(--manzer-dark);
        margin-bottom: 8px;
    }
    .contact-form-card .form-subtitle {
        font-size: 0.95rem;
        color: var(--manzer-gray);
        margin-bottom: 32px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--manzer-dark);
        margin-bottom: 8px;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        color: #333;
        background: var(--manzer-cream);
        transition: all 0.3s ease;
        outline: none;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        border-color: var(--manzer-green-muted);
        background: var(--manzer-white);
        box-shadow: 0 0 0 4px rgba(57, 255, 20, 0.08);
    }
    .form-group textarea {
        resize: vertical;
        min-height: 140px;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-message {
        margin-top: 20px;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
        display: none;
    }
    .form-message.success {
        background: rgba(57, 255, 20, 0.08);
        border: 1px solid rgba(57, 255, 20, 0.2);
        color: var(--manzer-forest);
        display: block;
    }
    .form-message.error {
        background: rgba(255, 59, 48, 0.08);
        border: 1px solid rgba(255, 59, 48, 0.2);
        color: #c0392b;
        display: block;
    }

    /* ===== MAP SECTION ===== */
    .map-section {
        padding: 0 0 100px;
        background: var(--manzer-cream);
    }
    .map-wrapper {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }
    .map-wrapper iframe,
    .map-section > iframe {
        width: 100%;
        height: 450px;
        border: none;
        display: block;
    }

    @media (max-width: 991px) {
        .contact-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .contact-hero { min-height: 45vh; }
    }
    @media (max-width: 575px) {
        .contact-info-card { padding: 36px 24px; }
        .contact-form-card { padding: 36px 24px; }
    }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="contact-hero">
    <div class="contact-hero-bg">
        <img src="{{ asset('images/hero-forest.jpg') }}" alt="Contacto" id="contactHeroBg">
    </div>
    <div class="contact-hero-overlay"></div>
    <div class="contact-hero-content">
        <span class="section-label reveal">Hablemos</span>
        <h1 class="reveal">Contacto</h1>
        <p class="reveal">{{ $contactInfo->description ?? 'Estamos aqui para ayudarle. Contactenos para solicitar presupuesto o resolver cualquier consulta.' }}</p>
    </div>
</section>

{{-- ===== CONTACT SECTION ===== --}}
<section class="contact-section section">
    <div class="container">
        <div class="contact-grid">
            {{-- INFO COLUMN --}}
            <div class="contact-info-card reveal-left">
                <h3>Informacion de contacto</h3>
                <p class="info-subtitle">No dude en ponerse en contacto con nosotros. Estaremos encantados de atenderle.</p>

                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h4>Direccion</h4>
                        <p>{{ $contactInfo->address ?? 'C/ Major, 54, 25139 Menarguens, Lleida' }}</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
                    <div>
                        <h4>Telefono</h4>
                        <a href="tel:{{ $contactInfo->phone ?? '+34698989666' }}"
                           onclick="return gtag_report_conversion('AW-17792196133/e7vtCMTc2NUbEKW8_aNC', this.href);">{{ $contactInfo->phone ?? '+34 698 98 96 66' }}</a>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <a href="mailto:{{ $contactInfo->email ?? 'contacto@manzeragroforestal.es' }}"
                           onclick="return gtag_report_conversion('AW-17792196133/FFoECMHc2NUbEKW8_aNC', this.href);">{{ $contactInfo->email ?? 'contacto@manzeragroforestal.es' }}</a>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="bi bi-whatsapp"></i></div>
                    <div>
                        <h4>WhatsApp</h4>
                        <a href="https://wa.me/34698989666" target="_blank"
                           onclick="return gtag_report_conversion('AW-17792196133/6sYcCO3By-4bEKW8_aNC', this.href);">Enviar mensaje por WhatsApp</a>
                    </div>
                </div>

                <div class="contact-info-divider"></div>

                <div class="contact-social">
                    @if($layoutConfig && $layoutConfig->instagram_url)
                    <a href="{{ $layoutConfig->instagram_url }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @else
                    <a href="https://www.instagram.com/manzer_agroforestal" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($layoutConfig && $layoutConfig->facebook_url)
                    <a href="{{ $layoutConfig->facebook_url }}" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    <a href="https://wa.me/34698989666" target="_blank" aria-label="WhatsApp"
                       onclick="return gtag_report_conversion('AW-17792196133/6sYcCO3By-4bEKW8_aNC', this.href);"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            {{-- FORM COLUMN --}}
            <div class="contact-form-card reveal-right">
                <h3>Enviar mensaje</h3>
                <p class="form-subtitle">Rellene el formulario y le responderemos lo antes posible.</p>

                <form id="contactForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name">Nombre</label>
                            <input type="text" id="contact-name" name="name" placeholder="Su nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email</label>
                            <input type="email" id="contact-email" name="email" placeholder="su@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact-subject">Asunto</label>
                        <input type="text" id="contact-subject" name="subject" placeholder="Asunto del mensaje">
                    </div>

                    <div class="form-group">
                        <label for="contact-message">Mensaje</label>
                        <textarea id="contact-message" name="message" placeholder="Cuentenos en que podemos ayudarle..." required></textarea>
                    </div>

                    <button type="submit" class="btn-manzer btn-manzer-primary" id="submitBtn" style="width: 100%; justify-content: center;">
                        <i class="bi bi-send"></i> Enviar mensaje
                    </button>

                    <div class="form-message" id="formMessage"></div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ===== MAP ===== --}}
<section class="map-section">
    <div class="container">
        <div class="map-wrapper reveal">
            @if($contactInfo && $contactInfo->google_maps_embed)
                {!! $contactInfo->google_maps_embed !!}
            @else
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2991.8!2d0.7228!3d41.7442!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sMenarguens%2C+Lleida!5e0!3m2!1ses!2ses!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Hero parallax
    const heroBg = document.getElementById('contactHeroBg');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 25,
            ease: 'none',
            scrollTrigger: {
                trigger: '.contact-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // Contact form submission
    const form = document.getElementById('contactForm');
    const btn = document.getElementById('submitBtn');
    const msg = document.getElementById('formMessage');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            msg.className = 'form-message';
            msg.style.display = 'none';

            try {
                const formData = new FormData(form);

                const response = await fetch('/contact/send', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                console.log('Response status:', response.status, 'Body:', result);

                if (result.success) {
                    gtag('event', 'conversion', {'send_to': 'AW-17792196133/oVcyCMrZy9UbEKW8_aNC', 'value': 1.0, 'currency': 'EUR'});

                    msg.className = 'form-message success';
                    msg.innerHTML = '<i class="bi bi-check-circle"></i> Su mensaje ha sido enviado correctamente. Le responderemos lo antes posible.';
                    msg.style.display = 'block';
                    form.reset();

                    setTimeout(() => {
                        msg.style.display = 'none';
                    }, 8000);
                } else {
                    throw new Error(result.error || result.message || 'Error al enviar (status: ' + response.status + ')');
                }
            } catch (error) {
                msg.className = 'form-message error';
                msg.innerHTML = '<i class="bi bi-exclamation-circle"></i> Error al enviar el mensaje. Por favor, intentelo de nuevo o contactenos por telefono.';
                msg.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send"></i> Enviar mensaje';
            }
        });
    }
});
</script>
@endpush
