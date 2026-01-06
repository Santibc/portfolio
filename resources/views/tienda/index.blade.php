@extends('tienda.layout')

@section('title', $empresa->nombre . ' - Flores frescas entregadas hoy')
@section('nav-inicio', 'active')

@section('content')
    <!-- Hero Section Minimalista -->
    <section class="hero-minimal">
      <div class="container">
        <div class="row align-items-center min-vh-75">
          <div class="col-lg-6 order-2 order-lg-1">
            <div class="hero-content-minimal">
              <h1 class="hero-title-minimal">
                Flores frescas<br>
                entregadas hoy en<br>
                <span class="ciudad-nombre" id="ciudadTexto">Santiago</span>
              </h1>

              <div class="hero-selectors">
                <div class="selector-group">
                  <label class="selector-label">Ciudad</label>
                  <div class="selector-input">
                    <select class="form-select" id="ciudadSelect">
                      <option value="Santiago">Santiago</option>
                      <option value="Providencia">Providencia</option>
                      <option value="Las Condes">Las Condes</option>
                      <option value="Ñuñoa">Ñuñoa</option>
                      <option value="Valparaíso">Valparaíso</option>
                      <option value="Viña del Mar">Viña del Mar</option>
                    </select>
                  </div>
                </div>

                <div class="selector-group">
                  <label class="selector-label">Fecha de entrega</label>
                  <div class="selector-input">
                    <select class="form-select" id="fechaEntrega">
                      <option value="hoy">Hoy</option>
                      <option value="otro">Otro día</option>
                    </select>
                  </div>
                </div>
              </div>

              <a href="{{ route('tienda.categorias') }}" class="btn-flores btn-hero-search">
                Ver ramos disponibles hoy
              </a>

              <!-- Hero Benefits -->
              <div class="hero-benefits">
                <div class="hero-benefit-item">
                  <span class="benefit-title">Entrega gratis</span>
                  <span class="benefit-desc">En pedidos sobre $50.000</span>
                </div>
                <div class="hero-benefit-item">
                  <span class="benefit-title">Flores frescas</span>
                  <span class="benefit-desc">Garantía de 7 días</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 order-1 order-lg-2">
            <div class="hero-image-minimal">
              @if($empresa->carruselImagenesActivas->count() > 1)
                {{-- Carrusel con Swiper --}}
                <div class="swiper hero-swiper">
                  <div class="swiper-wrapper">
                    @foreach($empresa->carruselImagenesActivas as $carruselImg)
                      <div class="swiper-slide">
                        @if($carruselImg->link)
                          <a href="{{ $carruselImg->link }}" class="hero-slide-link">
                        @endif
                        <img src="{{ $carruselImg->imagen_url }}" alt="{{ $carruselImg->titulo ?? 'Flores frescas' }}" class="img-fluid">
                        @if($carruselImg->titulo || $carruselImg->descripcion)
                          <div class="hero-slide-caption">
                            @if($carruselImg->titulo)
                              <h3>{{ $carruselImg->titulo }}</h3>
                            @endif
                            @if($carruselImg->descripcion)
                              <p>{{ $carruselImg->descripcion }}</p>
                            @endif
                          </div>
                        @endif
                        @if($carruselImg->link)
                          </a>
                        @endif
                      </div>
                    @endforeach
                  </div>
                  {{-- Pagination --}}
                  <div class="swiper-pagination"></div>
                  {{-- Navigation --}}
                  <div class="swiper-button-prev"></div>
                  <div class="swiper-button-next"></div>
                </div>
              @elseif($empresa->carruselImagenesActivas->count() == 1)
                {{-- Solo una imagen --}}
                @php $carruselImg = $empresa->carruselImagenesActivas->first(); @endphp
                @if($carruselImg->link)
                  <a href="{{ $carruselImg->link }}">
                @endif
                <img src="{{ $carruselImg->imagen_url }}" alt="{{ $carruselImg->titulo ?? 'Flores frescas' }}" class="img-fluid">
                @if($carruselImg->link)
                  </a>
                @endif
              @else
                <div class="hero-placeholder">
                  <i class="bi bi-flower1"></i>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filtros por Ocasión -->
    <section class="ocasiones-section">
      <div class="container">
        <div class="ocasiones-pills">
          <a href="{{ route('tienda.categorias', ['ocasion' => 'cumpleanos']) }}" class="ocasion-pill active">
            Cumpleaños
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'amor']) }}" class="ocasion-pill">
            Amor
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'aniversarios']) }}" class="ocasion-pill">
            Aniversarios
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'condolencias']) }}" class="ocasion-pill">
            Condolencias
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'gracias']) }}" class="ocasion-pill">
            Gracias
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'felicidades']) }}" class="ocasion-pill">
            Felicidades
          </a>
          <a href="{{ route('tienda.categorias', ['ocasion' => 'mejorate']) }}" class="ocasion-pill">
            Mejórate pronto
          </a>
        </div>
      </div>
    </section>

    <!-- Products Section - Diseño Minimalista -->
    <section id="productos" class="productos-minimal">
      <div class="container">
        <div class="section-header-minimal">
          <h2 class="section-title-minimal">Favoritos de hoy</h2>
          <a href="{{ route('tienda.categorias') }}" class="ver-todos-link">
            Ver todos →
          </a>
        </div>

        <div class="row g-4">
          @forelse($productos as $producto)
          @php
              $stockInfo = $producto->getStockInfo();
              $precioActual = $producto->precio_actual;
          @endphp
          <div class="col-lg-3 col-md-4 col-6">
            <div class="product-card-minimal" data-href="{{ route('tienda.producto', $producto->id) }}">
              <div class="product-image-minimal">
                <span class="badge-entrega-hoy">
                  <i class="bi bi-lightning-fill"></i> Entrega hoy
                </span>
                <img src="{{ $producto->url_imagen_principal }}" alt="{{ $producto->nombre }}" loading="lazy">
                <button class="btn-favorito" type="button" title="Agregar a favoritos">
                  <i class="bi bi-heart"></i>
                </button>
              </div>
              <div class="product-info-minimal">
                <h3 class="product-name-minimal">{{ $producto->nombre }}</h3>
                @if($precioActual)
                  <p class="product-price-minimal">${{ number_format($precioActual, 0, ',', '.') }}</p>
                @endif
                @if(!$stockInfo['hay_stock'] && $stockInfo['stock_limitado'])
                  <span class="btn-agotado">Agotado</span>
                @else
                  <button class="btn-agregar-minimal quick-add-btn"
                          data-producto-id="{{ $producto->id }}"
                          data-precio="{{ $precioActual }}">
                    Agregar
                  </button>
                @endif
              </div>
            </div>
          </div>
          @empty
          <div class="col-12">
            <div class="empty-state-minimal">
              <i class="bi bi-flower1"></i>
              <p>No hay productos disponibles en este momento.</p>
            </div>
          </div>
          @endforelse
        </div>

        @if($productos->hasPages())
        <div class="mt-5 d-flex justify-content-center">
          {{ $productos->links('pagination::bootstrap-5') }}
        </div>
        @endif
      </div>
    </section>

    <!-- Sección Beneficios -->
    <section class="beneficios-section">
      <div class="container">
        <div class="beneficios-grid">
          <div class="beneficio-item">
            <div class="beneficio-icon">
              <i class="bi bi-truck"></i>
            </div>
            <div class="beneficio-content">
              <h4>Envío gratis</h4>
              <p>En pedidos sobre $50.000</p>
            </div>
          </div>

          <div class="beneficio-item">
            <div class="beneficio-icon">
              <i class="bi bi-clock"></i>
            </div>
            <div class="beneficio-content">
              <h4>Entrega el mismo día</h4>
              <p>Pedidos antes de las 2pm</p>
            </div>
          </div>

          <div class="beneficio-item">
            <div class="beneficio-icon">
              <i class="bi bi-shield-check"></i>
            </div>
            <div class="beneficio-content">
              <h4>Garantía de frescura</h4>
              <p>Flores 100% frescas</p>
            </div>
          </div>

          <div class="beneficio-item">
            <div class="beneficio-icon">
              <i class="bi bi-headset"></i>
            </div>
            <div class="beneficio-content">
              <h4>Atención personalizada</h4>
              <p>Soporte por WhatsApp</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Banner CTA Arma tu Ramo -->
    <section class="cta-arma-ramo">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-title">¿No encuentras lo que buscas?</h2>
          <p class="cta-description">Crea tu ramo personalizado seleccionando tus flores favoritas. Nuestros expertos lo armarán especialmente para ti.</p>
          <a href="{{ route('arma-tu-ramo') }}" class="btn-cta-ramo">
            Arma tu ramo personalizado
          </a>
        </div>
      </div>
    </section>

