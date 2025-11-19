@extends('tienda.recife_layout')

@section('title', ($categoriaSeleccionada ? $categoriaSeleccionada->nombre . ' - ' : 'Categorías - ') . $empresa->nombre)
@section('description', $categoriaSeleccionada ? 'Explora nuestros productos de ' . $categoriaSeleccionada->nombre : 'Explora todos nuestros productos')

@section('content')
		<section class="page-header py-4 " data-store="page-title">
		<div class="container">
			<div class="breadcrumbs ">
				<a class="crumb" href="{{ route('tienda.empresa', $empresa->slug) }}" title="{{ $empresa->nombre }}">Inicio</a>
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
						<a href="#" class="js-modal-open d-block font-small font-md-body px-md-0 py-md-2" data-toggle="#sort-by">
							<div class="d-flex justify-content-center align-items-center">
								<svg class="icon-inline icon-lg mr-2 svg-icon-text"><use xlink:href="#sort"></use></svg>
								Ordenar
							</div>
						</a>
						

<div id="sort-by" class="js-modal   modal modal-bottom modal-centered modal-bottom-sheet modal-right-md modal-bottom modal--md transition-slide modal-docked-md transition-soft " style="display: none;">
                        <div class="js-modal-close   modal-header  ">
                                            <div class="row no-gutters align-items-center">
                            <div class="col p-3">
                                								Ordenar por
							                            </div>
                            <div class="col-auto">
                                <a class="js-modal-close modal-close ">
                                    <svg class="icon-inline svg-icon-text"><use xlink:href="#times"></use></svg>
                                </a>
                            </div>
                        </div>
                                    </div>
                <div class="modal-body ">
                    								


	
		
											<ul class="list-unstyled">
																								<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="price-ascending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Precio: menor a mayor
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="price-descending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Precio: mayor a menor
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="alpha-ascending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											A - Z
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="alpha-descending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Z - A
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="created-descending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Más nuevo al más viejo
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item " data-sort-value="created-ascending">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Más viejo al más nuevo
										</span>
									</div>
								</a>
							</li>
																														<li class="">
								<a href="#" class="js-apply-sort-private radio-button radio-button-item  selected" data-sort-value="best-selling">
									<div class="radio-button-content">
										<span class="radio-button-icons-container">
												<div class="radio-button-icon  unchecked"></div>

																									<div class="radio-button-icon  checked"></div>
																							</span>

										<span class="radio-button-label">
											Más vendidos
										</span>
									</div>
								</a>
							</li>
																																</ul>
			
		
	

	<div class="js-sorting-overlay-private  filters-overlay" style="display: none;">
		<div class="filters-updating-message">
			<span class="h5 mr-2">Ordenando productos</span>
							<svg class="icon-inline h5 icon-spin svg-icon-text"><use xlink:href="#spinner-third"></use></svg>
					</div>
	</div>

							                </div>
        
    </div>
<div class="js-modal-overlay modal-overlay " data-modal-id="#sort-by" style="display: none;"></div>
					</div>
													<div class="visible-when-content-ready col col-md-auto pl-md-2 category-control-item">
						<a href="#" class="js-modal-open d-block font-small font-md-body px-md-0 py-md-2" data-toggle="#nav-filters" data-component="filter-button">
							<div class="d-flex justify-content-center align-items-center">
								<svg class="icon-inline icon-lg mr-2 svg-icon-text"><use xlink:href="#filter"></use></svg>
								Filtrar
															</div>
						</a>
						

