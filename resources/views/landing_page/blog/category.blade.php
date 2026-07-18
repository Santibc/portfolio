@extends('landing_page.layout')

@section('content')
    <style>
        .blog-hero {
            padding: 160px 0 40px 0;
            background: linear-gradient(135deg, rgba(114,135,156,0.05) 0%, rgba(70,205,207,0.08) 100%);
        }
        .blog-hero h1 { font-weight: 700; font-size: 2.4rem; margin-bottom: 12px; }
        .blog-hero p.lead { color: #6c7684; }
        .breadcrumb-nav a { color: #6c7684; text-decoration: none; }
        .breadcrumb-nav .sep { margin: 0 8px; color: #adb5bd; }
        .blog-categories {
            padding: 20px 0;
            border-bottom: 1px solid #eef0f2;
        }
        .blog-categories a {
            display: inline-block;
            padding: 6px 16px;
            margin: 4px 6px 4px 0;
            border-radius: 20px;
            background: #f4f6f8;
            color: #374151;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .blog-categories a.active,
        .blog-categories a:hover {
            background: var(--accent-color);
            color: #fff;
        }
        .blog-list { padding: 40px 0 80px; }
        .post-card {
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            transition: all 0.3s ease;
            display: flex; flex-direction: column;
        }
        .post-card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(0,0,0,0.08); }
        .post-card .cover {
            aspect-ratio: 16 / 9;
            background: #eaecef;
            display:flex; align-items:center; justify-content:center;
            color:#adb5bd; font-size:2rem;
            overflow:hidden;
        }
        .post-card .cover img { width: 100%; height: 100%; object-fit: cover; }
        .post-card .body { padding: 20px 22px; display:flex; flex-direction:column; flex-grow:1; }
        .post-card h3 { font-size: 1.15rem; font-weight: 600; margin-bottom: 10px; }
        .post-card p { color: #6c7684; font-size: 0.95rem; flex-grow: 1; }
        .post-card .meta { font-size: 0.8rem; color: #94a1af; margin-top: 12px; }
    </style>

    <section class="blog-hero">
        <div class="container">
            <nav class="breadcrumb-nav text-center small mb-2">
                <a href="{{ route('welcome') }}">Home</a>
                <span class="sep">/</span>
                <a href="{{ route('blog.index') }}">Blog</a>
                <span class="sep">/</span>
                <span>{{ $category->name }}</span>
            </nav>
            <div class="text-center">
                <h1>{{ $category->name }}</h1>
                @if($category->description)
                    <p class="lead mb-0">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </section>

    @if($categories->count() > 0)
        <section class="blog-categories">
            <div class="container">
                <a href="{{ route('blog.index') }}">All</a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="{{ $cat->id === $category->id ? 'active' : '' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="blog-list">
        <div class="container">
            @if($posts->count() === 0)
                <div class="alert alert-info text-center">
                    No articles in this category yet.
                </div>
            @else
                <div class="row g-4">
                    @foreach($posts as $post)
                        <div class="col-lg-4 col-md-6">
                            <article class="post-card">
                                <a href="{{ route('blog.show', $post->slug) }}" class="cover">
                                    @if($post->cover_image_path)
                                        <img src="{{ asset($post->cover_image_path) }}" alt="{{ $post->title }}">
                                    @else
                                        <i class="bi bi-journal-text"></i>
                                    @endif
                                </a>
                                <div class="body">
                                    <h3>
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a>
                                    </h3>
                                    <p>{{ Str::limit(strip_tags($post->excerpt ?: $post->content_html), 130) }}</p>
                                    <div class="meta">
                                        <i class="bi bi-calendar3"></i> {{ optional($post->published_at)->format('M d, Y') }}
                                        &middot; <i class="bi bi-clock"></i> {{ $post->reading_time }} min
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