@endsection

@push('styles')
<style>
/* ============ HERO MINIMAL ============ */
.hero-minimal {
  padding: 60px 0 40px;
  background: var(--flores-bg);
}

.min-vh-75 {
  min-height: 75vh;
}

.hero-content-minimal {
  padding-right: 40px;
}

.hero-title-minimal {
  font-family: var(--font-serif);
  font-size: 48px;
  font-weight: 500;
  line-height: 1.2;
  color: var(--flores-text);
  margin-bottom: 32px;
}

.hero-title-minimal .ciudad-nombre {
  color: var(--flores-primary);
  display: block;
}

.hero-selectors {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}

.selector-group {
  flex: 1;
  min-width: 180px;
}

.selector-label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: var(--flores-text-light);
  margin-bottom: 8px;
}

.selector-input {
  position: relative;
}

.selector-input .form-select,
.selector-input .form-control {
  height: 52px;
  border: 1px solid var(--flores-border);
  border-radius: 30px;
  font-size: 15px;
  color: var(--flores-text);
  background-color: white;
  padding: 0 20px;
  padding-right: 40px;
}

.selector-input .form-select:focus,
.selector-input .form-control:focus {
  border-color: var(--flores-primary);
  box-shadow: 0 0 0 3px rgba(107, 116, 86, 0.1);
}

