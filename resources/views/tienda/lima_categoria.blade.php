@extends('tienda.lima_layout')

@section('title', ($categoriaSeleccionada ? $categoriaSeleccionada->nombre . ' - ' : 'Productos - ') . $empresa->nombre)

@section('main-class', 'class="category-page"')

@section('content')
<div class="container py-4">

    {{-- Breadcrumbs --}}
    <div class="breadcrumbs-container mb-4">
        <ul class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="{{ route('tienda.index', $empresa->slug) }}">
                    <span itemprop="name">Inicio</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            @if($categoriaSeleccionada)
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">{{ $categoriaSeleccionada->nombre }}</span>
                <meta itemprop="position" content="2">
            </li>
            @else
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">Productos</span>
                <meta itemprop="position" content="2">
            </li>
            @endif
        </ul>
    </div>

    {{-- Category Header --}}
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="category-name h1">{{ $categoriaSeleccionada ? $categoriaSeleccionada->nombre : 'Todos los Productos' }}</h1>
            @if($categoriaSeleccionada && $categoriaSeleccionada->descripcion)
            <p class="category-description">{{ $categoriaSeleccionada->descripcion }}</p>
            @endif
        </div>
    </div>

    {{-- Category Controls --}}
    <div class="category-controls-container mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <button class="js-modal-open btn btn-outline-secondary btn-sm" data-toggle="#filters-modal">
                    <svg class="icon-inline mr-1" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Filtros
                </button>
            </div>
            <div class="col text-center">
                <span class="products-count">{{ $productos->total() }} productos encontrados</span>
            </div>
            <div class="col-auto ml-auto">
                <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}" id="sortForm">
                    @foreach(request()->except(['orden']) as $key => $value)
                        @if($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="orden" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="" {{ !request('orden') ? 'selected' : '' }}>Más relevantes</option>
                        <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                        <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                        <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre A-Z</option>
                        <option value="nombre_desc" {{ request('orden') == 'nombre_desc' ? 'selected' : '' }}>Nombre Z-A</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="row row-grid">
        @forelse($productos as $producto)
        @php
            // Calcular precios y descuentos
            $precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
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

        <div class="col-6 col-md-4 col-lg-3 col-grid mb-4">
            <div class="js-item-product item-product">
                <div class="js-item-info-container item m-0">
                    <div class="js-product-container position-relative">

                        {{-- Product Image --}}
                        <div class="product-item-image-container item-image">
                            <div class="js-item-image-padding position-relative d-block product-image-wrapper">
                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="js-product-item-image-link">
                                    <img class="product-item-image lazyload fade-in"
                                         data-src="{{ $producto->url_imagen_principal ?? asset('images/no-image.png') }}"
                                         src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                         alt="{{ $producto->nombre }}">
                                </a>

                                {{-- Labels --}}
                                @if($descuentoActivo || !$stockInfo['hay_stock'])
                                <div class="labels js-labels-floating-group labels-absolute">
                                    @if($descuentoActivo)
                                    <div class="label label-primary">{{ $textoDescuento }}</div>
                                    @endif
                                    @if(!$stockInfo['hay_stock'] && $stockInfo['stock_limitado'])
                                    <div class="label label-secondary">Sin Stock</div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="item-info">
                            <div class="js-item-name item-name">
                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}">
                                    {{ $producto->nombre }}
                                </a>
                            </div>

                            {{-- Price --}}
                            <div class="item-price-container">
                                @if($descuentoActivo)
                                <span class="js-compare-price-display price-compare">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                @endif
                                <span class="js-price-display item-price">${{ number_format($precioConDescuento, 0, ',', '.') }}</span>
                            </div>

                            {{-- Installments --}}
                            @if($precioConDescuento >= 1000)
                            <div class="item-installments">
                                <span class="installment-amount">3</span> x
                                <span class="installment-price">${{ number_format($precioConDescuento / 3, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            {{-- Add to Cart Button --}}
                            @if(!$producto->tiene_variantes && ($stockInfo['hay_stock'] || !$stockInfo['stock_limitado']))
                            <button type="button"
                                    class="btn btn-primary btn-sm btn-block js-quick-add-to-cart"
                                    data-producto-id="{{ $producto->id }}"
                                    data-precio="{{ $precioConDescuento }}">
                                Agregar al Carrito
                            </button>
                            @else
                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="btn btn-primary btn-sm btn-block">
                                Ver Producto
                            </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <svg class="icon-inline icon-3x mb-3" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                <p class="mb-0">No se encontraron productos.</p>
                <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn btn-primary mt-3">
                    Ver todos los productos
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($productos->hasPages())
    <nav class="pagination-container text-center mt-5" aria-label="Paginación de productos">
        {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
    </nav>
    @endif

</div>

{{-- Filters Modal --}}
<div id="filters-modal" class="js-modal modal modal-filter modal-left transition-slide" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row w-100">
                    <div class="col">
                        <h5 class="modal-title">Filtros</h5>
                    </div>
                    <div class="col-2 text-right">
                        <a href="#" class="js-modal-close modal-close" aria-label="Cerrar">
                            <svg class="icon-inline icon-lg" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                {{-- Categories Filter --}}
                @if(isset($categorias) && $categorias->count() > 0)
                <div class="filter-section mb-4">
                    <h6 class="filter-subtitle mb-3">Categorías</h6>
                    <ul class="filter-list list-unstyled">
                        @foreach($categorias as $categoria)
                        <li class="mb-2">
                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                               class="filter-link {{ request('categoria') == $categoria->id ? 'active font-weight-bold' : '' }}">
                                {{ $categoria->nombre }}
                                @if(isset($categoria->productos_count))
                                <span class="badge badge-secondary">{{ $categoria->productos_count }}</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                        <li class="mt-3">
                            <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="filter-link">
                                Ver Todas
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                {{-- Price Range Filter --}}
                <div class="filter-section mb-4">
                    <h6 class="filter-subtitle mb-3">Rango de Precio</h6>
                    <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}">
                        @foreach(request()->except(['precio_min', 'precio_max']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="number"
                                       name="precio_min"
                                       class="form-control form-control-sm"
                                       placeholder="Mínimo"
                                       value="{{ request('precio_min', $precioMin ?? 0) }}"
                                       min="0">
                            </div>
                            <div class="col-6">
                                <input type="number"
                                       name="precio_max"
                                       class="form-control form-control-sm"
                                       placeholder="Máximo"
                                       value="{{ request('precio_max', $precioMax ?? 999999) }}"
                                       min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm btn-block">Aplicar</button>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                @if(request()->anyFilled(['categoria', 'precio_min', 'precio_max', 'buscar']))
                <a href="{{ route('tienda.categorias', $empresa->slug) }}" class="btn btn-link">Limpiar filtros</a>
                @endif
                <button type="button" class="btn btn-primary js-modal-close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Quick add to cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        const quickAddButtons = document.querySelectorAll('.js-quick-add-to-cart');

        quickAddButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productoId = this.getAttribute('data-producto-id');
                const originalText = this.innerHTML;

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch("{{ route('tienda.carrito.agregar', $empresa->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                        // Update cart badge
                        const cartBadge = document.querySelector('.js-cart-widget-amount');
                        if (cartBadge && data.total_items) {
                            cartBadge.textContent = data.total_items;
                        }

                        this.innerHTML = '<i class="bi bi-check"></i> Agregado';
                        setTimeout(() => {
                            this.disabled = false;
                            this.innerHTML = originalText;
                        }, 2000);
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
@endpush
