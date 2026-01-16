<x-app-layout>
    <x-slot name="header">Editar Página de Inicio</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.landing.paginas.update', 'inicio') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="titulo" value="{{ $pagina->titulo }}">

            {{-- Hero Section --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Sección Hero</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Imagen de Fondo del Hero</label>
                            @if(isset($pagina->contenido['imagen_hero_background']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['imagen_hero_background']) }}" style="max-height: 150px; border-radius: 8px;">
                                    <small class="d-block text-muted mt-1">Imagen actual del fondo del hero</small>
                                </div>
                            @else
                                <div class="mb-2">
                                    <img src="{{ asset('images/background1.png') }}" style="max-height: 150px; border-radius: 8px; opacity: 0.7;">
                                    <small class="d-block text-muted mt-1">Imagen por defecto (background1.png)</small>
                                </div>
                            @endif
                            <input type="file" name="imagen_hero_background" class="form-control" accept="image/*">
                            <small class="text-muted">Recomendado: imagen de al menos 1920x1080px para mejor calidad</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título Principal (HTML permitido)</label>
                            <textarea name="contenido[hero_title]" class="form-control" rows="2">{{ $pagina->contenido['hero_title'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subtítulo (HTML permitido)</label>
                            <textarea name="contenido[hero_subtitle]" class="form-control" rows="2">{{ $pagina->contenido['hero_subtitle'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Beneficios (separados por |)</label>
                            <input type="text" name="contenido[hero_benefits]" class="form-control"
                                   value="{{ $pagina->contenido['hero_benefits'] ?? '' }}"
                                   placeholder="Beneficio 1|Beneficio 2|Beneficio 3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Botón Primario</label>
                            <input type="text" name="contenido[hero_btn_primary]" class="form-control"
                                   value="{{ $pagina->contenido['hero_btn_primary'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Botón Secundario</label>
                            <input type="text" name="contenido[hero_btn_secondary]" class="form-control"
                                   value="{{ $pagina->contenido['hero_btn_secondary'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección Oferta --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gift"></i> Sección Oferta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[offer_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['offer_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[offer_title]" class="form-control"
                                   value="{{ $pagina->contenido['offer_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML permitido)</label>
                            <textarea name="contenido[offer_description]" class="form-control" rows="2">{{ $pagina->contenido['offer_description'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Tarjetas</h6>
                    @for($i = 1; $i <= 3; $i++)
                    <div class="border rounded p-3 mb-3">
                        <h6>Tarjeta {{ $i }}</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Icono (clase)</label>
                                <input type="text" name="contenido[card{{ $i }}_icon]" class="form-control"
                                       value="{{ $pagina->contenido['card'.$i.'_icon'] ?? '' }}"
                                       placeholder="bi bi-laptop">
                            </div>
                            <div class="col-md-8 mb-2">
                                <label class="form-label">Título</label>
                                <input type="text" name="contenido[card{{ $i }}_title]" class="form-control"
                                       value="{{ $pagina->contenido['card'.$i.'_title'] ?? '' }}">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Descripción (HTML permitido)</label>
                                <textarea name="contenido[card{{ $i }}_description]" class="form-control" rows="2">{{ $pagina->contenido['card'.$i.'_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Sección Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título (HTML permitido)</label>
                            <textarea name="contenido[stats_title]" class="form-control" rows="2">{{ $pagina->contenido['stats_title'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[stats_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['stats_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contador (número)</label>
                            <input type="number" name="contenido[stats_count]" class="form-control"
                                   value="{{ $pagina->contenido['stats_count'] ?? '' }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Etiqueta</label>
                            <input type="text" name="contenido[stats_label]" class="form-control"
                                   value="{{ $pagina->contenido['stats_label'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección Features --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Sección Características</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen Principal</label>
                            @if(isset($pagina->contenido['imagen_seccion_principal']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['imagen_seccion_principal']) }}" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="imagen_seccion_principal" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[features_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['features_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[features_title]" class="form-control"
                                   value="{{ $pagina->contenido['features_title'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Introducción (HTML)</label>
                            <textarea name="contenido[features_intro]" class="form-control" rows="2">{{ $pagina->contenido['features_intro'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Pasos</h6>
                    @for($i = 1; $i <= 3; $i++)
                    <div class="border rounded p-3 mb-3">
                        <h6>Paso {{ $i }}</h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Icono (clase)</label>
                                <input type="text" name="contenido[step{{ $i }}_icon]" class="form-control"
                                       value="{{ $pagina->contenido['step'.$i.'_icon'] ?? '' }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Color (pink/blue)</label>
                                <input type="text" name="contenido[step{{ $i }}_color]" class="form-control"
                                       value="{{ $pagina->contenido['step'.$i.'_color'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Título</label>
                                <input type="text" name="contenido[step{{ $i }}_title]" class="form-control"
                                       value="{{ $pagina->contenido['step'.$i.'_title'] ?? '' }}">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Descripción (HTML)</label>
                                <textarea name="contenido[step{{ $i }}_description]" class="form-control" rows="2">{{ $pagina->contenido['step'.$i.'_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Better Together --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Sección Better Together</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[bt_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['bt_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[bt_title]" class="form-control"
                                   value="{{ $pagina->contenido['bt_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML)</label>
                            <textarea name="contenido[bt_description]" class="form-control" rows="3">{{ $pagina->contenido['bt_description'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen</label>
                            @if(isset($pagina->contenido['imagen_better_together']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['imagen_better_together']) }}" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="imagen_better_together" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Footer</label>
                            <input type="text" name="contenido[bt_footer]" class="form-control"
                                   value="{{ $pagina->contenido['bt_footer'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Beneficios --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-check2-circle"></i> Sección Beneficios</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[access_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['access_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[access_title]" class="form-control"
                                   value="{{ $pagina->contenido['access_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML)</label>
                            <textarea name="contenido[access_description]" class="form-control" rows="2">{{ $pagina->contenido['access_description'] ?? '' }}</textarea>
                        </div>
                    </div>

                    @for($i = 1; $i <= 4; $i++)
                    <div class="row border-top pt-3 mt-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Beneficio {{ $i }} - Título</label>
                            <input type="text" name="contenido[benefit{{ $i }}_title]" class="form-control"
                                   value="{{ $pagina->contenido['benefit'.$i.'_title'] ?? '' }}">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Beneficio {{ $i }} - Descripción</label>
                            <input type="text" name="contenido[benefit{{ $i }}_description]" class="form-control"
                                   value="{{ $pagina->contenido['benefit'.$i.'_description'] ?? '' }}">
                        </div>
                    </div>
                    @endfor

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen Beneficios</label>
                            @if(isset($pagina->contenido['imagen_beneficios']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['imagen_beneficios']) }}" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="imagen_beneficios" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Botón CTA</label>
                            <input type="text" name="contenido[benefits_cta_text]" class="form-control"
                                   value="{{ $pagina->contenido['benefits_cta_text'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-megaphone"></i> Sección CTA Final</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[cta_title]" class="form-control"
                                   value="{{ $pagina->contenido['cta_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML)</label>
                            <textarea name="contenido[cta_description]" class="form-control" rows="2">{{ $pagina->contenido['cta_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
