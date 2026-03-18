@extends('landing_page.layout')

@section('content')

    {{-- ===== HERO WITH FEATURED IMAGE ===== --}}
    <section class="post-hero" @if($post->featured_image) style="background-image: url('{{ asset($post->featured_image) }}')" @endif>
        <div class="post-hero-overlay"></div>
        <div class="post-hero-content">
            @if ($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}" class="post-hero-badge reveal">{{ $post->category->name }}</a>
            @endif
            <h1 class="post-hero-title reveal">{{ $post->title }}</h1>
            <div class="post-hero-meta reveal">
                <span><i class="bi bi-calendar3"></i> {{ $post->published_at ? $post->published_at->format('d M, Y') : $post->created_at->format('d M, Y') }}</span>
                @if ($post->reading_time)
                    <span><i class="bi bi-clock"></i> {{ $post->reading_time }} min de lectura</span>
                @endif
                @if ($post->author)
                    <span><i class="bi bi-person"></i> {{ $post->author->name }}</span>
                @endif
            </div>
        </div>
    </section>

    {{-- ===== BREADCRUMBS ===== --}}
    <div class="post-breadcrumbs">
        <div class="container">
            <a href="{{ route('welcome') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('blog.index') }}">Blog</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ Str::limit($post->title, 40) }}</span>
        </div>
    </div>

    {{-- ===== ARTICLE CONTENT ===== --}}
    <section class="section post-section">
        <div class="container">
            <article class="post-article reveal">

                {{-- Author Info Bar --}}
                <div class="post-author-bar">
                    <div class="post-author-info">
                        <div class="post-author-avatar">
                            @if ($post->author && $post->author->profile_photo)
                                <img src="{{ asset($post->author->profile_photo) }}" alt="{{ $post->author->name }}">
                            @else
                                <i class="bi bi-person-fill"></i>
                            @endif
                        </div>
                        <div>
                            <span class="post-author-name">{{ $post->author->name ?? 'Manzer Agroforestal' }}</span>
                            <span class="post-author-date">
                                {{ $post->published_at ? $post->published_at->format('d \d\e F, Y') : $post->created_at->format('d \d\e F, Y') }}
                                @if ($post->category)
                                    &middot; <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Article Body --}}
                <div class="post-body">
                    {!! $post->body !!}
                </div>

                {{-- Tags --}}
                @if ($post->tags && $post->tags->count() > 0)
                    <div class="post-tags">
                        <span class="post-tags-label"><i class="bi bi-tags"></i> Etiquetas:</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="post-tag-pill">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif

                {{-- Share Buttons --}}
                <div class="post-share">
                    <span class="post-share-label">Compartir:</span>
                    <div class="post-share-buttons">
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . route('blog.show', $post->slug)) }}"
                           target="_blank" rel="noopener" class="share-btn share-whatsapp" aria-label="Compartir en WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank" rel="noopener" class="share-btn share-facebook" aria-label="Compartir en Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank" rel="noopener" class="share-btn share-twitter" aria-label="Compartir en Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank" rel="noopener" class="share-btn share-linkedin" aria-label="Compartir en LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>

            </article>
        </div>
    </section>

    {{-- ===== RELATED POSTS ===== --}}
    @if ($relatedPosts->count() > 0)
        <section class="section section-dark post-related-section">
            <div class="container">
                <div style="text-align: center; margin-bottom: 50px;">
                    <span class="section-label reveal">Sigue leyendo</span>
                    <h2 class="section-title reveal">Articulos relacionados</h2>
                </div>
                <div class="related-posts-grid stagger-children">
                    @foreach ($relatedPosts as $related)
                        <article class="related-card stagger-item">
                            <a href="{{ route('blog.show', $related->slug) }}" class="related-card-image-wrap">
                                @if ($related->featured_image)
                                    <img src="{{ asset($related->featured_image) }}" alt="{{ $related->title }}" loading="lazy">
                                @else
                                    <div class="related-card-placeholder">
                                        <i class="bi bi-tree"></i>
                                    </div>
                                @endif
                                @if ($related->category)
                                    <span class="related-card-badge">{{ $related->category->name }}</span>
                                @endif
                            </a>
                            <div class="related-card-body">
                                <div class="related-card-meta">
                                    <span>{{ $related->published_at ? $related->published_at->format('d M, Y') : $related->created_at->format('d M, Y') }}</span>
                                    @if ($related->reading_time)
                                        <span>{{ $related->reading_time }} min</span>
                                    @endif
                                </div>
                                <h3 class="related-card-title">
                                    <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
                                </h3>
                                @if ($related->excerpt)
                                    <p class="related-card-excerpt">{{ Str::limit($related->excerpt, 100) }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div style="text-align: center; margin-top: 50px;" class="reveal">
                    <a href="{{ route('blog.index') }}" class="btn-manzer btn-manzer-primary">
                        Ver todos los articulos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

@endsection

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "{{ $post->title }}",
    "description": "{{ $post->excerpt ?? Str::limit(strip_tags($post->body), 160) }}",
    @if($post->featured_image)
    "image": "{{ asset($post->featured_image) }}",
    @endif
    "datePublished": "{{ ($post->published_at ?? $post->created_at)->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $post->author->name ?? 'Manzer Agroforestal' }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Manzer Agroforestal, S.L.",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/manzer-logo.png') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ route('blog.show', $post->slug) }}"
    }
    @if($post->category)
    ,"articleSection": "{{ $post->category->name }}"
    @endif
    @if($post->tags && $post->tags->count() > 0)
    ,"keywords": "{{ $post->tags->pluck('name')->implode(', ') }}"
    @endif
}
</script>
@endpush