.btn-hero-search {
  height: 52px;
  padding: 0 32px;
  white-space: nowrap;
  border-radius: 30px;
  font-size: 15px;
  font-weight: 500;
}

/* Hero Benefits */
.hero-benefits {
  display: flex;
  gap: 32px;
  margin-top: 32px;
}

.hero-benefit-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hero-benefit-item .benefit-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--flores-primary);
}

.hero-benefit-item .benefit-desc {
  font-size: 13px;
  color: var(--flores-text-light);
}

.hero-image-minimal {
  text-align: center;
}

.hero-image-minimal img {
  max-height: 500px;
  width: auto;
  border-radius: 16px;
  object-fit: cover;
}

.hero-placeholder {
  height: 400px;
  background: var(--flores-beige);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-placeholder i {
  font-size: 120px;
  color: var(--flores-primary);
  opacity: 0.3;
}

/* ============ HERO SWIPER CARRUSEL ============ */
.hero-swiper {
  border-radius: 16px;
  overflow: visible;
  padding-bottom: 32px;
}

.hero-swiper .swiper-slide {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
}

.hero-swiper .swiper-slide img {
  width: 100%;
  height: 450px;
  object-fit: cover;
}

.hero-slide-link {
  display: block;
}

.hero-slide-caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 24px;
  background: linear-gradient(transparent, rgba(0,0,0,0.7));
  color: white;
}

.hero-slide-caption h3 {
  font-family: var(--font-serif);
  font-size: 20px;
  font-weight: 500;
  margin: 0 0 4px;
}

.hero-slide-caption p {
  font-size: 14px;
  margin: 0;
  opacity: 0.9;
}

/* Swiper Navigation */
.hero-swiper .swiper-button-prev,
.hero-swiper .swiper-button-next {
  width: 40px;
  height: 40px;
  background: white;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  opacity: 0;
  transition: opacity 0.3s;
  top: calc(50% - 16px);
}

.hero-swiper:hover .swiper-button-prev,
.hero-swiper:hover .swiper-button-next {
  opacity: 1;
}

.hero-swiper .swiper-button-prev::after,
.hero-swiper .swiper-button-next::after {
  font-size: 16px;
  font-weight: bold;
  color: var(--flores-text);
}

.hero-swiper .swiper-button-prev {
  left: 16px;
}

.hero-swiper .swiper-button-next {
  right: 16px;
}

/* Swiper Pagination - Debajo de la imagen */
.hero-swiper .swiper-pagination {
  bottom: 0;
  position: absolute;
}

.hero-swiper .swiper-pagination-bullet {
  width: 8px;
  height: 8px;
  background: var(--flores-border);
  opacity: 1;
  margin: 0 4px;
}

