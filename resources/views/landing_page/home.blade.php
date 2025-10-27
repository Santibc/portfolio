@extends('landing_page.layout')

@section('content')

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content">
              <h1>{{ $config->company_name ?? 'Adelaide' }}</h1>
              <h2 class="mb-4"><span>Top Quality Guaranteed</span></h2>
              <p>{{ $config->company_description ?? 'At Clean Me, we believe that putting in a lot of hard work ensures the best and fastest service. We are here to provide you the most suitable and highest solutions for your needs with a professional estimation.' }}</p>
              <div class="hero-actions justify-content-center justify-content-lg-start">
                <a href="{{ route('servicios') }}" class="btn-primary">Our Services</a>
                <a href="#contact" class="btn-primary scrollto">Get Free Estimate</a>
              </div>

              <!-- Company Values Icons -->
              <div class="hero-values mt-5">
                <div class="row gy-4">
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-shield-check"></i>
                      </div>
                      <h4>Trusted & Insured</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-lightning-charge"></i>
                      </div>
                      <h4>Fast Service</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-award"></i>
                      </div>
                      <h4>Professional</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-tree"></i>
                      </div>
                      <h4>Eco-Friendly</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-patch-check"></i>
                      </div>
                      <h4>Guaranteed</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-clock"></i>
                      </div>
                      <h4>Flexible Hours</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-people"></i>
                      </div>
                      <h4>Experienced Team</h4>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="value-card">
                      <div class="value-icon">
                        <i class="bi bi-star"></i>
                      </div>
                      <h4>Quality Work</h4>
                    </div>
                  </div>
                </div>
              </div><!-- End Hero Values -->

            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image">
              <img src="{{ asset('images/mujer.png') }}" class="img-fluid floating" alt="Clean Me Services">
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->
    <section id="social-media" class="clients section social-media-section">

      <div class="container">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 3000
              },
              "slidesPerView": "auto",
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
                  "slidesPerView": 4,
                  "spaceBetween": 100
                }
              }
            }
          </script>
          <div class="swiper-wrapper align-items-center">
            <div class="swiper-slide">
              <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/facebook.png') }}" class="img-fluid" alt="Facebook">
              </a>
            </div>
            <div class="swiper-slide">
              <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/instagram.png') }}" class="img-fluid" alt="Instagram">
              </a>
            </div>
            <div class="swiper-slide">
              <a href="linkedin.com/home?originalSubdomain=co" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/link.png') }}" class="img-fluid" alt="Twitter">
              </a>
            </div>
            <div class="swiper-slide">
              <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/youtube.png') }}" class="img-fluid" alt="YouTube">
              </a>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Social Media Section -->
    <!-- Social Media Section -->


    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row align-items-center">

          <!-- Image Column -->
          <div class="col-lg-6">
            <div class="about-image">
              <img src="{{ asset('images/paginaanterior/imagenluegodeltitulo.avif') }}" alt="We Are Clean Me" class="img-fluid">
            </div>
          </div>

          <!-- Content Column -->
          <div class="col-lg-6">
            <div class="content">
              <h2>WE ARE CLEAN ME</h2>
              <p class="lead">Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.</p>

              <p>We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep. Since 2009, our goal has remained the same—to provide reliable services and make sure our clients know we are professionals they can trust.</p>

              <!-- Stats Row -->
              <div class="stats-row">
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="16" data-purecounter-duration="1" class="purecounter"></span>+</h3>
                  <p>Years Experience</p>
                </div>
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="1" class="purecounter"></span>+</h3>
                  <p>Happy Clients</p>
                </div>
                <div class="stat-item">
                  <h3><span data-purecounter-start="0" data-purecounter-end="100" data-purecounter-duration="1" class="purecounter"></span>%</h3>
                  <p>Client Satisfaction</p>
                </div>
              </div><!-- End Stats Row -->

              <!-- CTA Button -->
              <div class="cta-wrapper">
                <a href="{{ route('nosotros') }}" class="btn-cta">
                  <span>Learn More About Us</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </div>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

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

      </div>

    </section><!-- /Services Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Client Testimonials</h2>
        <p>What our satisfied clients say about our cleaning services</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="testimonial-slider swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": 1,
              "spaceBetween": 30,
              "navigation": {
                "nextEl": ".swiper-button-next",
                "prevEl": ".swiper-button-prev"
              },
              "breakpoints": {
                "768": {
                  "slidesPerView": 2
                },
                "1200": {
                  "slidesPerView": 3
                }
              }
            }
          </script>

          <div class="swiper-wrapper">

            <!-- Testimonial 1 -->
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="testimonial-header">
                  <div class="rating">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
                <div class="testimonial-body">
                  <p>"Patti is very profession and thorough. She spent the whole day at the house for our first initial deep clean. Loved seeing all the ways to fold towels and Kleenex. Our woodwork and blinds look beautiful and dust free. Highly recommend!"</p>
                </div>
                <div class="testimonial-footer">
                  <h5>Rebekah Bower</h5>
                  <span>Wisconsin</span>
                  <div class="quote-icon">
                    <i class="bi bi-chat-quote-fill"></i>
                  </div>
                </div>
              </div>
            </div><!-- End Testimonial -->

            <!-- Testimonial 2 -->
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="testimonial-header">
                  <div class="rating">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
                <div class="testimonial-body">
                  <p>"Patty and co-worker did a great deep clean of my home! They were very professional and returned calls and texts immediately."</p>
                </div>
                <div class="testimonial-footer">
                  <h5>Maria McClellan</h5>
                  <span>Wisconsin</span>
                  <div class="quote-icon">
                    <i class="bi bi-chat-quote-fill"></i>
                  </div>
                </div>
              </div>
            </div><!-- End Testimonial -->

            <!-- Testimonial 3 -->
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="testimonial-header">
                  <div class="rating">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
                <div class="testimonial-body">
                  <p>"After being laid up after a surgery I call Patti to ask about her service, she came out that day, gave me a quote. I had my house cleaned in a few days to absolute perfection. 100% recommend this cleaning service."</p>
                </div>
                <div class="testimonial-footer">
                  <h5>Redwood Retreat</h5>
                  <span>Wisconsin</span>
                  <div class="quote-icon">
                    <i class="bi bi-chat-quote-fill"></i>
                  </div>
                </div>
              </div>
            </div><!-- End Testimonial -->

          </div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Get Your Free Estimate</h2>
        <p>{{ $contactInfo->description ?? 'Contact us today for a free estimate. We are here to help you with all your cleaning needs.' }}</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6">

            <div class="row gy-4">
              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-envelope"></i>
                  <h3>Email</h3>
                  <p>{{ $contactInfo->email ?? 'info@cleanme.com' }}</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-telephone"></i>
                  <h3>Phone</h3>
                  <p>{{ $contactInfo->phone ?? '+1 (555) 000-0000' }}</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-12">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-geo-alt"></i>
                  <h3>Location</h3>
                  <p>{{ $contactInfo->address ?? 'Wisconsin, USA' }}</p>
                </div>
              </div><!-- End Info Item -->
            </div>

          </div>

          <div class="col-lg-6">
            <form id="contactForm" class="php-email-form">
              @csrf
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="First Name" required="">
                </div>

                <div class="col-md-6">
                  <input type="text" name="lastname" class="form-control" placeholder="Last Name" required="">
                </div>

                <div class="col-md-12">
                  <input type="email" class="form-control" name="email" placeholder="Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="tel" class="form-control" name="phone" placeholder="Phone" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Comments (Optional)"></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading" style="display: none;">Sending...</div>
                  <div class="error-message" style="display: none;"></div>
                  <div class="sent-message" style="display: none;">Your message has been sent successfully!</div>

                  <button type="submit" class="btn-primary">Submit Request</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const loadingDiv = this.querySelector('.loading');
            const errorDiv = this.querySelector('.error-message');
            const successDiv = this.querySelector('.sent-message');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Reset states
            loadingDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            submitBtn.disabled = true;

            fetch('{{ route("contact.send") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                submitBtn.disabled = false;

                if (data.success) {
                    successDiv.style.display = 'block';
                    contactForm.reset();
                    setTimeout(() => {
                        successDiv.style.display = 'none';
                    }, 5000);
                } else {
                    errorDiv.textContent = data.error || 'Error sending message';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                submitBtn.disabled = false;
                errorDiv.textContent = 'Error sending message';
                errorDiv.style.display = 'block';
            });
        });
    }
});
</script>
@endpush
