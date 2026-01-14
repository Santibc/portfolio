@extends('tienda.layout')

@section('title', $empresa->nombre . ' - Flores frescas entregadas hoy')
@section('nav-inicio', 'active')

@section('content')
    <!-- Hero Section - Fondo Blanco Minimalista -->
    <section class="hero-white">
        <div class="container">
            <div class="hero-white-content">
                <h1 class="hero-white-title">
                    Flores frescas para<br>cada momento
                </h1>
                <p class="hero-white-subtitle">
                    Entrega el mismo día según disponibilidad
                </p>
                <a href="{{ route('tienda.categorias') }}" class="btn-comprar-ahora">
                    Comprar ahora
                </a>
            </div>
        </div>
    </section>

    <!-- Selector de Ubicación -->
    <section class="ubicacion-section">
        <div class="container">
            <div class="ubicacion-box">
                <h3 class="ubicacion-title">¿Dónde deseas enviar?</h3>

                @php
                    $ubicacionSeleccionada = session('ubicacion_seleccionada');
                    $zonaSeleccionada = null;
                    if ($ubicacionSeleccionada && isset($ubicacionSeleccionada['zona_id'])) {
                        $zonaSeleccionada = \App\Models\ZonaCobertura::find($ubicacionSeleccionada['zona_id']);
                    }
                @endphp

                <div class="ubicacion-selector-row">
                    <div class="ubicacion-input-wrapper">
                        <i class="bi bi-geo-alt"></i>
                        @if($zonaSeleccionada)
                            <span class="ubicacion-selected">{{ $zonaSeleccionada->nombre }}</span>
                        @else
                            <span class="ubicacion-placeholder">Comuna / Ciudad</span>
                        @endif
                    </div>
                    <button type="button" class="btn-ver-opciones" data-bs-toggle="modal" data-bs-target="#selectorUbicacionModal">
                        @if($zonaSeleccionada)
                            Cambiar
                        @else
                            Ver opciones
                        @endif
                    </button>
                </div>

                <p class="ubicacion-help">Para mostrar disponibilidad y tarifas de despacho</p>

                @if($zonaSeleccionada)
                    <button type="button" class="btn-limpiar-ubicacion" onclick="limpiarUbicacion()">
                        <i class="bi bi-x-circle"></i> Ver todas las zonas
                    </button>
                @endif
            </div>
        </div>
    </section>

    <!-- Compra por Ocasión -->
    @if(isset($categoriasOcasiones) && $categoriasOcasiones->count() > 0)
    <section class="ocasiones-section">
        <div class="container">
            <h2 class="section-title-center">Compra por ocasión</h2>

            <div class="ocasiones-grid">
                @foreach($categoriasOcasiones as $ocasion)
                    <a href="{{ route('tienda.categorias', ['categoria' => $ocasion->id]) }}" class="ocasion-card-simple">
                        <div class="ocasion-card-image">
                            @if($ocasion->imagen)
                                <img src="{{ asset($ocasion->imagen) }}" alt="{{ $ocasion->nombre }}">
                            @else
                                <i class="bi bi-flower1"></i>
                            @endif
                        </div>
                        <span class="ocasion-card-name">{{ $ocasion->nombre }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Explora por Formato -->
    @if(isset($categoriasFormatos) && $categoriasFormatos->count() > 0)
    <section class="formatos-section">
        <div class="container">
            <h2 class="section-title-center">Explora por formato</h2>

            <div class="formatos-grid">
                @foreach($categoriasFormatos as $formato)
                    <a href="{{ route('tienda.categorias', ['categoria' => $formato->id]) }}" class="formato-card-simple">
                        <div class="formato-card-image">
                            @if($formato->imagen)
                                <img src="{{ asset($formato->imagen) }}" alt="{{ $formato->nombre }}">
                            @else
                                <i class="bi bi-box"></i>
                            @endif
                        </div>
                        <span class="formato-card-name">{{ $formato->nombre }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Arma tu Ramo CTA -->
    <section class="arma-ramo-section">
        <div class="container">
            <div class="arma-ramo-card">
                <div class="arma-ramo-content-left">
                <h2 class="arma-ramo-title">Arma tu ramo</h2>
                <p class="arma-ramo-desc">
                    Crea un arreglo único y personalizado
                </p>
                <a href="{{ route('arma-tu-ramo') }}" class="btn-crear-ramo">
                    Crear mi ramo
                </a>
            </div>
            <div class="arma-ramo-image-right">
                @if(file_exists(public_path('imagenes/arma-tu-ramo-hero.jpg')))
                    <img src="{{ asset('imagenes/arma-tu-ramo-hero.jpg') }}" alt="Arma tu ramo">
                @else
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 64px; color: #cccccc;">
                        <i class="bi bi-flower1"></i>
                    </div>
                @endif
            </div>
        </div>
        </div>
    </section>

    <!-- Modal Selector de Ubicación -->
    @include('components.selector-ubicacion-modal')

@endsection

@push('styles')
<style>
/* ============ HERO WHITE SECTION ============ */
.hero-white {
    background: #ffffff;
    padding: 120px 0 100px;
    border-bottom: 1px solid #f0f0f0;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.hero-white-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.hero-white-title {
    font-family: var(--font-serif);
    font-size: 72px;
    font-weight: 400;
    color: #000000;
    line-height: 1.1;
    margin: 0 0 28px;
    letter-spacing: -2px;
}

.hero-white-subtitle {
    font-size: 18px;
    color: #666666;
    margin: 0 0 40px;
    font-weight: 400;
}

.btn-comprar-ahora {
    display: inline-block;
    padding: 16px 40px;
    background: #000000;
    color: #ffffff;
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.btn-comprar-ahora:hover {
    background: #333333;
    color: #ffffff;
}

/* ============ UBICACION SECTION ============ */
.ubicacion-section {
    padding: 60px 0;
    background: #f7f7f7;
    border-bottom: 1px solid #eeeeee;
}

.ubicacion-box {
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
}

.ubicacion-title {
    font-size: 18px;
    font-weight: 500;
    color: #000000;
    margin: 0 0 20px;
}

.ubicacion-selector-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.ubicacion-input-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background: #ffffff;
}

.ubicacion-input-wrapper i {
    color: #999999;
    font-size: 18px;
}

.ubicacion-placeholder {
    color: #999999;
    font-size: 14px;
}

.ubicacion-selected {
    color: #000000;
    font-size: 14px;
    font-weight: 500;
}

.btn-ver-opciones {
    padding: 12px 24px;
    background: #000000;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s ease;
    white-space: nowrap;
}

.btn-ver-opciones:hover {
    background: #333333;
}

.ubicacion-help {
    font-size: 13px;
    color: #999999;
    margin: 0;
}

.btn-limpiar-ubicacion {
    margin-top: 12px;
    background: none;
    border: none;
    color: #666666;
    font-size: 13px;
    cursor: pointer;
    padding: 4px 8px;
}

.btn-limpiar-ubicacion:hover {
    color: #000000;
}

/* ============ SECTION TITLE ============ */
.section-title-center {
    font-family: var(--font-serif);
    font-size: 28px;
    font-weight: 400;
    color: #000000;
    text-align: center;
    margin: 0 0 40px;
}

/* ============ OCASIONES SECTION ============ */
.ocasiones-section {
    padding: 60px 0;
    background: #ffffff;
    border-bottom: 1px solid #f0f0f0;
}

.ocasiones-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
}

.ocasion-card-simple {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: transform 0.2s ease;
}

.ocasion-card-simple:hover {
    transform: translateY(-4px);
}

.ocasion-card-image {
    width: 100%;
    aspect-ratio: 1;
    background: #f5f5f5;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    transition: border-color 0.2s ease;
}

.ocasion-card-simple:hover .ocasion-card-image {
    border-color: #cccccc;
}

.ocasion-card-image i {
    font-size: 32px;
    color: #cccccc;
}

.ocasion-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.ocasion-card-name {
    font-size: 14px;
    color: #000000;
    text-align: center;
    font-weight: 400;
}

/* ============ FORMATOS SECTION ============ */
.formatos-section {
    padding: 60px 0;
    background: #ffffff;
    border-bottom: 1px solid #f0f0f0;
}

.formatos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.formato-card-simple {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: transform 0.2s ease;
}

.formato-card-simple:hover {
    transform: translateY(-4px);
}

.formato-card-image {
    width: 100%;
    aspect-ratio: 4/3;
    background: #f5f5f5;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    transition: border-color 0.2s ease;
}

.formato-card-simple:hover .formato-card-image {
    border-color: #cccccc;
}

.formato-card-image i {
    font-size: 40px;
    color: #cccccc;
}

.formato-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.formato-card-name {
    font-size: 14px;
    color: #000000;
    text-align: center;
    font-weight: 400;
}

/* ============ ARMA TU RAMO SECTION ============ */
.arma-ramo-section {
    padding: 80px 0;
    background: #ffffff;
}

.arma-ramo-card {
    display: flex;
    align-items: stretch;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    min-height: 500px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.arma-ramo-content-left {
    flex: 1;
    background: #1a1a1a;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 80px 60px;
}

.arma-ramo-image-right {
    flex: 1;
    background: #FFF8E7;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    overflow: hidden;
}

.arma-ramo-image-right img {
    width: 100%;
    height: 100%;
    min-height: 500px;
    object-fit: cover;
    object-position: center;
}

.arma-ramo-title {
    font-family: var(--font-serif);
    font-size: 48px;
    font-weight: 400;
    color: #ffffff;
    margin: 0 0 16px;
    line-height: 1.2;
}

.arma-ramo-desc {
    font-size: 18px;
    color: #cccccc;
    line-height: 1.6;
    margin: 0 0 32px;
}

.btn-crear-ramo {
    display: inline-block;
    padding: 16px 40px;
    background: #ffffff;
    color: #1a1a1a;
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s ease;
    align-self: flex-start;
}

.btn-crear-ramo:hover {
    background: #f0f0f0;
    color: #000000;
    transform: translateY(-2px);
}

/* ============ RESPONSIVE ============ */
@media (max-width: 1199px) {
    .ocasiones-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 991px) {
    .hero-white {
        padding: 80px 0 70px;
        min-height: 50vh;
    }

    .hero-white-title {
        font-size: 56px;
    }

    .ocasiones-section,
    .formatos-section,
    .section-title-center {
        font-size: 24px;
        margin-bottom: 32px;
    }

    .ocasiones-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .formatos-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .arma-ramo-card {
        flex-direction: column;
        min-height: auto;
    }

    .arma-ramo-content-left {
        padding: 60px 40px;
        text-align: center;
        align-items: center;
    }

    .arma-ramo-image-right {
        padding: 40px 20px;
    }

    .arma-ramo-title {
        font-size: 36px;
    }

    .arma-ramo-desc {
        font-size: 16px;
    }

    .btn-crear-ramo {
        align-self: center;
    }
}

@media (max-width: 767px) {
    .hero-white {
        padding: 60px 0 50px;
        min-height: 45vh;
    }

    .hero-white-title {
        font-size: 42px;
        line-height: 1.15;
    }

    .hero-white-subtitle {
        font-size: 16px;
        margin-bottom: 28px;
    }

    .btn-comprar-ahora {
        padding: 14px 32px;
        font-size: 14px;
    }

    .ubicacion-section {
        padding: 32px 0;
    }

    .ubicacion-title {
        font-size: 16px;
    }

    .ubicacion-selector-row {
        flex-direction: column;
        gap: 12px;
    }

    .ubicacion-input-wrapper {
        width: 100%;
    }

    .btn-ver-opciones {
        width: 100%;
    }

    .ocasiones-section,
    .formatos-section,
    .arma-ramo-section {
        padding: 40px 0;
    }

    .section-title-center {
        font-size: 22px;
        margin-bottom: 28px;
    }

    .ocasiones-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .ocasion-card-name,
    .formato-card-name {
        font-size: 13px;
    }

    .ocasion-card-image i,
    .formato-card-image i {
        font-size: 24px;
    }

    .arma-ramo-title {
        font-size: 24px;
    }

    .arma-ramo-desc {
        font-size: 14px;
    }

    .btn-crear-ramo {
        padding: 12px 24px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .hero-white {
        padding: 50px 0 40px;
        min-height: 40vh;
    }

    .hero-white-title {
        font-size: 34px;
    }

    .hero-white-subtitle {
        font-size: 14px;
    }

    .ocasiones-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .formatos-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .ocasion-card-image,
    .formato-card-image {
        border-radius: 6px;
    }

    .arma-ramo-image {
        aspect-ratio: 16/9;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // El script del modal de ubicación se carga desde el componente
});

// Función global para limpiar ubicación
function limpiarUbicacion() {
    $.ajax({
        url: '/tienda/limpiar-ubicacion',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            }
        }
    });
}
</script>
@endpush