.hero-swiper .swiper-pagination-bullet-active {
  background: var(--flores-primary);
  width: 24px;
  border-radius: 4px;
}

@media (max-width: 991px) {
  .hero-swiper .swiper-slide img {
    height: 350px;
  }
}

@media (max-width: 767px) {
  .hero-swiper .swiper-slide img {
    height: 280px;
  }

  .hero-swiper .swiper-button-prev,
  .hero-swiper .swiper-button-next {
    display: none;
  }
}

/* ============ OCASIONES PILLS ============ */
.ocasiones-section {
  padding: 48px 0;
}

.ocasiones-pills {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding-bottom: 8px;
  scrollbar-width: none;
  -ms-overflow-style: none;
  justify-content: center;
  flex-wrap: wrap;
}

.ocasiones-pills::-webkit-scrollbar {
  display: none;
}

.ocasion-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 28px;
  background: white;
  border: 1px solid var(--flores-border);
  border-radius: 30px;
  font-size: 15px;
  font-weight: 500;
  color: var(--flores-text);
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s;
}

.ocasion-pill:hover {
  border-color: var(--flores-primary);
  color: var(--flores-primary);
}

.ocasion-pill.active {
  background: var(--flores-primary);
  border-color: var(--flores-primary);
  color: white;
}

/* ============ PRODUCTOS MINIMAL ============ */
.productos-minimal {
  padding: 60px 0;
}

.section-header-minimal {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}

.section-title-minimal {
  font-family: var(--font-serif);
  font-size: 32px;
  font-weight: 500;
  color: var(--flores-text);
  margin: 0;
}

.ver-todos-link {
  font-size: 15px;
  font-weight: 400;
  color: var(--flores-text);
  text-decoration: none;
}

.ver-todos-link:hover {
  color: var(--flores-primary);
}

