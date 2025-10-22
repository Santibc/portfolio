@extends('tienda.brasilia_layout')

@section('title', ($categoriaSeleccionada ? $categoriaSeleccionada->nombre . ' - ' : 'Productos - ') . $empresa->nombre)

@section('main-class', 'class="category-page"')

@section('content')
    <div class="container py-4">
        <!-- Category Controls -->
        <div class="category-controls mb-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="category-title">{{ $categoriaSeleccionada ? $categoriaSeleccionada->nombre : 'Productos' }}</h1>
                    <p class="category-count">Mostrando <span class="js-products-count">{{ $productos->total() }}</span> productos</p>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end gap-2 align-items-center">
                        <!-- Mobile Filter Button -->
                        <button class="btn btn-outline-secondary d-md-none" data-bs-toggle="modal" data-bs-target="#modal-filters">
                            <svg class="icon-inline mr-1"><use xlink:href="#filter"></use></svg>
                            Filtros
                        </button>
                        <!-- Sort Select (Desktop) -->
                        <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}" id="sortForm" class="d-none d-md-inline-block mb-0">
                            @foreach(request()->except(['orden']) as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <div class="form-group mb-0">
                                <label class="font-small d-block mb-1">Ordenar por</label>
                                <select name="orden" class="form-select form-select-sm" aria-label="Ordenar por" onchange="this.form.submit()">
                                    <option value="" {{ !request('orden') ? 'selected' : '' }}>Más recientes</option>
                                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                                    <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                                </select>
                            </div>
                        </form>
                        <!-- Sort Button (Mobile) -->
                        <button class="btn btn-outline-secondary d-md-none" data-bs-toggle="modal" data-bs-target="#modal-sort-by">
                            <svg class="icon-inline mr-1"><use xlink:href="#sort"></use></svg>
                            Ordenar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid: Filters + Products -->
        <div class="category-grid">
            <!-- Sidebar Filters (Desktop) -->
            <aside class="category-sidebar d-none d-md-block">
                <h2 class="filter-title mb-4">Filtrar por</h2>

                <!-- Categories Filter -->
                <div class="filter-section mb-4">
                    <h6 class="filter-subtitle">Categorías</h6>
                    <ul class="filter-list">
                        @foreach($categorias as $categoria)
                        <li>
                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                               class="filter-link {{ request('categoria') == $categoria->id ? 'active' : '' }}">
                                {{ $categoria->nombre }} ({{ $categoria->productos_count ?? 0 }})
                            </a>
                        </li>
                        @endforeach

                        @if($categorias->count() > 0)
                        <li class="mt-3">
                            <a href="{{ route('tienda.categorias', $empresa->slug) }}"
                               class="filter-link {{ !request('categoria') ? 'active' : '' }}">
                                Ver Todas
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <!-- Price Filter -->
                <div class="filter-section mb-4">
                    <h6 class="filter-subtitle">Precio</h6>
                    <form id="priceRangeForm" method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}">
                        @foreach(request()->except(['precio_min', 'precio_max']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="price-range-container">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <input type="number" name="precio_min" class="form-control form-control-sm"
                                           placeholder="Mínimo" id="priceMin"
                                           value="{{ request('precio_min', $precioMin) }}"
                                           min="{{ $precioMin }}" max="{{ $precioMax }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="precio_max" class="form-control form-control-sm"
                                           placeholder="Máximo" id="priceMax"
                                           value="{{ request('precio_max', $precioMax) }}"
                                           min="{{ $precioMin }}" max="{{ $precioMax }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Aplicar</button>
                        </div>
                    </form>
                </div>

                <!-- Clear Filters -->
                @if(request()->anyFilled(['categoria', 'precio_min', 'precio_max', 'buscar']))
                <div class="filter-clear">
                    <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn btn-link text-decoration-none">Limpiar filtros</a>
                </div>
                @endif
            </aside>

            <!-- Products Grid -->
            <div class="category-products">
                <div class="products-grid">
                    @forelse($productos as $index => $producto)
                    @php
                        $precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
                        $precioAnterior = is_object($producto->precio_actual) && isset($producto->precio_actual->precio_anterior) ? $producto->precio_actual->precio_anterior : null;

                        // Buscar descuentos activos para este producto
                        $descuentoActivo = null;
                        $textoDescuento = null;
                        $precioConDescuento = $precioActual;

                        if (isset($descuentosActivos)) {
                            foreach ($descuentosActivos as $desc) {
                                $aplica = false;

                                if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                    $aplica = true;
                                } elseif ($desc->aplica_a === 'producto' && in_array($producto->id, $desc->productos_aplicables ?? [])) {
                                    $aplica = true;
                                } elseif ($desc->aplica_a === 'categoria' && in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])) {
                                    $aplica = true;
                                }

                                if ($aplica) {
                                    $descuentoActivo = $desc;
                                    if ($desc->tipo === 'porcentaje') {
                                        $montoDescuento = ($precioActual * $desc->valor) / 100;
                                        $textoDescuento = round($desc->valor) . '% OFF';
                                    } else {
                                        $montoDescuento = $desc->valor;
                                        $textoDescuento = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                    }
                                    $precioConDescuento = $precioActual - $montoDescuento;
                                    break;
                                }
                            }
                        }

                        $stockInfo = $producto->getStockInfo();
                    @endphp
                    <div class="product-card-cat">
                        @if($descuentoActivo)
                        <span class="product-badge product-badge-discount">{{ $textoDescuento }}</span>
                        @endif
                        <div class="product-image-wrapper">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="product-image-link">
                                <div class="product-image-container-square">
                                    <img src="{{ $producto->url_imagen_principal ?? asset('images/no-image.png') }}"
                                         alt="{{ $producto->nombre }}"
                                         class="product-image"
                                         style="width: 100% !important; height: 100% !important; object-fit: cover !important; object-position: center !important;">
                                </div>
                            </a>
                            <div class="product-actions">
                                @if($producto->tiene_variantes)
                                    <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="btn btn-primary btn-small">
                                        <span>Ver Opciones</span>
                                    </a>
                                @else
                                    <button class="btn btn-primary btn-small quick-add-btn"
                                            data-producto-id="{{ $producto->id }}"
                                            data-precio="{{ $precioActual }}"
                                            {{ (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado']) ? 'disabled' : '' }}>
                                        <span>{{ (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado']) ? 'Sin Stock' : 'Comprar' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}">{{ $producto->nombre }}</a>
                            </h3>
                            <div class="product-price-container">
                                @if($descuentoActivo)
                                    <span class="product-price-old">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                    <span class="product-price">${{ number_format($precioConDescuento, 0, ',', '.') }}</span>
                                @else
                                    <span class="product-price">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <p class="product-installments" style="font-size: 11px; color: #6c757d; margin-top: 4px;">3 x ${{ number_format(($descuentoActivo ? $precioConDescuento : $precioActual) / 3, 0, ',', '.') }} sin interés</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p class="mb-0">No se encontraron productos con los filtros seleccionados.</p>
                            <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn btn-primary mt-3">
                                Ver todos los productos
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($productos->hasPages())
                <nav class="pagination-container mt-5" aria-label="Paginación de productos">
                    {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
                </nav>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Filters (Mobile) -->
    <div class="modal fade" id="modal-filters" tabindex="-1" aria-labelledby="modalFiltersLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFiltersLabel">Filtrar por</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Categories Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Categorías</h6>
                        <ul class="filter-list">
                            @foreach($categorias as $categoria)
                            <li>
                                <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                                   class="filter-link {{ request('categoria') == $categoria->id ? 'active' : '' }}">
                                    {{ $categoria->nombre }} ({{ $categoria->productos_count ?? 0 }})
                                </a>
                            </li>
                            @endforeach

                            @if($categorias->count() > 0)
                            <li class="mt-3">
                                <a href="{{ route('tienda.categorias', $empresa->slug) }}"
                                   class="filter-link {{ !request('categoria') ? 'active' : '' }}">
                                    Ver Todas
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Precio</h6>
                        <form id="priceRangeFormMobile" method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}">
                            @foreach(request()->except(['precio_min', 'precio_max']) as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <div class="price-range-container">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <input type="number" name="precio_min" class="form-control form-control-sm"
                                               placeholder="Mínimo"
                                               value="{{ request('precio_min', $precioMin) }}"
                                               min="{{ $precioMin }}" max="{{ $precioMax }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="precio_max" class="form-control form-control-sm"
                                               placeholder="Máximo"
                                               value="{{ request('precio_max', $precioMax) }}"
                                               min="{{ $precioMin }}" max="{{ $precioMax }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Aplicar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(request()->anyFilled(['categoria', 'precio_min', 'precio_max', 'buscar']))
                    <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn btn-link text-decoration-none">Limpiar filtros</a>
                    @endif
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ver productos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sort By -->
    <div class="modal fade" id="modal-sort-by" tabindex="-1" aria-labelledby="modalSortByLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSortByLabel">Ordenar por</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}" id="sortFormMobile">
                        @foreach(request()->except(['orden']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="sort-options">
                            <label class="sort-option">
                                <input type="radio" name="orden" value="" {{ !request('orden') ? 'checked' : '' }}>
                                <span class="sort-label">Más recientes</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="orden" value="precio_asc" {{ request('orden') == 'precio_asc' ? 'checked' : '' }}>
                                <span class="sort-label">Precio: menor a mayor</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="orden" value="precio_desc" {{ request('orden') == 'precio_desc' ? 'checked' : '' }}>
                                <span class="sort-label">Precio: mayor a menor</span>
                            </label>
                            <label class="sort-option">
                                <input type="radio" name="orden" value="nombre" {{ request('orden') == 'nombre' ? 'checked' : '' }}>
                                <span class="sort-label">Nombre</span>
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('sortFormMobile').submit()">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Quick add to cart (Vanilla JavaScript)
        document.addEventListener('DOMContentLoaded', function() {
            const quickAddButtons = document.querySelectorAll('.quick-add-btn');

            quickAddButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productoId = this.getAttribute('data-producto-id');
                    const precio = this.getAttribute('data-precio');

                    if (!precio) {
                        alert('Este producto no tiene precio configurado');
                        return;
                    }

                    const originalText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch("{{ route('tienda.carrito.agregar', $empresa->slug) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            producto_id: productoId,
                            cantidad: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            this.disabled = false;
                            this.innerHTML = originalText;
                        } else {
                            // Update cart badge if exists
                            const badge = document.querySelector('.header-action-btn .badge');
                            if (data.total_items > 0) {
                                if (badge) {
                                    badge.textContent = data.total_items;
                                } else {
                                    const cartBtn = document.querySelector('.header-action-btn');
                                    if (cartBtn) {
                                        const newBadge = document.createElement('span');
                                        newBadge.className = 'badge';
                                        newBadge.textContent = data.total_items;
                                        cartBtn.appendChild(newBadge);
                                    }
                                }
                            }

                            this.innerHTML = '<span>Agregado</span>';
                            setTimeout(() => {
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }, 1500);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al agregar al carrito');
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
                });
            });
        });
    </script>
@endsection
