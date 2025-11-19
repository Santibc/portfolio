@extends('landing_page.layout')

@section('content')

    <!-- Hero Section -->
    <section id="hero" class="hero section">

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1>{{ $homeConfig->hero_title ?? 'CLEAN ME' }}</h1>
                        <h2 class="mb-4"><span>{{ $homeConfig->hero_subtitle ?? 'Top Quality Guaranteed' }}</span></h2>
                        <p>{{ $homeConfig->hero_description ?? 'At Clean Me, we believe that putting in a lot of hard work ensures the best and fastest service.' }}
                        </p>
                        <div class="hero-actions justify-content-center justify-content-lg-start">
                            <a href="{{ $homeConfig->hero_services_button_url ?? route('servicios') }}"
                                class="btn-primary">Our Services</a>
                            <a href="{{ $homeConfig->hero_estimate_button_url ?? '#contact' }}"
                                class="btn-primary scrollto">Get Free Estimate</a>
                        </div>

                        <!-- Company Values Icons -->
                        @if ($heroValues && $heroValues->count() > 0)
                            <div class="hero-values mt-5">
                                <div class="row gy-4">
                                    @foreach ($heroValues as $heroValue)
                                        <div class="col-6 col-md-3">
                                            <div class="value-card">
                                                <div class="value-icon">
                                                    <i class="{{ $heroValue->icon_class }}"></i>
                                                </div>
                                                <h4>{{ $heroValue->title }}</h4>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div><!-- End Hero Values -->
                        @endif

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="{{ $homeConfig && $homeConfig->hero_image_path ? asset($homeConfig->hero_image_path) : asset('images/mujer.png') }}"
                            class="img-fluid floating" alt="Clean Me Services">
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
                    @if ($homeConfig && $homeConfig->facebook_url)
                        <div class="swiper-slide">
                            <a href="{{ $homeConfig->facebook_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('images/facebook.png') }}" class="img-fluid" alt="Facebook">
                            </a>
                        </div>
                    @endif
                    @if ($homeConfig && $homeConfig->instagram_url)
                        <div class="swiper-slide">
                            <a href="{{ $homeConfig->instagram_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('images/instagram.png') }}" class="img-fluid" alt="Instagram">
                            </a>
                        </div>
                    @endif
                    @if ($homeConfig && $homeConfig->linkedin_url)
                        <div class="swiper-slide">
                            <a href="{{ $homeConfig->linkedin_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('images/link.png') }}" class="img-fluid" alt="LinkedIn">
                            </a>
                        </div>
                    @endif
                    @if ($homeConfig && $homeConfig->youtube_url)
                        <div class="swiper-slide">
                            <a href="{{ $homeConfig->youtube_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('images/youtube.png') }}" class="img-fluid" alt="TikTok">
                            </a>
                        </div>
                    @endif
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
                        <img src="{{ $homeConfig && $homeConfig->about_image_path ? asset($homeConfig->about_image_path) : asset('images/paginaanterior/imagenluegodeltitulo.avif') }}"
                            alt="{{ $homeConfig->about_title ?? 'We Are Clean Me' }}" class="img-fluid">
                    </div>
                </div>

                <!-- Content Column -->
                <div class="col-lg-6">
                    <div class="content">
                        <h2>{{ $homeConfig->about_title ?? 'WE ARE CLEAN ME' }}</h2>
                        <p class="lead">
                            {{ $homeConfig->about_lead ?? 'Excellence and professionalism are first when it comes to our Residential and Commercial Cleaning Services.' }}
                        </p>

                        <p>{{ $homeConfig->about_description ?? 'We are constantly improving our services, staying up-to-date on all the latest industry advancements, and bringing our knowledge to your doorstep.' }}
                        </p>

                        <!-- Stats Row -->
                        <div class="stats-row">
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0"
                                        data-purecounter-end="{{ $homeConfig->about_years_experience ?? 16 }}"
                                        data-purecounter-duration="1" class="purecounter"></span>+</h3>
                                <p>Years Experience</p>
                            </div>
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0"
                                        data-purecounter-end="{{ $homeConfig->about_happy_clients ?? 500 }}"
                                        data-purecounter-duration="1" class="purecounter"></span>+</h3>
                                <p>Happy Clients</p>
                            </div>
                            <div class="stat-item">
                                <h3><span data-purecounter-start="0"
                                        data-purecounter-end="{{ $homeConfig->about_client_satisfaction ?? 100 }}"
                                        data-purecounter-duration="1" class="purecounter"></span>%</h3>
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

                    @if ($testimonials && $testimonials->count() > 0)
                        @foreach ($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testimonial-header">
                                        <div class="rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="testimonial-body">
                                        <p>"{{ $testimonial->testimonial }}"</p>
                                    </div>
                                    <div class="testimonial-footer">
                                        <h5>{{ $testimonial->client_name }}</h5>
                                        @if ($testimonial->client_role)
                                            <span>{{ $testimonial->client_role }}</span>
                                        @endif
                                        <div class="quote-icon">
                                            <i class="bi bi-chat-quote-fill"></i>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Testimonial -->
                        @endforeach
                    @endif

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
            <p>{{ $contactInfo->description ?? 'Contact us today for a free estimate. We are here to help you with all your cleaning needs.' }}
            </p>
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
                                <input type="text" name="name" class="form-control" placeholder="First Name"
                                    required="">
                            </div>

                            <div class="col-md-6">
                                <input type="text" name="lastname" class="form-control" placeholder="Last Name"
                                    required="">
                            </div>

                            <div class="col-md-12">
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                    required="">
                            </div>

                            <div class="col-md-12">
                                <input type="tel" class="form-control" name="phone" placeholder="Phone"
                                    required="">
                            </div>

                            <div class="col-md-12">
                                <textarea class="form-control" name="message" rows="6" placeholder="Comments (Optional)"></textarea>
                            </div>

                            <div class="col-md-12 text-center">
                                <div class="loading" style="display: none;">Sending...</div>
                                <div class="error-message" style="display: none;"></div>
                                <div class="sent-message" style="display: none;">Your message has been sent successfully!
                                </div>

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

                    fetch('{{ route('contact.send') }}', {
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
