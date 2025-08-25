@extends('landing_page.layout')

@section('content')

    <section style="padding: 160px 0 100px 0;" id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Nosotros<br></span>
        <h2>Nosotros<br></h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="{{ asset('images/justicia.jpg') }}" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Propósito: </h3>
            <p class="fst-italic">
               Ofrecer soluciones jurídicas confiables, de alta calidad y orientadas al éxito de nuestros clientes.</p>
            <h3>Misión:</h3>
            <p class="fst-italic">
               
Somos una firma legal que fortalece el crecimiento de sus clientes mediante asesoría estratégica, basada en la confianza, el compromiso y la excelencia profesional. Contamos con un equipo diverso y altamente especializado que colabora de forma articulada y se alinea con las metas del cliente para maximizar su valor jurídico y su impacto social.
            </p>
            <h3>Visión:</h3>
            <p class="fst-italic">
               
Construir relaciones de confianza con empresas y emprendedores, siendo la firma legal que los acompaña con cercanía, comunicación transparente y soluciones jurídicas que generan impacto.</p>

          </div>

        </div>

      </div>

    </section><!-- /About Section -->


@endsection
