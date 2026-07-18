<x-app-layout>
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor { min-height: 320px; font-size: 1rem; line-height: 1.7; }
            .ql-toolbar.ql-snow, .ql-container.ql-snow { border-color: #dee2e6; }
            .ql-container.ql-snow { border-radius: 0 0 6px 6px; background: #fff; }
            .ql-toolbar.ql-snow { border-radius: 6px 6px 0 0; background: #f8f9fa; }
            .ql-editor img { max-width: 100%; height: auto; border-radius: 6px; }

            .icon-picker {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
                gap: 6px;
                max-height: 220px;
                overflow-y: auto;
                padding: 10px;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                background: #f8f9fa;
                margin-top: 8px;
            }
            .icon-picker button {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 10px 0;
                font-size: 1.3rem;
                color: #4b5563;
                cursor: pointer;
                transition: all 0.15s;
            }
            .icon-picker button:hover {
                background: #46cdcf;
                color: #fff;
                border-color: #46cdcf;
            }
            .icon-picker button.selected {
                background: #46cdcf;
                color: #fff;
                border-color: #46cdcf;
                box-shadow: 0 2px 6px rgba(70,205,207,0.4);
            }
            .icon-preview {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: rgba(70,205,207,0.1);
                color: #46cdcf;
                font-size: 1.6rem;
                margin-right: 12px;
            }

            .publish-toggle {
                display: flex; align-items: center; gap: 12px;
                background: #fff; border: 2px solid #e5e7eb;
                border-radius: 10px; padding: 16px 20px;
            }
            .publish-toggle.active {
                border-color: #46cdcf;
                background: linear-gradient(180deg, rgba(70,205,207,0.06) 0%, #fff 100%);
            }
            .publish-toggle .form-check-input {
                width: 3rem; height: 1.6rem; margin: 0;
            }
            .publish-toggle strong { color: #2d3a4a; font-size: 0.95rem; }
            .publish-toggle small { color: #6c7684; }

            /* ============ SEO TAB (same styling as blog) ============ */
            .seo-analysis-card {
                background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
                border: 1px solid #dfe3e7;
                border-radius: 12px;
                padding: 18px 22px;
                margin-bottom: 28px;
            }
            .seo-score-badge {
                font-size: 1.6rem; font-weight: 700; color: #2d3a4a;
                background: #fff; border-radius: 12px;
                padding: 8px 16px; min-width: 90px; text-align: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            }
            .seo-score-badge.good { color: #16a34a; }
            .seo-score-badge.warn { color: #ea580c; }
            .seo-score-badge.bad { color: #dc2626; }
            .seo-checks { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px 20px; }
            .seo-checks li {
                display: flex; align-items: center; gap: 8px;
                font-size: 0.88rem; color: #6c7684; padding: 4px 0;
            }
            .seo-checks li i { font-size: 0.95rem; }
            .seo-checks li.ok { color: #16a34a; }
            .seo-checks li.ok i::before { content: "\f26a"; }
            .seo-checks li.bad { color: #dc2626; }
            .seo-checks li.bad i::before { content: "\f622"; }

            .seo-section {
                background: #fff; border: 1px solid #e5e7eb;
                border-radius: 12px; padding: 22px; margin-bottom: 22px;
            }
            .seo-section-header {
                display: flex; gap: 14px; align-items: flex-start;
                padding-bottom: 14px; margin-bottom: 18px;
                border-bottom: 1px solid #f1f3f5;
            }
            .seo-section-header i {
                font-size: 1.6rem; color: #46cdcf;
                width: 40px; height: 40px;
                background: rgba(70,205,207,0.1); border-radius: 10px;
                display: flex; align-items: center; justify-content: center;
            }
            .seo-section-header h5 { font-size: 1.05rem; font-weight: 600; margin: 0 0 2px; }
            .seo-section-header small { color: #6c7684; }

            .counter { color: #94a1af; font-weight: 500; }
            .counter.warn { color: #ea580c; }
            .counter.bad { color: #dc2626; }
            .counter-bar {
                height: 4px; background: #f1f3f5; border-radius: 2px;
                margin-top: 4px; overflow: hidden;
            }
            .counter-fill {
                height: 100%; background: #46cdcf; width: 0%;
                transition: width 0.2s ease, background 0.2s ease;
            }
            .counter-fill.warn { background: #f59e0b; }
            .counter-fill.bad { background: #dc2626; }

            /* Google Preview */
            .seo-preview-google {
                background: #fff; border: 1px solid #dfe3e7;
                border-radius: 10px; padding: 16px 20px; font-family: Arial, sans-serif;
            }
            .gp-site { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
            .gp-favicon {
                width: 26px; height: 26px; background: #f1f3f5; border-radius: 50%;
                display: flex; align-items: center; justify-content: center; font-size: 14px;
            }
            .gp-name { font-size: 0.85rem; color: #202124; font-weight: 500; }
            .gp-url { font-size: 0.78rem; color: #4d5156; }
            .gp-title {
                display: block; color: #1a0dab; font-size: 1.25rem; text-decoration: none;
                margin-top: 4px; line-height: 1.3; font-weight: 400;
            }
            .gp-title:hover { text-decoration: underline; }
            .gp-desc { font-size: 0.9rem; color: #4d5156; margin: 4px 0 0; line-height: 1.5; }

            /* Facebook Preview */
            .seo-preview-fb {
                background: #fff; border: 1px solid #dfe3e7;
                border-radius: 10px; overflow: hidden; max-width: 520px;
            }
            .fbp-image {
                width: 100%; aspect-ratio: 1.91 / 1; background: #e4e6eb;
                display: flex; align-items: center; justify-content: center; overflow: hidden;
            }
            .fbp-image img { width: 100%; height: 100%; object-fit: cover; }
            .fbp-placeholder { color: #b0b3b8; text-align: center; font-size: 2rem; }
            .fbp-placeholder small { font-size: 0.7rem; display: block; margin-top: 6px; }
            .fbp-body { padding: 12px 16px; background: #f0f2f5; }
            .fbp-domain {
                font-size: 0.72rem; color: #65676b; text-transform: uppercase;
                letter-spacing: 0.3px; margin-bottom: 4px;
            }
            .fbp-title {
                font-size: 1.05rem; font-weight: 600; color: #050505;
                line-height: 1.3; margin-bottom: 4px;
            }
            .fbp-desc { font-size: 0.85rem; color: #65676b; line-height: 1.4; }

            /* Twitter Preview */
            .seo-preview-tw {
                background: #fff; border: 1px solid #cfd9de;
                border-radius: 16px; overflow: hidden; max-width: 520px;
            }
            .twp-image {
                width: 100%; aspect-ratio: 1.91 / 1; background: #eff3f4;
                display: flex; align-items: center; justify-content: center;
                overflow: hidden; border-bottom: 1px solid #cfd9de;
            }
            .twp-image img { width: 100%; height: 100%; object-fit: cover; }
            .twp-placeholder { color: #536471; font-size: 2rem; }
            .twp-body { padding: 12px 16px; }
            .twp-title {
                font-size: 0.98rem; font-weight: 500; color: #0f1419;
                line-height: 1.3; margin-bottom: 4px;
            }
            .twp-desc { font-size: 0.85rem; color: #536471; margin-bottom: 6px; }
            .twp-domain { font-size: 0.8rem; color: #536471; }

            .short-counter { font-size: 0.8rem; color: #6c757d; text-align: right; margin-top: 4px; }
            .short-counter.warn { color: #dc3545; font-weight: 600; }

            @media (max-width: 768px) {
                .seo-checks { grid-template-columns: 1fr; }
            }
        </style>
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                @if($service->icon_class)
                    <span class="icon-preview"><i class="{{ $service->icon_class }}"></i></span>
                @endif
                <div>
                    <h1 class="h3 mb-0">
                        {{ $service->exists ? 'Editar servicio' : 'Nuevo servicio' }}
                    </h1>
                    @if($service->exists)
                        <small class="text-muted">/services/{{ $service->slug }}</small>
                    @endif
                </div>
            </div>
            <div>
                @if($service->exists && $service->slug)
                    <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Ver en el sitio
                    </a>
                @endif
                <a href="{{ route('admin.landing.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="serviceForm"
              action="{{ $service->exists ? route('admin.landing.services.update', $service) : route('admin.landing.services.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($service->exists) @method('PUT') @endif

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-content" type="button">Contenido</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-publish" type="button">Publicación</button></li>
            </ul>

            <div class="tab-content">
                {{-- ============ CONTENIDO ============ --}}
                <div class="tab-pane fade show active" id="tab-content">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Título del servicio <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg"
                                   value="{{ old('title', $service->title) }}" required maxlength="255"
                                   placeholder="Ej: NDIS Cleaning">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Slug (URL)</label>
                            <input type="text" name="slug" class="form-control"
                                   value="{{ old('slug', $service->slug) }}"
                                   placeholder="ndis-cleaning (auto si vacío)">
                            <div class="form-text small">/services/{slug}</div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="subtitle" class="form-control"
                                   value="{{ old('subtitle', $service->subtitle) }}" maxlength="255"
                                   placeholder="Una frase corta bajo el título">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Icono <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="iconPreview" class="{{ old('icon_class', $service->icon_class) }}"></i></span>
                                <input type="text" name="icon_class" id="iconInput" class="form-control"
                                       value="{{ old('icon_class', $service->icon_class) }}" required
                                       placeholder="bi bi-house">
                            </div>
                            <div class="form-text small">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#iconPickerModal">Elegir icono visualmente</a>
                                o buscar en <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción corta (para tarjetas y meta)</label>
                            <textarea name="short_description" id="short_description" class="form-control" rows="2" maxlength="300"
                                      placeholder="Resumen breve para la tarjeta del listado y como fallback de meta description">{{ old('short_description', $service->short_description) }}</textarea>
                            <div class="short-counter"><span id="shortCount">0</span> / 300 caracteres</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Contenido de la página</label>
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
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-clean"></button>
                                    </span>
                                </div>
                                <div id="content-editor">{!! old('content_html', $service->content_html) !!}</div>
                            </div>
                            <input type="hidden" name="content_html" id="content-input">
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle"></i>
                                Este contenido se muestra en la página /services/{slug}. Puedes insertar imágenes desde el botón <i class="bi bi-image"></i>.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Imagen hero (cabecera del detalle)</label>
                            <input type="file" name="hero_image" id="hero_image" class="form-control"
                                   accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small">Recomendado: 1200 × 600 px.</div>
                            @if($service->hero_image_path)
                                <div class="mt-2 small">
                                    Actual: <a href="{{ asset($service->hero_image_path) }}" target="_blank">ver imagen</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ============ SEO ============ --}}
                <div class="tab-pane fade" id="tab-seo">

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
                            <li data-check="content_len"><i class="bi bi-circle"></i> <span>Contenido con más de 200 palabras</span></li>
                            <li data-check="hero"><i class="bi bi-circle"></i> <span>Imagen hero configurada</span></li>
                            <li data-check="og_image"><i class="bi bi-circle"></i> <span>Imagen social (OG) configurada</span></li>
                        </ul>
                    </div>

                    {{-- Google Snippet --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-google"></i>
                            <div>
                                <h5>Search Snippet — Google</h5>
                                <small>Cómo se verá esta página de servicio en los resultados de búsqueda.</small>
                            </div>
                        </div>

                        <div class="seo-preview-google" id="googlePreview">
                            <div class="gp-site">
                                <div class="gp-favicon">🌐</div>
                                <div>
                                    <div class="gp-name">{{ config('app.name') }}</div>
                                    <div class="gp-url">{{ url('/') }}/services › <span id="gpSlug">tu-slug</span></div>
                                </div>
                            </div>
                            <a class="gp-title" href="#" id="gpTitle">Tu Meta Title aparecerá aquí</a>
                            <p class="gp-desc" id="gpDesc">La Meta Description aparecerá aquí.</p>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-8">
                                <label class="form-label d-flex justify-content-between">
                                    <span>Meta Title</span>
                                    <small class="counter" id="metaTitleCounter">0 / 60</small>
                                </label>
                                <input type="text" name="meta_title" id="input-meta-title" class="form-control" maxlength="150"
                                       value="{{ old('meta_title', $service->meta_title) }}"
                                       placeholder="Título optimizado para Google (50-60 caracteres)">
                                <div class="counter-bar"><div class="counter-fill" id="metaTitleBar"></div></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Focus Keyword</label>
                                <input type="text" name="focus_keyword" id="input-focus" class="form-control" maxlength="100"
                                       value="{{ old('focus_keyword', $service->focus_keyword) }}"
                                       placeholder="ndis cleaning adelaide">
                            </div>
                            <div class="col-12">
                                <label class="form-label d-flex justify-content-between">
                                    <span>Meta Description</span>
                                    <small class="counter" id="metaDescCounter">0 / 160</small>
                                </label>
                                <textarea name="meta_description" id="input-meta-desc" class="form-control" rows="3"
                                          placeholder="Descripción que aparece en Google (150-160 caracteres)">{{ old('meta_description', $service->meta_description) }}</textarea>
                                <div class="counter-bar"><div class="counter-fill" id="metaDescBar"></div></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" maxlength="500"
                                       value="{{ old('meta_keywords', $service->meta_keywords) }}"
                                       placeholder="separadas por comas">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Robots</label>
                                <select name="robots" class="form-select">
                                    @php $curRobots = old('robots', $service->robots ?? 'index,follow'); @endphp
                                    <option value="index,follow" @selected($curRobots === 'index,follow')>index, follow (recomendado)</option>
                                    <option value="noindex,follow" @selected($curRobots === 'noindex,follow')>noindex, follow</option>
                                    <option value="index,nofollow" @selected($curRobots === 'index,nofollow')>index, nofollow</option>
                                    <option value="noindex,nofollow" @selected($curRobots === 'noindex,nofollow')>noindex, nofollow</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control" maxlength="500"
                                       value="{{ old('canonical_url', $service->canonical_url) }}"
                                       placeholder="Solo si quieres apuntar a otra URL como versión canónica">
                            </div>
                        </div>
                    </div>

                    {{-- Open Graph --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-facebook"></i>
                            <div>
                                <h5>Redes sociales — Open Graph (Facebook, LinkedIn, WhatsApp)</h5>
                                <small>Cómo se verá al compartir este servicio.</small>
                            </div>
                        </div>

                        <div class="seo-preview-fb" id="fbPreview">
                            <div class="fbp-image" id="fbpImage">
                                @if($service->og_image_path)
                                    <img src="{{ asset($service->og_image_path) }}">
                                @elseif($service->hero_image_path)
                                    <img src="{{ asset($service->hero_image_path) }}">
                                @else
                                    <div class="fbp-placeholder"><i class="bi bi-image"></i><br><small>1200 × 630 px</small></div>
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
                                <label class="form-label">OG Title</label>
                                <input type="text" name="og_title" id="input-og-title" class="form-control" maxlength="150"
                                       value="{{ old('og_title', $service->og_title) }}"
                                       placeholder="Déjalo vacío para usar el Meta Title">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">OG Type</label>
                                <select name="og_type" class="form-select">
                                    @php $curOgType = old('og_type', $service->og_type ?? 'website'); @endphp
                                    <option value="website" @selected($curOgType === 'website')>website (recomendado)</option>
                                    <option value="article" @selected($curOgType === 'article')>article</option>
                                    <option value="product" @selected($curOgType === 'product')>product</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">OG Description</label>
                                <textarea name="og_description" id="input-og-desc" class="form-control" rows="2"
                                          placeholder="Déjalo vacío para usar la Meta Description">{{ old('og_description', $service->og_description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">OG Image</label>
                                <input type="file" name="og_image" id="input-og-image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small">Recomendado: 1200 × 630 px. Si no cargas una, se usa la imagen hero.</div>
                                @if($service->og_image_path)
                                    <div class="mt-2 small">Actual: <a href="{{ asset($service->og_image_path) }}" target="_blank">ver imagen OG</a></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Twitter --}}
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
                                @if($service->twitter_image_path)
                                    <img src="{{ asset($service->twitter_image_path) }}">
                                @elseif($service->og_image_path)
                                    <img src="{{ asset($service->og_image_path) }}">
                                @elseif($service->hero_image_path)
                                    <img src="{{ asset($service->hero_image_path) }}">
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
                                    @php $curTwCard = old('twitter_card', $service->twitter_card ?? 'summary_large_image'); @endphp
                                    <option value="summary_large_image" @selected($curTwCard === 'summary_large_image')>Summary Large Image</option>
                                    <option value="summary" @selected($curTwCard === 'summary')>Summary</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Twitter Title</label>
                                <input type="text" name="twitter_title" id="input-tw-title" class="form-control" maxlength="150"
                                       value="{{ old('twitter_title', $service->twitter_title) }}"
                                       placeholder="Vacío para heredar del OG Title">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Twitter Description</label>
                                <textarea name="twitter_description" id="input-tw-desc" class="form-control" rows="2"
                                          placeholder="Vacío para heredar del OG Description">{{ old('twitter_description', $service->twitter_description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Twitter Image</label>
                                <input type="file" name="twitter_image" id="input-tw-image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small">Si no cargas una, se hereda de la OG Image.</div>
                                @if($service->twitter_image_path)
                                    <div class="mt-2 small">Actual: <a href="{{ asset($service->twitter_image_path) }}" target="_blank">ver imagen Twitter</a></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Schema --}}
                    <div class="seo-section">
                        <div class="seo-section-header">
                            <i class="bi bi-braces"></i>
                            <div>
                                <h5>Datos estructurados — Schema.org</h5>
                                <small>Ayuda a Google a mostrar rich results (precios, disponibilidad, área de servicio).</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Schema Type</label>
                                <select name="schema_type" class="form-select">
                                    @php $curSchema = old('schema_type', $service->schema_type ?? 'Service'); @endphp
                                    <option value="Service" @selected($curSchema === 'Service')>Service (recomendado)</option>
                                    <option value="ProfessionalService" @selected($curSchema === 'ProfessionalService')>ProfessionalService</option>
                                    <option value="LocalBusiness" @selected($curSchema === 'LocalBusiness')>LocalBusiness</option>
                                    <option value="Product" @selected($curSchema === 'Product')>Product</option>
                                    <option value="Offer" @selected($curSchema === 'Offer')>Offer</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">JSON Schema personalizado (opcional)</label>
                                @php
                                    $schemaJson = old('schema_data',
                                        is_array($service->schema_data) ? json_encode($service->schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ''
                                    );
                                @endphp
                                <textarea name="schema_data" class="form-control font-monospace" rows="6"
                                          placeholder='{"@context":"https://schema.org","@type":"Service","serviceType":"NDIS Cleaning",...}'>{{ $schemaJson }}</textarea>
                                <div class="form-text small">
                                    Déjalo vacío para generación automática. Personalízalo si necesitas AggregateOffer, areaServed detallado o servicios ofrecidos.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ============ PUBLICACIÓN ============ --}}
                <div class="tab-pane fade" id="tab-publish">
                    <label class="publish-toggle {{ $service->is_published || !$service->exists ? 'active' : '' }}" id="publishToggle">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" class="form-check-input"
                               id="is_published" role="switch" @checked(old('is_published', $service->exists ? $service->is_published : true))>
                        <div>
                            <strong id="publishLabel">
                                {{ $service->is_published || !$service->exists ? 'Publicado (visible en el sitio)' : 'Oculto (no visible)' }}
                            </strong>
                            <div><small>Cuando está publicado aparece en /servicios y es indexable por Google.</small></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.landing.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>{{ $service->exists ? 'Actualizar' : 'Crear' }} servicio
                </button>
            </div>
        </form>
    </div>

    {{-- Icon picker modal --}}
    <div class="modal fade" id="iconPickerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Elegir icono</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="iconSearch" class="form-control mb-3" placeholder="Buscar (ej: house, brush, star)...">
                    <div class="icon-picker" id="iconPickerGrid"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            (function () {
                const CSRF = '{{ csrf_token() }}';
                const UPLOAD_URL = '{{ route('admin.landing.services.upload-image') }}';

                // ---------- Quill content editor ----------
                const contentQuill = new Quill('#content-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: '#content-toolbar',
                            handlers: { image: () => uploadImage(contentQuill) }
                        }
                    },
                    placeholder: 'Describe el servicio en profundidad. Usa encabezados, listas e imágenes para explicar qué incluye, para quién es y por qué elegirte.'
                });

                function uploadImage(editor) {
                    const input = document.createElement('input');
                    input.type = 'file'; input.accept = 'image/*'; input.click();
                    input.onchange = async () => {
                        const file = input.files[0]; if (!file) return;
                        const range = editor.getSelection(true);
                        editor.insertText(range.index, 'Subiendo imagen...', { italic: true });
                        const fd = new FormData();
                        fd.append('image', file); fd.append('_token', CSRF);
                        try {
                            const res = await fetch(UPLOAD_URL, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
                            if (!res.ok) throw new Error('upload');
                            const data = await res.json();
                            editor.deleteText(range.index, 'Subiendo imagen...'.length);
                            editor.insertEmbed(range.index, 'image', data.url);
                            editor.setSelection(range.index + 1);
                        } catch (e) {
                            editor.deleteText(range.index, 'Subiendo imagen...'.length);
                            alert('No se pudo subir la imagen (max 5MB, formatos jpg/png/webp).');
                        }
                    };
                }

                // ---------- Short description counter ----------
                const shortDesc = document.getElementById('short_description');
                const shortCount = document.getElementById('shortCount');
                function updateShortCount() {
                    const len = shortDesc.value.length;
                    shortCount.textContent = len;
                    shortCount.parentElement.classList.toggle('warn', len >= 280);
                }
                shortDesc.addEventListener('input', updateShortCount);
                updateShortCount();

                // ---------- Icon preview + picker ----------
                const iconInput = document.getElementById('iconInput');
                const iconPreview = document.getElementById('iconPreview');
                iconInput.addEventListener('input', () => {
                    iconPreview.className = iconInput.value.trim();
                });

                const BOOTSTRAP_ICONS = ['bi-house','bi-house-fill','bi-house-heart','bi-house-check','bi-building','bi-shop','bi-shop-window','bi-briefcase','bi-briefcase-fill','bi-brush','bi-brush-fill','bi-bucket','bi-bucket-fill','bi-droplet','bi-droplet-fill','bi-droplet-half','bi-water','bi-stars','bi-star','bi-star-fill','bi-shield-check','bi-shield-fill-check','bi-shield-plus','bi-award','bi-award-fill','bi-patch-check','bi-patch-check-fill','bi-check-circle','bi-check-circle-fill','bi-hand-thumbs-up','bi-heart','bi-heart-fill','bi-people','bi-people-fill','bi-person','bi-person-check','bi-person-heart','bi-tools','bi-wrench','bi-hammer','bi-truck','bi-box-seam','bi-currency-dollar','bi-cash-coin','bi-clock','bi-clock-history','bi-calendar-check','bi-calendar-heart','bi-geo-alt','bi-geo-alt-fill','bi-map','bi-globe','bi-globe-americas','bi-eco','bi-flower1','bi-flower2','bi-flower3','bi-tree','bi-tree-fill','bi-lightning','bi-lightning-fill','bi-fire','bi-snow','bi-sun','bi-moon','bi-camera','bi-image','bi-tag','bi-tag-fill','bi-bookmark-star','bi-magic','bi-recycle'];
                const grid = document.getElementById('iconPickerGrid');
                function renderIcons(filter = '') {
                    grid.innerHTML = '';
                    BOOTSTRAP_ICONS.filter(n => !filter || n.includes(filter.toLowerCase())).forEach(name => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.innerHTML = `<i class="bi ${name}"></i>`;
                        if (('bi ' + name) === iconInput.value.trim()) b.classList.add('selected');
                        b.addEventListener('click', () => {
                            iconInput.value = 'bi ' + name;
                            iconPreview.className = 'bi ' + name;
                            bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'))?.hide();
                        });
                        grid.appendChild(b);
                    });
                }
                document.getElementById('iconSearch').addEventListener('input', (e) => renderIcons(e.target.value));
                document.getElementById('iconPickerModal').addEventListener('show.bs.modal', () => renderIcons(''));

                // ---------- Publish toggle visual ----------
                const publishCb = document.getElementById('is_published');
                const publishToggle = document.getElementById('publishToggle');
                const publishLabel = document.getElementById('publishLabel');
                publishCb.addEventListener('change', () => {
                    const on = publishCb.checked;
                    publishToggle.classList.toggle('active', on);
                    publishLabel.textContent = on ? 'Publicado (visible en el sitio)' : 'Oculto (no visible)';
                });

                // ---------- SEO live analysis + previews ----------
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
                    heroImg:   document.getElementById('hero_image'),
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
                    const file = fileInput?.files?.[0];
                    if (file) { previewEl.innerHTML = '<img src="' + URL.createObjectURL(file) + '">'; return; }
                    for (const fb of fallbacks) {
                        if (fb?.files?.[0]) { previewEl.innerHTML = '<img src="' + URL.createObjectURL(fb.files[0]) + '">'; return; }
                    }
                    if (!previewEl.querySelector('img')) {
                        previewEl.innerHTML = '<div class="fbp-placeholder"><i class="bi bi-image"></i></div>';
                    }
                }
                function refreshSeo() {
                    const titleValue = seo.title?.value || '';
                    const slugValue  = (seo.slug?.value || slugify(titleValue)) || 'tu-slug';
                    const metaTitle  = seo.metaTitle.value || titleValue;
                    const metaDesc   = seo.metaDesc.value || '';
                    const focus      = (seo.focus.value || '').toLowerCase().trim();
                    const ogTitle    = seo.ogTitle.value || metaTitle;
                    const ogDesc     = seo.ogDesc.value || metaDesc;
                    const twTitle    = seo.twTitle.value || ogTitle;
                    const twDesc     = seo.twDesc.value || ogDesc;

                    counter('metaTitleCounter', 'metaTitleBar', metaTitle, 60, 70);
                    counter('metaDescCounter',  'metaDescBar',  metaDesc,  160, 200);

                    seo.gpTitle.textContent = metaTitle || 'Tu Meta Title aparecerá aquí';
                    seo.gpDesc.textContent  = metaDesc  || 'La Meta Description aparecerá aquí.';
                    seo.gpSlug.textContent  = slugValue;

                    seo.fbpTitle.textContent = ogTitle || 'Tu OG Title';
                    seo.fbpDesc.textContent  = ogDesc  || 'Tu OG Description';
                    updatePreviewImage(seo.fbpImage, seo.ogImage, [seo.heroImg]);

                    seo.twpTitle.textContent = twTitle || 'Twitter Title';
                    seo.twpDesc.textContent  = twDesc  || 'Twitter Description';
                    updatePreviewImage(seo.twpImage, seo.twImage, [seo.ogImage, seo.heroImg]);

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
                        content_len:   wordCount >= 200,
                        hero:          (seo.heroImg?.files?.[0]) || {{ $service->hero_image_path ? 'true' : 'false' }},
                        og_image:      (seo.ogImage?.files?.[0]) || (seo.heroImg?.files?.[0]) || {{ ($service->og_image_path || $service->hero_image_path) ? 'true' : 'false' }},
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

                ['metaTitle','metaDesc','focus','ogTitle','ogDesc','twTitle','twDesc','title','slug'].forEach(k => {
                    seo[k]?.addEventListener('input', refreshSeo);
                });
                ['ogImage','twImage','heroImg'].forEach(k => {
                    seo[k]?.addEventListener('change', refreshSeo);
                });
                if (contentQuill) contentQuill.on('text-change', refreshSeo);
                refreshSeo();

                // ---------- Submit: copy Quill HTML into hidden field ----------
                document.getElementById('serviceForm').addEventListener('submit', function () {
                    document.getElementById('content-input').value = contentQuill.root.innerHTML;
                });
            })();
        </script>
    @endpush
</x-app-layout>
