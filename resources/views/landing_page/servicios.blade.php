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

        <h3 class="text-center mb-5">Commercial Cleaning Services</h3>

        <!-- Commercial Images Gallery -->


        <div class="row gy-4 mb-5">

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-fire"></i>
              </div>
              <h3>Hood Cleaning</h3>
              <p>Our professional team specializes in thorough hood cleaning for commercial kitchens. We ensure that your kitchen exhaust systems are free from grease buildup and fire hazards, keeping your workspace safe and compliant with regulations.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <h3>Sanitation Services</h3>
              <p>We provide comprehensive sanitation services to maintain a clean and hygienic environment in your commercial space. Our experts use industry-standard disinfectants to eliminate bacteria, viruses, and germs, ensuring the well-being of your staff and customers.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-thermometer-half"></i>
              </div>
              <h3>Stove and Grill Cleaning</h3>
              <p>We deep clean stoves and grills to remove grease, carbon buildup, and food residues. This not only enhances the longevity of your equipment but also ensures that your food preparation areas meet the highest hygiene standards.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-snow"></i>
              </div>
              <h3>Refrigerator Cleaning</h3>
              <p>We offer professional cleaning of commercial refrigerators, ensuring a clean and safe storage environment for your perishable goods. Our services help maintain food quality and reduce the risk of contamination.</p>
            </div>
          </div>

        </div>

        <h3 class="text-center mb-5">Residential Cleaning Services</h3>

        <!-- Residential Images Gallery -->


        <div class="row gy-4 mb-5">

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-house-door"></i>
              </div>
              <h3>Basic Cleaning</h3>
              <p>Our basic residential cleaning service covers essential tasks like dusting, vacuuming, mopping, and sanitizing common living areas, ensuring a clean and tidy home for your everyday comfort.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-stars"></i>
              </div>
              <h3>Deep Cleaning</h3>
              <p>For a more thorough and comprehensive clean, our deep cleaning service goes beyond the basics. We pay attention to every nook and cranny, tackling accumulated grime, dirt, and dust. Ideal for periodic deep cleans or when moving in/out.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-balloon"></i>
              </div>
              <h3>Special Occasions</h3>
              <p>Whether you're hosting a party, celebrating a special event, or having guests over, our special occasion cleaning service ensures your home is spotless and ready to impress. We'll take care of the cleaning so you can focus on the celebration.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-6">
            <div class="service-card">
              <div class="service-icon">
                <i class="bi bi-tools"></i>
              </div>
              <h3>Construction Cleaning</h3>
              <p>After a construction or renovation project, our construction cleaning service helps you rid your home of construction debris, dust, and dirt. We'll leave your space clean, safe, and ready for you to enjoy.</p>
            </div>
          </div>

        </div>

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
