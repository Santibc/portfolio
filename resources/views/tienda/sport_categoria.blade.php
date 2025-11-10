@extends('tienda.sport_layout')

@section('title', ($categoriaSeleccionada ? $categoriaSeleccionada->nombre . ' - ' : 'Categorías - ') . $empresa->nombre)
@section('description', $categoriaSeleccionada ? 'Explora nuestros productos de ' . $categoriaSeleccionada->nombre : 'Explora todos nuestros productos')

@push('styles')
<style>
    /* Category Page Styles */
    .category-header {
        padding: 40px 0 20px;
    }

    .category-title {
        font-size: var(--h1-huge);
        font-weight: 700;
        margin-bottom: 10px;
    }

    @media (min-width: 768px) {
        .category-title {
            font-size: var(--h1-huge-md);
        }
    }

    /* Sidebar Filters */
    .filters-sidebar {
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .filter-section {
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .filter-section:last-child {
        border-bottom: none;
    }

    .filter-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .filter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .filter-list li {
        margin-bottom: 10px;
    }

    .filter-list a {
        font-size: 14px;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .filter-list a:hover,
    .filter-list a.active {
        opacity: 1;
        font-weight: 700;
    }

    /* Price Filter */
    .price-filter-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .price-input-group {
        display: flex;
        flex-direction: column;
    }

    .price-input-group label {
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .price-input-group input {
        padding: 10px;
        border: 1px solid rgba(0,0,0,0.2);
        border-radius: var(--border-radius);
        font-family: var(--body-font);
    }

    .price-filter-btn {
        width: 100%;
    }

    .price-filter-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Sort Section */
    .sort-section {
        margin-bottom: 30px;
    }

    .sort-select {
        padding: 10px 15px;
        border: 1px solid rgba(0,0,0,0.2);
        border-radius: var(--border-radius);
        font-family: var(--body-font);
        font-size: 14px;
        cursor: pointer;
        background-color: #fff;
        min-width: 220px;
    }

    /* Products Grid */
    .products-container {
        padding-left: 30px;
    }

    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .products-count {
        font-size: 14px;
        opacity: 0.7;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .product-card {
        background: #fff;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.3s;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }

    .product-image-container {
        position: relative;
        height: 350px;
        overflow: hidden;
        background-color: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }

    .product-labels {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 10;
    }

    .product-label {
        background-color: #000000;
        color: #FFFFFF;
        padding: 5px 15px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .product-label.discount,
    .product-label.new,
    .product-label.shipping {
        background-color: #000000;
        color: #FFFFFF;
    }

    .product-info {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .product-content {
        flex: 1;
    }

    .product-prices {
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 24px;
        font-weight: 700;
        color: var(--accent-color);
    }

    .product-price-old {
        font-size: 16px;
        text-decoration: line-through;
        opacity: 0.5;
        margin-left: 10px;
    }

    .product-variants {
        margin-bottom: 15px;
    }

    .variant-label {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
        display: block;
    }

    .variant-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .variant-btn {
        padding: 5px 10px;
        border: 1px solid rgba(0,0,0,0.2);
        border-radius: var(--border-radius);
        background: #fff;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s;
    }

    .variant-btn:hover,
    .variant-btn.selected {
        background-color: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
    }

    .btn-add-to-cart {
        width: 100%;
    }

    /* Mobile Filters Toggle */
    .mobile-filters-toggle {
        display: none;
        width: 100%;
        margin-bottom: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filters-sidebar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: #fff;
            z-index: 2000;
            overflow-y: auto;
            padding: 20px;
        }

        .filters-sidebar.show {
            display: block;
        }

        .products-container {
            padding-left: 0;
        }

        .mobile-filters-toggle {
            display: block;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .category-title {
            font-size: 48px;
        }
    }
</style>
@endpush

@section('content')

<!-- Category Header -->
<section class="category-header">
    <div class="container-fluid">
        <h1 class="category-title">{{ $categoriaSeleccionada ? strtoupper($categoriaSeleccionada->nombre) : 'PRODUCTOS' }}</h1>
        <p>{{ $categoriaSeleccionada ? $categoriaSeleccionada->descripcion ?? 'Productos de ' . $categoriaSeleccionada->nombre : 'Todos los productos' }}</p>
    </div>
</section>

<!-- Mobile Filters Toggle -->
<div class="container-fluid">
    <button class="btn mobile-filters-toggle" onclick="toggleMobileFilters()">
        <svg class="icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; margin-right: 10px;">
            <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
        </svg>
        FILTROS Y ORDENAR
    </button>
</div>

<!-- Category Body -->
<section class="category-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3 filters-sidebar" id="filtersSidebar">
                <!-- Close button (mobile) -->
                <button class="btn btn-link d-md-none mb-3" onclick="toggleMobileFilters()" style="float: right;">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>

                <!-- Sort By -->
                <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}" id="filterForm">
                    @foreach(request()->except(['orden']) as $key => $value)
                        @if($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <div class="sort-section filter-section">
                        <h2 class="filter-title">ORDENAR POR</h2>
                        <select class="sort-select" name="orden" id="sortSelect" onchange="this.form.submit()">
                            <option value="" {{ !request('orden') ? 'selected' : '' }}>Más recientes</option>
                            <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre A-Z</option>
                        </select>
                    </div>
                </form>

                <!-- Categories Filter -->
                <div class="filter-section">
                    <h2 class="filter-title">CATEGORÍAS</h2>
                    <ul class="filter-list">
                        <li>
                            <a href="{{ route('tienda.categorias', $empresa->slug) }}"
                               class="{{ !request('categoria') ? 'active' : '' }}">
                                Todos los productos
                            </a>
                        </li>
                        @foreach($categorias as $categoria)
                        <li>
                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                               class="{{ request('categoria') == $categoria->id ? 'active' : '' }}">
                                {{ $categoria->nombre }}
                                <span style="opacity: 0.6;">({{ $categoria->productos_count ?? 0 }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Price Filter -->
                <div class="filter-section">
                    <h2 class="filter-title">PRECIO</h2>
                    <form class="price-filter-form" method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}" id="priceFilterForm">
                        @foreach(request()->except(['precio_min', 'precio_max']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <div class="price-input-group">
                            <label for="minPrice">Desde</label>
                            <input type="number" id="minPrice" name="precio_min"
                                   placeholder="{{ number_format($precioMin, 0, ',', '.') }}"
                                   value="{{ request('precio_min') }}"
                                   min="{{ $precioMin }}" max="{{ $precioMax }}" step="1000">
                        </div>
                        <div class="price-input-group">
                            <label for="maxPrice">Hasta</label>
                            <input type="number" id="maxPrice" name="precio_max"
                                   placeholder="{{ number_format($precioMax, 0, ',', '.') }}"
                                   value="{{ request('precio_max') }}"
                                   min="{{ $precioMin }}" max="{{ $precioMax }}" step="1000">
                        </div>
                        <button type="submit" class="btn price-filter-btn" id="applyPriceFilter">Aplicar</button>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-md-9 products-container">
                <div class="products-header">
                    <div class="products-count">
                        Mostrando {{ $productos->count() }} de {{ $productos->total() }} productos
                    </div>
                </div>

                <div class="products-grid">
                    @forelse($productos as $producto)
                    @php
                        // Buscar descuentos activos para este producto
                        $descuentoActivo = null;
                        $textoDescuento = null;
                        $precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
                        $precioConDescuento = $precioActual;
                        $montoDescuento = 0;

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

                    <div class="product-card">
                        <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" style="text-decoration: none; color: inherit;">
                            <div class="product-image-container">
                                <img src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $producto->nombre }}" class="product-image">
                                @if($descuentoActivo || ($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock']))
                                <div class="product-labels">
                                    @if($descuentoActivo)
                                        <span class="product-label discount">{{ $textoDescuento }}</span>
                                    @elseif($stockInfo['stock_disponible'] <= 5 && $stockInfo['stock_disponible'] > 0)
                                        <span class="product-label new">¡ÚLTIMAS!</span>
                                    @elseif($stockInfo['stock_disponible'] == 0)
                                        <span class="product-label">SIN STOCK</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="product-info">
                                <div class="product-content">
                                    <h3 class="product-name">{{ strtoupper($producto->nombre) }}</h3>
                                    <div class="product-prices">
                                        @if($precioActual)
                                            @if($descuentoActivo)
                                                <span class="product-price">${{ number_format($precioConDescuento, 0, ',', '.') }}</span>
                                                <span class="product-price-old">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                            @else
                                                <span class="product-price">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                            @endif
                                        @else
                                            <span class="product-price" style="font-size: 16px;">Consultar precio</span>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-add-to-cart" onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito({{ $producto->id }}, this);"
                                        {{ (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado']) ? 'disabled' : '' }}>
                                    {{ (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado']) ? 'SIN STOCK' : 'AGREGAR AL CARRITO' }}
                                </button>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                        <h3 style="font-size: 24px; margin-bottom: 20px;">No se encontraron productos</h3>
                        <p style="margin-bottom: 30px; opacity: 0.7;">Intenta ajustar los filtros o buscar otra categoría</p>
                        <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn">VER TODOS LOS PRODUCTOS</a>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($productos->hasPages())
                <div class="pagination-container" style="margin-top: 40px; display: flex; justify-content: center;">
                    {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Mobile filters toggle
    function toggleMobileFilters() {
        const sidebar = document.getElementById('filtersSidebar');
        sidebar.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }

    // Agregar al carrito function
    function agregarAlCarrito(productoId, btn) {
        if (!btn) return;

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span style="font-size: 12px;">...</span>';

        // Simulate AJAX call
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
            btn.innerHTML = '✓ AGREGADO';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);

            // Update cart badge using global function
            if (typeof window.updateCartBadge === 'function' && data.total_items) {
                window.updateCartBadge(data.total_items);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = 'ERROR';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);
        });
    }

    // Variant buttons - prevent default on click
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.variant-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });
    });
</script>
@endpush
