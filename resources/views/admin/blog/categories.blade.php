<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="bi bi-folder me-2"></i>Categorías del blog</h1>
            <div>
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver a artículos
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i>Nueva categoría
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($categories->count() === 0)
                    <p class="text-muted mb-0">No hay categorías. Crea la primera.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th>Artículos</th>
                                    <th>Estado</th>
                                    <th>Orden</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $cat)
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        <td class="small text-muted">/blog/category/{{ $cat->slug }}</td>
                                        <td>{{ $cat->posts_count }}</td>
                                        <td>
                                            @if($cat->is_active)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-secondary">Inactiva</span>
                                            @endif
                                        </td>
                                        <td>{{ $cat->order }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-warning btn-sm"
                                                onclick='openEditCategory(@json($cat))'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar categoría?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="Auto si vacío">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Orden</label>
                                <input type="number" name="order" class="form-control" value="0">
                            </div>
                            <div class="col d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="cat_add_active" checked>
                                    <label class="form-check-label" for="cat_add_active">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editCatName" class="form-control" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="editCatSlug" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" id="editCatDescription" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="editCatMetaTitle" class="form-control" maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="editCatMetaDescription" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Orden</label>
                                <input type="number" name="order" id="editCatOrder" class="form-control">
                            </div>
                            <div class="col d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="editCatActive">
                                    <label class="form-check-label" for="editCatActive">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEditCategory(cat) {
                document.getElementById('editCategoryForm').action = "{{ url('admin/blog/categories') }}/" + cat.id;
                document.getElementById('editCatName').value = cat.name || '';
                document.getElementById('editCatSlug').value = cat.slug || '';
                document.getElementById('editCatDescription').value = cat.description || '';
                document.getElementById('editCatMetaTitle').value = cat.meta_title || '';
                document.getElementById('editCatMetaDescription').value = cat.meta_description || '';
                document.getElementById('editCatOrder').value = cat.order ?? 0;
                document.getElementById('editCatActive').checked = !!cat.is_active;
                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            }
        </script>
    @endpush
</x-app-layout>
