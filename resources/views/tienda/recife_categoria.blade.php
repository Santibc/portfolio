@extends('tienda.recife_layout')

@section('title', ($categoriaSeleccionada ? $categoriaSeleccionada->nombre . ' - ' : 'Categorías - ') .
    $empresa->nombre)
@section('description', $categoriaSeleccionada ? 'Explora nuestros productos de ' . $categoriaSeleccionada->nombre :
    'Explora todos nuestros productos')

@section('content')
    <section class="page-header py-4 " data-store="page-title">
        <div class="container">
            <div class="breadcrumbs ">
                <a class="crumb" href="{{ route('tienda.empresa', $empresa->slug) }}"
                    title="{{ $empresa->nombre }}">Inicio</a>
                <span class="separator">.</span>
                <span class="crumb active">{{ $categoriaSeleccionada ? $categoriaSeleccionada->nombre : 'Productos' }}</span>
            </div>
            <h1 class="h2 h1-huge-md ">{{ $categoriaSeleccionada ? $categoriaSeleccionada->nombre : 'Productos' }}</h1>
        </div>
    </section>

    <section class="js-category-controls category-controls visible-when-content-ready d-md-none">
        <div class="container category-controls-container">
            <div class="category-controls-row row">
                <div class="col col-md-auto category-control-item">
                    <a href="#" class="d-block font-small font-md-body px-md-0 py-md-2"
                        onclick="openSortModal(event)">
                        <div class="d-flex justify-content-center align-items-center">
                            <svg class="icon-inline icon-lg mr-2 svg-icon-text">
                                <use xlink:href="#sort"></use>
                            </svg>
                            Ordenar
                        </div>
                    </a>


                    <div id="sort-by"
                        class="js-modal   modal modal-bottom modal-centered modal-bottom-sheet modal-right-md modal-bottom modal--md transition-slide modal-docked-md transition-soft "
                        style="display: none;">
                        <div class="js-modal-close   modal-header  ">
                            <div class="row no-gutters align-items-center">
                                <div class="col p-3">
                                    Ordenar por
                                </div>
                                <div class="col-auto">
                                    <a class="modal-close" onclick="closeSortModal(event)">
                                        <svg class="icon-inline svg-icon-text">
                                            <use xlink:href="#times"></use>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body ">





                            <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}"
                                id="filterFormMobile">
                                @foreach (request()->except(['orden']) as $key => $value)
                                    @if ($value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <ul class="list-unstyled">
                                    <li class="">
                                        <a href="#"
                                            class="radio-button radio-button-item {{ !request('orden') ? 'selected' : '' }}"
                                            onclick="event.preventDefault(); document.querySelector('#filterFormMobile input[name=orden]').value=''; document.getElementById('filterFormMobile').submit();">
                                            <div class="radio-button-content">
                                                <span class="radio-button-icons-container">
                                                    <div class="radio-button-icon unchecked"></div>
                                                    <div class="radio-button-icon checked"></div>
                                                </span>
                                                <span class="radio-button-label">Más recientes</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="#"
                                            class="radio-button radio-button-item {{ request('orden') == 'precio_asc' ? 'selected' : '' }}"
                                            onclick="event.preventDefault(); document.querySelector('#filterFormMobile input[name=orden]').value='precio_asc'; document.getElementById('filterFormMobile').submit();">
                                            <div class="radio-button-content">
                                                <span class="radio-button-icons-container">
                                                    <div class="radio-button-icon unchecked"></div>
                                                    <div class="radio-button-icon checked"></div>
                                                </span>
                                                <span class="radio-button-label">Precio: menor a mayor</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="#"
                                            class="radio-button radio-button-item {{ request('orden') == 'precio_desc' ? 'selected' : '' }}"
                                            onclick="event.preventDefault(); document.querySelector('#filterFormMobile input[name=orden]').value='precio_desc'; document.getElementById('filterFormMobile').submit();">
                                            <div class="radio-button-content">
                                                <span class="radio-button-icons-container">
                                                    <div class="radio-button-icon unchecked"></div>
                                                    <div class="radio-button-icon checked"></div>
                                                </span>
                                                <span class="radio-button-label">Precio: mayor a menor</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="#"
                                            class="radio-button radio-button-item {{ request('orden') == 'nombre' ? 'selected' : '' }}"
                                            onclick="event.preventDefault(); document.querySelector('#filterFormMobile input[name=orden]').value='nombre'; document.getElementById('filterFormMobile').submit();">
                                            <div class="radio-button-content">
                                                <span class="radio-button-icons-container">
                                                    <div class="radio-button-icon unchecked"></div>
                                                    <div class="radio-button-icon checked"></div>
                                                </span>
                                                <span class="radio-button-label">Nombre A-Z</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <input type="hidden" name="orden" value="{{ request('orden', '') }}">
                            </form>






                        </div>

                    </div>
                    <div class="modal-overlay" id="sort-overlay" onclick="closeSortModal(event)" style="display: none;">
                    </div>
                </div>
                <div class="visible-when-content-ready col col-md-auto pl-md-2 category-control-item">
                    <a href="#" class="d-block font-small font-md-body px-md-0 py-md-2"
                        onclick="openFilterModal(event)">
                        <div class="d-flex justify-content-center align-items-center">
                            <svg class="icon-inline icon-lg mr-2 svg-icon-text">
                                <use xlink:href="#filter"></use>
                            </svg>
                            Filtrar
                        </div>
                    </a>


                    <div id="nav-filters"
                        class="js-modal   modal modal-filters modal-docked-small modal-right modal--md transition-slide modal-docked-md transition-soft "
                        style="display: none;">
                        <div class="js-modal-close   modal-header  ">
                            <div class="row no-gutters align-items-center">
                                <div class="col p-3">
                                    Filtrar
                                </div>
                                <div class="col-auto">
                                    <a class="modal-close" onclick="closeFilterModal(event)">
                                        <svg class="icon-inline svg-icon-text">
                                            <use xlink:href="#times"></use>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body h-100 p-0">






                            <div class="js-accordion-private-container filter-accordion filters-categories-container ">
                                <a href="#" class="js-accordion-private-toggle row no-gutters align-items-center">
                                    <div class="col my-1 pr-3 d-flex align-items-center">
                                        <div class="h6 font-body font-weight-bold mb-0">
                                            Categorías
                                        </div>
                                    </div>
                                    <div class="col-auto my-1">
                                        <span class="js-accordion-private-toggle-inactive">
                                            <svg class="icon-inline svg-icon-text font-big mr-1">
                                                <use xlink:href="#plus"></use>
                                            </svg>
                                        </span>
                                        <span class="js-accordion-private-toggle-active" style="display: none;">
                                            <svg class="icon-inline svg-icon-text font-big mr-1">
                                                <use xlink:href="#minus"></use>
                                            </svg>
                                        </span>
                                    </div>
                                </a>
                                <ul class="js-accordion-private-content list-unstyled my-3" style="display: none;">
                                    <li class="mb-2">
                                        <a href="{{ route('tienda.categorias', $empresa->slug) }}"
                                            title="Todos los productos"
                                            class="font-small {{ !request('categoria') ? 'active font-weight-bold' : '' }}">
                                            Todos los productos
                                        </a>
                                    </li>
                                    @foreach ($categorias as $index => $categoria)
                                        <li data-item="{{ $index + 1 }}" class="mb-2">
                                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                                                title="{{ $categoria->nombre }}"
                                                class="font-small {{ request('categoria') == $categoria->id ? 'active font-weight-bold' : '' }}">
                                                {{ $categoria->nombre }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>



                            <div id="filters" class="filters-properties-container visible-when-content-ready"
                                data-store="filters-nav">

                                <div class="price-filter-container filter-accordion" data-store="filters-group">
                                    <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}"
                                        id="priceFilterFormMobile">
                                        @foreach (request()->except(['precio_min', 'precio_max']) as $key => $value)
                                            @if ($value)
                                                <input type="hidden" name="{{ $key }}"
                                                    value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <div class="h6 font-weight-bold mb-4 font-big">Precio</div>
                                        <div class="form-group">
                                            <span class="filter-input-price-container">
                                                <label class="form-label">Desde</label>
                                                <input type="number" name="precio_min" step="1" min="0"
                                                    class="form-control filter-input-price"
                                                    value="{{ request('precio_min') }}" placeholder="10000">
                                            </span>
                                            <span class="filter-input-price-container">
                                                <label class="form-label">Hasta</label>
                                                <input type="number" name="precio_max" step="1" min="0"
                                                    class="form-control filter-input-price"
                                                    value="{{ request('precio_max') }}" placeholder="200000">
                                            </span>
                                            <button type="submit" class="btn btn-default d-inline-block">Aplicar</button>
                                        </div>
                                    </form>
                                </div>


                            </div>

                            <div class="js-filters-private-overlay  filters-overlay" style="display: none;">
                                <div class="filters-updating-message">
                                    <span class="js-applying-filter h5 mr-2" style="display: none;">Aplicando
                                        filtro</span>
                                    <span class="js-removing-filter h5 mr-2" style="display: none;">Borrando filtro</span>
                                    <span class="js-filtering-spinner">
                                        <svg class="icon-inline h5 icon-spin svg-icon-text">
                                            <use xlink:href="#spinner-third"></use>
                                        </svg>
                                    </span>
                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="modal-overlay" id="filter-overlay" onclick="closeFilterModal(event)"
                        style="display: none;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="js-category-controls-prev category-controls-sticky-detector"></section>

    <section class="category-body " data-store="category-grid-0">
        <div class="container mt-3 mb-5">
            <div class="row">
                <div class="col-md-auto filters-sidebar d-none d-md-block visible-when-content-ready">




                    <div class="mb-4 pb-2">
                        <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}"
                            id="filterFormDesktop">
                            @foreach (request()->except(['orden']) as $key => $value)
                                @if ($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <div class="form-group form-group d-inline-block w-auto mb-0">
                                <label class="h6 font-big mb-3 d-block">Ordenar por</label>
                                <select class="form-select form-select-small" name="orden" aria-label="Ordenar por"
                                    onchange="this.form.submit()">
                                    <option value="" {{ !request('orden') ? 'selected' : '' }}>Más recientes
                                    </option>
                                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>
                                        Precio: menor a mayor</option>
                                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>
                                        Precio: mayor a menor</option>
                                    <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre A-Z
                                    </option>
                                </select>
                                <div class="form-select-icon ">
                                    <svg class="icon-inline icon-w-14">
                                        <use xlink:href="#chevron-down"></use>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    </div>








                    <div class=" filters-categories-container ">
                        <div class="h6 font-big mb-4">Categorías</div>
                        <ul class=" mb-4 pb-2 list-unstyled">
                            <li class="mb-2">
                                <a href="{{ route('tienda.categorias', $empresa->slug) }}" title="Todos los productos"
                                    class="font-small {{ !request('categoria') ? 'active font-weight-bold' : '' }}">
                                    Todos los productos
                                </a>
                            </li>
                            @foreach ($categorias as $index => $categoria)
                                <li data-item="{{ $index + 1 }}" class="mb-2">
                                    <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                                        title="{{ $categoria->nombre }}"
                                        class="font-small {{ request('categoria') == $categoria->id ? 'active font-weight-bold' : '' }}">
                                        {{ $categoria->nombre }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>



                    <div id="filters" class="filters-properties-container visible-when-content-ready"
                        data-store="filters-nav">

                        <div class="price-filter-container mb-4 pb-2" data-store="filters-group">
                            <form method="GET" action="{{ route('tienda.categorias', $empresa->slug) }}"
                                id="priceFilterFormDesktop">
                                @foreach (request()->except(['precio_min', 'precio_max']) as $key => $value)
                                    @if ($value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <div class="h6 font-weight-bold mb-4 font-big">Precio</div>
                                <div class="form-group">
                                    <span class="filter-input-price-container">
                                        <label class="form-label">Desde</label>
                                        <input type="number" name="precio_min" step="1" min="0"
                                            class="form-control filter-input-price" value="{{ request('precio_min') }}"
                                            placeholder="10000">
                                    </span>
                                    <span class="filter-input-price-container">
                                        <label class="form-label">Hasta</label>
                                        <input type="number" name="precio_max" step="1" min="0"
                                            class="form-control filter-input-price" value="{{ request('precio_max') }}"
                                            placeholder="200000">
                                    </span>
                                    <button type="submit" class="btn btn-default btn-small">Aplicar</button>
                                </div>
                            </form>
                        </div>


                    </div>

                    <div class="js-filters-private-overlay  filters-overlay" style="display: none;">
                        <div class="filters-updating-message">
                            <span class="js-applying-filter " style="display: none;">Aplicando filtro</span>
                            <span class="js-removing-filter " style="display: none;">Borrando filtro</span>
                        </div>
                    </div>

                </div>
                <div class="col pt-2 pt-md-0" data-store="category-grid-0">
                    <div class="js-product-table row row-grid">
                        <div class="last-page" style="display:none;"></div>

                        @forelse($productos as $producto)
                            @php
                                // Buscar descuentos activos para este producto
                                $descuentoProducto = null;
                                $textoDescuentoProducto = null;
                                $precioNumerico = is_object($producto->precio_actual)
                                    ? $producto->precio_actual->precio
                                    : $producto->precio_actual;
                                $precioConDescuentoProducto = $precioNumerico;
                                $montoDescuentoProducto = 0;

                                if (isset($descuentosActivos) && $precioNumerico) {
                                    foreach ($descuentosActivos as $desc) {
                                        $aplica = false;

                                        if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                            $aplica = true;
                                        } elseif (
                                            $desc->aplica_a === 'producto' &&
                                            in_array($producto->id, $desc->productos_aplicables ?? [])
                                        ) {
                                            $aplica = true;
                                        } elseif (
                                            $desc->aplica_a === 'categoria' &&
                                            in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])
                                        ) {
                                            $aplica = true;
                                        }

                                        if ($aplica) {
                                            $descuentoProducto = $desc;
                                            if ($desc->tipo === 'porcentaje') {
                                                $montoDescuentoProducto = ($precioNumerico * $desc->valor) / 100;
                                                $textoDescuentoProducto = round($desc->valor) . '% OFF';
                                            } else {
                                                $montoDescuentoProducto = $desc->valor;
                                                $textoDescuentoProducto =
                                                    '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                            }
                                            $precioConDescuentoProducto = $precioNumerico - $montoDescuentoProducto;
                                            break;
                                        }
                                    }
                                }
                                $stockInfo = $producto->getStockInfo();
                            @endphp

                            <div class="js-item-product col-6 col-md-2-4 item-product col-grid" data-product-type="list"
                                data-product-id="{{ $producto->id }}" data-store="product-item-{{ $producto->id }}">
                                <div class="item">
                                    <div class="js-product-container position-relative">
                                        <!-- Imagen del producto -->
                                        <div class="js-product-item-image-container-private product-item-image-container item-image"
                                            data-store="product-item-image-{{ $producto->id }}">
                                            <div style="padding-bottom: 100%;"
                                                class="js-item-image-padding position-relative d-block">
                                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                    title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                    class="js-product-item-image-link-private">

                                                    <img src="{{ $producto->url_imagen_principal ?? asset('images/placeholder.jpg') }}"
                                                        alt="{{ $producto->nombre }}"
                                                        class="js-product-item-image-private product-item-image js-item-image lazyautosizes img-absolute img-absolute-centered fade-in item-image-featured lazyloaded"
                                                        loading="lazy">

                                                    <div class="placeholder-fade"></div>
                                                </a>

                                                <!-- Labels de stock/envío y descuentos -->
                                                <div class="labels js-labels-floating-group labels-absolute">
                                                    @if ($descuentoProducto)
                                                        <div class="label js-discount-label label label-accent">
                                                            {{ $textoDescuentoProducto }}
                                                        </div>
                                                    @elseif($stockInfo['controlar_stock'] && !$stockInfo['hay_stock'])
                                                        <div class="label js-stock-label label label-default">
                                                            Sin stock
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Información del producto -->
                                        <div class="item-description pt-3"
                                            data-store="product-item-info-{{ $producto->id }}">
                                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                class="item-link">

                                                <div class="js-item-name item-name mb-2 font-small opacity-80"
                                                    data-store="product-item-name-{{ $producto->id }}">
                                                    {{ $producto->nombre }}
                                                </div>

                                                <div class="item-price-container"
                                                    data-store="product-item-price-{{ $producto->id }}">
                                                    <div class="d-block mb-1 mr-1">
                                                        @if ($precioNumerico)
                                                            @if ($descuentoProducto)
                                                                <span
                                                                    class="js-price-display item-price text-decoration-line-through opacity-50 mr-2"
                                                                    data-product-price="{{ $precioNumerico * 100 }}">
                                                                    ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                </span>
                                                                <span
                                                                    class="js-price-display item-price font-weight-bold text-accent"
                                                                    data-product-price="{{ $precioConDescuentoProducto * 100 }}">
                                                                    ${{ number_format($precioConDescuentoProducto, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="js-price-display item-price font-weight-bold"
                                                                    data-product-price="{{ $precioNumerico * 100 }}">
                                                                    ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted font-small">Precio no disponible</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No se encontraron productos en esta categoría.</p>
                            </div>
                        @endforelse

                        <!-- Paginación -->
                        @if (isset($productos) && method_exists($productos, 'links'))
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $productos->links() }}
                            </div>
                        @endif

                        <div class="row justify-content-center align-items-center mt-5">
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Global functions for mobile modals
        function openSortModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('sort-by');
            const overlay = document.getElementById('sort-overlay');
            if (modal && overlay) {
                modal.style.display = 'block';
                modal.style.zIndex = '10001'; // Ensure it's above everything
                modal.style.opacity = '1';
                modal.style.bottom = '0'; // Force bottom position
                modal.style.transform = 'none'; // Reset any transforms

                overlay.style.display = 'block';
                overlay.style.zIndex = '10000'; // Ensure it's just below modal
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSortModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('sort-by');
            const overlay = document.getElementById('sort-overlay');
            if (modal && overlay) {
                modal.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        function openFilterModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('nav-filters');
            const overlay = document.getElementById('filter-overlay');
            if (modal && overlay) {
                modal.style.display = 'block';
                modal.style.zIndex = '10001';
                modal.style.opacity = '1';
                // For the filter modal which is a side modal (modal-right)
                modal.style.right = '0';
                modal.style.transform = 'none';

                overlay.style.display = 'block';
                overlay.style.zIndex = '10000';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFilterModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('nav-filters');
            const overlay = document.getElementById('filter-overlay');
            if (modal && overlay) {
                modal.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Accordion toggle for filter categories in mobile
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-accordion-private-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.js-accordion-private-container');
                    const content = container.querySelector('.js-accordion-private-content');
                    const activeIcon = container.querySelector(
                        '.js-accordion-private-toggle-active');
                    const inactiveIcon = container.querySelector(
                        '.js-accordion-private-toggle-inactive');

                    if (content.style.display === 'none' || !content.style.display) {
                        content.style.display = 'block';
                        activeIcon.style.display = 'inline';
                        inactiveIcon.style.display = 'none';
                    } else {
                        content.style.display = 'none';
                        activeIcon.style.display = 'none';
                        inactiveIcon.style.display = 'inline';
                    }
                });
            });
        });
    </script>
@endpush
