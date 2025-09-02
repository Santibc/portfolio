@extends('tienda.layout')

@section('title', $producto->nombre . ' - ' . $empresa->nombre)
@section('description', $producto->descripcion)
@section('body-class', 'product-details-page')

@section('content')

  <main class="main">

    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">{{ $producto->nombre }}</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ route('tienda.empresa', $empresa->slug) }}">Inicio</a></li>
            <li><a href="{{ route('tienda.empresa', [$empresa->slug, 'categoria' => $producto->categoria_id]) }}">{{ $producto->categoria->nombre }}</a></li>
            <li class="current">{{ $producto->nombre }}</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Product Details Section -->
    <section id="product-details" class="product-details section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4">
          <!-- Product Gallery -->
          <div class="col-lg-7" data-aos="zoom-in" data-aos-delay="150">
            <div class="product-gallery">
              <div class="main-showcase">
                <div class="image-zoom-container">
                  <img src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" 
                       alt="{{ $producto->nombre }}" 
                       class="img-fluid main-product-image drift-zoom" 
                       id="main-product-image" 
                       data-zoom="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}">

                  @if($producto->imagenes->count() > 1)
                  <div class="image-navigation">
                    <button class="nav-arrow prev-image image-nav-btn prev-image" type="button" onclick="navigateImages(-1)">
                      <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-arrow next-image image-nav-btn next-image" type="button" onclick="navigateImages(1)">
                      <i class="bi bi-chevron-right"></i>
                    </button>
                  </div>
                  @endif
                </div>
              </div>

              @if($producto->imagenes->count() > 0)
              <div class="thumbnail-grid">
                @foreach($producto->imagenes as $index => $imagen)
                <div class="thumbnail-wrapper thumbnail-item {{ $loop->first ? 'active' : '' }}" 
                     data-image="{{ $imagen->url }}"
                     onclick="changeMainImage('{{ $imagen->url }}', this)">
                  <img src="{{ $imagen->url }}" alt="{{ $producto->nombre }} - Vista {{ $loop->iteration }}" class="img-fluid">
                </div>
                @endforeach
              </div>
              @else
              {{-- Si no hay imágenes, mostrar una sola con placeholder --}}
              <div class="thumbnail-grid">
                <div class="thumbnail-wrapper thumbnail-item active" 
                     data-image="{{ asset('assets/img/product/placeholder.webp') }}">
                  <img src="{{ asset('assets/img/product/placeholder.webp') }}" alt="{{ $producto->nombre }}" class="img-fluid">
                </div>
              </div>
              @endif
            </div>
          </div>

          <!-- Product Details -->
          <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
            <div class="product-details">
{{--               <div class="product-badge-container">
                <span class="badge-category">{{ $producto->categoria->nombre }}</span>
                <div class="rating-group">
                  <div class="stars">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                  </div>
                  <span class="review-text">(127 reviews)</span>
                </div>
              </div> --}}

              <h1 class="product-name">{{ $producto->nombre }}</h1>

              <div class="pricing-section">
                @if($producto->precio_actual)
                <div class="price-display">
                  <span class="sale-price">${{ number_format($producto->precio_actual, 0, ',', '.') }}</span>
                  {{-- Precio anterior quemado por ahora --}}
                  @if(false)
                  <span class="regular-price">$239.99</span>
                  @endif
                </div>
                @if(false)
                <div class="savings-info">
                  <span class="save-amount">Save $50.00</span>
                  <span class="discount-percent">(21% off)</span>
                </div>
                @endif
                @else
                <div class="price-display">
                  <span class="text-muted">Precio no disponible</span>
                </div>
                @endif
              </div>

              <div class="product-description">
                <p>{{ $producto->descripcion ?: 'No hay descripción disponible para este producto.' }}</p>
              </div>

              {{-- Estado de disponibilidad --}}
              <div class="availability-status">
                @if($producto->tiene_variantes)
                  <div class="stock-indicator" id="stockInfo">
                    <i class="bi bi-info-circle"></i>
                    <span class="stock-text">Selecciona una opción para ver disponibilidad</span>
                  </div>
                @else
                  @php 
                    $stockInfo = $producto->getStockInfo();
                    $stockDisponible = $stockInfo['stock_disponible'];
                  @endphp
                  @if(!$stockInfo['controlar_stock'] || $stockInfo['permitir_venta_sin_stock'])
                    <div class="stock-indicator">
                      <i class="bi bi-check-circle-fill"></i>
                      <span class="stock-text">Disponible</span>
                    </div>
                  @elseif($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock'])
                    @if($stockDisponible > 10)
                      <div class="stock-indicator">
                        <i class="bi bi-check-circle-fill"></i>
                        <span class="stock-text">Disponible</span>
                      </div>
                    @elseif($stockDisponible > 0)
                      <div class="stock-indicator">
                        <i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>
                        <span class="stock-text">Limitado</span>
                      </div>
                      <div class="quantity-left">Solo {{ $stockDisponible }} unidades disponibles</div>
                    @else
                      <div class="stock-indicator">
                        <i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>
                        <span class="stock-text">Sin Stock</span>
                      </div>
                    @endif
                  @endif
                @endif
              </div>

              <!-- Product Variants -->
              @if($producto->tiene_variantes && $producto->variantes->count() > 0)
                <div class="variant-section">
                  <div class="variant-selection">
                    <label class="variant-label">Variantes Disponibles:</label>
                    <div class="d-flex flex-wrap gap-2">
                      @foreach($producto->variantes as $variante)
                        @php
                          $varianteStockInfo = $producto->getStockInfo($variante->id);
                          $tieneStockDisponible = $varianteStockInfo['hay_stock'];
                          $nombreVariante = $variante->nombre_variante;
                        @endphp
                        <button class="btn btn-outline-secondary variant-option {{ !$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock ? 'disabled' : '' }}"
                                data-type="variante"
                                data-variante-id="{{ $variante->id }}"
                                data-talla="{{ $variante->talla }}"
                                data-color="{{ $variante->color }}"
                                data-value="{{ $nombreVariante }}"
                                data-stock-disponible="{{ $varianteStockInfo['stock_disponible'] }}"
                                data-puede-agregar-sin-stock="{{ $varianteStockInfo['puede_agregar_sin_stock'] ? 'true' : 'false' }}"
                                {{ (!$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock) ? 'disabled' : '' }}>
                          {{ $nombreVariante ?: 'Sin especificar' }}
                        </button>
                      @endforeach
                    </div>
                    <div class="selected-variant mt-2">Variante seleccionada: <span id="selectedVariant">-</span></div>
                  </div>
                </div>
              @endif

              <!-- Purchase Options -->
              <div class="purchase-section">
                <div class="quantity-control">
                  <label class="control-label">Cantidad:</label>
                  <div class="quantity-input-group">
                    <div class="quantity-selector">
                      <button class="quantity-btn decrease" type="button" onclick="updateQuantity(-1)">
                        <i class="bi bi-dash"></i>
                      </button>
                      <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="99">
                      <button class="quantity-btn increase" type="button" onclick="updateQuantity(1)">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="action-buttons">
                  <button class="btn primary-action" id="addToCartBtn"
                    @php $stockInfo = $producto->getStockInfo(); @endphp
                    {{ (!$producto->precio_actual || (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado'])) ? 'disabled' : '' }}>
                    <i class="bi bi-bag-plus"></i>
                    Agregar al Carrito
                  </button>
                  <button class="btn secondary-action" onclick="comprarAhora()">
                    <i class="bi bi-lightning"></i>
                    Comprar Ahora
                  </button>
                  <button class="btn icon-action" title="Agregar a favoritos">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
              </div>

              <!-- Benefits List -->
              @if($producto->info_envio || $producto->dias_devolucion || $producto->garantia)
              <div class="benefits-list">
                @if($producto->info_envio)
                <div class="benefit-item">
                  <i class="bi bi-truck"></i>
                  <span>{{ $producto->info_envio }}</span>
                </div>
                @endif
                
                @if($producto->dias_devolucion)
                <div class="benefit-item">
                  <i class="bi bi-arrow-clockwise"></i>
                  <span>{{ $producto->dias_devolucion }}</span>
                </div>
                @endif
                
                @if($producto->garantia)
                <div class="benefit-item">
                  <i class="bi bi-shield-check"></i>
                  <span>{{ $producto->garantia }}</span>
                </div>
                @endif
              </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Information Tabs -->
        <div class="row mt-5" data-aos="fade-up" data-aos-delay="300">
          <div class="col-12">
            <div class="info-tabs-container">
              <nav class="tabs-navigation nav">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-overview" type="button">Descripción</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-technical" type="button">Detalles Técnicos</button>
               {{--  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-customer-reviews" type="button">Reseñas (127)</button> --}}
              </nav>

              <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="ecommerce-product-details-5-overview">
                  <div class="overview-content">
                    <div class="row g-4">
                      <div class="col-lg-8">
                        <div class="content-section">
                          <h3>Descripción del Producto</h3>
                          <p>{{ $producto->descripcion ?: 'No hay descripción disponible para este producto.' }}</p>

                          <h4>Características Principales</h4>
                          <div class="highlights-grid">
                            <div class="highlight-card">
                              <i class="bi bi-box"></i>
                              <h5>Unidad de Venta</h5>
                              <p>{{ $producto->unidad_venta }}</p>
                            </div>
                            <div class="highlight-card">
                              <i class="bi bi-box-seam"></i>
                              <h5>Unidad de Empaque</h5>
                              <p>{{ $producto->unidad_empaque }}</p>
                            </div>
                            @if($producto->extension)
                            <div class="highlight-card">
                              <i class="bi bi-rulers"></i>
                              <h5>Extensión</h5>
                              <p>{{ $producto->extension }}</p>
                            </div>
                            @endif
                            <div class="highlight-card">
                              <i class="bi bi-tag"></i>
                              <h5>Referencia</h5>
                              <p>{{ $producto->referencia }}</p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="package-contents">
                          <h4>Contenido del Paquete</h4>
                          <ul class="contents-list">
                            <li><i class="bi bi-check-circle"></i>{{ $producto->nombre }}</li>
                            <li><i class="bi bi-check-circle"></i>Empaque Premium</li>
                        {{--     <li><i class="bi bi-check-circle"></i>Instrucciones de Uso</li> --}}
                            <li><i class="bi bi-check-circle"></i>Garantía del Fabricante</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Technical Details Tab -->
                <div class="tab-pane fade" id="ecommerce-product-details-5-technical">
                  <div class="technical-content">
                    <div class="row g-4">
                      <div class="col-md-12">
                        <div class="tech-group">
                          <h4>Especificaciones del Producto</h4>
                          <div class="spec-table">
                            <div class="spec-row">
                              <span class="spec-name">Referencia</span>
                              <span class="spec-value">{{ $producto->referencia }}</span>
                            </div>
                            <div class="spec-row">
                              <span class="spec-name">Categoría</span>
                              <span class="spec-value">{{ $producto->categoria->nombre }}</span>
                            </div>
                            <div class="spec-row">
                              <span class="spec-name">Unidad de Venta</span>
                              <span class="spec-value">{{ $producto->unidad_venta }}</span>
                            </div>
                            <div class="spec-row">
                              <span class="spec-name">Unidad de Empaque</span>
                              <span class="spec-value">{{ $producto->unidad_empaque }}</span>
                            </div>
                            @if($producto->extension)
                            <div class="spec-row">
                              <span class="spec-name">Extensión</span>
                              <span class="spec-value">{{ $producto->extension }}</span>
                            </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reviews Tab -->
    {{--             <div class="tab-pane fade" id="ecommerce-product-details-5-customer-reviews">
                  <div class="reviews-content">
                    <div class="reviews-header">
                      <div class="rating-overview">
                        <div class="average-score">
                          <div class="score-display">4.6</div>
                          <div class="score-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                          </div>
                          <div class="total-reviews">127 reseñas de clientes</div>
                        </div>

                        <div class="rating-distribution">
                          <div class="rating-row">
                            <span class="stars-label">5★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 68%;"></div>
                            </div>
                            <span class="count-label">86</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">4★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 22%;"></div>
                            </div>
                            <span class="count-label">28</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">3★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 6%;"></div>
                            </div>
                            <span class="count-label">8</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">2★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 3%;"></div>
                            </div>
                            <span class="count-label">4</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">1★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 1%;"></div>
                            </div>
                            <span class="count-label">1</span>
                          </div>
                        </div>
                      </div>

                      <div class="write-review-cta">
                        <h4>Comparte tu Experiencia</h4>
                        <p>Ayuda a otros a tomar decisiones informadas</p>
                        <button class="btn review-btn">Escribir Reseña</button>
                      </div>
                    </div>

                    <div class="customer-reviews-list">
                      <div class="review-card">
                        <div class="reviewer-profile">
                          <img src="{{ asset('assets/img/person/person-f-3.webp') }}" alt="Cliente" class="profile-pic">
                          <div class="profile-details">
                            <div class="customer-name">María González</div>
                            <div class="review-meta">
                              <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                              </div>
                              <span class="review-date">28 de Marzo, 2024</span>
                            </div>
                          </div>
                        </div>
                        <h5 class="review-headline">Excelente calidad y comodidad</h5>
                        <div class="review-text">
                          <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam. Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                        </div>
                        <div class="review-actions">
                          <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Útil (12)</button>
                          <button class="action-btn"><i class="bi bi-chat-dots"></i> Responder</button>
                        </div>
                      </div>

                      <div class="review-card">
                        <div class="reviewer-profile">
                          <img src="{{ asset('assets/img/person/person-m-5.webp') }}" alt="Cliente" class="profile-pic">
                          <div class="profile-details">
                            <div class="customer-name">Carlos Rodríguez</div>
                            <div class="review-meta">
                              <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                              </div>
                              <span class="review-date">15 de Marzo, 2024</span>
                            </div>
                          </div>
                        </div>
                        <h5 class="review-headline">Buen producto, entrega rápida</h5>
                        <div class="review-text">
                          <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. En general satisfecho con la compra.</p>
                        </div>
                        <div class="review-actions">
                          <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Útil (8)</button>
                          <button class="action-btn"><i class="bi bi-chat-dots"></i> Responder</button>
                        </div>
                      </div>

                      <div class="load-more-section">
                        <button class="btn load-more-reviews">Mostrar Más Reseñas</button>
                      </div>
                    </div>
                  </div>
                </div> --}}
              </div>
            </div>
          </div>
        </div>

        {{-- Productos relacionados --}}
        @if($relacionados->count() > 0)
        <div class="row mt-5" data-aos="fade-up" data-aos-delay="400">
          <div class="col-12">
            <h3 class="mb-4">Productos Relacionados</h3>
            <div class="row g-4">
              @foreach($relacionados as $relacionado)
              <div class="col-lg-3 col-md-6">
                <div class="product-item">
                  <div class="product-image">
                    @if($relacionado->stock_disponible <= 5 && $relacionado->stock_disponible > 0)
                      <div class="product-badge">¡Últimas unidades!</div>
                    @elseif($relacionado->stock_disponible == 0 && !$relacionado->permitir_venta_sin_stock)
                      <div class="product-badge sale-badge">Sin Stock</div>
                    @endif
                    <img src="{{ $relacionado->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" 
                         alt="{{ $relacionado->nombre }}" 
                         class="img-fluid" 
                         loading="lazy">
                    <div class="product-actions">
                      <button class="action-btn wishlist-btn">
                        <i class="bi bi-heart"></i>
                      </button>
                      <button class="action-btn compare-btn">
                        <i class="bi bi-arrow-left-right"></i>
                      </button>
                      <button class="action-btn quickview-btn">
                        <i class="bi bi-zoom-in"></i>
                      </button>
                    </div>
                    <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}" class="cart-btn">Ver Producto</a>
                  </div>
                  <div class="product-info">
                    <div class="product-category">{{ $relacionado->categoria->nombre }}</div>
                    <h4 class="product-name">
                      <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}">{{ $relacionado->nombre }}</a>
                    </h4>
                    <div class="product-rating">
                      <div class="stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star"></i>
                      </div>
                      <span class="rating-count">({{ rand(10, 50) }})</span>
                    </div>
                    @if($relacionado->precio_actual)
                      <div class="product-price">${{ number_format($relacionado->precio_actual, 0, ',', '.') }}</div>
                    @else
                      <div class="product-price text-muted">Precio no disponible</div>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

      </div>
    </section><!-- /Product Details Section -->

  </main>

@endsection

@push('scripts')
<script>
  // === Variantes disponibles del producto (JSON) ===
  const variantes = @json($producto->variantes);
  const tieneVariantes = {{ $producto->tiene_variantes ? 'true' : 'false' }};
  let selectedVariant = null;
  let selectedTalla = null;
  let selectedColor = null;
  let currentImageIndex = 0;
  const productImages = @json($producto->imagenes->pluck('url'));

  $(document).ready(function() {
    // Inicializar Drift zoom si está disponible
    if (typeof Drift !== 'undefined') {
      new Drift(document.querySelector('.drift-zoom'), {
        paneContainer: document.querySelector('.image-zoom-container'),
        inlinePane: true,
        inlineOffsetY: -85,
        containInline: true,
        hoverBoundingBox: true
      });
    }

    // Selección de variantes
    $('.variant-option:not(:disabled)').on('click', function() {
      const type = $(this).data('type');
      
      if (type === 'variante') {
        // Nueva lógica para variantes unificadas
        $('.variant-option[data-type="variante"]').removeClass('selected active');
        $(this).addClass('selected active');
        
        const varianteId = $(this).data('variante-id');
        const varianteNombre = $(this).data('value');
        selectedTalla = $(this).data('talla');
        selectedColor = $(this).data('color');
        
        // Encontrar la variante seleccionada
        selectedVariant = variantes.find(v => v.id == varianteId);
        
        $('#selectedVariant').text(varianteNombre);
        
        if (selectedVariant) {
          updateStockInfo(varianteId);
          // Solo habilitar si puede agregar al carrito
          const puedeAgregar = $(this).data('puede-agregar-sin-stock') === 'true' || $(this).data('stock-disponible') > 0;
          $('#addToCartBtn').prop('disabled', !puedeAgregar);
        }
      } else {
        // Lógica anterior para compatibilidad (si se necesita)
        const value = $(this).data('value');
        $(`.variant-option[data-type="${type}"]`).removeClass('selected active');
        $(this).addClass('selected active');

        if (type === 'talla') {
          selectedTalla = value;
        }
        if (type === 'color') {
          selectedColor = value;
          $('#selectedColor').text(value);
        }

        updateVariantAvailability();
        if (tieneVariantes) findSelectedVariant();
      }
    });

    // Agregar al carrito
    $('#addToCartBtn').on('click', function() {
      const btn = $(this);
      const quantity = parseInt($('#quantity').val());

      if (tieneVariantes && !selectedVariant) {
        showToast('error', 'Por favor selecciona todas las opciones del producto');
        return;
      }

      btn.prop('disabled', true);
      btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Agregando...');

      const data = {
        producto_id: {{ $producto->id }},
        cantidad: quantity
      };
      if (selectedVariant) data.variante_id = selectedVariant.id;

      $.ajax({
        url: "{{ route('tienda.carrito.agregar', $empresa->slug) }}",
        method: 'POST',
        data: data,
        success: function(response) {
          showToast('success', 'Producto agregado al carrito');
          if (response && typeof response.total_items !== 'undefined') {
            updateCartBadge(response.total_items);
          }
          btn.html('<i class="bi bi-check"></i> Agregado al Carrito');

          setTimeout(() => {
            btn.prop('disabled', false);
            btn.html('<i class="bi bi-bag-plus"></i> Agregar al Carrito');
          }, 2000);
        },
        error: function(xhr) {
          const error = xhr.responseJSON?.error || 'Error al agregar al carrito';
          showToast('error', error);
          btn.prop('disabled', false);
          btn.html('<i class="bi bi-bag-plus"></i> Agregar al Carrito');
        }
      });
    });

    // Cambiar imagen con thumbnails
    $('.thumbnail-item').on('click', function() {
      const index = $('.thumbnail-item').index(this);
      currentImageIndex = index;
      updateMainImage();
    });
  });

  // Cambiar imagen principal
  function changeMainImage(url, thumbnail) {
    $('#main-product-image').attr('src', url);
    $('#main-product-image').attr('data-zoom', url);
    $('.thumbnail-item').removeClass('active');
    $(thumbnail).addClass('active');
    
    // Re-inicializar Drift si existe
    if (typeof Drift !== 'undefined') {
      const oldDrift = document.querySelector('.drift-zoom').drift;
      if (oldDrift) oldDrift.destroy();
      
      new Drift(document.querySelector('.drift-zoom'), {
        paneContainer: document.querySelector('.image-zoom-container'),
        inlinePane: true,
        inlineOffsetY: -85,
        containInline: true,
        hoverBoundingBox: true
      });
    }
  }

  // Navegación de imágenes con flechas
  function navigateImages(direction) {
    const totalImages = productImages.length;
    currentImageIndex = (currentImageIndex + direction + totalImages) % totalImages;
    updateMainImage();
  }

  function updateMainImage() {
    const url = productImages[currentImageIndex];
    $('#main-product-image').attr('src', url);
    $('#main-product-image').attr('data-zoom', url);
    $('.thumbnail-item').removeClass('active');
    $('.thumbnail-item').eq(currentImageIndex).addClass('active');
  }

  // Actualizar cantidad
  function updateQuantity(change) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + change;

    if (value < 1) value = 1;

    // Verificar límite por stock usando el input max attribute
    const maxStock = parseInt(input.getAttribute('max'));
    if (maxStock && value > maxStock) {
      value = maxStock;
      showToast('error', `Solo hay ${maxStock} unidades disponibles`);
    }

    input.value = value;
  }

  // Actualizar disponibilidad de variantes (simplificada)
  function updateVariantAvailability() {
    // Con las variantes unificadas, esta función es menos necesaria
    // ya que el stock se maneja directamente en la renderización inicial
    if (!tieneVariantes) return;

    // Solo ejecutar si hay variantes separadas (compatibilidad)
    if ($('.variant-option[data-type="color"]').length > 0 || $('.variant-option[data-type="talla"]').length > 0) {
      // Filtra colores por talla
      if (selectedTalla) {
        $('.variant-option[data-type="color"]').each(function() {
          const color = $(this).data('value');
          const hayStock = variantes.some(v =>
            v.talla === selectedTalla &&
            v.color === color &&
            v.stock &&
            v.stock.stock_real > 0
          );
          $(this).prop('disabled', !hayStock).toggleClass('disabled', !hayStock);
        });
      }

      // Filtra tallas por color
      if (selectedColor) {
        $('.variant-option[data-type="talla"]').each(function() {
          const talla = $(this).data('value');
          const hayStock = variantes.some(v =>
            v.talla === talla &&
            v.color === selectedColor &&
            v.stock &&
            v.stock.stock_real > 0
          );
          $(this).prop('disabled', !hayStock).toggleClass('disabled', !hayStock);
        });
      }
    }
  }

  // Encuentra la variante seleccionada (simplificada para variantes unificadas)
  function findSelectedVariant() {
    // Esta función ahora es más simple porque las variantes se seleccionan directamente
    if (!selectedVariant) {
      $('#stockInfo').html('<i class="bi bi-info-circle"></i> <span class="stock-text">Selecciona una variante</span>');
      $('#addToCartBtn').prop('disabled', true);
      return;
    }

    updateStockInfo(selectedVariant);
    $('#addToCartBtn').prop('disabled', false);
  }

  // Actualizar información de stock
  function updateStockInfo(varianteId) {
    // Obtener información de stock vía AJAX
    $.ajax({
      url: "{{ route('tienda.stock.info', $empresa->slug) }}",
      method: 'POST',
      data: {
        producto_id: {{ $producto->id }},
        variante_id: varianteId
      },
      success: function(stockInfo) {
        const stock = stockInfo.stock_disponible || 0;
        let stockClass, stockText, stockIcon, quantityText = '';
        
        if (!stockInfo.controlar_stock || stockInfo.permitir_venta_sin_stock) {
          stockClass = 'stock-available';
          stockText = 'Disponible';
          stockIcon = 'check-circle-fill';
        } else if (stockInfo.controlar_stock && !stockInfo.permitir_venta_sin_stock) {
          if (stock > 10) {
            stockClass = 'stock-available';
            stockText = 'Disponible';
            stockIcon = 'check-circle-fill';
          } else if (stock > 0) {
            stockClass = 'stock-low';
            stockText = 'Limitado';
            stockIcon = 'exclamation-circle-fill';
            quantityText = `<div class="quantity-left">Solo ${stock} unidades disponibles</div>`;
          } else {
            stockClass = 'stock-out';
            stockText = 'Sin stock';
            stockIcon = 'x-circle-fill';
          }
        }

        $('#stockInfo').html(`
          <div class="stock-indicator">
            <i class="bi bi-${stockIcon}"></i>
            <span class="stock-text">${stockText}</span>
          </div>
          ${quantityText}
        `);
        
        // Actualizar límite de cantidad
        const quantityInput = document.getElementById('quantity');
        if (stockInfo.stock_limitado && stock > 0) {
          quantityInput.max = stock;
        } else {
          quantityInput.removeAttribute('max');
        }
      }
    });
  }

  // Comprar ahora
  function comprarAhora() {
    // Primero agregar al carrito
    const quantity = parseInt($('#quantity').val());

    if (tieneVariantes && !selectedVariant) {
      showToast('error', 'Por favor selecciona todas las opciones del producto');
      return;
    }

    const data = {
      producto_id: {{ $producto->id }},
      cantidad: quantity
    };
    if (selectedVariant) data.variante_id = selectedVariant.id;

    $.ajax({
      url: "{{ route('tienda.carrito.agregar', $empresa->slug) }}",
      method: 'POST',
      data: data,
      success: function(response) {
        // Redirigir al checkout
        window.location.href = "{{ route('tienda.checkout', $empresa->slug) }}";
      },
      error: function(xhr) {
        const error = xhr.responseJSON?.error || 'Error al procesar la compra';
        showToast('error', error);
      }
    });
  }

  // Toast notification
  function showToast(type, message) {
    const toastEl = document.getElementById('cartToast');
    const toast = new bootstrap.Toast(toastEl);

    $('.toast-body').text(message);
    if (type === 'error') {
      $('.toast-header i').removeClass('text-success').addClass('text-danger');
      $('.toast-header i').removeClass('bi-check-circle-fill').addClass('bi-exclamation-circle-fill');
    } else {
      $('.toast-header i').removeClass('text-danger').addClass('text-success');
      $('.toast-header i').removeClass('bi-exclamation-circle-fill').addClass('bi-check-circle-fill');
    }

    toast.show();
  }

  // Actualiza el badge del carrito
  function updateCartBadge(count) {
    if (count > 0) {
      if ($('.header-action-btn .badge').length) {
        $('.header-action-btn .badge').text(count);
      } else {
        $('.header-action-btn').append('<span class="badge">' + count + '</span>');
      }
    } else {
      $('.header-action-btn .badge').remove();
    }
  }
</script>
@endpush