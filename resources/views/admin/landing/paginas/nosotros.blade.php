<x-app-layout>
    <x-slot name="header">Editar Página Sobre Nosotros</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.landing.paginas.update', 'nosotros') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="titulo" value="{{ $pagina->titulo }}">

            {{-- Hero --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Sección Hero</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="contenido[hero_subtitle]" class="form-control"
                                   value="{{ $pagina->contenido['hero_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[hero_title]" class="form-control"
                                   value="{{ $pagina->contenido['hero_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML permitido)</label>
                            <textarea name="contenido[hero_description]" class="form-control" rows="2">{{ $pagina->contenido['hero_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Misión --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bullseye"></i> Misión</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[mision_titulo]" class="form-control"
                                   value="{{ $pagina->contenido['mision_titulo'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Texto (HTML permitido)</label>
                            <textarea name="contenido[mision_texto]" class="form-control" rows="4">{{ $pagina->contenido['mision_texto'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen</label>
                            @if(isset($pagina->contenido['mision_imagen']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['mision_imagen']) }}" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" name="mision_imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Visión --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-eye"></i> Visión</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[vision_titulo]" class="form-control"
                                   value="{{ $pagina->contenido['vision_titulo'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Texto (HTML permitido)</label>
                            <textarea name="contenido[vision_texto]" class="form-control" rows="4">{{ $pagina->contenido['vision_texto'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen</label>
                            @if(isset($pagina->contenido['vision_imagen']))
                                <div class="mb-2">
                                    <img src="{{ asset($pagina->contenido['vision_imagen']) }}" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" name="vision_imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Valores --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-heart"></i> Valores</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título de la Sección</label>
                            <input type="text" name="contenido[valores_titulo]" class="form-control"
                                   value="{{ $pagina->contenido['valores_titulo'] ?? '' }}">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Los valores se gestionan desde
                        <a href="{{ route('admin.landing.valores.index') }}">Valores</a>
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Las estadísticas se gestionan desde
                        <a href="{{ route('admin.landing.estadisticas.index') }}">Estadísticas</a>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-megaphone"></i> Sección CTA</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="contenido[cta_titulo]" class="form-control"
                                   value="{{ $pagina->contenido['cta_titulo'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="contenido[cta_descripcion]" class="form-control"
                                   value="{{ $pagina->contenido['cta_descripcion'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Botón 1</label>
                            <input type="text" name="contenido[cta_btn1_texto]" class="form-control"
                                   value="{{ $pagina->contenido['cta_btn1_texto'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Texto Botón 2</label>
                            <input type="text" name="contenido[cta_btn2_texto]" class="form-control"
                                   value="{{ $pagina->contenido['cta_btn2_texto'] ?? '' }}">
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
