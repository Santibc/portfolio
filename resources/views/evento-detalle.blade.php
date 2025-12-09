@extends('layouts.public')

@section('title', $evento->titulo . ' - Betogether')
@section('title-suffix', ' | Evento')
@section('body-id', 'evento-detalle-body')

@section('content')
{{-- Hero del Evento --}}
<section class="evento-detalle-hero" style="background-image: linear-gradient(rgba(28, 28, 46, 0.85), rgba(45, 31, 78, 0.9)), url('{{ asset($evento->imagen) }}');">
    <div class="evento-detalle-hero-content">
        <span class="evento-detalle-estado {{ $evento->estado }}">{{ $evento->estadoTexto }}</span>
        <h1 class="evento-detalle-title">{{ $evento->titulo }}</h1>
        <div class="evento-detalle-meta">
            @if($evento->fecha)
                <span><i class="bi bi-calendar-event"></i> {{ $evento->fecha }}</span>
            @endif
            @if($evento->hora)
                <span><i class="bi bi-clock"></i> {{ $evento->hora }}</span>
            @endif
            @if($evento->ubicacion)
                <span><i class="bi bi-geo-alt"></i> {{ $evento->ubicacion }}</span>
            @endif
        </div>
    </div>
</section>

{{-- Contenido del Evento --}}
<section class="evento-detalle-content">
    <div class="evento-detalle-container">
        <div class="evento-detalle-main">
            {{-- Descripción --}}
            <div class="evento-detalle-section">
                <h2><i class="bi bi-info-circle"></i> Acerca del Evento</h2>
                <div class="evento-descripcion">
                    {!! $evento->contenido !!}
                </div>
            </div>

            {{-- Qué incluye --}}
            @if($evento->incluye && count($evento->incluye) > 0)
            <div class="evento-detalle-section">
                <h2><i class="bi bi-check-circle"></i> ¿Qué incluye?</h2>
                <ul class="evento-incluye-list">
                    @foreach($evento->incluye as $item)
                    <li><i class="bi bi-check-lg"></i> {{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Galería --}}
            @if($evento->galeria && count($evento->galeria) > 0)
            <div class="evento-detalle-section">
                <h2><i class="bi bi-images"></i> Galería</h2>
                <div class="evento-galeria">
                    @foreach($evento->galeria as $imagen)
                    <div class="evento-galeria-item">
                        <img src="{{ asset($imagen) }}" alt="Imagen del evento">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="evento-detalle-sidebar">
            <div class="evento-info-card">
                <h3>Información del Evento</h3>

                @if($evento->fecha)
                <div class="evento-info-item">
                    <i class="bi bi-calendar-event"></i>
                    <div>
                        <span class="label">Fecha</span>
                        <span class="value">{{ $evento->fecha }}</span>
                    </div>
                </div>
                @endif

                @if($evento->hora)
                <div class="evento-info-item">
                    <i class="bi bi-clock"></i>
                    <div>
                        <span class="label">Hora</span>
                        <span class="value">{{ $evento->hora }}</span>
                    </div>
                </div>
                @endif

                @if($evento->ubicacion)
                <div class="evento-info-item">
                    <i class="bi bi-geo-alt"></i>
                    <div>
                        <span class="label">Ubicación</span>
                        <span class="value">{{ $evento->ubicacion }}</span>
                    </div>
                </div>
                @endif

                @if($evento->precio)
                <div class="evento-info-item">
                    <i class="bi bi-tag"></i>
                    <div>
                        <span class="label">Precio</span>
                        <span class="value precio">{{ $evento->precio }}</span>
                    </div>
                </div>
                @endif

                @if($evento->estado !== 'finalizado')
                <a href="{{ route('contacto') }}" class="evento-cta-btn">
                    <i class="bi bi-person-plus"></i>
                    Quiero participar
                </a>
                @endif
            </div>

            {{-- Compartir --}}
            <div class="evento-compartir-card">
                <h4>Compartir evento</h4>
                <div class="evento-compartir-links">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="share-btn facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($evento->titulo) }}" target="_blank" class="share-btn twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($evento->titulo . ' - ' . request()->url()) }}" target="_blank" class="share-btn whatsapp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($evento->titulo) }}" target="_blank" class="share-btn linkedin">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</section>

