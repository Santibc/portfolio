@extends('tienda.lima_layout')

@section('title', isset($empresa) ? $empresa->nombre . ' - Tienda Online' : 'Lima Theme - Tienda Online')

@section('content')

{{-- Hero Slider Section --}}
@if(isset($slides) && $slides->count() > 0)
<section class="section-slider-home">
    <div class="js-home-main-slider-container home-slider-container">
        <div class="section-slider">
            <div class="js-home-slider nube-slider-home swiper-container">
                <div class="swiper-wrapper">
                    @foreach($slides as $slide)
                    <div class="swiper-slide slide-container">
                        <div class="slider-slide">
                            <a href="{{ $slide->url ?? '#' }}">
                                <img class="slider-image swiper-lazy lazyload"
                                     data-src="{{ asset($slide->imagen) }}"
                                     src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                     alt="{{ $slide->titulo ?? '' }}">
                            </a>

                            @if($slide->titulo || $slide->descripcion || $slide->boton_texto)
                            <div class="swiper-text swiper-light">
                                <div class="container">
                                    <div class="row">
                                        <div class="slider-text-container col-12 col-md-8">
                                            @if($slide->titulo)
                                            <h2 class="slider-title">{{ $slide->titulo }}</h2>
                                            @endif
                                            @if($slide->descripcion)
                                            <p class="slider-description">{{ $slide->descripcion }}</p>
                                            @endif
                                            @if($slide->boton_texto && $slide->url)
                                            <a href="{{ $slide->url }}" class="btn btn-primary btn-lg">{{ $slide->boton_texto }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-overlay swiper-overlay-light"></div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="js-swiper-home-control swiper-pagination"></div>
                <div class="js-swiper-home-prev swiper-button-prev"></div>
                <div class="js-swiper-home-next swiper-button-next"></div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Featured Products Section (Productos Destacados) --}}
@if(isset($productosDestacados) && $productosDestacados->count() > 0)
<section class="section-featured-products py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="h1 section-title">Productos Destacados</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="js-products-featured-grid swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($productosDestacados as $producto)
                        @php
                            // Calcular precios y descuentos
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

                        <div class="swiper-slide">
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
                                                    class="btn btn-primary btn-block js-quick-add-to-cart"
                                                    data-producto-id="{{ $producto->id }}"
                                                    data-precio="{{ $precioConDescuento }}">
                                                Agregar al Carrito
                                            </button>
                                            @else
                                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="btn btn-primary btn-block">
                                                Ver Producto
                                            </a>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="js-swiper-featured-prev swiper-button-prev"></div>
                    <div class="js-swiper-featured-next swiper-button-next"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Home Banners Section --}}
@if(isset($banners) && $banners->count() > 0)
<section class="section-banners-home py-4">
    <div class="container">
        <div class="row">
            @foreach($banners->take(4) as $banner)
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ $banner->url ?? '#' }}" class="textbanner">
                    <img class="textbanner-image lazyload"
                         data-src="{{ asset($banner->imagen) }}"
                         src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                         alt="{{ $banner->titulo ?? '' }}">
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- New Products Section (Lo Nuevo) --}}
@if(isset($productosNuevos) && $productosNuevos->count() > 0)
<section class="section-new-products py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="h1 section-title">Lo Nuevo</h2>
            </div>
        </div>

        <div class="row row-grid">
            @foreach($productosNuevos as $nuevo)
            @php
                // Calcular precios y descuentos
                $descuentoNuevo = null;
                $textoDescuentoNuevo = null;
                $precioActualNuevo = is_object($nuevo->precio_actual) ? $nuevo->precio_actual->precio : $nuevo->precio_actual;
                $precioConDescuentoNuevo = $precioActualNuevo;

                if (isset($descuentosActivos)) {
                    foreach ($descuentosActivos as $desc) {
                        $aplica = false;

                        if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                            $aplica = true;
                        } elseif ($desc->aplica_a === 'producto' && in_array($nuevo->id, $desc->productos_aplicables ?? [])) {
                            $aplica = true;
                        } elseif ($desc->aplica_a === 'categoria' && in_array($nuevo->categoria_id, $desc->categorias_aplicables ?? [])) {
                            $aplica = true;
                        }

                        if ($aplica) {
                            $descuentoNuevo = $desc;
                            if ($desc->tipo === 'porcentaje') {
                                $montoDescuentoNuevo = ($precioActualNuevo * $desc->valor) / 100;
                                $textoDescuentoNuevo = round($desc->valor) . '% OFF';
                            } else {
                                $montoDescuentoNuevo = $desc->valor;
                                $textoDescuentoNuevo = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                            }
                            $precioConDescuentoNuevo = $precioActualNuevo - $montoDescuentoNuevo;
                            break;
                        }
                    }
                }
            @endphp

            <div class="col-6 col-md-4 col-lg-3 col-grid mb-4">
                <div class="js-item-product item-product">
                    <div class="js-item-info-container item m-0">
                        <div class="js-product-container position-relative">
                            <div class="product-item-image-container item-image">
                                <div class="js-item-image-padding position-relative d-block product-image-wrapper">
                                    <a href="{{ route('tienda.producto', [$empresa->slug, $nuevo->id]) }}">
                                        <img class="product-item-image lazyload fade-in"
                                             data-src="{{ $nuevo->url_imagen_principal ?? asset('images/no-image.png') }}"
                                             src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                             alt="{{ $nuevo->nombre }}">
                                    </a>

                                    <div class="labels js-labels-floating-group labels-absolute">
                                        <div class="label label-success">NUEVO</div>
                                        @if($descuentoNuevo)
                                        <div class="label label-primary">{{ $textoDescuentoNuevo }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="item-info">
                                <div class="js-item-name item-name">
                                    <a href="{{ route('tienda.producto', [$empresa->slug, $nuevo->id]) }}">
                                        {{ $nuevo->nombre }}
                                    </a>
                                </div>

                                <div class="item-price-container">
                                    @if($descuentoNuevo)
                                    <span class="price-compare">${{ number_format($precioActualNuevo, 0, ',', '.') }}</span>
                                    @endif
                                    <span class="item-price">${{ number_format($precioConDescuentoNuevo, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- NOTE: Las secciones de Marcas, Testimonios y Newsletter CTA han sido eliminadas según solicitud del usuario --}}

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
                const precio = this.getAttribute('data-precio');
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
