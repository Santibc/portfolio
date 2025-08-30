@extends('landing_page.layout')

@section('content')
    
    <!-- Sección Hero -->
    <section id="hero" class="hero section dark-background">

      <img src="{{ asset('montano_assets/assets/img/hero.png') }}" alt="" data-aos="fade-in">

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-start">
          <div class="col-lg-8"><br><br>
            <h2 style="color: white">Montano & Co.</h2><br>
            <p style="text-align: justify; color: white;">Somos un despacho orientado a resultados. Diseñamos soluciones jurídicas
    integrales en derecho empresarial, comercio exterior (aduanas, importación
    y exportación) y cumplimiento (PLD/FT, anticorrupción y ética).
    Representamos en penal económico y litigios comerciales. También
    brindamos servicios notariales para estructurar y dar seguridad a tus
    operaciones. Operamos desde El Salvador con alcance en Centroamérica
    y el Caribe. Transparencia, eficiencia y acompañamiento en cada decisión.</p>
            <div class="d-flex gap-3 mt-4">
              <a style="color: white;border-color: white;" href="#services" class="btn-get-started">Nuestros Servicios</a>
              <a style="color: white;border-color: white;" href="mailto:contacto@ejemplo.com" class="btn-get-started">
                <i class="bi bi-envelope"></i> Contáctanos
              </a>
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

        </div>

      </div>

    </section><!-- /Sección Servicios -->

    <!-- Sección Tarjetas -->
    <section id="cards" class="cards section" style="padding: 100px 0;">

      <div class="container">

        <div class="row no-gutters">

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
              <p>A108 Adam Street, New York, NY 535022</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Teléfono </h3>
              <p>+1 5589 55488 55</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Correo</h3>
              <p>info@example.com</p>
            </div>
          </div><!-- End Info Item -->

        </div>

        <div class="row gy-4 mt-1">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus" frameborder="0" style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div><!-- End Google Maps -->

          <div class="col-lg-6">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="400">
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
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Tu mensaje ha sido enviado</div>

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
      .hero:before {
        background: rgba(3, 35, 68, 0.6); /* Azul del menú con 60% de opacidad */
      }
      
      .hero .btn-get-started {
        margin-right: 10px;
      }
      
      .hero .btn-get-started i {
        margin-right: 5px;
      }
    </style>

@endsection