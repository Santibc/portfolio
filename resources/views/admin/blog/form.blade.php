<x-app-layout>
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor { min-height: 320px; font-size: 1rem; line-height: 1.7; }
            .ql-editor.excerpt-editor { min-height: 100px; font-size: 0.95rem; }
            .ql-toolbar.ql-snow, .ql-container.ql-snow { border-color: #dee2e6; }
            .ql-container.ql-snow { border-radius: 0 0 6px 6px; background: #fff; }
            .ql-toolbar.ql-snow { border-radius: 6px 6px 0 0; background: #f8f9fa; }
            .ql-editor img { max-width: 100%; height: auto; border-radius: 6px; }

            .publish-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
            .publish-option {
                border: 2px solid #e5e7eb;
                border-radius: 10px;
                padding: 18px 16px;
                cursor: pointer;
                background: #fff;
                transition: all 0.2s ease;
                position: relative;
                text-align: center;
            }
            .publish-option:hover { border-color: #adb5bd; background: #fafbfc; }
            .publish-option.selected {
                border-color: #46cdcf;
                background: linear-gradient(180deg, rgba(70,205,207,0.06) 0%, #fff 100%);
                box-shadow: 0 4px 12px rgba(70,205,207,0.15);
            }
            .publish-option .icon { font-size: 1.8rem; margin-bottom: 6px; color: #6c757d; }
            .publish-option.selected .icon { color: #46cdcf; }
            .publish-option h6 { font-size: 0.95rem; font-weight: 600; margin: 0 0 4px; color: #2d3a4a; }
            .publish-option small { color: #6c757d; font-size: 0.78rem; display: block; line-height: 1.3; }
            .publish-option input[type="radio"] {
                position: absolute; top: 10px; right: 10px;
            }

            .schedule-panel {
                margin-top: 16px;
                padding: 16px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 3px solid #46cdcf;
                display: none;
            }
            .schedule-panel.active { display: block; }

            .category-picker { display: flex; gap: 8px; align-items: stretch; }
            .category-picker select { flex: 1; }
            .category-picker button {
                background: #fff;
                border: 1px dashed #46cdcf;
                color: #46cdcf;
                border-radius: 6px;
                padding: 0 14px;
                font-size: 0.85rem;
                white-space: nowrap;
                transition: all 0.2s ease;
            }
            .category-picker button:hover {
                background: #46cdcf;
                color: #fff;
                border-style: solid;
            }
            .excerpt-counter {
                font-size: 0.8rem;
                color: #6c757d;
                text-align: right;
                margin-top: 4px;
            }
            .excerpt-counter.warn { color: #dc3545; font-weight: 600; }

            @media (max-width: 768px) {
                .publish-options { grid-template-columns: 1fr; }
            }

            /* ============ SEO TAB ============ */
            .seo-analysis-card {
                background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
                border: 1px solid #dfe3e7;
                border-radius: 12px;
                padding: 18px 22px;
                margin-bottom: 28px;
            }
            .seo-score-badge {
                font-size: 1.6rem;
                font-weight: 700;
                color: #2d3a4a;
                background: #fff;
                border-radius: 12px;
                padding: 8px 16px;
                min-width: 90px;
                text-align: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            }
            .seo-score-badge.good { color: #16a34a; }
            .seo-score-badge.warn { color: #ea580c; }
            .seo-score-badge.bad { color: #dc2626; }
            .seo-checks { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px 20px; }
            .seo-checks li {
                display: flex; align-items: center; gap: 8px;
                font-size: 0.88rem; color: #6c7684;
                padding: 4px 0;
            }
            .seo-checks li i { font-size: 0.95rem; }
            .seo-checks li.ok { color: #16a34a; }
            .seo-checks li.ok i::before { content: "\f26a"; }
            .seo-checks li.bad { color: #dc2626; }
            .seo-checks li.bad i::before { content: "\f622"; }

            .seo-section {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 22px;
                margin-bottom: 22px;
            }
            .seo-section-header {
                display: flex;
                gap: 14px;
                align-items: flex-start;
                padding-bottom: 14px;
                margin-bottom: 18px;
                border-bottom: 1px solid #f1f3f5;
            }
            .seo-section-header i {
                font-size: 1.6rem;
                color: #46cdcf;
                width: 40px; height: 40px;
                background: rgba(70,205,207,0.1);
                border-radius: 10px;
                display: flex; align-items: center; justify-content: center;
            }
            .seo-section-header h5 { font-size: 1.05rem; font-weight: 600; margin: 0 0 2px; }
            .seo-section-header small { color: #6c7684; }

            /* Character counter */
            .counter { color: #94a1af; font-weight: 500; }
            .counter.warn { color: #ea580c; }
            .counter.bad { color: #dc2626; }
            .counter-bar {
                height: 4px;
                background: #f1f3f5;
                border-radius: 2px;
                margin-top: 4px;
                overflow: hidden;
            }
            .counter-fill {
                height: 100%;
                background: #46cdcf;
                width: 0%;
                transition: width 0.2s ease, background 0.2s ease;
            }
            .counter-fill.warn { background: #f59e0b; }
            .counter-fill.bad { background: #dc2626; }

            /* Google Preview */
            .seo-preview-google {
                background: #fff;
                border: 1px solid #dfe3e7;
                border-radius: 10px;
                padding: 16px 20px;
                font-family: Arial, sans-serif;
            }
            .gp-site { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
            .gp-favicon {
                width: 26px; height: 26px;
                background: #f1f3f5;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 14px;
            }
            .gp-name { font-size: 0.85rem; color: #202124; font-weight: 500; }
            .gp-url { font-size: 0.78rem; color: #4d5156; }
            .gp-title {
                display: block;
                color: #1a0dab;
                font-size: 1.25rem;
                text-decoration: none;
                margin-top: 4px;
                line-height: 1.3;
                font-weight: 400;
            }
            .gp-title:hover { text-decoration: underline; }
            .gp-desc { font-size: 0.9rem; color: #4d5156; margin: 4px 0 0; line-height: 1.5; }

            /* Facebook Preview */
            .seo-preview-fb {
                background: #fff;
                border: 1px solid #dfe3e7;
                border-radius: 10px;
                overflow: hidden;
                max-width: 520px;
            }
            .fbp-image {
                width: 100%;
                aspect-ratio: 1.91 / 1;
                background: #e4e6eb;
                display: flex; align-items: center; justify-content: center;
                overflow: hidden;
            }
            .fbp-image img { width: 100%; height: 100%; object-fit: cover; }
            .fbp-placeholder { color: #b0b3b8; text-align: center; font-size: 2rem; }
            .fbp-placeholder small { font-size: 0.7rem; display: block; margin-top: 6px; }
            .fbp-body { padding: 12px 16px; background: #f0f2f5; }
            .fbp-domain {
                font-size: 0.72rem;
                color: #65676b;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                margin-bottom: 4px;
            }
            .fbp-title {
                font-size: 1.05rem;
                font-weight: 600;
                color: #050505;
                line-height: 1.3;
                margin-bottom: 4px;
            }
            .fbp-desc { font-size: 0.85rem; color: #65676b; line-height: 1.4; }

            /* Twitter Preview */
            .seo-preview-tw {
                background: #fff;
                border: 1px solid #cfd9de;
                border-radius: 16px;
                overflow: hidden;
                max-width: 520px;
            }
            .twp-image {
                width: 100%;
                aspect-ratio: 1.91 / 1;
                background: #eff3f4;
                display: flex; align-items: center; justify-content: center;
                overflow: hidden;
                border-bottom: 1px solid #cfd9de;
            }
            .twp-image img { width: 100%; height: 100%; object-fit: cover; }
            .twp-placeholder { color: #536471; font-size: 2rem; }
            .twp-body { padding: 12px 16px; }
            .twp-title {
                font-size: 0.98rem;
                font-weight: 500;
                color: #0f1419;
                line-height: 1.3;
                margin-bottom: 4px;
            }
            .twp-desc { font-size: 0.85rem; color: #536471; margin-bottom: 6px; }
            .twp-domain { font-size: 0.8rem; color: #536471; }

            @media (max-width: 768px) {
                .seo-checks { grid-template-columns: 1fr; }
            }
        </style>
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-journal-text me-2"></i>
                {{ $post->exists ? 'Editar artículo' : 'Nuevo artículo' }}
            </h1>
            <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="postForm"
              action="{{ $post->exists ? route('admin.blog.posts.update', $post) : route('admin.blog.posts.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($post->exists) @method('PUT') @endif

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-content" type="button">Contenido</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-publish" type="button">Publicación</button></li>
            </ul>

            <div class="tab-content">
                {{-- ---------- CONTENIDO ---------- --}}
                <div class="tab-pane fade show active" id="tab-content">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $post->title) }}" required maxlength="200" placeholder="Un título claro y atractivo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <div class="category-picker">
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">— Sin categoría —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#quickCategoryModal" title="Crear nueva categoría">
                                    <i class="bi bi-plus-lg"></i> Nueva
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug (URL)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}" placeholder="Auto-generado desde el título">
                            <div class="form-text">/blog/{slug}. Déjalo vacío para generar del título.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Autor</label>
                            <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $post->author_name) }}" placeholder="Nombre del autor">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Extracto</label>
                            <div class="border rounded">
                                <div id="excerpt-toolbar">
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-link"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-clean"></button>
                                    </span>
                                </div>
                                <div id="excerpt-editor" class="excerpt-editor">{!! old('excerpt', $post->excerpt) !!}</div>
                            </div>
                            <input type="hidden" name="excerpt" id="excerpt-input">
                            <div class="excerpt-counter"><span id="excerpt-count">0</span> / 500 caracteres</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Contenido <span class="text-danger">*</span></label>
                            <div class="border rounded">
                                <div id="content-toolbar">
                                    <span class="ql-formats">
                                        <select class="ql-header">
                                            <option value="2">Encabezado 2</option>
                                            <option value="3">Encabezado 3</option>
                                            <option value="4">Encabezado 4</option>
                                            <option selected>Párrafo</option>
                                        </select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                        <button class="ql-strike"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <select class="ql-color"></select>
                                        <select class="ql-background"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <select class="ql-align"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-link"></button>
                                        <button class="ql-image"></button>
                                        <button class="ql-video"></button>
                                        <button class="ql-blockquote"></button>
                                        <button class="ql-code-block"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-clean"></button>
                                    </span>
                                </div>
                                <div id="content-editor">{!! old('content_html', $post->content_html) !!}</div>
                            </div>
                            <input type="hidden" name="content_html" id="content-input">
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle"></i>
                                Usa la barra para dar formato. Para <strong>insertar imágenes</strong>, haz click en el icono de imagen <i class="bi bi-image"></i> y súbela desde tu computador (se guarda en el servidor).
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Imagen de portada</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                            @if($post->cover_image_path)
                                <div class="mt-2 small">
                                    Actual: <a href="{{ asset($post->cover_image_path) }}" target="_blank">ver imagen</a>
                                </div>
                            @endif
                            <div class="form-text">Se muestra en el listado del blog y como imagen destacada.</div>
                        </div>
                    </div>
                </div>

                {{-- ---------- SEO ---------- --}}
                <div class="tab-pane fade" id="tab-seo">

                    {{-- ============ SEO ANALYSIS ============ --}}
                    <div class="seo-analysis-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>SEO Analysis</h5>
                                <small class="text-muted">Se actualiza en tiempo real mientras editas</small>
                            </div>
                            <div class="seo-score-badge" id="seoScoreBadge">
                                <span id="seoScoreValue">0</span><span class="text-muted">/100</span>
                            </div>
                        </div>
                        <ul class="seo-checks list-unstyled mb-0" id="seoChecks">
                            <li data-check="focus"><i class="bi bi-circle"></i> <span>Focus keyword definida</span></li>
                            <li data-check="focus_title"><i class="bi bi-circle"></i> <span>Focus keyword en Meta Title</span></li>
                            <li data-check="focus_slug"><i class="bi bi-circle"></i> <span>Focus keyword en el slug</span></li>
                            <li data-check="focus_meta"><i class="bi bi-circle"></i> <span>Focus keyword en Meta Description</span></li>
                            <li data-check="focus_content"><i class="bi bi-circle"></i> <span>Focus keyword en el contenido</span></li>
                            <li data-check="title_len"><i class="bi bi-circle"></i> <span>Meta Title entre 30–60 caracteres</span></li>
                            <li data-check="meta_len"><i class="bi bi-circle"></i> <span>Meta Description entre 120–160 caracteres</span></li>
                            <li data-check="content_len"><i class="bi bi-circle"></i> <span>Contenido con más de 300 palabras</span></li>
                            <li data-check="cover"><i class="bi bi-circle"></i> <span>Imagen de portada configurada</span></li>
                            <li data-check="og_image"><i class="bi bi-circle"></i> <span>Imagen social (OG) configurada</span></li>
                        </ul>
                    </div>

                    {{-- ============ SEARCH SNIPPET ============ --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-google"></i>
                            <div>
                                <h5>Search Snippet — Google</h5>
                                <small>Cómo se verá tu artículo en los resultados de búsqueda.</small>
                            </div>
                        </div>

                        <div class="seo-preview-google" id="googlePreview">
                            <div class="gp-site">
                                <div class="gp-favicon">🌐</div>
                                <div>
                                    <div class="gp-name">{{ config('app.name') }}</div>
                                    <div class="gp-url" id="gpUrl">{{ url('/') }}/blog › <span id="gpSlug">tu-slug-aqui</span></div>
                                </div>
                            </div>
                            <a class="gp-title" href="#" id="gpTitle">Tu Meta Title aparecerá aquí</a>
                            <p class="gp-desc" id="gpDesc">La Meta Description aparecerá aquí. Optimízala para atraer clicks desde los resultados de Google.</p>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-8">
                                <label class="form-label d-flex justify-content-between">
                                    <span>Meta Title</span>
                                    <small class="counter" id="metaTitleCounter">0 / 60</small>
                                </label>
                                <input type="text" name="meta_title" id="input-meta-title" class="form-control" maxlength="150" value="{{ old('meta_title', $post->meta_title) }}" placeholder="Título optimizado para Google (50-60 caracteres)">
                                <div class="counter-bar"><div class="counter-fill" id="metaTitleBar"></div></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Focus Keyword</label>
                                <input type="text" name="focus_keyword" id="input-focus" class="form-control" maxlength="100" value="{{ old('focus_keyword', $post->focus_keyword) }}" placeholder="deep cleaning adelaide">
                                <div class="form-text small">La palabra o frase por la que quieres rankear.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-flex justify-content-between">
                                    <span>Meta Description</span>
                                    <small class="counter" id="metaDescCounter">0 / 160</small>
                                </label>
                                <textarea name="meta_description" id="input-meta-desc" class="form-control" rows="3" placeholder="Resumen que aparece en Google (150-160 caracteres)">{{ old('meta_description', $post->meta_description) }}</textarea>
                                <div class="counter-bar"><div class="counter-fill" id="metaDescBar"></div></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" maxlength="500" value="{{ old('meta_keywords', $post->meta_keywords) }}" placeholder="separadas por comas">
                                <div class="form-text small">Legacy — Google casi no las usa, pero Bing y otros sí.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Robots</label>
                                <select name="robots" class="form-select">
                                    @php $curRobots = old('robots', $post->robots ?? 'index,follow'); @endphp
                                    <option value="index,follow" @selected($curRobots === 'index,follow')>index, follow (recomendado)</option>
                                    <option value="noindex,follow" @selected($curRobots === 'noindex,follow')>noindex, follow</option>
                                    <option value="index,nofollow" @selected($curRobots === 'index,nofollow')>index, nofollow</option>
                                    <option value="noindex,nofollow" @selected($curRobots === 'noindex,nofollow')>noindex, nofollow</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control" maxlength="500" value="{{ old('canonical_url', $post->canonical_url) }}" placeholder="Solo si quieres apuntar a otra URL como versión canónica">
                                <div class="form-text small">Déjalo vacío para usar la URL del artículo (recomendado).</div>
                            </div>
                        </div>
                    </div>

                    {{-- ============ OPEN GRAPH ============ --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-facebook"></i>
                            <div>
                                <h5>Redes sociales — Open Graph (Facebook, LinkedIn, WhatsApp)</h5>
                                <small>Cómo se verá tu artículo cuando alguien lo comparta.</small>
                            </div>
                        </div>

                        <div class="seo-preview-fb" id="fbPreview">
                            <div class="fbp-image" id="fbpImage">
                                @if($post->og_image_path)
                                    <img src="{{ asset($post->og_image_path) }}">
                                @elseif($post->cover_image_path)
                                    <img src="{{ asset($post->cover_image_path) }}">
                                @else
                                    <div class="fbp-placeholder"><i class="bi bi-image"></i><br><small>1200 × 630 px recomendado</small></div>
                                @endif
                            </div>
                            <div class="fbp-body">
                                <div class="fbp-domain">{{ parse_url(url('/'), PHP_URL_HOST) ?: 'cleanmeadelaide.au' }}</div>
                                <div class="fbp-title" id="fbpTitle">Tu OG Title</div>
                                <div class="fbp-desc" id="fbpDesc">Tu OG Description</div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-8">
                                <label class="form-label d-flex justify-content-between">
                                    <span>OG Title</span>
                                    <small class="counter" id="ogTitleCounter">0 / 90</small>
                                </label>
                                <input type="text" name="og_title" id="input-og-title" class="form-control" maxlength="150" value="{{ old('og_title', $post->og_title) }}" placeholder="Déjalo vacío para usar el Meta Title">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">OG Type</label>
                                <select name="og_type" class="form-select">
                                    @php $curOgType = old('og_type', $post->og_type ?? 'article'); @endphp
                                    <option value="article" @selected($curOgType === 'article')>article (recomendado)</option>
                                    <option value="website" @selected($curOgType === 'website')>website</option>
                                    <option value="blog" @selected($curOgType === 'blog')>blog</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-flex justify-content-between">
                                    <span>OG Description</span>
                                    <small class="counter" id="ogDescCounter">0 / 200</small>
                                </label>
                                <textarea name="og_description" id="input-og-desc" class="form-control" rows="2" placeholder="Déjalo vacío para usar la Meta Description">{{ old('og_description', $post->og_description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">OG Image</label>
                                <input type="file" name="og_image" id="input-og-image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small">Recomendado: 1200 × 630 px, JPG/PNG. Si no cargas una, se usará la imagen de portada.</div>
                                @if($post->og_image_path)
                                    <div class="mt-2 small">Actual: <a href="{{ asset($post->og_image_path) }}" target="_blank">ver imagen OG</a></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ============ TWITTER / X ============ --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-twitter-x"></i>
                            <div>
                                <h5>Twitter / X Card</h5>
                                <small>Cómo se verá al compartir en X (Twitter).</small>
                            </div>
                        </div>

                        <div class="seo-preview-tw" id="twPreview">
                            <div class="twp-image" id="twpImage">
                                @if($post->twitter_image_path)
                                    <img src="{{ asset($post->twitter_image_path) }}">
                                @elseif($post->og_image_path)
                                    <img src="{{ asset($post->og_image_path) }}">
                                @elseif($post->cover_image_path)
                                    <img src="{{ asset($post->cover_image_path) }}">
                                @else
                                    <div class="twp-placeholder"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                            <div class="twp-body">
                                <div class="twp-title" id="twpTitle">Twitter Title</div>
                                <div class="twp-desc" id="twpDesc">Twitter Description</div>
                                <div class="twp-domain">🔗 {{ parse_url(url('/'), PHP_URL_HOST) ?: 'cleanmeadelaide.au' }}</div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Card Type</label>
                                <select name="twitter_card" class="form-select">
                                    @php $curTwCard = old('twitter_card', $post->twitter_card ?? 'summary_large_image'); @endphp
                                    <option value="summary_large_image" @selected($curTwCard === 'summary_large_image')>Summary Large Image</option>
                                    <option value="summary" @selected($curTwCard === 'summary')>Summary</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Twitter Title</label>
                                <input type="text" name="twitter_title" id="input-tw-title" class="form-control" maxlength="150" value="{{ old('twitter_title', $post->twitter_title) }}" placeholder="Déjalo vacío para heredar del OG Title">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Twitter Description</label>
                                <textarea name="twitter_description" id="input-tw-desc" class="form-control" rows="2" placeholder="Déjalo vacío para heredar del OG Description">{{ old('twitter_description', $post->twitter_description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Twitter Image</label>
                                <input type="file" name="twitter_image" id="input-tw-image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small">Si no cargas una, se hereda de la OG Image.</div>
                                @if($post->twitter_image_path)
                                    <div class="mt-2 small">Actual: <a href="{{ asset($post->twitter_image_path) }}" target="_blank">ver imagen Twitter</a></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ============ SCHEMA.ORG ============ --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-braces"></i>
                            <div>
                                <h5>Datos estructurados — Schema.org</h5>
                                <small>Ayuda a Google a entender de qué trata el artículo (Rich Results).</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Schema Type</label>
                                <select name="schema_type" class="form-select">
                                    @php $curSchema = old('schema_type', $post->schema_type ?? 'BlogPosting'); @endphp
                                    <option value="BlogPosting" @selected($curSchema === 'BlogPosting')>BlogPosting (recomendado)</option>
                                    <option value="Article" @selected($curSchema === 'Article')>Article</option>
                                    <option value="NewsArticle" @selected($curSchema === 'NewsArticle')>NewsArticle</option>
                                    <option value="HowTo" @selected($curSchema === 'HowTo')>HowTo</option>
                                    <option value="FAQPage" @selected($curSchema === 'FAQPage')>FAQPage</option>
                                    <option value="Review" @selected($curSchema === 'Review')>Review</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">JSON Schema personalizado (opcional)</label>
                                @php
                                    $schemaJson = old('schema_data',
                                        is_array($post->schema_data) ? json_encode($post->schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ''
                                    );
                                @endphp
                                <textarea name="schema_data" class="form-control font-monospace" rows="6" placeholder='{"@context":"https://schema.org","@type":"HowTo","step":[...]}'>{{ $schemaJson }}</textarea>
                                <div class="form-text small">
                                    Déjalo vacío para que se genere automáticamente. Solo pon JSON aquí si necesitas un schema personalizado (HowTo con pasos, FAQ con preguntas, etc.). Debe ser JSON válido.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ---------- PUBLICACIÓN ---------- --}}
                <div class="tab-pane fade" id="tab-publish">
                    <label class="form-label mb-3">Estado del artículo</label>
                    <div class="publish-options">
                        <label class="publish-option" data-mode="draft">
                            <input type="radio" name="publish_mode" value="draft"
                                   @checked(old('publish_mode', $post->exists && !$post->is_published ? 'draft' : ($post->exists ? '' : 'draft')) === 'draft')>
                            <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
                            <h6>Borrador</h6>
                            <small>No visible en el sitio. Puedes seguir editando.</small>
                        </label>
                        <label class="publish-option" data-mode="now">
                            <input type="radio" name="publish_mode" value="now"
                                   @checked(old('publish_mode', $post->exists && $post->is_published && (!$post->published_at || $post->published_at <= now()) ? 'now' : '') === 'now')>
                            <div class="icon"><i class="bi bi-broadcast"></i></div>
                            <h6>Publicar ahora</h6>
                            <small>Se publica inmediatamente y aparece en el blog.</small>
                        </label>
                        <label class="publish-option" data-mode="schedule">
                            <input type="radio" name="publish_mode" value="schedule"
                                   @checked(old('publish_mode', $post->exists && $post->is_published && $post->published_at && $post->published_at > now() ? 'schedule' : '') === 'schedule')>
                            <div class="icon"><i class="bi bi-calendar-event"></i></div>
                            <h6>Programar</h6>
                            <small>Publicar automáticamente en una fecha futura.</small>
                        </label>
                    </div>

                    <div class="schedule-panel" id="schedule-panel">
                        <label class="form-label"><i class="bi bi-clock me-1"></i>Fecha y hora de publicación</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" style="max-width: 300px;"
                               value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}">
                        <div class="form-text mt-2">El artículo aparecerá en el sitio a partir de esa fecha.</div>
                    </div>

                    {{-- Los hidden se generan al submit --}}
                    <input type="hidden" name="is_published" id="is_published_hidden" value="0">
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>{{ $post->exists ? 'Actualizar' : 'Crear' }} artículo
                </button>
            </div>
        </form>
    </div>

    {{-- Quick create category modal --}}
    <div class="modal fade" id="quickCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-folder-plus me-2"></i>Nueva categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" id="quickCategoryName" class="form-control" placeholder="Ej: Cleaning Tips" maxlength="150">
                    <div class="form-text mt-2">Se crea activa. Podrás editar detalles después desde el listado de categorías.</div>
                    <div class="alert alert-danger mt-3 d-none" id="quickCategoryError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="quickCategoryCreate">
                        <i class="bi bi-check-lg me-1"></i>Crear
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            (function () {
                const CSRF = '{{ csrf_token() }}';
                const UPLOAD_URL = '{{ route('admin.blog.upload-image') }}';
                const CATEGORY_QUICK_URL = '{{ route('admin.blog.categories.quick') }}';
                const EXCERPT_MAX = 500;

                // -------- Content editor (full toolbar) --------
                const contentQuill = new Quill('#content-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: '#content-toolbar',
                            handlers: {
                                image: () => uploadAndInsertImage(contentQuill)
                            }
                        }
                    },
                    placeholder: 'Escribe el contenido del artículo... Usa la barra para dar formato, insertar imágenes, links y más.'
                });

                // -------- Excerpt editor (minimal) --------
                const excerptQuill = new Quill('#excerpt-editor', {
                    theme: 'snow',
                    modules: { toolbar: '#excerpt-toolbar' },
                    placeholder: 'Resumen breve del artículo (máx 500 caracteres)...'
                });

                // Excerpt character limit + counter
                const excerptCounter = document.getElementById('excerpt-count');
                excerptQuill.on('text-change', () => {
                    const text = excerptQuill.getText().trim();
                    if (text.length > EXCERPT_MAX) {
                        excerptQuill.deleteText(EXCERPT_MAX, excerptQuill.getLength());
                    }
                    const len = excerptQuill.getText().trim().length;
                    excerptCounter.textContent = len;
                    excerptCounter.parentElement.classList.toggle('warn', len >= EXCERPT_MAX);
                });
                // Initialize counter
                excerptCounter.textContent = excerptQuill.getText().trim().length;

                // -------- Image upload handler for Quill --------
                function uploadAndInsertImage(editor) {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();

                    input.onchange = async () => {
                        const file = input.files[0];
                        if (!file) return;

                        // Show a placeholder while uploading
                        const range = editor.getSelection(true);
                        editor.insertText(range.index, 'Subiendo imagen...', { italic: true });

                        const formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', CSRF);

                        try {
                            const res = await fetch(UPLOAD_URL, {
                                method: 'POST',
                                body: formData,
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                            });
                            if (!res.ok) throw new Error('Upload failed');
                            const data = await res.json();

                            // Replace placeholder text with actual image
                            editor.deleteText(range.index, 'Subiendo imagen...'.length);
                            editor.insertEmbed(range.index, 'image', data.url);
                            editor.setSelection(range.index + 1);
                        } catch (err) {
                            editor.deleteText(range.index, 'Subiendo imagen...'.length);
                            alert('No se pudo subir la imagen. Verifica que sea una imagen válida (JPG/PNG/WEBP) y menor a 5MB.');
                        }
                    };
                }

                // -------- Publish mode radio behavior --------
                const options = document.querySelectorAll('.publish-option');
                const schedulePanel = document.getElementById('schedule-panel');
                const publishedAt = document.getElementById('published_at');
                const isPublishedHidden = document.getElementById('is_published_hidden');

                function updatePublishUI() {
                    let selectedMode = 'draft';
                    options.forEach(opt => {
                        const input = opt.querySelector('input[type="radio"]');
                        const isSelected = input.checked;
                        opt.classList.toggle('selected', isSelected);
                        if (isSelected) selectedMode = input.value;
                    });
                    schedulePanel.classList.toggle('active', selectedMode === 'schedule');
                    isPublishedHidden.value = (selectedMode === 'now' || selectedMode === 'schedule') ? '1' : '0';

                    // If switching away from schedule, clear datetime (unless user set it)
                    if (selectedMode !== 'schedule' && selectedMode !== '') {
                        // Keep the value but ignore on submit if not scheduled
                    }
                }
                options.forEach(opt => {
                    opt.addEventListener('click', () => {
                        opt.querySelector('input[type="radio"]').checked = true;
                        updatePublishUI();
                    });
                });
                // Ensure at least one option is selected on load
                if (!document.querySelector('.publish-option input[type="radio"]:checked')) {
                    document.querySelector('.publish-option[data-mode="draft"] input[type="radio"]').checked = true;
                }
                updatePublishUI();

                // -------- Quick category creation --------
                document.getElementById('quickCategoryCreate').addEventListener('click', async () => {
                    const name = document.getElementById('quickCategoryName').value.trim();
                    const errBox = document.getElementById('quickCategoryError');
                    errBox.classList.add('d-none');

                    if (!name) {
                        errBox.textContent = 'Escribe un nombre.';
                        errBox.classList.remove('d-none');
                        return;
                    }

                    try {
                        const res = await fetch(CATEGORY_QUICK_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ name })
                        });
                        if (!res.ok) throw new Error('Error');
                        const data = await res.json();

                        // Insert into select and select it
                        const select = document.getElementById('category_id');
                        const opt = document.createElement('option');
                        opt.value = data.id;
                        opt.textContent = data.name;
                        opt.selected = true;
                        select.appendChild(opt);

                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('quickCategoryModal')).hide();
                        document.getElementById('quickCategoryName').value = '';
                    } catch (err) {
                        errBox.textContent = 'No se pudo crear la categoría. Intenta de nuevo.';
                        errBox.classList.remove('d-none');
                    }
                });

                // -------- SEO: live counters + previews + analysis --------
                const seo = {
                    metaTitle: document.getElementById('input-meta-title'),
                    metaDesc:  document.getElementById('input-meta-desc'),
                    focus:     document.getElementById('input-focus'),
                    ogTitle:   document.getElementById('input-og-title'),
                    ogDesc:    document.getElementById('input-og-desc'),
                    ogImage:   document.getElementById('input-og-image'),
                    twTitle:   document.getElementById('input-tw-title'),
                    twDesc:    document.getElementById('input-tw-desc'),
                    twImage:   document.getElementById('input-tw-image'),
                    title:     document.querySelector('input[name=title]'),
                    slug:      document.querySelector('input[name=slug]'),
                    coverImg:  document.querySelector('input[name=cover_image]'),
                    // previews
                    gpTitle:   document.getElementById('gpTitle'),
                    gpDesc:    document.getElementById('gpDesc'),
                    gpSlug:    document.getElementById('gpSlug'),
                    fbpTitle:  document.getElementById('fbpTitle'),
                    fbpDesc:   document.getElementById('fbpDesc'),
                    fbpImage:  document.getElementById('fbpImage'),
                    twpTitle:  document.getElementById('twpTitle'),
                    twpDesc:   document.getElementById('twpDesc'),
                    twpImage:  document.getElementById('twpImage'),
                };

                function slugify(s) {
                    return (s || '').toLowerCase()
                        .normalize('NFD').replace(/[̀-ͯ]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim().replace(/\s+/g, '-').replace(/-+/g, '-');
                }

                function counter(elId, barId, val, target, hardMax) {
                    const c = document.getElementById(elId);
                    const bar = document.getElementById(barId);
                    const len = (val || '').length;
                    if (c) c.textContent = len + ' / ' + target;
                    // Color logic based on target range
                    const goodMin = Math.round(target * 0.5);
                    let state = 'ok';
                    if (len === 0) state = '';
                    else if (len > target) state = 'bad';
                    else if (len < goodMin) state = 'warn';
                    if (c) { c.classList.remove('warn','bad'); if (state) c.classList.add(state); }
                    if (bar) {
                        const pct = Math.min(100, (len / (hardMax || target)) * 100);
                        bar.style.width = pct + '%';
                        bar.classList.remove('warn','bad');
                        if (state === 'warn') bar.classList.add('warn');
                        if (state === 'bad') bar.classList.add('bad');
                    }
                }

                function updatePreviewImage(previewEl, fileInput, fallbacks) {
                    // Priority: fileInput file → next fallback file → keep existing
                    const file = fileInput?.files?.[0];
                    if (file) {
                        const url = URL.createObjectURL(file);
                        previewEl.innerHTML = '<img src="' + url + '">';
                        return;
                    }
                    for (const fb of fallbacks) {
                        if (fb?.files?.[0]) {
                            const url = URL.createObjectURL(fb.files[0]);
                            previewEl.innerHTML = '<img src="' + url + '">';
                            return;
                        }
                    }
                    // If nothing selected, and the preview currently has no <img>, show placeholder
                    if (!previewEl.querySelector('img')) {
                        previewEl.innerHTML = '<div class="fbp-placeholder"><i class="bi bi-image"></i></div>';
                    }
                }

                function refreshSeo() {
                    const titleValue = seo.title?.value || '';
                    const slugValue  = (seo.slug?.value || slugify(titleValue)) || 'tu-slug-aqui';
                    const metaTitle  = seo.metaTitle.value || titleValue;
                    const metaDesc   = seo.metaDesc.value || '';
                    const focus      = (seo.focus.value || '').toLowerCase().trim();
                    const ogTitle    = seo.ogTitle.value || metaTitle;
                    const ogDesc     = seo.ogDesc.value || metaDesc;
                    const twTitle    = seo.twTitle.value || ogTitle;
                    const twDesc     = seo.twDesc.value || ogDesc;

                    // Counters
                    counter('metaTitleCounter', 'metaTitleBar', metaTitle, 60, 70);
                    counter('metaDescCounter',  'metaDescBar',  metaDesc,  160, 200);
                    counter('ogTitleCounter',   null,           ogTitle,   90);
                    counter('ogDescCounter',    null,           ogDesc,    200);

                    // Google preview
                    seo.gpTitle.textContent = metaTitle || 'Tu Meta Title aparecerá aquí';
                    seo.gpDesc.textContent  = metaDesc  || 'La Meta Description aparecerá aquí. Optimízala para atraer clicks desde los resultados de Google.';
                    seo.gpSlug.textContent  = slugValue;

                    // Facebook preview
                    seo.fbpTitle.textContent = ogTitle || 'Tu OG Title';
                    seo.fbpDesc.textContent  = ogDesc  || 'Tu OG Description';
                    updatePreviewImage(seo.fbpImage, seo.ogImage, [seo.coverImg]);

                    // Twitter preview
                    seo.twpTitle.textContent = twTitle || 'Twitter Title';
                    seo.twpDesc.textContent  = twDesc  || 'Twitter Description';
                    updatePreviewImage(seo.twpImage, seo.twImage, [seo.ogImage, seo.coverImg]);

                    // SEO analysis
                    const contentText = contentQuill ? contentQuill.getText().trim() : '';
                    const wordCount = contentText.split(/\s+/).filter(Boolean).length;

                    const checks = {
                        focus:         focus.length > 0,
                        focus_title:   focus && metaTitle.toLowerCase().includes(focus),
                        focus_slug:    focus && slugValue.toLowerCase().includes(focus.replace(/\s+/g,'-')),
                        focus_meta:    focus && metaDesc.toLowerCase().includes(focus),
                        focus_content: focus && contentText.toLowerCase().includes(focus),
                        title_len:     metaTitle.length >= 30 && metaTitle.length <= 60,
                        meta_len:      metaDesc.length >= 120 && metaDesc.length <= 160,
                        content_len:   wordCount >= 300,
                        cover:         (seo.coverImg?.files?.[0]) || {{ $post->cover_image_path ? 'true' : 'false' }},
                        og_image:      (seo.ogImage?.files?.[0]) || (seo.coverImg?.files?.[0]) || {{ ($post->og_image_path || $post->cover_image_path) ? 'true' : 'false' }},
                    };

                    let passed = 0, total = 0;
                    document.querySelectorAll('#seoChecks li[data-check]').forEach(li => {
                        const key = li.getAttribute('data-check');
                        const ok = !!checks[key];
                        li.classList.remove('ok','bad');
                        li.classList.add(ok ? 'ok' : 'bad');
                        total++; if (ok) passed++;
                    });

                    const score = total ? Math.round((passed / total) * 100) : 0;
                    const badge = document.getElementById('seoScoreBadge');
                    document.getElementById('seoScoreValue').textContent = score;
                    badge.classList.remove('good','warn','bad');
                    if (score >= 80) badge.classList.add('good');
                    else if (score >= 50) badge.classList.add('warn');
                    else badge.classList.add('bad');
                }

                // Bind listeners to all SEO inputs
                ['metaTitle','metaDesc','focus','ogTitle','ogDesc','twTitle','twDesc','title','slug'].forEach(k => {
                    seo[k]?.addEventListener('input', refreshSeo);
                });
                ['ogImage','twImage','coverImg'].forEach(k => {
                    seo[k]?.addEventListener('change', refreshSeo);
                });
                if (contentQuill) contentQuill.on('text-change', refreshSeo);

                // Initial render
                refreshSeo();

                // -------- Copy Quill content into hidden fields before submit --------
                document.getElementById('postForm').addEventListener('submit', function (e) {
                    // Content
                    const contentHtml = contentQuill.root.innerHTML;
                    document.getElementById('content-input').value = contentHtml;

                    // Excerpt: strip HTML for a plain-text-ish version
                    const excerptHtml = excerptQuill.root.innerHTML;
                    const excerptText = excerptQuill.getText().trim();
                    // Store the styled version (HTML minus <p><br></p> when empty)
                    document.getElementById('excerpt-input').value = excerptText ? excerptHtml : '';

                    // Validate content is not empty
                    const contentText = contentQuill.getText().trim();
                    if (!contentText) {
                        e.preventDefault();
                        alert('El contenido del artículo no puede estar vacío.');
                        return false;
                    }

                    // If not scheduled, clear the published_at to let the backend/model decide
                    const mode = document.querySelector('.publish-option input[type="radio"]:checked')?.value;
                    if (mode !== 'schedule') {
                        publishedAt.value = '';
                    }
                });
            })();
        </script>
    @endpush
</x-app-layout>
