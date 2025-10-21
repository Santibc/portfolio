@extends('tienda.brasilia_layout')

@section('title', isset($empresa) ? $empresa->nombre . ' - Tienda Online' : 'Brasilia Theme - Tienda de Indumentaria')

@section('content')
    <!-- Hero Section with Video -->
    @if($empresa->hero_video_url)
    <section class="hero-section-video">
        <div class="container-fluid p-0">
            <div class="hero-video-wrapper">
                <video class="hero-video" autoplay muted loop playsinline>
                      <source src="{{ $empresa->hero_video_url }}" type="video/mp4">
                </video>
                <div class="hero-video-overlay">
                    <div class="hero-content">
                        @if($empresa->hero_video_message)
                        <h2 class="hero-title">{{ $empresa->hero_video_message }}</h2>
                        @endif
                        @if($empresa->hero_video_button_text && $empresa->hero_video_button_link)
                        <a href="{{ $empresa->hero_video_button_link }}" class="btn btn-primary btn-hero">{{ $empresa->hero_video_button_text }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Categories Carousel -->
    @if(isset($categorias) && $categorias && $categorias->count() > 0)
    <section class="categories-carousel-section py-4">
        <div class="container">
            <div class="swiper categories-swiper">
                <div class="swiper-wrapper">
                    @foreach($categorias as $categoria)
                    <div class="swiper-slide">
                        <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}" class="category-carousel-item">
                            <div class="category-carousel-icon">
                                @if($categoria->imagen)
                                <img src="{{ asset($categoria->imagen) }}" alt="{{ $categoria->nombre }}" class="category-carousel-image">
                                @else
                                <div class="category-carousel-placeholder">
                                    <i class="bi bi-tag-fill" style="font-size: 2rem;"></i>
                                </div>
                                @endif
                            </div>
                            <span class="category-carousel-name">{{ $categoria->nombre }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev categories-btn-prev"></div>
                <div class="swiper-button-next categories-btn-next"></div>
            </div>
        </div>
    </section>
    @endif

    <!-- Products Section - Destacados (Más Vendidos) -->
    @if($productosDestacados && $productosDestacados->count() > 0)
    <section class="products-section py-5">
        <div class="container">
            <div class="section-header mb-4">
                <h2 class="section-title">Destacados</h2>
            </div>
            <div class="swiper products-swiper">
                <div class="swiper-wrapper">
                    @foreach($productosDestacados as $producto)
                    <div class="swiper-slide">
                        <div class="product-card-square">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}">
                                <div class="product-image-container-square">
                                    @if($producto->precio_actual && $producto->precio_actual->precio_anterior && $producto->precio_actual->precio_anterior > $producto->precio_actual->precio)
                                        @php
                                            $descuento = round((($producto->precio_actual->precio_anterior - $producto->precio_actual->precio) / $producto->precio_actual->precio_anterior) * 100);
                                        @endphp
                                        <span class="product-badge-square badge-green">{{ $descuento }}% OFF</span>
                                    @endif
                                    <img src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $producto->nombre }}" class="product-image-square">
                                </div>
                                <div class="product-info-square">
                                    <h4 class="product-name-square">{{ $producto->nombre }}</h4>
                                    <div class="product-price-square">
                                        @if($producto->precio_actual)
                                            @if($producto->precio_actual->precio_anterior && $producto->precio_actual->precio_anterior > $producto->precio_actual->precio)
                                                <span class="price-old-square">${{ number_format($producto->precio_actual->precio_anterior, 0, ',', '.') }}</span>
                                            @endif
                                            <span class="price-current-square">${{ number_format($producto->precio_actual->precio, 0, ',', '.') }}</span>
                                        @else
                                            <span class="price-current-square">Consultar precio</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="btn btn-comprar-square w-100">Ver producto</a>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev products-btn-prev"></div>
                <div class="swiper-button-next products-btn-next"></div>
            </div>
        </div>
    </section>
    @endif

    <!-- Three Image Cards Section - Categorías Destacadas -->
    @if(isset($categorias) && $categorias && $categorias->count() >= 3)
    <section class="three-cards-section py-5">
        <div class="container">
            <div class="row">
                @foreach($categorias->take(3) as $categoriaDestacada)
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="image-card-square">
                        @if($categoriaDestacada->imagen)
                            <img src="{{ asset($categoriaDestacada->imagen) }}" alt="{{ $categoriaDestacada->nombre }}" class="image-card-image">
                        @else
                            <img src="https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=600&h=600&fit=crop" alt="{{ $categoriaDestacada->nombre }}" class="image-card-image">
                        @endif
                        <div class="image-card-overlay">
                            <h3 class="image-card-title">{{ $categoriaDestacada->nombre }}</h3>
                            @if($categoriaDestacada->descripcion)
                            <p class="image-card-subtitle">{{ Str::limit($categoriaDestacada->descripcion, 60) }}</p>
                            @endif
                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoriaDestacada->id]) }}" class="btn btn-light-card">Ver más</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Lo Nuevo Section - Productos Nuevos -->
    @if($productosNuevos && $productosNuevos->count() > 0)
    <section class="lo-nuevo-section py-5">
        <div class="container">
            <div class="section-header mb-4">
                <h2 class="section-title">Lo nuevo</h2>
            </div>

            <div class="swiper lo-nuevo-swiper">
                <div class="swiper-wrapper">
                    @foreach($productosNuevos as $nuevo)
                    <div class="swiper-slide">
                        <div class="product-card-nuevo">
                            @if($nuevo->precio_actual && $nuevo->precio_actual->precio_anterior && $nuevo->precio_actual->precio_anterior > $nuevo->precio_actual->precio)
                                @php
                                    $descuentoNuevo = round((($nuevo->precio_actual->precio_anterior - $nuevo->precio_actual->precio) / $nuevo->precio_actual->precio_anterior) * 100);
                                @endphp
                                <span class="product-badge-nuevo badge-green">NUEVO</span>
                            @endif
                            <a href="{{ route('tienda.producto', [$empresa->slug, $nuevo->id]) }}">
                                <div class="product-image-container-nuevo">
                                    <img src="{{ $nuevo->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $nuevo->nombre }}" class="product-image-nuevo">
                                </div>
                                <div class="product-info-nuevo">
                                    <h4 class="product-name-nuevo">{{ $nuevo->nombre }}</h4>
                                    <div class="product-price-nuevo">
                                        @if($nuevo->precio_actual)
                                            @if($nuevo->precio_actual->precio_anterior && $nuevo->precio_actual->precio_anterior > $nuevo->precio_actual->precio)
                                                <span class="price-old-nuevo">${{ number_format($nuevo->precio_actual->precio_anterior, 0, ',', '.') }}</span>
                                                <div class="product-discount-nuevo">{{ $descuentoNuevo }}% OFF</div>
                                            @endif
                                            <span class="price-current-nuevo">${{ number_format($nuevo->precio_actual->precio, 0, ',', '.') }}</span>
                                        @else
                                            <span class="price-current-nuevo">Consultar precio</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('tienda.producto', [$empresa->slug, $nuevo->id]) }}" class="btn btn-comprar-nuevo w-100">Ver producto</a>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev lo-nuevo-btn-prev"></div>
                <div class="swiper-button-next lo-nuevo-btn-next"></div>
            </div>
        </div>
    </section>
    @endif

    <!-- Estilo Auténtico Section - Producto Aleatorio -->
    @if($productoAleatorio)
    <section class="estilo-autentico-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title mb-3">Destacado del Día</h2>
                <p class="section-description">Producto seleccionado especialmente para ti</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="estilo-images-wrapper">
                        @if($productoAleatorio->imagenes && $productoAleatorio->imagenes->count() > 0)
                        <div class="estilo-thumbnails">
                            @foreach($productoAleatorio->imagenes->take(4) as $imagen)
                            <div class="estilo-thumbnail {{ $loop->first ? 'active' : '' }}" data-image="{{ $imagen->url }}">
                                <img src="{{ $imagen->url }}" alt="{{ $productoAleatorio->nombre }}">
                            </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="estilo-main-image">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $productoAleatorio->id]) }}">
                                <img src="{{ $productoAleatorio->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $productoAleatorio->nombre }}" class="js-estilo-main-image" id="estiloMainImage">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="estilo-content">
                        <h3 class="estilo-title">{{ $productoAleatorio->nombre }}</h3>
                        <div class="estilo-price mb-3">
                            @if($productoAleatorio->precio_actual)
                                @if($productoAleatorio->precio_actual->precio_anterior && $productoAleatorio->precio_actual->precio_anterior > $productoAleatorio->precio_actual->precio)
                                    <span class="price-old-estilo">${{ number_format($productoAleatorio->precio_actual->precio_anterior, 0, ',', '.') }}</span>
                                    @php
                                        $ahorro = $productoAleatorio->precio_actual->precio_anterior - $productoAleatorio->precio_actual->precio;
                                        $descuentoAleatorio = round(($ahorro / $productoAleatorio->precio_actual->precio_anterior) * 100);
                                    @endphp
                                    <span class="price-discount-estilo">{{ $descuentoAleatorio }}% OFF</span>
                                @endif
                                <span class="price-current-estilo">${{ number_format($productoAleatorio->precio_actual->precio, 0, ',', '.') }}</span>
                            @else
                                <span class="price-current-estilo">Consultar precio</span>
                            @endif
                        </div>
                        @if($productoAleatorio->precio_actual && isset($ahorro) && $ahorro > 0)
                        <p class="estilo-savings mb-2">Ahorras: ${{ number_format($ahorro, 0, ',', '.') }}</p>
                        @endif

                        @if($productoAleatorio->precio_actual)
                        <!-- Installments Info -->
                        <div class="installments-info-estilo mb-3">
                            <div class="mb-2">
                                <svg class="icon-inline icon-sm me-1"><use xlink:href="#credit-card"></use></svg>
                                <strong>3 cuotas sin interés de ${{ number_format($productoAleatorio->precio_actual->precio / 3, 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-muted small">
                                <span>Total en 1 pago: </span>
                                <strong>${{ number_format($productoAleatorio->precio_actual->precio, 0, ',', '.') }}</strong>
                                <span class="d-block">con todas las tarjetas.</span>
                            </div>
                        </div>
                        @endif

                        @if($productoAleatorio->variantes && $productoAleatorio->variantes->count() > 0)
                        <div class="estilo-options mb-3">
                            <p class="mb-2"><strong>Variantes Disponibles:</strong></p>
                            <div class="variantes-list">
                                @foreach($productoAleatorio->variantes->unique('nombre') as $variante)
                                <span class="badge bg-secondary me-1 mb-1">{{ $variante->nombre }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <a href="{{ route('tienda.producto', [$empresa->slug, $productoAleatorio->id]) }}" class="btn btn-agregar-carrito w-100">Ver producto completo</a>

                        @if($productoAleatorio->descripcion)
                        <div class="estilo-description mt-3">
                            <p class="estilo-description-text">{{ Str::limit($productoAleatorio->descripcion, 200) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Beneficios del Producto Section -->
    <section class="features-banner-section py-4">
        <div class="container">
            <div class="row text-center">
                @if($productoAleatorio && $productoAleatorio->info_envio)
                <div class="col-md-4">
                    <div class="feature-banner-item">
                        <svg class="feature-banner-icon mb-2"><use xlink:href="#truck"></use></svg>
                        <h5 class="feature-banner-title">Información de Envío</h5>
                        <p class="feature-banner-text">{{ $productoAleatorio->info_envio }}</p>
                    </div>
                </div>
                @endif

                @if($productoAleatorio && $productoAleatorio->dias_devolucion)
                <div class="col-md-4">
                    <div class="feature-banner-item">
                        <svg class="feature-banner-icon mb-2"><use xlink:href="#arrow-clockwise"></use></svg>
                        <h5 class="feature-banner-title">Devoluciones</h5>
                        <p class="feature-banner-text">{{ $productoAleatorio->dias_devolucion }} días para devolver</p>
                    </div>
                </div>
                @endif

                @if($productoAleatorio && $productoAleatorio->garantia)
                <div class="col-md-4">
                    <div class="feature-banner-item">
                        <svg class="feature-banner-icon mb-2"><use xlink:href="#shield-check"></use></svg>
                        <h5 class="feature-banner-title">Garantía</h5>
                        <p class="feature-banner-text">{{ $productoAleatorio->garantia }}</p>
                    </div>
                </div>
                @endif

                @if($empresa->whatsapp)
                <div class="col-md-4">
                    <div class="feature-banner-item">
                        <svg class="feature-banner-icon mb-2"><use xlink:href="#whatsapp"></use></svg>
                        <h5 class="feature-banner-title">Asesorate por WhatsApp</h5>
                        <p class="feature-banner-text">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}" target="_blank">¡Comunicate con nosotros!</a>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

{{--     <!-- Nos Acompañan Section -->
    <section class="brands-section py-5">
        <div class="container text-center">
            <h3 class="brands-title mb-4">Nos acompañan</h3>
            <div class="brands-logos">
                <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=YSL" alt="YSL" class="brand-logo">
                <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=ZARA" alt="ZARA" class="brand-logo">
                <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=CK" alt="CK" class="brand-logo">
                <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=Bershka" alt="Bershka" class="brand-logo">
                <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=Levis" alt="Levis" class="brand-logo">
            </div>
        </div>
    </section> --}}

    <!-- Liquidación en Carteras (Video Section) -->


    <!-- Ofertas Section -->
    <section class="ofertas-section py-5">
        <div class="container">
            <div class="section-header mb-4">
                <h2 class="section-title">Ofertas</h2>
            </div>

            <div class="swiper ofertas-swiper">
                <div class="swiper-wrapper">
                    @php
                    $ofertas = [
                        ['nombre' => 'Vestido largo Lino', 'precio' => 87000, 'precio_antes' => 120000, 'descuento' => '28% OFF', 'stock' => 'Solo quedan 3 en stock!', 'imagen' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=300&h=400&fit=crop'],
                        ['nombre' => 'Conjunto Pink', 'precio' => 70000, 'precio_antes' => 90000, 'descuento' => '13% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=400&fit=crop'],
                        ['nombre' => 'Vestido largo Flowers', 'precio' => 60000, 'precio_antes' => 110000, 'descuento' => '23% OFF', 'stock' => 'Solo quedan 2 en stock!', 'imagen' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&h=400&fit=crop'],
                        ['nombre' => 'Camisa seda fría', 'precio' => 100000, 'precio_antes' => 140000, 'descuento' => '20% MO MAS', 'imagen' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=300&h=400&fit=crop'],
                        ['nombre' => 'Blazer entero corto', 'precio' => 55000, 'precio_antes' => 150000, 'descuento' => '63% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=400&fit=crop'],
                    ];
                    @endphp

                    @foreach($ofertas as $oferta)
                    <div class="swiper-slide">
                        <div class="product-card-oferta">
                            <span class="product-badge-oferta badge-green">GRATIS</span>
                            <div class="product-image-container-oferta">
                                <img src="{{ $oferta['imagen'] }}" alt="{{ $oferta['nombre'] }}" class="product-image-oferta">
                            </div>
                            <div class="product-info-oferta">
                                <span class="product-badge-text-oferta">LLEVA 2 Y PAGA 1</span>
                                <h4 class="product-name-oferta">{{ $oferta['nombre'] }}</h4>
                                <div class="product-price-oferta">
                                    <span class="price-old-oferta">${{ number_format($oferta['precio_antes'], 0, ',', '.') }}</span>
                                    <span class="price-current-oferta">${{ number_format($oferta['precio'], 0, ',', '.') }}</span>
                                </div>
                                <div class="product-discount-oferta">{{ $oferta['descuento'] }}</div>
                                @if(isset($oferta['stock']))
                                <div class="product-stock-alert">{{ $oferta['stock'] }}</div>
                                @endif
                                <div class="product-installments-oferta">3 x ${{ number_format($oferta['precio'] / 3, 0, ',', '.') }} sin interés</div>
                                <button class="btn btn-comprar-oferta w-100">Comprar</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev ofertas-btn-prev"></div>
                <div class="swiper-button-next ofertas-btn-next"></div>
            </div>
        </div>
    </section>

    <!-- Opiniones de Clientes Section -->
 {{--    <section class="testimonials-section py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Opiniones de clientes</h2>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <img src="https://i.pravatar.cc/60?img=1" alt="Cami" class="testimonial-avatar">
                            <div class="testimonial-author">
                                <h5 class="testimonial-name">Cami</h5>
                                <div class="testimonial-stars">
                                    ★★★★★
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <h4 class="testimonial-title">Variedad de colores y talles!</h4>
                            <p class="testimonial-text">Camisetas de calidad, buena tela, tallaje real, cómodas y bien ajustadas. Las divertidas son que las colores no son 100% como en fotos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <img src="https://i.pravatar.cc/60?img=5" alt="Laura" class="testimonial-avatar">
                            <div class="testimonial-author">
                                <h5 class="testimonial-name">Laura</h5>
                                <div class="testimonial-stars">
                                    ★★★★★
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <h4 class="testimonial-title">Buena atención, llegó rápido</h4>
                            <p class="testimonial-text">Me atendieron re rápido y me ayudaron con la elección para un regalo. Las super recomiendo!</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <img src="https://i.pravatar.cc/60?img=9" alt="Patri" class="testimonial-avatar">
                            <div class="testimonial-author">
                                <h5 class="testimonial-name">Patri</h5>
                                <div class="testimonial-stars">
                                    ★★★★★
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <h4 class="testimonial-title">La calidad es excelente!</h4>
                            <p class="testimonial-text">Compré online y lo que llegó es idéntico a lo que ves en la web, me encantó!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Instagram Section -->
    @if($empresa->instagram_url)
    <section class="instagram-section py-5">
        <div class="container text-center">
            <div class="instagram-header mb-4">
                <svg class="instagram-icon mb-3"><use xlink:href="#instagram"></use></svg>
                @php
                    // Extract Instagram username from URL
                    $instagramHandle = preg_match('/instagram\.com\/([^\/]+)/', $empresa->instagram_url, $matches) ? '@' . $matches[1] : '@' . $empresa->nombre;
                @endphp
                <h3 class="instagram-title">Seguinos en {{ $instagramHandle }}</h3>
                <a href="{{ $empresa->instagram_url }}" target="_blank" class="btn btn-outline-dark mt-2">Visitar Perfil</a>
            </div>
            <div class="instagram-grid">
                @php
                    // Get random product images for Instagram grid
                    $instagramImages = $productosDestacados->take(4);
                @endphp
                @foreach($instagramImages as $productoInsta)
                <div class="instagram-item">
                    <a href="{{ route('tienda.producto', [$empresa->slug, $productoInsta->id]) }}">
                        <img src="{{ $productoInsta->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $productoInsta->nombre }}" class="instagram-image">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif


@endsection