.product-card-minimal {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.product-card-minimal:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.product-image-minimal {
  position: relative;
  aspect-ratio: 1;
  background: #f8f8f8;
  overflow: hidden;
}

.product-image-minimal img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card-minimal:hover .product-image-minimal img {
  transform: scale(1.05);
}

.badge-entrega-hoy {
  position: absolute;
  top: 12px;
  left: 12px;
  background: var(--flores-primary);
  color: white;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}

.badge-entrega-hoy i {
  font-size: 10px;
}

.btn-favorito {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 36px;
  height: 36px;
  background: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  opacity: 0;
}

.product-card-minimal:hover .btn-favorito {
  opacity: 1;
}

.btn-favorito:hover {
  background: var(--flores-primary);
  color: white;
}

.btn-favorito i {
  font-size: 16px;
}

.product-info-minimal {
  padding: 16px;
}

.product-name-minimal {
  font-size: 14px;
  font-weight: 500;
  color: var(--flores-text);
  margin: 0 0 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price-minimal {
  font-size: 18px;
  font-weight: 600;
  color: var(--flores-text);
  margin: 0 0 12px;
}

.btn-agregar-minimal {
  width: 100%;
  padding: 10px 16px;
  background: transparent;
  border: 1px solid var(--flores-primary);
  border-radius: 8px;
  color: var(--flores-primary);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-agregar-minimal:hover:not(:disabled) {
  background: var(--flores-primary);
  color: white;
}

.btn-agregar-minimal:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-agotado {
  display: block;
  width: 100%;
  padding: 10px 16px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  color: #999;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
}

.empty-state-minimal {
  text-align: center;
  padding: 60px 20px;
  color: var(--flores-text-light);
}

.empty-state-minimal i {
  font-size: 64px;
  opacity: 0.3;
  margin-bottom: 16px;
}

/* ============ BENEFICIOS ============ */
.beneficios-section {
  padding: 48px 0;
  background: #fafafa;
}

.beneficios-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.beneficio-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.beneficio-icon {
  width: 48px;
  height: 48px;
  background: var(--flores-beige);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.beneficio-icon i {
  font-size: 22px;
  color: var(--flores-primary);
}

.beneficio-content h4 {
  font-size: 14px;
  font-weight: 600;
  color: var(--flores-text);
  margin: 0 0 4px;
}

.beneficio-content p {
  font-size: 13px;
  color: var(--flores-text-light);
  margin: 0;
}

/* ============ CTA ARMA TU RAMO ============ */
.cta-arma-ramo {
  padding: 80px 0;
  background: var(--flores-beige);
}

.cta-content {
  max-width: 700px;
  margin: 0 auto;
  text-align: center;
}

.cta-title {
  font-family: var(--font-serif);
  font-size: 36px;
  font-weight: 500;
  color: var(--flores-text);
  margin: 0 0 20px;
}

.cta-description {
  font-size: 16px;
  color: var(--flores-text-light);
  margin: 0 0 32px;
  line-height: 1.7;
}

.btn-cta-ramo {
  display: inline-block;
  padding: 16px 40px;
  background: var(--flores-primary);
  color: white;
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 30px;
  transition: all 0.3s ease;
}

.btn-cta-ramo:hover {
  background: var(--flores-primary-dark, #5a6349);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(107, 116, 86, 0.3);
}

/* ============ RESPONSIVE ============ */
@media (max-width: 991px) {
  .hero-title-minimal {
    font-size: 36px;
  }

  .hero-content-minimal {
    padding-right: 0;
    margin-top: 32px;
  }

  .hero-image-minimal img {
    max-height: 350px;
  }

  .beneficios-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .cta-title {
    font-size: 28px;
  }

  .cta-description {
    font-size: 15px;
  }
}

@media (max-width: 767px) {
  .hero-minimal {
    padding: 40px 0 24px;
  }

  .min-vh-75 {
    min-height: auto;
  }

  .hero-title-minimal {
    font-size: 28px;
    margin-bottom: 24px;
  }

  .hero-selectors {
    flex-direction: row;
  }

  .selector-group {
    flex: 1;
    min-width: 140px;
  }

  .btn-hero-search {
    width: 100%;
    justify-content: center;
  }

  .hero-benefits {
    gap: 24px;
    margin-top: 24px;
  }

  .section-title-minimal {
    font-size: 24px;
  }

  .beneficios-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .cta-arma-ramo {
    padding: 60px 0;
  }

  .cta-title {
    font-size: 24px;
  }

  .cta-description {
    font-size: 14px;
  }

  .btn-cta-ramo {
    padding: 14px 32px;
    font-size: 14px;
  }
}
</style>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    // Cambiar texto de ciudad dinámicamente
    $('#ciudadSelect').on('change', function() {
      const ciudadSeleccionada = $(this).val();
      $('#ciudadTexto').text(ciudadSeleccionada);
    });

    // Inicializar Hero Swiper Carrusel
    if ($('.hero-swiper').length) {
      new Swiper('.hero-swiper', {
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    }

    // Make entire product card clickable
    $('.product-card-minimal').on('click', function(e) {
      if (!$(e.target).closest('button, .btn-favorito, .btn-agregar-minimal').length) {
        window.location.href = $(this).data('href');
      }
    });

    // Quick add to cart
    $('.quick-add-btn').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const btn = $(this);
      const productoId = btn.data('producto-id');
      const precio = btn.data('precio');

      if (!precio) {
        showToast('error', 'Este producto no tiene precio configurado');
        return;
      }

      btn.prop('disabled', true);
      const originalText = btn.text();
      btn.html('<span class="spinner-border spinner-border-sm"></span>');

      $.ajax({
        url: "{{ route('tienda.carrito.agregar') }}",
        method: 'POST',
        data: {
          producto_id: productoId,
          cantidad: 1
        },
        success: function(response) {
          showToast('success', 'Producto agregado al carrito');
          updateCartBadge(response.total_items);
          btn.html('<i class="bi bi-check"></i> Agregado');
          setTimeout(() => {
            btn.prop('disabled', false);
            btn.html(originalText);
          }, 1500);
        },
        error: function(xhr) {
          const error = xhr.responseJSON?.error || 'Error al agregar al carrito';
          showToast('error', error);
          btn.prop('disabled', false);
          btn.html(originalText);
        }
      });
    });

    // Show toast notification
    function showToast(type, message) {
      const toastEl = document.getElementById('cartToast');
      if (!toastEl) return;

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

    // Update cart badge
    function updateCartBadge(count) {
      const badge = $('.header-minimal .header-action-btn .badge');
      if (count > 0) {
        if (badge.length) {
          badge.text(count);
        } else {
          $('a[href*="carrito"]').append('<span class="badge">' + count + '</span>');
        }
      } else {
        badge.remove();
      }
    }
  });
</script>
@endpush