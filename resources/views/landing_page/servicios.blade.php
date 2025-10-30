@extends('landing_page.layout')

@section('content')
    <!-- Services Section -->
    <section id="services" style="padding: 160px 0 100px 0;" class="services section">

      <style>
        .services .btn-primary {
          background: var(--accent-color);
          color: var(--contrast-color);
          padding: 14px 35px;
          border-radius: 8px;
          font-weight: 500;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 0.8px;
          transition: all 0.3s ease;
          box-shadow: 0 10px 30px color-mix(in srgb, var(--accent-color), transparent 70%);
          border: 2px solid transparent;
          display: inline-block;
          text-decoration: none;
        }

        .services .btn-primary:hover {
          background: transparent;
          color: var(--accent-color);
          border-color: var(--accent-color);
          box-shadow: 0 15px 40px color-mix(in srgb, var(--accent-color), transparent 80%);
          transform: translateY(-2px);
        }

        .services .btn-outline {
          background: transparent;
          color: var(--accent-color);
          padding: 14px 35px;
          border-radius: 8px;
          font-weight: 500;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 0.8px;
          transition: all 0.3s ease;
          border: 2px solid var(--accent-color);
          display: inline-block;
          text-decoration: none;
        }

        .services .btn-outline:hover {
          background: var(--accent-color);
          color: var(--contrast-color);
          box-shadow: 0 15px 40px color-mix(in srgb, var(--accent-color), transparent 80%);
          transform: translateY(-2px);
        }
      </style>

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Our Services</h2>
        <p>Professional cleaning solutions for your residential and commercial needs</p>
      </div><!-- End Section Title -->

      <div class="container">

        @if($services && $services->count() > 0)
          <div class="row gy-4 mb-5">
            @foreach($services as $service)
              <div class="col-lg-6 col-md-6">
                <div class="service-card">
                  <div class="service-icon">
                    <i class="{{ $service->icon_class }}"></i>
                  </div>
                  <h3>{{ $service->title }}</h3>
                  <p>{{ $service->description }}</p>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="alert alert-info text-center">
            <p class="mb-0">No hay servicios disponibles en este momento. Por favor, contacte con nosotros para más información.</p>
          </div>
        @endif

        <!-- Additional Note -->
        <div class="row mt-5">
          <div class="col-12">
            <div class="alert alert-info text-center">
              <p class="mb-0"><strong>Eco-Friendly Commitment:</strong> In both our commercial and residential cleaning services, we use eco-friendly cleaning products, employ highly trained and professional staff, and tailor our services to meet your specific needs. Our goal is to provide a clean, healthy, and welcoming environment for your home or business.</p>
            </div>
          </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
          <div class="col-12 text-center">
            <h3 class="mb-4">Ready to Get Started?</h3>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
              <a href="{{ route('services.calculator') }}" class="btn-primary">
                <i class="bi bi-calculator"></i> Get a Quote
              </a>
              <a href="{{ route('contacto') }}" class="btn-outline">
                <i class="bi bi-envelope"></i> Contact Us
              </a>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Services Section -->

@endsection
