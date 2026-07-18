@extends('landing_page.layout')

@section('content')
    <style>
        .service-hero {
            padding: 160px 0 60px 0;
            background: linear-gradient(135deg, rgba(114,135,156,0.05) 0%, rgba(70,205,207,0.08) 100%);
        }
        .service-hero h1 {
            font-weight: 700;
            font-size: 2.4rem;
        }
        .service-hero .lead {
            color: #6c7684;
        }
        .service-hero .service-icon-lg {
            font-size: 4rem;
            color: var(--accent-color);
        }
        .service-content {
            padding: 40px 0 80px 0;
        }
        .service-content .content-body {
            font-size: 1.05rem;
            line-height: 1.75;
            color: #374151;
        }
        .service-content .content-body h2,
        .service-content .content-body h3 {
            margin-top: 1.6rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }
        .service-content .content-body ul,
        .service-content .content-body ol {
            padding-left: 1.4rem;
            margin-bottom: 1.2rem;
        }
        .service-sidebar-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            position: sticky;
            top: 100px;
        }
        .service-sidebar-card h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 14px;
        }
        .btn-book-service {
            background: var(--accent-color);
            color: var(--contrast-color);
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: inline-block;
            text-decoration: none;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-book-service:hover {
            background: #3ab5b8;
            color: #fff;
        }
        .related-list a {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f5;
            color: #374151;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .related-list a:hover { color: var(--accent-color); }
        .related-list a:last-child { border-bottom: 0; }
        .breadcrumb-nav {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .breadcrumb-nav a { color: #6c7684; text-decoration: none; }
        .breadcrumb-nav a:hover { color: var(--accent-color); }
        .breadcrumb-nav .sep { margin: 0 8px; color: #adb5bd; }
        .related-services {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .related-services .service-card {
            background: #fff;
            padding: 28px;
            border-radius: 12px;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #eef0f2;
        }
        .related-services .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }
        .related-services .service-card .service-icon {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 14px;
        }
        .related-services .service-card h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .related-services .service-card p {
            color: #6c7684;
            font-size: 0.95rem;
        }
        .related-services .service-card a.more {
            color: var(--accent-color);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>

    <section class="service-hero">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="breadcrumb">
                <a href="{{ route('welcome') }}">Home</a>
                <span class="sep">/</span>
                <a href="{{ route('servicios') }}">Services</a>
                <span class="sep">/</span>
                <span>{{ $service->title }}</span>
            </nav>
            <div class="row align-items-center">
                <div class="col-lg-8">
                    @if($service->icon_class)
                        <div class="service-icon-lg mb-3"><i class="{{ $service->icon_class }}"></i></div>
                    @endif
                    <h1>{{ $service->title }}</h1>
                    @if($service->subtitle)
                        <p class="lead mt-3">{{ $service->subtitle }}</p>
                    @endif
                </div>
                @if($service->hero_image_path)
                    <div class="col-lg-4 text-center">
                        <img src="{{ asset($service->hero_image_path) }}" alt="{{ $service->title }}" class="img-fluid rounded-3" style="max-height: 260px; object-fit: cover;">
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="service-content">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="content-body">
                        @if($service->content_html)
                            {!! $service->content_html !!}
                        @else
                            <p>{{ $service->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="service-sidebar-card">
                        <h4>Book this service</h4>
                        <p class="text-muted small mb-3">Get an instant quote and book online in under 2 minutes.</p>
                        <a href="{{ route('services.calculator') }}" class="btn-book-service">
                            <i class="bi bi-calculator"></i> Get a Quote
                        </a>

                        @if($relatedServices->count() > 0)
                            <hr class="my-4">
                            <h4>Other services</h4>
                            <div class="related-list">
                                @foreach($relatedServices as $rs)
                                    <a href="{{ route('services.show', $rs->slug) }}">
                                        <i class="{{ $rs->icon_class }} me-2"></i>{{ $rs->title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if($relatedPosts && $relatedPosts->count() > 0)
                            <hr class="my-4">
                            <h4>From the blog</h4>
                            <div class="related-list">
                                @foreach($relatedPosts as $post)
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        <i class="bi bi-journal-text me-2"></i>{{ $post->title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($relatedServices->count() > 0)
        <section class="related-services">
            <div class="container">
                <h2 class="text-center mb-5" style="font-weight:700;">Explore other services</h2>
                <div class="row g-4">
                    @foreach($relatedServices as $rs)
                        <div class="col-md-4">
                            <div class="service-card">
                                <div class="service-icon"><i class="{{ $rs->icon_class }}"></i></div>
                                <h3>{{ $rs->title }}</h3>
                                <p>{{ Str::limit($rs->short_description ?: $rs->description, 110) }}</p>
                                <a href="{{ route('services.show', $rs->slug) }}" class="more">Learn more <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
