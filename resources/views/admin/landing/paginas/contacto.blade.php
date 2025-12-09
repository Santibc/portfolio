<x-app-layout>
    <x-slot name="header">Editar Página de Contacto</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.landing.paginas.update', 'contacto') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="titulo" value="{{ $pagina->titulo }}">

            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-envelope"></i> Contenido del Formulario</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título Principal</label>
                            <input type="text" name="contenido[hero_title]" class="form-control"
                                   value="{{ $pagina->contenido['hero_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción (HTML permitido)</label>
                            <textarea name="contenido[hero_description]" class="form-control" rows="3">{{ $pagina->contenido['hero_description'] ?? '' }}</textarea>
                            <small class="text-muted">Puedes usar <code>&lt;span class="highlight3"&gt;texto&lt;/span&gt;</code> para resaltar texto</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Las redes sociales y el email de contacto se configuran desde
                <a href="{{ route('admin.landing.configuracion.edit') }}">Layout</a>
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