@push('styles')
<style>
    /* ===== POST HERO ===== */
    .post-hero {
        position: relative;
        min-height: 65vh;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        background-color: var(--manzer-dark);
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        overflow: hidden;
    }
    @supports (-webkit-touch-callout: none) {
        .post-hero {
            background-attachment: scroll;
        }
    }
    .post-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(13, 31, 13, 0.95) 0%, rgba(13, 31, 13, 0.5) 40%, rgba(13, 31, 13, 0.3) 100%);
    }
    .post-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 820px;
        padding: 140px 20px 60px;
    }
    .post-hero-badge {
        display: inline-block;
        background: var(--manzer-green);
        color: var(--manzer-dark);
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 8px 20px;
        border-radius: 30px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .post-hero-badge:hover {
        background: var(--manzer-white);
        color: var(--manzer-dark);
        transform: translateY(-2px);
    }
    .post-hero-title {
        font-size: clamp(1.8rem, 4vw, 3rem);
        font-weight: 800;
        color: var(--manzer-white);
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .post-hero-meta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
    }
    .post-hero-meta i {
        margin-right: 6px;
        color: var(--manzer-green);
    }

    /* ===== BREADCRUMBS ===== */
    .post-breadcrumbs {
        background: var(--manzer-dark);
        border-bottom: 1px solid rgba(57, 255, 20, 0.1);
        padding: 14px 0;
    }
    .post-breadcrumbs .container {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
    }
    .post-breadcrumbs a {
        color: rgba(255, 255, 255, 0.5);
        transition: color 0.3s ease;
    }
    .post-breadcrumbs a:hover {
        color: var(--manzer-green);
    }
    .post-breadcrumbs i {
        color: rgba(255, 255, 255, 0.25);
        font-size: 0.7rem;
    }
    .post-breadcrumbs span {
        color: var(--manzer-green);
        font-weight: 600;
    }

    /* ===== ARTICLE ===== */
    .post-section {
        background: var(--manzer-cream);
    }
    .post-article {
        max-width: 800px;
        margin: 0 auto;
    }

    /* Author Bar */
    .post-author-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 0;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 40px;
    }
    .post-author-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .post-author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--manzer-forest);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--manzer-green);
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .post-author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .post-author-name {
        display: block;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--manzer-dark);
    }
    .post-author-date {
        display: block;
        font-size: 0.8rem;
        color: var(--manzer-gray);
        margin-top: 2px;
    }
    .post-author-date a {
        color: var(--manzer-forest);
        font-weight: 600;
    }
    .post-author-date a:hover {
        color: var(--manzer-green);
    }

    /* ===== POST BODY TYPOGRAPHY ===== */
    .post-body {
        font-family: 'Inter', sans-serif;
        font-size: 1.05rem;
        line-height: 1.85;
        color: #374151;
    }
    .post-body h2 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--manzer-dark);
        margin: 48px 0 16px;
        line-height: 1.3;
    }
    .post-body h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--manzer-dark);
        margin: 36px 0 12px;
        line-height: 1.35;
    }
    .post-body h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--manzer-forest);
        margin: 28px 0 10px;
    }
    .post-body p {
        margin-bottom: 20px;
    }
    .post-body a {
        color: var(--manzer-forest);
        font-weight: 600;
        border-bottom: 2px solid var(--manzer-green);
        transition: all 0.3s ease;
    }
    .post-body a:hover {
        color: var(--manzer-green);
    }
    .post-body img {
        border-radius: 14px;
        margin: 32px 0;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }
    .post-body blockquote {
        border-left: 4px solid var(--manzer-green);
        background: rgba(57, 255, 20, 0.04);
        padding: 24px 28px;
        margin: 32px 0;
        border-radius: 0 14px 14px 0;
        font-style: italic;
        color: var(--manzer-forest);
        font-size: 1.1rem;
        line-height: 1.7;
    }
    .post-body ul,
    .post-body ol {
        margin: 20px 0;
        padding-left: 24px;
    }
    .post-body li {
        margin-bottom: 8px;
    }
    .post-body ul li::marker {
        color: var(--manzer-green);
    }
    .post-body pre {
        background: var(--manzer-dark);
        color: var(--manzer-green);
        padding: 24px;
        border-radius: 14px;
        overflow-x: auto;
        margin: 28px 0;
        font-size: 0.9rem;
        line-height: 1.6;
    }
    .post-body code {
        background: rgba(27, 67, 50, 0.08);
        color: var(--manzer-forest);
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.9em;
    }
    .post-body pre code {
        background: transparent;
        color: inherit;
        padding: 0;
        border-radius: 0;
    }
    .post-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 28px 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }
    .post-body th {
        background: var(--manzer-forest);
        color: var(--manzer-white);
        padding: 14px 18px;
        text-align: left;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .post-body td {
        padding: 12px 18px;
        border-bottom: 1px solid #f3f4f6;
    }
    .post-body tr:last-child td {
        border-bottom: none;
    }
    .post-body hr {
        border: none;
        height: 2px;
        background: linear-gradient(to right, transparent, var(--manzer-green), transparent);
        margin: 48px 0;
    }

    /* ===== TAGS ===== */
    .post-tags {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 32px 0;
        border-top: 1px solid #e5e7eb;
        margin-top: 48px;
    }
    .post-tags-label {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--manzer-dark);
    }
    .post-tags-label i {
        margin-right: 4px;
        color: var(--manzer-green);
    }
    .post-tag-pill {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 30px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        background: #f3f4f6;
        color: var(--manzer-gray);
        border: none;
        transition: all 0.3s ease;
    }
    .post-tag-pill:hover {
        background: var(--manzer-forest);
        color: var(--manzer-white);
        transform: translateY(-2px);
    }

    /* ===== SHARE BUTTONS ===== */
    .post-share {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 28px 0;
        border-top: 1px solid #e5e7eb;
    }
    .post-share-label {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--manzer-dark);
    }
    .post-share-buttons {
        display: flex;
        gap: 10px;
    }
    .share-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        color: var(--manzer-white);
    }
    .share-btn:hover {
        transform: translateY(-3px);
        color: var(--manzer-white);
    }
    .share-whatsapp { background: #25d366; }
    .share-whatsapp:hover { box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4); }
    .share-facebook { background: #1877f2; }
    .share-facebook:hover { box-shadow: 0 6px 20px rgba(24, 119, 242, 0.4); }
    .share-twitter { background: #0f1419; }
    .share-twitter:hover { box-shadow: 0 6px 20px rgba(15, 20, 25, 0.4); }
    .share-linkedin { background: #0a66c2; }
    .share-linkedin:hover { box-shadow: 0 6px 20px rgba(10, 102, 194, 0.4); }

    /* ===== RELATED POSTS ===== */
    .post-related-section {
        background: var(--manzer-dark);
    }
    .related-posts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    .related-card {
        background: rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: all 0.4s var(--manzer-transition);
    }
    .related-card:hover {
        transform: translateY(-8px);
        border-color: rgba(57, 255, 20, 0.2);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    }
    .related-card-image-wrap {
        display: block;
        position: relative;
        overflow: hidden;
        aspect-ratio: 16 / 10;
    }
    .related-card-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s var(--manzer-transition);
    }
    .related-card:hover .related-card-image-wrap img {
        transform: scale(1.08);
    }
    .related-card-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--manzer-forest), rgba(27, 67, 50, 0.5));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--manzer-green);
        font-size: 2.5rem;
    }
    .related-card-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--manzer-green);
        color: var(--manzer-dark);
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.65rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 30px;
    }
    .related-card-body {
        padding: 22px;
    }
    .related-card-meta {
        display: flex;
        gap: 16px;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 10px;
    }
    .related-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 10px;
    }
    .related-card-title a {
        color: var(--manzer-white);
        transition: color 0.3s ease;
    }
    .related-card-title a:hover {
        color: var(--manzer-green);
    }
    .related-card-excerpt {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.45);
        line-height: 1.6;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991px) {
        .related-posts-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .post-hero {
            min-height: 50vh;
            background-attachment: scroll;
        }
        .post-hero-content {
            padding: 120px 20px 40px;
        }
        .post-hero-meta {
            gap: 12px;
            font-size: 0.8rem;
        }
        .post-body {
            font-size: 1rem;
        }
        .post-share {
            flex-direction: column;
            align-items: flex-start;
        }
        .related-posts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Parallax hero background
        if (document.querySelector('.post-hero')) {
            gsap.to('.post-hero', {
                backgroundPositionY: '40%',
                ease: 'none',
                scrollTrigger: {
                    trigger: '.post-hero',
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true
                }
            });
        }

        // Animate post body children on scroll
        const bodyElements = document.querySelectorAll('.post-body > *');
        bodyElements.forEach((el, i) => {
            gsap.fromTo(el,
                { opacity: 0, y: 20 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    delay: i * 0.03,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 90%',
                        once: true
                    }
                }
            );
        });

        // Reading progress bar
        const progressBar = document.createElement('div');
        progressBar.style.cssText = 'position:fixed;top:0;left:0;height:3px;background:var(--manzer-green);z-index:9999;transition:width 0.1s linear;width:0;';
        document.body.appendChild(progressBar);

        const article = document.querySelector('.post-article');
        if (article) {
            ScrollTrigger.create({
                trigger: article,
                start: 'top top',
                end: 'bottom bottom',
                onUpdate: (self) => {
                    progressBar.style.width = (self.progress * 100) + '%';
                }
            });
        }
    });
</script>
@endpush
