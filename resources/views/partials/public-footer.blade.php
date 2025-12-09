{{-- Footer Moderno --}}
<footer class="modern-footer">
    <div class="footer-container">
        <div class="footer-grid">
            {{-- Columna Logo --}}
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="footer-logo">
                    <img src="{{ asset($config->logo ?? 'images/logo1.png') }}" alt="Better Together">
                </a>
                <p class="footer-slogan">{{ $config->footer_texto ?? 'Potenciando al talento creativo de Colombia.' }}</p>
                <div class="footer-social">
                    @if($config->facebook_url)
                        <a href="{{ $config->facebook_url }}" class="social-link" target="_blank" rel="noopener">
                            <i class="bi bi-facebook"></i>
                        </a>
                    @endif
                    @if($config->tiktok_url)
                        <a href="{{ $config->tiktok_url }}" class="social-link" target="_blank" rel="noopener">
                            <i class="bi bi-tiktok"></i>
                        </a>
                    @endif
                    @if($config->instagram_url)
                        <a href="{{ $config->instagram_url }}" class="social-link" target="_blank" rel="noopener">
                            <i class="bi bi-instagram"></i>
                        </a>
                    @endif
                    @if($config->linkedin_url)
                        <a href="{{ $config->linkedin_url }}" class="social-link" target="_blank" rel="noopener">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Columna Plataforma --}}
            <div class="footer-links">
                <h4>Plataforma</h4>
                <ul>
                    <li><a href="{{ route('planes') }}">Planes</a></li>
                    <li><a href="{{ route('eventos') }}">Eventos</a></li>
                    <li><a href="{{ route('register') }}">Registrarse</a></li>
                </ul>
            </div>

            {{-- Columna Compañía --}}
            <div class="footer-links">
                <h4>Compañía</h4>
                <ul>
                    <li><a href="{{ route('sobre-nosotros') }}">Sobre nosotros</a></li>
                    <li><a href="{{ route('contacto') }}">Contacto</a></li>
                    @if($config->terminos_url)
                        <li><a href="{{ $config->terminos_url }}" target="_blank">Términos y Condiciones</a></li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Better Together COMPANY. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

{{-- WhatsApp Floating Button --}}
@if($config->whatsapp_numero)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $config->whatsapp_numero) }}" class="whatsapp-button" target="_blank">
        <i class="bi bi-whatsapp"></i> {{ $config->whatsapp_texto ?? 'Contáctanos vía Whatsapp' }}
    </a>
@endif

<style>
/* Modern Footer Styles */
.modern-footer {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 100%);
    padding: 60px 20px 0;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 60px;
    padding-bottom: 40px;
}

/* Footer Brand */
.footer-brand {
    max-width: 300px;
}

.footer-logo {
    display: inline-block;
    margin-bottom: 15px;
}

.footer-logo img {
    height: 60px;
    width: auto;
}

.footer-slogan {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    font-style: italic;
}

.footer-social {
    display: flex;
    gap: 12px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    transform: translateY(-3px);
}

/* Footer Links */
.footer-links h4 {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #ff00c8;
    padding-left: 5px;
}

/* Footer Bottom */
.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 25px 0;
    text-align: center;
}

.footer-bottom p {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9rem;
    margin: 0;
}

/* WhatsApp Button */
.whatsapp-button {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    box-shadow: 0 5px 20px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    z-index: 1000;
}

.whatsapp-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(37, 211, 102, 0.5);
    color: white;
}

.whatsapp-button i {
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 968px) {
    .footer-grid {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .footer-brand {
        grid-column: span 2;
        max-width: 100%;
        text-align: center;
    }

    .footer-social {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
    }

    .footer-brand {
        grid-column: span 1;
    }

    .footer-links h4 {
        margin-bottom: 15px;
    }

    .footer-links a:hover {
        padding-left: 0;
    }

    .whatsapp-button {
        padding: 10px 15px;
        font-size: 0.85rem;
        bottom: 15px;
        right: 15px;
    }

    .whatsapp-button span {
        display: none;
    }

    .whatsapp-button i {
        font-size: 1.4rem;
    }
}
</style>
