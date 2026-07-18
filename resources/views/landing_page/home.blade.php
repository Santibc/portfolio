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
                            <a href="{{ route('services.calculator') }}"
                                class="btn-primary">Book Now</a>
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
                                <img src="{{ asset('images/facebook.svg') }}" class="img-fluid" alt="Facebook">
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

        <style>
            #testimonials {
                padding: 80px 0 90px;
                background: linear-gradient(180deg, #f8f6f2 0%, #fdfcfa 100%);
                position: relative;
                overflow: hidden;
            }
            #testimonials::before {
                content: "";
                position: absolute;
                top: -80px; right: -80px;
                width: 260px; height: 260px;
                border-radius: 50%;
                background: rgba(70, 205, 207, 0.08);
                z-index: 0;
            }
            #testimonials::after {
                content: "";
                position: absolute;
                bottom: -100px; left: -60px;
                width: 220px; height: 220px;
                border-radius: 50%;
                background: rgba(114, 135, 156, 0.08);
                z-index: 0;
            }
            #testimonials .container { position: relative; z-index: 1; }

            #testimonials .section-title h2 {
                font-weight: 700;
                font-size: 2.2rem;
                color: #2d3a4a;
                margin-bottom: 8px;
            }
            #testimonials .section-title h2::after {
                content: "";
                display: block;
                width: 60px;
                height: 3px;
                margin: 14px auto 0;
                background: var(--accent-color);
                border-radius: 2px;
            }
            #testimonials .section-title p {
                color: #6c7684;
                font-size: 1rem;
                max-width: 560px;
                margin: 0 auto;
            }

            .testimonial-slider {
                padding: 50px 4px 60px !important;
            }
            .testimonial-slider .swiper-slide {
                height: auto;
                display: flex;
            }
            .testimonial-item {
                background: #ffffff;
                border-radius: 18px;
                padding: 32px 32px 28px;
                box-shadow: 0 10px 40px rgba(45, 58, 74, 0.08);
                border: 1px solid rgba(114, 135, 156, 0.08);
                position: relative;
                display: flex;
                flex-direction: column;
                width: 100%;
                transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            }
            .testimonial-item:hover {
                transform: translateY(-6px);
                box-shadow: 0 20px 50px rgba(45, 58, 74, 0.14);
                border-color: rgba(70, 205, 207, 0.2);
            }
            .testimonial-item .quote-mark {
                position: absolute;
                top: 22px;
                right: 26px;
                color: rgba(70, 205, 207, 0.18);
                font-size: 2.2rem;
                line-height: 1;
                pointer-events: none;
            }

            .testimonial-item .rating {
                display: flex;
                gap: 4px;
                margin-bottom: 18px;
            }
            .testimonial-item .rating i {
                color: #fbbf24;
                font-size: 1.15rem;
            }
            .testimonial-item .rating i.bi-star {
                color: #e5e7eb;
            }

            .testimonial-item .testimonial-body p {
                color: #3f4b5b;
                font-size: 1.02rem;
                line-height: 1.7;
                font-style: italic;
                margin-bottom: 24px;
                flex-grow: 1;
                min-height: 108px;
            }

            .testimonial-item .testimonial-footer {
                display: flex;
                align-items: center;
                gap: 14px;
                padding-top: 22px;
                border-top: 1px solid rgba(114, 135, 156, 0.15);
                margin-top: auto;
            }
            .testimonial-item .avatar {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--accent-color), #3ab5b8);
                color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-weight: 600;
                font-size: 1.15rem;
                text-transform: uppercase;
                flex-shrink: 0;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(70, 205, 207, 0.35);
            }
            .testimonial-item .avatar img {
                width: 100%; height: 100%; object-fit: cover;
            }
            .testimonial-item .client-info { flex-grow: 1; }
            .testimonial-item .client-info h5 {
                font-size: 1rem;
                font-weight: 600;
                color: #2d3a4a;
                margin: 0 0 2px 0;
            }
            .testimonial-item .client-info span {
                font-size: 0.85rem;
                color: #8892a0;
            }
            .testimonial-item .verified {
                color: var(--accent-color);
                font-size: 1rem;
            }

            .testimonial-slider .swiper-button-prev,
            .testimonial-slider .swiper-button-next {
                position: static;
                display: inline-flex;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #fff;
                color: var(--accent-color);
                margin: 0 8px;
                box-shadow: 0 4px 16px rgba(45, 58, 74, 0.12);
                transition: all 0.3s ease;
            }
            .testimonial-slider .swiper-button-prev:hover,
            .testimonial-slider .swiper-button-next:hover {
                background: var(--accent-color);
                color: #fff;
                transform: translateY(-2px);
            }
            .testimonial-slider .swiper-button-prev::after,
            .testimonial-slider .swiper-button-next::after {
                font-size: 1.1rem;
                font-weight: 700;
            }
            .testimonial-nav-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                margin-top: 30px;
            }

            @media (max-width: 768px) {
                #testimonials { padding: 60px 0 70px; }
                #testimonials .section-title h2 { font-size: 1.7rem; }
                .testimonial-item { padding: 34px 26px 26px; }
                .testimonial-item .testimonial-body p { min-height: auto; }
            }
        </style>

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
                "delay": 5000,
                "disableOnInteraction": false
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
                                    <i class="bi bi-quote quote-mark"></i>
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <div class="testimonial-body">
                                        <p>{{ $testimonial->testimonial }}</p>
                                    </div>
                                    <div class="testimonial-footer">
                                        <div class="avatar">
                                            @if($testimonial->client_image_path)
                                                <img src="{{ asset($testimonial->client_image_path) }}" alt="{{ $testimonial->client_name }}">
                                            @else
                                                {{ strtoupper(substr($testimonial->client_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="client-info">
                                            <h5>{{ $testimonial->client_name }}</h5>
                                            @if ($testimonial->client_role)
                                                <span>{{ $testimonial->client_role }}</span>
                                            @else
                                                <span>Verified customer</span>
                                            @endif
                                        </div>
                                        <div class="verified" title="Verified customer">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Testimonial -->
                        @endforeach
                    @endif

                </div>
            </div>

            <div class="testimonial-nav-wrapper">
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
