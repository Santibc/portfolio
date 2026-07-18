@extends('landing_page.layout')

@section('content')
    <style>
        .post-hero {
            padding: 140px 0 20px 0;
            background: linear-gradient(135deg, rgba(114,135,156,0.05) 0%, rgba(70,205,207,0.08) 100%);
        }
        .post-hero .breadcrumb-nav { font-size: 0.85rem; color: #6c7684; margin-bottom: 12px; }
        .post-hero .breadcrumb-nav a { color: #6c7684; text-decoration: none; }
        .post-hero .breadcrumb-nav .sep { margin: 0 8px; color: #adb5bd; }
        .post-hero h1 { font-weight: 700; font-size: 2.2rem; line-height: 1.25; }
        .post-hero .meta { color: #6c7684; font-size: 0.9rem; margin-top: 12px; }
        .post-hero .meta .cat {
            display: inline-block;
            padding: 3px 12px;
            background: var(--accent-color);
            color: #fff;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-right: 10px;
        }
        .post-cover {
            max-height: 460px;
            overflow: hidden;
            border-radius: 12px;
            margin: 20px 0;
        }
        .post-cover img { width: 100%; height: 100%; object-fit: cover; }
        .post-body { padding: 30px 0 60px; }
        .post-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #374151;
        }
        .post-content h2, .post-content h3 {
            margin-top: 1.6rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }
        .post-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
        .post-content ul, .post-content ol { padding-left: 1.4rem; margin-bottom: 1.2rem; }
        .post-content blockquote {
            border-left: 4px solid var(--accent-color);
            padding: 12px 20px;
            margin: 20px 0;
            background: #f8f9fa;
            color: #4b5563;
            font-style: italic;
        }
        .sidebar-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 22px;
        }
        .sidebar-card h4 { font-size: 1.05rem; font-weight: 600; margin-bottom: 12px; }
        .sidebar-list a {
            display: block;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f5;
            color: #374151;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .sidebar-list a:hover { color: var(--accent-color); }
        .sidebar-list a:last-child { border-bottom: 0; }
        .cta-card {
            background: linear-gradient(135deg, var(--accent-color), #3ab5b8);
            color: #fff;
            border-radius: 12px;
            padding: 22px;
            text-align: center;
        }
        .cta-card h4 { color: #fff; font-weight: 600; }
        .cta-card p { color: rgba(255,255,255,0.9); font-size: 0.9rem; }
        .cta-card a {
            display:inline-block;
            background: #fff;
            color: var(--accent-color);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .related-posts { background: #f8f9fa; padding: 60px 0; }
        .related-posts h2 { text-align: center; font-weight: 700; margin-bottom: 30px; }
        .related-posts .post-card {
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        .related-posts .post-card .body { padding: 18px 20px; }
        .related-posts .post-card h3 { font-size: 1.05rem; font-weight: 600; margin-bottom: 8px; }
        .related-posts .post-card .cover {
            aspect-ratio: 16 / 9;
            overflow:hidden; background: #eaecef;
            display:flex; align-items:center; justify-content:center; color:#adb5bd;
        }
        .related-posts .post-card .cover img { width:100%; height:100%; object-fit:cover; }
    </style>

    <section class="post-hero">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="breadcrumb">
                <a href="{{ route('welcome') }}">Home</a>
                <span class="sep">/</span>
                <a href="{{ route('blog.index') }}">Blog</a>
                @if($post->category)
                    <span class="sep">/</span>
                    <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                @endif
                <span class="sep">/</span>
                <span>{{ Str::limit($post->title, 60) }}</span>
            </nav>
            <h1>{{ $post->title }}</h1>
            <div class="meta">
                @if($post->category)
                    <span class="cat">{{ $post->category->name }}</span>
                @endif
                <i class="bi bi-calendar3"></i> {{ optional($post->published_at)->format('M d, Y') }}
                &middot; <i class="bi bi-clock"></i> {{ $post->reading_time }} min read
                @if($post->author_name)
                    &middot; <i class="bi bi-person"></i> {{ $post->author_name }}
                @endif
            </div>
        </div>
    </section>

    @if($post->cover_image_path)
        <div class="container">
            <div class="post-cover">
                <img src="{{ asset($post->cover_image_path) }}" alt="{{ $post->title }}">
            </div>
        </div>
    @endif

    <section class="post-body">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <article class="post-content">
                        {!! $post->content_html !!}
                    </article>
                </div>
                <aside class="col-lg-4">
                    @if($relatedServices->count() > 0)
                        <div class="sidebar-card">
                            <h4><i class="bi bi-list-check me-2"></i>Our services</h4>
                            <div class="sidebar-list">
                                @foreach($relatedServices as $svc)
                                    <a href="{{ route('services.show', $svc->slug) }}">
                                        <i class="{{ $svc->icon_class }} me-2"></i>{{ $svc->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="cta-card">
                        <h4 class="mb-2">Ready to book?</h4>
                        <p class="mb-3">Get an instant quote in under 2 minutes.</p>
                        <a href="{{ route('services.calculator') }}"><i class="bi bi-calculator"></i> Get a Quote</a>
                    </div>

                    @if($categories->count() > 0)
                        <div class="sidebar-card mt-3">
                            <h4><i class="bi bi-folder me-2"></i>Categories</h4>
                            <div class="sidebar-list">
                                @foreach($categories as $cat)
                                    <a href="{{ route('blog.category', $cat->slug) }}">{{ $cat->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>

    @if($relatedPosts->count() > 0)
        <section class="related-posts">
            <div class="container">
                <h2>Related articles</h2>
                <div class="row g-4">
                    @foreach($relatedPosts as $rp)
                        <div class="col-md-4">
                            <article class="post-card">
                                <a href="{{ route('blog.show', $rp->slug) }}" class="cover">
                                    @if($rp->cover_image_path)
                                        <img src="{{ asset($rp->cover_image_path) }}" alt="{{ $rp->title }}">
                                    @else
                                        <i class="bi bi-journal-text"></i>
                                    @endif
                                </a>
                                <div class="body">
                                    <h3>
                                        <a href="{{ route('blog.show', $rp->slug) }}" class="text-decoration-none text-dark">{{ $rp->title }}</a>
                                    </h3>
                                    <p class="text-muted small mb-0">{{ Str::limit(strip_tags($rp->excerpt ?: $rp->content_html), 100) }}</p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
