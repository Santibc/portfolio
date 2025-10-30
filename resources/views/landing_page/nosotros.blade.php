@extends('landing_page.layout')

@section('content')

    <section style="padding: 160px 0 100px 0;" id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>{{ $about->page_title ?? 'About Us' }}</h2>
        <p>{{ $about->page_subtitle ?? 'Learn more about Clean Me' }}</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row align-items-center">

          <!-- Image Column -->
          <div class="col-lg-6">
            <div class="about-image">
              <img src="{{ $about && $about->main_image_path ? asset($about->main_image_path) : asset('images/limpieza.png') }}" class="img-fluid" alt="{{ $about->page_title ?? 'About Us' }}">
            </div>
          </div>

          <!-- Content Column -->
          <div class="col-lg-6">
            <div class="content">
              <h3>{{ $about->purpose_title ?? 'Our Purpose' }}</h3>
              <p class="lead">
                {{ $about->purpose_content ?? 'Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.' }}
              </p>

              <h3>{{ $about->mission_title ?? 'Our Mission' }}</h3>
              <p>
                {{ $about->mission_content ?? 'Since 2009, our goal has remained the same—to provide reliable services and make sure our clients know we are professionals they can trust. We focus on delivering top-quality cleaning solutions that exceed expectations.' }}
              </p>

              <h3>{{ $about->vision_title ?? 'Our Vision' }}</h3>
              <p>
                {{ $about->vision_content ?? 'We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep. We aim to be the most trusted cleaning company in Wisconsin.' }}
              </p>

              <!-- Stats Row -->
              <div class="stats-row">
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="{{ $about->stats_years_experience ?? 16 }}" data-purecounter-duration="1" class="purecounter"></span>+</h3>
                  <p>Years Experience</p>
                </div>
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="{{ $about->stats_happy_clients ?? 500 }}" data-purecounter-duration="1" class="purecounter"></span>+</h3>
                  <p>Happy Clients</p>
                </div>
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="{{ $about->stats_client_satisfaction ?? 100 }}" data-purecounter-duration="1" class="purecounter"></span>%</h3>
                  <p>Client Satisfaction</p>
                </div>
              </div><!-- End Stats Row -->

              <!-- CTA Button -->
              <div class="cta-wrapper">
                <a href="{{ route('contacto') }}" class="btn-cta">
                  <span>Contact Us Today</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </div>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Values Section -->
    <section class="section light-background">
      <div class="container">
        <div class="row gy-4">

          <div class="col-lg-4 col-md-6">
            <div class="service-card text-center">
              <div class="service-icon">
                <i class="{{ $about->value1_icon ?? 'bi bi-award' }}"></i>
              </div>
              <h3>{{ $about->value1_title ?? 'Quality Assurance' }}</h3>
              <p>{{ $about->value1_description ?? 'We use eco-friendly cleaning products and employ highly trained professionals to deliver exceptional results every time.' }}</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="service-card text-center">
              <div class="service-icon">
                <i class="{{ $about->value2_icon ?? 'bi bi-people' }}"></i>
              </div>
              <h3>{{ $about->value2_title ?? 'Customer Focus' }}</h3>
              <p>{{ $about->value2_description ?? 'Your satisfaction is our priority. We tailor our services to meet your specific needs and exceed your expectations.' }}</p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="service-card text-center">
              <div class="service-icon">
                <i class="{{ $about->value3_icon ?? 'bi bi-clock-history' }}"></i>
              </div>
              <h3>{{ $about->value3_title ?? 'Reliability' }}</h3>
              <p>{{ $about->value3_description ?? 'Since 2009, we\'ve built our reputation on consistent, dependable service that you can count on.' }}</p>
            </div>
          </div>

        </div>
      </div>
    </section><!-- /Values Section -->

@endsection
