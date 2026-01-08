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
                <span class="ciudad-nombre" id="ciudadTexto">Chile</span>
              </h1>

              <a href="{{ route('tienda.categorias') }}" class="btn-flores btn-hero-search">
                Ver ramos disponibles hoy
              </a>

              <!-- Hero Benefits -->
              <div class="hero-benefits">
                @php
                  // Beneficios hero basados en info de envío gratis (desde tarifa)
                  $heroBenefits = [];

                  // Priorizar info de envío gratis desde la tarifa de logística
                  if ($infoEnvioGratis && $infoEnvioGratis['activo']) {
                    $heroBenefits[] = [
                      'title' => $infoEnvioGratis['nombre'],
                      'desc' => $infoEnvioGratis['descripcion']
                    ];
                  }

                  // Beneficio por defecto de frescura siempre se muestra
                  $heroBenefits[] = [
                    'title' => 'Flores frescas',
                    'desc' => 'Garantía de 7 días'
                  ];

                  // Si no hay envío gratis configurado, mostrar beneficio genérico
                  if (count($heroBenefits) === 1) {
                    array_unshift($heroBenefits, [
                      'title' => 'Entrega rápida',
                      'desc' => 'Servicio eficiente'
                    ]);
                  }
                @endphp

                @foreach(array_slice($heroBenefits, 0, 2) as $benefit)
                <div class="hero-benefit-item">
                  <span class="benefit-title">{{ $benefit['title'] }}</span>
                  <span class="benefit-desc">{{ $benefit['desc'] }}</span>
                </div>
                @endforeach
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

    <!-- Selector de Ubicación -->
    <section class="ubicacion-section py-5 mt-4" style="background-color: #f8f9fa;">
      <div class="container">
        @include('components.selector-ubicacion')
      </div>
    </section>

    <!-- Filtros por Ocasión -->
    <section class="ocasiones-section py-4" style="background-color: #ffffff;">
      <div class="container">
        <div class="ocasiones-pills d-flex justify-content-center flex-wrap gap-2">
          @foreach($categoriasMenu ?? [] as $index => $categoria)
            <a href="{{ route('tienda.categorias', ['categoria' => $categoria->id]) }}" class="ocasion-pill {{ $index === 0 ? 'active' : '' }}">
              {{ $categoria->nombre }}
            </a>
          @endforeach
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

                {{-- Badge de disponibilidad geográfica --}}
                @php
                    $ubicacionSel = session('ubicacion_seleccionada');
                    $zonaSel = null;
                    if ($ubicacionSel && isset($ubicacionSel['zona_id'])) {
                        $zonaSel = \App\Models\ZonaCobertura::find($ubicacionSel['zona_id']);
                    }
                @endphp
                @if($zonaSel)
                  <span class="badge-disponibilidad-zona bg-success">
                    <i class="bi bi-check-circle-fill"></i> {{ $zonaSel->nombre }}
                  </span>
                @endif

                <img src="{{ $producto->url_imagen_principal }}" alt="{{ $producto->nombre }}" loading="lazy">
                {{-- Botón de favoritos deshabilitado temporalmente
                <button class="btn-favorito" type="button" title="Agregar a favoritos">
                  <i class="bi bi-heart"></i>
                </button>
                --}}
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
          @php
            // Beneficios dinámicos basados en descuentos activos y envío gratis
            $beneficios = [];

            // Primero agregar envío gratis si está configurado en la tarifa
            if ($infoEnvioGratis && $infoEnvioGratis['activo']) {
              $beneficios[] = [
                'icon' => 'bi-truck',
                'titulo' => $infoEnvioGratis['nombre'],
                'descripcion' => $infoEnvioGratis['descripcion']
              ];
            }

            // Luego agregar descuentos activos (excluyendo descuentos de envío que ya no se usan)
            foreach($descuentosActivos as $descuento) {
              $codigo = strtoupper($descuento->codigo);

              // Saltar descuentos de envío porque ahora se manejan desde la tarifa
              if (str_contains($codigo, 'ENVIO') || str_contains($codigo, 'SHIPPING')) {
                continue;
              }

              $icon = 'bi-percent'; // Icono por defecto
              $titulo = $descuento->nombre;
              $descripcion = $descuento->descripcion ?? '';

              // Asignar iconos inteligentemente
              if (str_contains($codigo, 'NAVIDAD') || str_contains($codigo, 'CHRISTMAS')) {
                $icon = 'bi-gift';
                if (!$descripcion) $descripcion = 'Oferta especial de temporada';
              } elseif ($descuento->tipo === 'porcentaje') {
                $icon = 'bi-percent';
                if (!$descripcion) $descripcion = $descuento->valor . '% de descuento';
              } elseif ($descuento->tipo === 'monto_fijo') {
                $icon = 'bi-tag';
                if (!$descripcion) $descripcion = '$' . number_format($descuento->valor, 0, ',', '.') . ' de descuento';
              }

              // Agregar requisito de monto mínimo si aplica
              if ($descuento->monto_minimo_compra && !$descripcion) {
                $descripcion = 'En compras sobre $' . number_format($descuento->monto_minimo_compra, 0, ',', '.');
              }

              $beneficios[] = [
                'icon' => $icon,
                'titulo' => $titulo,
                'descripcion' => $descripcion
              ];
            }

            // Beneficios por defecto si no hay suficientes descuentos
            $beneficiosPorDefecto = [
              ['icon' => 'bi-shield-check', 'titulo' => 'Garantía de frescura', 'descripcion' => 'Flores 100% frescas'],
              ['icon' => 'bi-headset', 'titulo' => 'Atención personalizada', 'descripcion' => 'Soporte por WhatsApp'],
              ['icon' => 'bi-clock', 'titulo' => 'Entrega rápida', 'descripcion' => 'Servicio eficiente'],
              ['icon' => 'bi-heart', 'titulo' => 'Con amor', 'descripcion' => 'Arreglos únicos y especiales']
            ];

            // Combinar descuentos activos con beneficios por defecto (máximo 4)
            while (count($beneficios) < 4 && count($beneficiosPorDefecto) > 0) {
              $beneficios[] = array_shift($beneficiosPorDefecto);
            }

            // Limitar a 4 beneficios
            $beneficios = array_slice($beneficios, 0, 4);
          @endphp

          @foreach($beneficios as $beneficio)
          <div class="beneficio-item">
            <div class="beneficio-icon">
              <i class="bi {{ $beneficio['icon'] }}"></i>
            </div>
            <div class="beneficio-content">
              <h4>{{ $beneficio['titulo'] }}</h4>
              <p>{{ $beneficio['descripcion'] }}</p>
            </div>
          </div>
          @endforeach
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
  padding: 60px 0 0;
  background: var(--flores-bg);
  margin-bottom: 0;
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
  box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
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
  margin-top: 0;
  overflow: visible;
}

