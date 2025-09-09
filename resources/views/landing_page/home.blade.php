@extends('landing_page.layout')

@section('content')
    
    <!-- Sección Hero -->
    <section id="hero" class="hero section dark-background">
      <!-- Carousel -->
      <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
          @forelse($carouselImages as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
              <img src="{{ asset($image->image_path) }}" class="hero-bg-img" alt="{{ $image->alt_text }}">
            </div>
          @empty
            <!-- Imágenes por defecto si no hay en la BD -->
            <div class="carousel-item active">
              <img src="{{ asset('imagenes/car1.jpg') }}" class="hero-bg-img" alt="">
            </div>
            <div class="carousel-item">
              <img src="{{ asset('imagenes/car2.jpg') }}" class="hero-bg-img" alt="">
            </div>
            <div class="carousel-item">
              <img src="{{ asset('imagenes/car3.jpg') }}" class="hero-bg-img" alt="">
            </div>
            <div class="carousel-item">
              <img src="{{ asset('imagenes/car4.jpg') }}" class="hero-bg-img" alt="">
            </div>
            <div class="carousel-item">
              <img src="{{ asset('imagenes/car5.jpg') }}" class="hero-bg-img" alt="">
            </div>
          @endforelse
        </div>
        
        <!-- Carousel Controls - Desktop -->
        <button class="carousel-control-prev d-none d-lg-flex" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next d-none d-lg-flex" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

      <!-- Fixed content overlay -->
      <div class="container hero-content" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-start">
          <div class="col-lg-8"><br><br>
            <h2 style="color: white">{{ $config->company_name ?? 'Montano & Co.' }}</h2><br>
            <p style="text-align: justify; color: white;">{{ $config->company_description ?? 'Somos un despacho orientado a resultados. Diseñamos soluciones jurídicas integrales en derecho empresarial, comercio exterior (aduanas, importación y exportación) y cumplimiento (PLD/FT, anticorrupción y ética). Representamos en penal económico y litigios comerciales. También brindamos servicios notariales para estructurar y dar seguridad a tus operaciones. Operamos desde El Salvador con alcance en Centroamérica y el Caribe. Transparencia, eficiencia y acompañamiento en cada decisión.' }}</p>
            <div class="d-flex gap-3 mt-4">
              <a style="color: white;border-color: white;" href="{{ $config->services_button_url ?? '#services' }}" class="btn-get-started">Nuestros Servicios</a>
              @if($config && $config->contact_email)
                <a style="color: white;border-color: white;" href="mailto:{{ $config->contact_email }}" class="btn-get-started">
                  <i class="bi bi-envelope"></i> Contáctanos
                </a>
              @else
                <a style="color: white;border-color: white;" href="mailto:contacto@ejemplo.com" class="btn-get-started">
                  <i class="bi bi-envelope"></i> Contáctanos
                </a>
              @endif
            </div>
            
            <!-- Mobile Carousel Controls -->
            <div class="mobile-carousel-controls d-lg-none">
              <button class="mobile-carousel-btn" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <i class="bi bi-chevron-left"></i>
              </button>
              <button class="mobile-carousel-btn" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <i class="bi bi-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Sección Hero -->

    <!-- Sección Servicios -->
    <section id="services" class="services section" style="padding: 100px 0;">

      <!-- Título de Sección -->
      <div class="container section-title" data-aos="fade-up">
        <span>Servicios</span>
        <h2>Servicios</h2>
        <p></p>
      </div><!-- Fin Título de Sección -->

      <div class="container">

        <div class="row gy-4">
          @forelse($services as $index => $service)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-{{ $service->icon_class }}"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>{{ $service->title }}</h3>
                </a>
                <p>{{ $service->description }}</p>
              </div>
            </div><!-- Fin Servicio -->
          @empty
            <!-- Servicios por defecto si no hay en la BD -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Administración de Cartera y Patrimonio</h3>
                </a>
                <p>Acompañamiento estratégico en gestión patrimonial, optimización de inversiones y planificación sucesoria con seguridad jurídica.</p>
              </div>
            </div><!-- Fin Servicio -->

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-shield-check"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Prevención de Lavado y Gestión de Riesgos</h3>
                </a>
                <p>Programas de cumplimiento normativo en PLD/FT, anticorrupción y ética empresarial adaptados a estándares internacionales.</p>
              </div>
            </div><!-- Fin Servicio -->

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-building"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Derecho Empresarial</h3>
                </a>
                <p>Asesoría integral desde constitución de sociedades hasta fusiones, gobierno corporativo y resolución de conflictos comerciales.</p>
              </div>
            </div><!-- Fin Servicio -->

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-globe-americas"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Derecho Aduanero y Comercio Exterior</h3>
                </a>
                <p>Asesoría especializada en importación/exportación para operaciones en Centroamérica, el Caribe y mercados globales.</p>
              </div>
            </div><!-- Fin Servicio -->

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-shield-lock"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Derecho Penal</h3>
                </a>
                <p>Defensa penal integral en delitos económicos, financieros, societarios, corrupción y delitos comunes.</p>
              </div>
            </div><!-- Fin Servicio -->

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
              <div class="service-item position-relative">
                <div class="icon">
                  <i class="bi bi-file-text"></i>
                </div>
                <a href="#" class="stretched-link">
                  <h3>Derecho Notarial y Registral</h3>
                </a>
                <p>Autenticación de documentos, escrituras públicas, constitución de sociedades y actos jurídicos con seguridad legal.</p>
              </div>
            </div><!-- Fin Servicio -->
          @endforelse
        </div>

      </div>

    </section><!-- /Sección Servicios -->

    <!-- Sección Tarjetas -->
    <section id="cards" class="cards section" style="padding: 100px 0;">

      <div class="container">

        <div class="row no-gutters">
          @forelse($steps as $index => $step)
            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
              <span>{{ str_pad($step->step_number, 2, '0', STR_PAD_LEFT) }}</span>
              <h4>{{ $step->title }}</h4>
              <p>{{ $step->description }}</p>
            </div><!-- Fin Ítem Tarjeta -->
          @empty
            <!-- Pasos por defecto si no hay en la BD -->
            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="100">
              <span>01</span>
              <h4>Consulta Inicial</h4>
              <p>Escuchamos tu caso y brindamos una primera asesoría clara y transparente.</p>
            </div><!-- Fin Ítem Tarjeta -->

            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="200">
              <span>02</span>
              <h4>Análisis Jurídico</h4>
              <p>Revisamos a fondo la situación legal para diseñar la mejor estrategia.</p>
            </div><!-- Fin Ítem Tarjeta -->

            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="300">
              <span>03</span>
              <h4>Plan de Acción</h4>
              <p>Te presentamos opciones claras y viables para defender tus derechos.</p>
            </div><!-- Fin Ítem Tarjeta -->

            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="400">
              <span>04</span>
              <h4>Representación Legal</h4>
              <p>Te acompañamos en audiencias, procesos y negociaciones legales.</p>
            </div><!-- Fin Ítem Tarjeta -->

            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="500">
              <span>05</span>
              <h4>Seguimiento Constante</h4>
              <p>Mantenemos comunicación permanente sobre el avance de tu caso.</p>
            </div><!-- Fin Ítem Tarjeta -->

            <div class="col-lg-4 col-md-6 card" data-aos="fade-up" data-aos-delay="600">
              <span>06</span>
              <h4>Resultados</h4>
              <p>Trabajamos para lograr la mejor solución legal, justa y favorable para ti.</p>
            </div><!-- Fin Ítem Tarjeta -->
          @endforelse
        </div>

      </div>

    </section><!-- /Sección Tarjetas -->
    
    <section id="contact" style="padding: 100px 0;" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Contacto</span>
        <h2>Contacto</h2>
        <p>Estamos aquí para ayudarte. Contáctanos y recibe asesoría legal personalizada.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Dirección</h3>
              <p>{{ $contactInfo->address ?? 'A108 Adam Street, New York, NY 535022' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Teléfono </h3>
              <p>{{ $contactInfo->phone ?? '+1 5589 55488 55' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Correo</h3>
              <p>{{ $contactInfo->email ?? 'info@example.com' }}</p>
            </div>
          </div><!-- End Info Item -->

        </div>

        <div class="row gy-4 mt-1">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            @if($contactInfo && $contactInfo->google_maps_embed)
              {!! $contactInfo->google_maps_embed !!}
            @else
              <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus" frameborder="0" style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
          </div><!-- End Google Maps -->

          <div class="col-lg-6">
            <form id="contactForm" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="400">
              @csrf
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Nombre" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Correo" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Asunto" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Mensaje" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading" style="display: none;">Enviando...</div>
                  <div class="error-message" style="display: none;"></div>
                  <div class="sent-message" style="display: none;">Tu mensaje ha sido enviado correctamente</div>

                  <button type="submit">Enviar</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

    <!-- Estilos adicionales para la sección Hero -->
    <style>
      /* Estilos del carrusel hero */
      .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
      }
      
      .hero .carousel {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
      }
      
      .hero .carousel-inner {
        height: 100%;
      }
      
      .hero .carousel-item {
        height: 100%;
      }
      
      .hero .hero-bg-img {
        position: absolute;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.95);
        z-index: 1;
      }
      
      .hero .hero-content {
        position: relative;
        z-index: 3;
        color: white;
        width: 100%;
      }
      
      /* Controles del carrusel - Desktop */
      .hero .carousel-control-prev,
      .hero .carousel-control-next {
        z-index: 10 !important;
        opacity: 0.9;
        transition: all 0.3s ease;
        width: 60px;
        height: 60px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        margin: 0 20px;
      }
      
      .hero .carousel-control-prev {
        left: 20px;
      }
      
      .hero .carousel-control-next {
        right: 20px;
      }
      
      .hero .carousel-control-prev:hover,
      .hero .carousel-control-next:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-50%) scale(1.1);
      }
      
      .hero .carousel-control-prev-icon,
      .hero .carousel-control-next-icon {
        background-size: 20px 20px !important;
        width: 20px !important;
        height: 20px !important;
        filter: drop-shadow(1px 1px 3px rgba(0,0,0,0.5));
      }
      
      /* Mobile Carousel Controls */
      .mobile-carousel-controls {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        padding: 0 20px;
      }
      
      .mobile-carousel-btn {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
      }
      
      .mobile-carousel-btn:hover,
      .mobile-carousel-btn:focus {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      }
      
      .mobile-carousel-btn:active {
        transform: scale(0.95);
      }

      /* Responsive - Mobile */
      @media (max-width: 768px) {
        .hero {
          min-height: 90vh;
          padding: 100px 0 120px 0;
        }
        
        .hero .d-flex {
          flex-direction: column !important;
          gap: 20px !important;
          margin-top: 30px !important;
        }
        
        .hero .btn-get-started {
          width: 100%;
          text-align: center;
          padding: 15px 20px !important;
          font-size: 16px !important;
          font-weight: 600;
          border-radius: 8px;
          transition: all 0.3s ease;
        }
        
        .hero .btn-get-started:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .hero .container .row .col-lg-8 {
          padding: 0 20px;
        }
        
        .hero h2 {
          font-size: 2.2rem !important;
          margin-bottom: 20px !important;
          text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
          font-size: 16px !important;
          line-height: 1.6 !important;
          text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
          margin-bottom: 0 !important;
        }
        
        .mobile-carousel-controls {
          margin-top: 25px;
        }
        
        .mobile-carousel-btn {
          width: 45px;
          height: 45px;
          font-size: 18px;
        }
      }

      /* Extra small devices */
      @media (max-width: 576px) {
        .hero {
          min-height: 85vh;
          padding: 80px 0 100px 0;
        }
        
        .hero h2 {
          font-size: 1.9rem !important;
        }
        
        .hero p {
          font-size: 15px !important;
        }
        
        .hero .container .row .col-lg-8 {
          padding: 0 15px;
        }
        
        .mobile-carousel-controls {
          margin-top: 20px;
          gap: 15px;
        }
        
        .mobile-carousel-btn {
          width: 40px;
          height: 40px;
          font-size: 16px;
        }
      }
      
      /* Mejoras visuales adicionales */
      @media (max-width: 768px) {
        .hero::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(45deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
          z-index: 2;
          pointer-events: none;
        }
      }
      
      /* Animación suave para las flechas */
      .hero .carousel-control-prev,
      .hero .carousel-control-next {
        animation: fadeInControls 1s ease-in-out;
      }
      
      @keyframes fadeInControls {
        from {
          opacity: 0;
          transform: translateY(-50%) scale(0.8);
        }
        to {
          opacity: 0.9;
          transform: translateY(-50%) scale(1);
        }
      }
    </style>

<script>
      document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el carrusel con Bootstrap
        var heroCarousel = document.getElementById('heroCarousel');
        if (heroCarousel) {
          // Bootstrap maneja automáticamente los controles con data-bs-target y data-bs-slide
          // Solo necesitamos asegurarnos de que esté inicializado con las opciones correctas
          var carousel = new bootstrap.Carousel(heroCarousel, {
            interval: 5000,
            ride: 'carousel',
            pause: 'hover',
            wrap: true
          });
        }
      });
    </script>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        const loading = contactForm.querySelector('.loading');
        const errorMessage = contactForm.querySelector('.error-message');
        const sentMessage = contactForm.querySelector('.sent-message');

        contactForm.addEventListener('submit', function(e) {
          e.preventDefault();

          // Mostrar loading y ocultar otros mensajes
          loading.style.display = 'block';
          errorMessage.style.display = 'none';
          sentMessage.style.display = 'none';

          // Obtener datos del formulario
          const formData = new FormData(contactForm);

          // Enviar por AJAX
          fetch('{{ route('contact.send') }}', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
              sentMessage.style.display = 'block';
              contactForm.reset();
              
              // Ocultar mensaje de éxito después de 5 segundos
              setTimeout(() => {
                sentMessage.style.display = 'none';
              }, 5000);
            } else {
              errorMessage.textContent = data.error || 'Ha ocurrido un error al enviar el mensaje';
              errorMessage.style.display = 'block';
            }
          })
          .catch(error => {
            loading.style.display = 'none';
            errorMessage.textContent = 'Ha ocurrido un error al enviar el mensaje';
            errorMessage.style.display = 'block';
            console.error('Error:', error);
          });
        });
      });
    </script>

@endsection