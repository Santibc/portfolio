<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="bi bi-journal-text me-2"></i>Blog — Artículos</h1>
            <div>
                <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-folder me-1"></i>Categorías
                </a>
                <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo artículo
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="q" class="form-control" placeholder="Buscar por título..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="published" @selected(request('status') === 'published')>Publicados</option>
                            <option value="draft" @selected(request('status') === 'draft')>Borradores</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($posts->count() === 0)
                    <p class="text-muted mb-0">No hay artículos. <a href="{{ route('admin.blog.posts.create') }}">Crea el primero</a>.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Publicado</th>
                                    <th>Vistas</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                    <tr>
                                        <td>
                                            <strong>{{ $post->title }}</strong>
                                            <div class="small text-muted">/blog/{{ $post->slug }}</div>
                                        </td>
                                        <td>{{ $post->category?->name ?? '—' }}</td>
                                        <td>
                                            @if($post->is_published)
                                                <span class="badge bg-success">Publicado</span>
                                            @else
                                                <span class="badge bg-secondary">Borrador</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ optional($post->published_at)->format('Y-m-d H:i') ?? '—' }}
                                        </td>
                                        <td>{{ $post->views_count }}</td>
                                        <td class="text-end">
                                            @if($post->is_published)
                                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este artículo?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
