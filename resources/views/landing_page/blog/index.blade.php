@extends('landing_page.layout')

@section('content')

    {{-- ===== HERO BANNER ===== --}}
    <section class="blog-hero">
        <div class="blog-hero-overlay"></div>
        <div class="blog-hero-content">
            <span class="section-label reveal">Manzer Agroforestal</span>
            <h1 class="section-title reveal" style="color: var(--manzer-white);">
                @if (isset($category))
                    Blog &mdash; {{ $category->name }}
                @elseif (isset($tag))
                    Blog &mdash; {{ $tag->name }}
                @else
                    Blog
                @endif
            </h1>
            <p class="blog-hero-sub reveal">Noticias, consejos y novedades del sector forestal y agroforestal</p>
        </div>
    </section>

    {{-- ===== BREADCRUMBS ===== --}}
    <div class="blog-breadcrumbs">
        <div class="container">
            <a href="{{ route('welcome') }}">Inicio</a>
            <i class="bi bi-chevron-right"></i>
            @if (isset($category))
                <a href="{{ route('blog.index') }}">Blog</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $category->name }}</span>
            @elseif (isset($tag))
                <a href="{{ route('blog.index') }}">Blog</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $tag->name }}</span>
            @else
                <span>Blog</span>
            @endif
        </div>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="section blog-section">
        <div class="container">
            <div class="blog-grid-layout">

                {{-- LEFT: Posts Grid --}}
                <div class="blog-posts-area">
                    @if ($posts->count() > 0)
                        <div class="blog-posts-grid stagger-children">
                            @foreach ($posts as $post)
                                <article class="blog-card stagger-item">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-image-wrap">
                                        @if ($post->featured_image)
                                            <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="blog-card-image" loading="lazy">
                                        @else
                                            <div class="blog-card-image blog-card-placeholder">
                                                <i class="bi bi-tree"></i>
                                            </div>
                                        @endif
                                        @if ($post->category)
                                            <span class="blog-card-badge">{{ $post->category->name }}</span>
                                        @endif
                                    </a>
                                    <div class="blog-card-body">
                                        <div class="blog-card-meta">
                                            <span><i class="bi bi-calendar3"></i> {{ $post->published_at ? $post->published_at->format('d M, Y') : $post->created_at->format('d M, Y') }}</span>
                                            @if ($post->reading_time)
                                                <span><i class="bi bi-clock"></i> {{ $post->reading_time }} min</span>
                                            @endif
                                        </div>
                                        <h3 class="blog-card-title">
                                            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                        </h3>
                                        @if ($post->excerpt)
                                            <p class="blog-card-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                                        @endif
                                        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-link">
                                            Leer mas <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($posts->hasPages())
                            <div class="blog-pagination reveal">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @else
                        <div class="blog-empty reveal">
                            <i class="bi bi-journal-x"></i>
                            <h3>No se encontraron articulos</h3>
                            <p>Aun no hay publicaciones en esta seccion. Vuelve pronto para nuevos contenidos.</p>
                            <a href="{{ route('blog.index') }}" class="btn-manzer btn-manzer-primary" style="margin-top: 20px;">
                                Ver todos los articulos
                            </a>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Sidebar --}}
                <aside class="blog-sidebar reveal-right">

                    {{-- Search --}}
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Buscar</h4>
                        <form class="sidebar-search" action="{{ route('blog.index') }}" method="GET">
                            <input type="text" name="q" placeholder="Buscar articulos..." value="{{ request('q') }}">
                            <button type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
                        </form>
                    </div>

                    {{-- Categories --}}
                    @if ($categories->count() > 0)
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Categorias</h4>
                            <ul class="sidebar-categories">
                                <li>
                                    <a href="{{ route('blog.index') }}" class="{{ !isset($category) ? 'active' : '' }}">
                                        <span>Todas</span>
                                        <span class="cat-count">{{ $posts->total() }}</span>
                                    </a>
                                </li>
                                @foreach ($categories as $cat)
                                    <li>
                                        <a href="{{ route('blog.category', $cat->slug) }}" class="{{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                                            <span>{{ $cat->name }}</span>
                                            <span class="cat-count">{{ $cat->posts_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Recent Posts --}}
                    @if ($recentPosts->count() > 0)
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Recientes</h4>
                            <div class="sidebar-recent">
                                @foreach ($recentPosts as $recent)
                                    <a href="{{ route('blog.show', $recent->slug) }}" class="recent-post-item">
                                        <div class="recent-post-thumb">
                                            @if ($recent->featured_image)
                                                <img src="{{ asset($recent->featured_image) }}" alt="{{ $recent->title }}" loading="lazy">
                                            @else
                                                <div class="recent-post-placeholder"><i class="bi bi-tree"></i></div>
                                            @endif
                                        </div>
                                        <div class="recent-post-info">
                                            <h5>{{ Str::limit($recent->title, 50) }}</h5>
                                            <span>{{ $recent->published_at ? $recent->published_at->format('d M, Y') : $recent->created_at->format('d M, Y') }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Tags Cloud --}}
                    @if ($tags->count() > 0)
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Etiquetas</h4>
                            <div class="sidebar-tags">
                                @foreach ($tags as $t)
                                    <a href="{{ route('blog.tag', $t->slug) }}" class="tag-pill {{ isset($tag) && $tag->id === $t->id ? 'active' : '' }}">
                                        {{ $t->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </aside>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    /* ===== BLOG HERO ===== */
    .blog-hero {
        position: relative;
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--manzer-dark);
        overflow: hidden;
    }
    .blog-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2339ff14' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .blog-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--manzer-dark) 0%, var(--manzer-forest) 50%, var(--manzer-dark) 100%);
        opacity: 0.9;
    }
    .blog-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 120px 20px 60px;
    }
    .blog-hero-sub {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 12px;
    }

    /* ===== BREADCRUMBS ===== */
    .blog-breadcrumbs {
        background: var(--manzer-dark);
        border-bottom: 1px solid rgba(57, 255, 20, 0.1);
        padding: 14px 0;
    }
    .blog-breadcrumbs .container {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
    }
    .blog-breadcrumbs a {
        color: rgba(255, 255, 255, 0.5);
        transition: color 0.3s ease;
    }
    .blog-breadcrumbs a:hover {
        color: var(--manzer-green);
    }
    .blog-breadcrumbs i {
        color: rgba(255, 255, 255, 0.25);
        font-size: 0.7rem;
    }
    .blog-breadcrumbs span {
        color: var(--manzer-green);
        font-weight: 600;
    }

    /* ===== BLOG SECTION ===== */
    .blog-section {
        background: var(--manzer-cream);
    }

    /* ===== GRID LAYOUT ===== */
    .blog-grid-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 50px;
        align-items: start;
    }

    /* ===== POSTS GRID ===== */
    .blog-posts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }

    /* ===== POST CARD ===== */
    .blog-card {
        background: var(--manzer-white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.4s var(--manzer-transition);
    }
    .blog-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }
    .blog-card-image-wrap {
        display: block;
        position: relative;
        overflow: hidden;
        aspect-ratio: 16 / 10;
    }
    .blog-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s var(--manzer-transition);
    }
    .blog-card:hover .blog-card-image {
        transform: scale(1.08);
    }
    .blog-card-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--manzer-forest), var(--manzer-dark));
        color: var(--manzer-green);
        font-size: 2.5rem;
    }
    .blog-card-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: var(--manzer-green);
        color: var(--manzer-dark);
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 30px;
    }
    .blog-card-body {
        padding: 24px;
    }
    .blog-card-meta {
        display: flex;
        gap: 16px;
        font-size: 0.8rem;
        color: var(--manzer-gray);
        margin-bottom: 12px;
    }
    .blog-card-meta i {
        margin-right: 4px;
        color: var(--manzer-green);
    }
    .blog-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 10px;
    }
    .blog-card-title a {
        color: var(--manzer-dark);
        transition: color 0.3s ease;
    }
    .blog-card-title a:hover {
        color: var(--manzer-forest);
    }
    .blog-card-excerpt {
        font-size: 0.9rem;
        color: var(--manzer-gray);
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .blog-card-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--manzer-forest);
        transition: all 0.3s ease;
    }
    .blog-card-link:hover {
        color: var(--manzer-green);
        gap: 12px;
    }

    /* ===== PAGINATION ===== */
    .blog-pagination {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }
    .blog-pagination nav {
        display: flex;
        align-items: center;
    }
    .blog-pagination .pagination,
    .blog-pagination nav > div {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .blog-pagination .page-link,
    .blog-pagination nav a,
    .blog-pagination nav span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        padding: 0 14px;
        border-radius: 12px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        background: var(--manzer-white);
        color: var(--manzer-dark);
        border: 2px solid transparent;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .blog-pagination .page-item.active .page-link,
    .blog-pagination nav span[aria-current] {
        background: var(--manzer-forest);
        color: var(--manzer-white);
        border-color: var(--manzer-forest);
    }
    .blog-pagination .page-link:hover,
    .blog-pagination nav a:hover {
        background: var(--manzer-dark);
        color: var(--manzer-green);
        border-color: var(--manzer-dark);
    }
    .blog-pagination .page-item.disabled .page-link,
    .blog-pagination nav span.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    /* ===== EMPTY STATE ===== */
    .blog-empty {
        text-align: center;
        padding: 80px 20px;
        background: var(--manzer-white);
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }
    .blog-empty i {
        font-size: 4rem;
        color: var(--manzer-gray);
        margin-bottom: 20px;
        display: block;
    }
    .blog-empty h3 {
        font-size: 1.5rem;
        color: var(--manzer-dark);
        margin-bottom: 10px;
    }
    .blog-empty p {
        color: var(--manzer-gray);
        font-size: 1rem;
    }

    /* ===== SIDEBAR ===== */
    .blog-sidebar {
        position: sticky;
        top: 100px;
    }
    .sidebar-widget {
        background: var(--manzer-white);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }
    .sidebar-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--manzer-dark);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--manzer-green);
        display: inline-block;
    }

    /* Search */
    .sidebar-search {
        display: flex;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }
    .sidebar-search:focus-within {
        border-color: var(--manzer-green);
    }
    .sidebar-search input {
        flex: 1;
        border: none;
        outline: none;
        padding: 12px 16px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        background: transparent;
    }
    .sidebar-search button {
        background: var(--manzer-forest);
        border: none;
        color: var(--manzer-white);
        padding: 12px 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    .sidebar-search button:hover {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }

    /* Categories */
    .sidebar-categories {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-categories li {
        border-bottom: 1px solid #f3f4f6;
    }
    .sidebar-categories li:last-child {
        border-bottom: none;
    }
    .sidebar-categories a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        font-size: 0.9rem;
        color: var(--manzer-gray);
        transition: all 0.3s ease;
    }
    .sidebar-categories a:hover,
    .sidebar-categories a.active {
        color: var(--manzer-forest);
        font-weight: 600;
    }
    .sidebar-categories a.active {
        color: var(--manzer-green);
    }
    .cat-count {
        background: #f3f4f6;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--manzer-gray);
    }
    .sidebar-categories a.active .cat-count {
        background: var(--manzer-forest);
        color: var(--manzer-white);
    }

    /* Recent Posts */
    .sidebar-recent {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .recent-post-item {
        display: flex;
        gap: 14px;
        align-items: center;
        transition: all 0.3s ease;
    }
    .recent-post-item:hover {
        transform: translateX(4px);
    }
    .recent-post-thumb {
        width: 70px;
        height: 56px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .recent-post-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .recent-post-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--manzer-forest), var(--manzer-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--manzer-green);
        font-size: 1.2rem;
    }
    .recent-post-info h5 {
        font-size: 0.85rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--manzer-dark);
        margin-bottom: 4px;
    }
    .recent-post-item:hover .recent-post-info h5 {
        color: var(--manzer-forest);
    }
    .recent-post-info span {
        font-size: 0.75rem;
        color: var(--manzer-gray);
    }

    /* Tags Cloud */
    .sidebar-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .tag-pill {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 30px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        background: #f3f4f6;
        color: var(--manzer-gray);
        transition: all 0.3s ease;
    }
    .tag-pill:hover,
    .tag-pill.active {
        background: var(--manzer-forest);
        color: var(--manzer-white);
    }
    .tag-pill.active {
        background: var(--manzer-green);
        color: var(--manzer-dark);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1100px) {
        .blog-grid-layout {
            grid-template-columns: 1fr 300px;
            gap: 30px;
        }
    }
    @media (max-width: 991px) {
        .blog-grid-layout {
            grid-template-columns: 1fr;
        }
        .blog-sidebar {
            position: static;
        }
    }
    @media (max-width: 640px) {
        .blog-posts-grid {
            grid-template-columns: 1fr;
        }
        .blog-hero-content {
            padding: 100px 20px 40px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Parallax effect on hero
        gsap.to('.blog-hero-overlay', {
            yPercent: 30,
            ease: 'none',
            scrollTrigger: {
                trigger: '.blog-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    });
</script>
@endpush