{{-- Navegación --}}
<section class="evento-navegacion">
    <a href="{{ route('eventos') }}" class="evento-volver">
        <i class="bi bi-arrow-left"></i>
        Volver a todos los eventos
    </a>
</section>
@endsection

@push('styles')
<style>
/* Hero del Evento */
.evento-detalle-hero {
    background-size: cover;
    background-position: center;
    padding: 100px 20px 80px;
    text-align: center;
    position: relative;
}

.evento-detalle-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.evento-detalle-estado {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.evento-detalle-estado.proximo {
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
}

.evento-detalle-estado.activo {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
}

.evento-detalle-estado.finalizado {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.evento-detalle-title {
    color: white;
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 25px;
    line-height: 1.2;
}

.evento-detalle-meta {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 25px;
}

.evento-detalle-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
}

.evento-detalle-meta i {
    color: #ff00c8;
    font-size: 1.1rem;
}

/* Contenido */
.evento-detalle-content {
    padding: 60px 20px;
    background: #f8f9fa;
}

.evento-detalle-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
}

.evento-detalle-main {
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.evento-detalle-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.evento-detalle-section h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.evento-detalle-section h2 i {
    color: #ff00c8;
}

.evento-descripcion {
    color: #555;
    font-size: 1rem;
    line-height: 1.8;
}

.evento-descripcion p {
    margin-bottom: 15px;
}

.evento-descripcion p:last-child {
    margin-bottom: 0;
}

/* Lista incluye */
.evento-incluye-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.evento-incluye-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 10px;
    font-size: 0.95rem;
    color: #444;
}

.evento-incluye-list li i {
    color: #25D366;
    font-size: 1.1rem;
}

/* Galería */
.evento-galeria {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.evento-galeria-item {
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 4/3;
}

.evento-galeria-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.evento-galeria-item:hover img {
    transform: scale(1.05);
}

/* Sidebar */
.evento-detalle-sidebar {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.evento-info-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.evento-info-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.evento-info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.evento-info-item:last-of-type {
    border-bottom: none;
}

.evento-info-item > i {
    color: #ff00c8;
    font-size: 1.2rem;
    margin-top: 2px;
}

.evento-info-item div {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.evento-info-item .label {
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.evento-info-item .value {
    font-size: 0.95rem;
    color: #333;
    font-weight: 500;
}

.evento-info-item .value.precio {
    color: #ff00c8;
    font-weight: 700;
    font-size: 1.1rem;
}

.evento-cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.evento-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 200, 0.4);
    color: white;
}

/* Compartir */
.evento-compartir-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.evento-compartir-card h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

.evento-compartir-links {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.share-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    color: white;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.share-btn:hover {
    transform: translateY(-3px);
    color: white;
}

.share-btn.facebook {
    background: #1877f2;
}

.share-btn.twitter {
    background: #000;
}

.share-btn.whatsapp {
    background: #25D366;
}

.share-btn.linkedin {
    background: #0077b5;
}

/* Navegación */
.evento-navegacion {
    padding: 30px 20px 60px;
    background: #f8f9fa;
}

.evento-volver {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ff00c8;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    max-width: 1200px;
    margin: 0 auto;
}

.evento-volver:hover {
    color: #7000ff;
    gap: 12px;
}

/* Responsive */
@media (max-width: 968px) {
    .evento-detalle-container {
        grid-template-columns: 1fr;
    }

    .evento-detalle-sidebar {
        order: -1;
    }

    .evento-detalle-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .evento-detalle-title {
        font-size: 1.6rem;
    }

    .evento-detalle-meta {
        flex-direction: column;
        gap: 15px;
    }

    .evento-incluye-list {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