<div id="nav-filters" class="js-modal   modal modal-filters modal-docked-small modal-right modal--md transition-slide modal-docked-md transition-soft " style="display: none;">
                        <div class="js-modal-close   modal-header  ">
                                            <div class="row no-gutters align-items-center">
                            <div class="col p-3">
                                								Filtrar 
							                            </div>
                            <div class="col-auto">
                                <a class="js-modal-close modal-close ">
                                    <svg class="icon-inline svg-icon-text"><use xlink:href="#times"></use></svg>
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
                                        <a href="{{ route('tienda.categorias', $empresa->slug) }}" title="Todos los productos" class="font-small {{ !request('categoria') ? 'active font-weight-bold' : '' }}">
                                            Todos los productos
                                        </a>
                                    </li>
                                    @foreach($categorias as $index => $categoria)
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
            
    
    
    <div id="filters" class="filters-properties-container visible-when-content-ready" data-store="filters-nav">

                    <div class="js-price-filter-container price-filter-container filter-accordion" data-store="filters-group" data-component="list.filter-price">
	<form>
		<div class="h6 font-weight-bold mb-4 font-big">Precio</div>
		<div class="form-group">
			<span class="js-filter-input-price-container filter-input-price-container">
				<label class="form-label">Desde</label>
				<input type="number" name="min_price" step="1" min="0" pattern="\d*" oninput="validity.valid||(value='');" class="js-price-filter-input form-control filter-input-price" data-component="list.filter-price.min" value="" placeholder="10000">
				<a class="js-price-filter-empty input-clear-content" style="display:none">
				</a>  
			</span>
			<span class="js-filter-input-price-container filter-input-price-container">
				<label class="form-label">Hasta</label>
				<input type="number" name="max_price" step="1" min="0" pattern="\d*" oninput="validity.valid||(value='');" class="js-price-filter-input form-control filter-input-price" data-component="list.filter-price.max" value="" placeholder="200000">
				<a class="js-price-filter-empty input-clear-content" style="display:none">
				</a>
			</span>
			<button type="submit" class="js-price-filter-btn btn btn-default d-inline-block disabled" disabled="" data-component="list.filter-price.submit">Aplicar</button>
		</div>
	</form>
</div>


                        </div>
    
    <div class="js-filters-private-overlay  filters-overlay" style="display: none;">
        <div class="filters-updating-message">
            <span class="js-applying-filter h5 mr-2" style="display: none;">Aplicando filtro</span>
            <span class="js-removing-filter h5 mr-2" style="display: none;">Borrando filtro</span>
                            <span class="js-filtering-spinner">
                    <svg class="icon-inline h5 icon-spin svg-icon-text"><use xlink:href="#spinner-third"></use></svg>
                </span>
                    </div>
    </div>


															                </div>
        
    </div>