.ocasiones-pills {
  display: flex;
  gap: 12px;
  overflow: visible;
  padding: 8px 0;
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
  padding: 10px 24px;
  background: white;
  border: 2px solid #e9ecef;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 500;
  color: #495057;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
}

.ocasion-pill:hover {
  border-color: #1a1a1a;
  color: #1a1a1a;
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
  z-index: 2;
}

.ocasion-pill.active {
  background: #1a1a1a;
  border-color: #1a1a1a;
  color: white;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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

.badge-disponibilidad-zona {
  position: absolute;
  top: 42px;
  left: 12px;
  background: #1a1a1a !important;
  color: white;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
  z-index: 2;
}

.badge-disponibilidad-zona i {
  font-size: 9px;
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
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  line-height: 1.5;
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
  background: var(--flores-primary-dark, #333333);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(26, 26, 26, 0.3);
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

  .beneficio-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .beneficio-content {
    text-align: center;
  }

  .beneficio-content h4,
  .beneficio-content p {
    text-align: center;
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

@media (max-width: 576px) {
  .hero-swiper .swiper-slide img {
    height: 220px;
  }

  .hero-minimal {
    padding: 24px 0;
  }

  .hero-content-minimal {
    text-align: center;
    max-width: 100%;
    padding: 0 10px;
  }

  .hero-title-minimal {
    font-size: 26px;
    line-height: 1.4;
    margin-bottom: 24px;
    text-align: center;
  }

  .btn-hero-search {
    width: 100%;
    max-width: 320px;
    margin: 0 auto;
    height: 48px;
    font-size: 14px;
    padding: 0 24px;
  }

  .hero-benefits {
    flex-direction: row;
    justify-content: center;
    gap: 32px;
    margin-top: 28px;
    align-items: flex-start;
  }

  .hero-benefit-item {
    text-align: center;
    align-items: center;
    flex: 0 1 auto;
  }

  .hero-benefit-item .benefit-title {
    font-size: 14px;
    text-align: center;
  }

  .hero-benefit-item .benefit-desc {
    font-size: 12px;
    text-align: center;
  }

  .ocasiones-section {
    padding: 32px 0;
  }

  .ocasiones-pills {
    justify-content: flex-start;
    flex-wrap: nowrap;
    padding-bottom: 12px;
  }

  .ocasion-pill {
    padding: 10px 20px;
    font-size: 14px;
    flex-shrink: 0;
  }

  .productos-minimal {
    padding: 40px 0;
  }

  .section-header-minimal {
    margin-bottom: 28px;
  }

  .section-title-minimal {
    font-size: 20px;
  }

  .ver-todos-link {
    font-size: 14px;
  }

  .product-name-minimal {
    font-size: 13px;
  }

  .product-price-minimal {
    font-size: 16px;
  }

  .btn-agregar-minimal,
  .btn-agotado {
    padding: 8px 12px;
    font-size: 13px;
  }

  .badge-entrega-hoy {
    font-size: 10px;
    padding: 3px 8px;
  }

  .beneficios-section {
    padding: 36px 0;
  }

  .beneficio-item {
    gap: 12px;
  }

  .beneficio-icon {
    width: 44px;
    height: 44px;
  }

  .beneficio-icon i {
    font-size: 20px;
  }

  .beneficio-content h4 {
    font-size: 13px;
  }

  .beneficio-content p {
    font-size: 12px;
  }

  .cta-arma-ramo {
    padding: 48px 0;
  }

  .cta-content {
    padding: 0 16px;
  }

  .cta-title {
    font-size: 20px;
  }

  .cta-description {
    font-size: 13px;
    margin-bottom: 24px;
  }

  .btn-cta-ramo {
    padding: 12px 28px;
    font-size: 13px;
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