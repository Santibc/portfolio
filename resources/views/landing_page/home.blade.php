@extends('landing_page.layout')

@section('content')
    
    <!-- Sección Hero -->
    <section id="hero" class="hero section dark-background">

      <img src="{{ asset('montano_assets/assets/img/hero-bg.jpg') }}" alt="" data-aos="fade-in">

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-start">
          <div class="col-lg-8">
            <h2 style="color: white">Montano & Co.</h2><br>
            <p  style="text-align: justify;">Somos un equipo de abogados comprometidos en brindar soluciones legales integrales y estratégicas. Ofrecemos asesoría confiable y representación efectiva en áreas como derecho civil, laboral, empresarial y familiar. Nuestro objetivo es proteger tus derechos, resolver tus conflictos con eficiencia y acompañarte en cada paso con transparencia y profesionalismo.</p>
            <a style="color: white;border-color: white;" href="#about" class="btn-get-started">Comenzar</a>
          </div>
        </div>
      </div>

    </section><!-- /Sección Hero -->

    <!-- Sección Servicios -->
<section id="services" class="services section">

  <!-- Título de Sección -->
  <div class="container section-title" data-aos="fade-up">
    <span>Servicios</span>
    <h2>Servicios</h2>
    <p>Brindamos asesoría y representación legal en las principales áreas del derecho, siempre con compromiso, ética y soluciones efectivas.</p>
  </div><!-- Fin Título de Sección -->

  <div class="container">

    <div class="row gy-4">

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="service-item position-relative">
          <div class="icon">
            <i class="bi bi-people"></i>
          </div>
          <a href="#" class="stretched-link">
            <h3>Derecho Familiar</h3>
          </a>
          <p>Divorcios, custodias, alimentos y procesos de adopción con un trato humano y confidencial.</p>
        </div>
      </div><!-- Fin Servicio -->

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="service-item position-relative">
          <div class="icon">
            <i class="bi bi-briefcase"></i>
          </div>
          <a href="#" class="stretched-link">
            <h3>Derecho Laboral</h3>
          </a>
          <p>Defensa de trabajadores y empresas en casos de despidos, contratos y reclamaciones.</p>
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
          <p>Constitución de sociedades, asesoría contractual y cumplimiento normativo para empresas.</p>
        </div>
      </div><!-- Fin Servicio -->

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="service-item position-relative">
          <div class="icon">
            <i class="bi bi-house"></i>
          </div>
          <a href="#" class="stretched-link">
            <h3>Derecho Civil</h3>
          </a>
          <p>Contratos, propiedad, arrendamientos y resolución de conflictos civiles.</p>
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
          <p>Defensa legal en procesos judiciales y representación en casos penales.</p>
        </div>
      </div><!-- Fin Servicio -->

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
        <div class="service-item position-relative">
          <div class="icon">
            <i class="bi bi-chat-dots"></i>
          </div>
          <a href="#" class="stretched-link">
            <h3>Mediación y Negociación</h3>
          </a>
          <p>Resolución de conflictos mediante acuerdos justos y estrategias de conciliación.</p>
        </div>
      </div><!-- Fin Servicio -->

    </div>

  </div>

</section><!-- /Sección Servicios -->

<section id="call-to-action" class="call-to-action section dark-background">
  <div class="overlay"></div> <!-- capa para atenuar -->

  <img src="{{ asset('montano_assets/assets/img/cta-bg.jpg') }}" alt="">

  <div class="container">
    <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
      <div class="col-xl-10">
        <div class="text-center">
          <h3 style="color: white">Reconocimientos</h3>
          <p style="color: white">
            Nos enorgullece compartir los logros y reconocimientos que hemos obtenido a lo largo de nuestra trayectoria.
            Estos premios no es solo un reflejo del arduo trabajo y dedicación de nuestro talentoso equipo, sino también
            un testimonio de nuestro compromiso con la excelencia y con nuestros clientes.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

    <!-- Sección Clientes -->
    <section id="clients" class="clients section light-background">

      <div class="container">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 2,
                  "spaceBetween": 40
                },
                "480": {
                  "slidesPerView": 3,
                  "spaceBetween": 60
                },
                "640": {
                  "slidesPerView": 4,
                  "spaceBetween": 80
                },
                "992": {
                  "slidesPerView": 6,
                  "spaceBetween": 120
                }
              }
            }
          </script>
          <div class="swiper-wrapper align-items-center">
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-1.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-2.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-3.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-4.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-5.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-6.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-7.png') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('montano_assets/assets/img/clients/client-8.png') }}" class="img-fluid" alt=""></div>
          </div>
        </div>

      </div>

    </section><!-- /Sección Clientes -->
<!-- Sección Tarjetas -->
<section id="cards" class="cards section">

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




    <!-- Sección Llamado a la Acción -->







@endsection