<div class="js-modal-overlay modal-overlay " data-modal-id="#nav-filters" style="display: none;"></div>
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
		
							<div class="form-group form-group d-inline-block w-auto mb-0">
											<label class="h6 font-big mb-3 d-block">Ordenar por</label>
										<select class="js-sort-by-private form-select form-select-small" aria-label="Ordenar por" data-component="sort-by">
													<option value="price-ascending">Precio: menor a mayor</option>
													<option value="price-descending">Precio: mayor a menor</option>
													<option value="alpha-ascending">A - Z</option>
													<option value="alpha-descending">Z - A</option>
													<option value="created-descending">Más nuevo al más viejo</option>
													<option value="created-ascending">Más viejo al más nuevo</option>
													<option value="best-selling" selected="">Más vendidos</option>
													<option value="user">Destacado</option>
											</select>

											<div class="form-select-icon ">
							<svg class="icon-inline icon-w-14"><use xlink:href="#chevron-down"></use></svg>
						</div>
									</div>
			
					</div>
		
	


            
    
    
            
        <div class=" filters-categories-container ">
                            <div class="h6 font-big mb-4">Categorías</div>
                        <ul class=" mb-4 pb-2 list-unstyled">
                                    <li class="mb-2">
                                        <a href="{{ route('tienda.categorias', $empresa->slug) }}" title="Todos los productos" class="font-small {{ !request('categoria') ? 'active font-weight-bold' : '' }}">
                                            Todos los productos
                                        </a>
                                    </li>
                                    @foreach($categorias as $index => $categoria)
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
            
    
    
    <div id="filters" class="filters-properties-container visible-when-content-ready" data-store="filters-nav">

                    <div class="js-price-filter-container price-filter-container mb-4 pb-2" data-store="filters-group" data-component="list.filter-price">
	<form>
		<div class="h6 font-weight-bold mb-4 font-big">Precio</div>
		<div class="form-group">
			<span class="js-filter-input-price-container filter-input-price-container">
				<label class="form-label">Desde</label>
				<input type="number" name="min_price" step="1" min="0" pattern="\d*" oninput="validity.valid||(value='');" class="js-price-filter-input form-control filter-input-price" data-component="list.filter-price.min" value="" placeholder="10000">
				<a class="js-price-filter-empty input-clear-content" style="display:none">
				</a>  
			</span>
			<span class="js-filter-input-price-container filter-input-price-container">
				<label class="form-label">Hasta</label>
				<input type="number" name="max_price" step="1" min="0" pattern="\d*" oninput="validity.valid||(value='');" class="js-price-filter-input form-control filter-input-price" data-component="list.filter-price.max" value="" placeholder="200000">
				<a class="js-price-filter-empty input-clear-content" style="display:none">
				</a>
			</span>
			<button type="submit" class="js-price-filter-btn btn btn-default btn-small disabled" disabled="" data-component="list.filter-price.submit">Aplicar</button>
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
            $precioNumerico = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
            $precioConDescuentoProducto = $precioNumerico;
            $montoDescuentoProducto = 0;

            if (isset($descuentosActivos) && $precioNumerico) {
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
                        $descuentoProducto = $desc;
                        if ($desc->tipo === 'porcentaje') {
                            $montoDescuentoProducto = ($precioNumerico * $desc->valor) / 100;
                            $textoDescuentoProducto = round($desc->valor) . '% OFF';
                        } else {
                            $montoDescuentoProducto = $desc->valor;
                            $textoDescuentoProducto = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                        }
                        $precioConDescuentoProducto = $precioNumerico - $montoDescuentoProducto;
                        break;
                    }
                }
            }
            $stockInfo = $producto->getStockInfo();
        @endphp

        <div class="js-item-product col-6 col-md-2-4 item-product col-grid" data-product-type="list" data-product-id="{{ $producto->id }}" data-store="product-item-{{ $producto->id }}">
            <div class="item">
                <div class="js-product-container position-relative">
                    <!-- Imagen del producto -->
                    <div class="js-product-item-image-container-private product-item-image-container item-image" data-store="product-item-image-{{ $producto->id }}">
                        <div style="padding-bottom: 100%;" class="js-item-image-padding position-relative d-block">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                               title="{{ $producto->nombre }}"
                               aria-label="{{ $producto->nombre }}"
                               class="js-product-item-image-link-private">

                                <img src="{{ $producto->url_imagen_principal ?? asset('images/placeholder.jpg') }}"
                                     alt="{{ $producto->nombre }}"
                                     class="js-product-item-image-private product-item-image js-item-image lazyautosizes img-absolute img-absolute-centered fade-in item-image-featured lazyloaded"
                                     loading="lazy">

                                <div class="placeholder-fade"></div>
                            </a>

                            <!-- Labels de stock/envío y descuentos -->
                            <div class="labels js-labels-floating-group labels-absolute">
                                @if($descuentoProducto)
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
                    <div class="item-description pt-3" data-store="product-item-info-{{ $producto->id }}">
                        <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                           title="{{ $producto->nombre }}"
                           aria-label="{{ $producto->nombre }}"
                           class="item-link">

                            <div class="js-item-name item-name mb-2 font-small opacity-80" data-store="product-item-name-{{ $producto->id }}">
                                {{ $producto->nombre }}
                            </div>

                            <div class="item-price-container" data-store="product-item-price-{{ $producto->id }}">
                                <div class="d-block mb-1 mr-1">
                                    @if($precioNumerico)
                                        @if($descuentoProducto)
                                            <span class="js-price-display item-price text-decoration-line-through opacity-50 mr-2" data-product-price="{{ $precioNumerico * 100 }}">
                                                ${{ number_format($precioNumerico, 0, ',', '.') }}
                                            </span>
                                            <span class="js-price-display item-price font-weight-bold text-accent" data-product-price="{{ $precioConDescuentoProducto * 100 }}">
                                                ${{ number_format($precioConDescuentoProducto, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="js-price-display item-price font-weight-bold" data-product-price="{{ $precioNumerico * 100 }}">
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
            @if(isset($productos) && method_exists($productos, 'links'))
                <div class="mt-4 d-flex justify-content-center">
                    {{ $productos->links() }}
                </div>
            @endif

        	<div class="row justify-content-center align-items-center mt-5">
			</div>
    </div>		</div>
	</div>
</section>
@endsection
