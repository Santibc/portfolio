@extends('layouts.public')

@section('title', 'Eventos - Betogether')
@section('title-suffix', ' | Eventos')
@section('body-id', 'eventos-body')

@section('content')
{{-- Hero Section --}}
<section class="eventos-hero">
    <div class="eventos-hero-content">
        <p class="eventos-subtitle">{{ $pagina->contenido['hero_subtitle'] ?? 'NUESTROS EVENTOS' }}</p>
        <h1 class="eventos-title">{{ $pagina->contenido['hero_title'] ?? 'Festivales y Encuentros' }}</h1>
        <p class="eventos-description">
            {!! $pagina->contenido['hero_description'] ?? 'Participa en nuestros eventos exclusivos para emprendedores y fundaciones. <strong>Conecta, vende y haz crecer tu negocio.</strong>' !!}
        </p>
    </div>
</section>

{{-- Eventos Grid Section --}}
<section class="eventos-section">
    <div class="eventos-container">
        <div class="eventos-grid">
            @foreach($eventos as $evento)
            <article class="evento-card">
                <div class="evento-card-image">
                    <img src="{{ asset($evento->imagen) }}" alt="{{ $evento->titulo }}">
                    <span class="evento-estado {{ $evento->estado }}">{{ $evento->estadoTexto }}</span>
                </div>
                <div class="evento-card-content">
                    <div class="evento-meta">
                        @if($evento->fecha)
                            <span class="evento-fecha"><i class="bi bi-calendar-event"></i> {{ $evento->fecha }}</span>
                        @endif
                        @if($evento->ubicacion)
                            <span class="evento-ubicacion"><i class="bi bi-geo-alt"></i> {{ $evento->ubicacion }}</span>
                        @endif
                    </div>
                    <h2 class="evento-card-title">{{ $evento->titulo }}</h2>
                    <p class="evento-card-excerpt">{{ $evento->extracto }}</p>
                    <a href="{{ route('eventos.show', $evento->slug) }}" class="evento-ver-mas">
                        Ver detalles
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="eventos-cta-section">
    <div class="eventos-cta-content">
        <h2>¿Quieres participar en nuestros eventos?</h2>
        <p>Regístrate y sé parte de la comunidad de emprendedores más grande de Colombia</p>
        <a href="{{ route('contacto') }}" class="eventos-cta-btn">
            <i class="bi bi-person-plus"></i>
            Contáctanos
        </a>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Eventos Hero */
.eventos-hero {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 50%, #1c1c2e 100%);
    padding: 80px 20px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.eventos-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.eventos-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.eventos-subtitle {
    color: #ff00c8;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 3px;
    margin-bottom: 15px;
    text-transform: uppercase;
}

.eventos-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.eventos-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.eventos-description strong {
    color: white;
}

/* Eventos Section */
.eventos-section {
    padding: 60px 20px;
    background: #f8f9fa;
}

.eventos-container {
    max-width: 1200px;
    margin: 0 auto;
}

.eventos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 30px;
}

/* Evento Card */
.evento-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.evento-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.evento-card-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.evento-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.evento-card:hover .evento-card-image img {
    transform: scale(1.1);
}

.evento-estado {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.evento-estado.proximo {
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
}

.evento-estado.activo {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
}

.evento-estado.finalizado {
    background: rgba(0, 0, 0, 0.6);
    color: white;
}

.evento-card-content {
    padding: 25px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.evento-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.evento-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #888;
}

.evento-meta i {
    font-size: 0.9rem;
    color: #ff00c8;
}

.evento-card-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.evento-card-excerpt {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.evento-ver-mas {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ff00c8;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.evento-ver-mas:hover {
    color: #7000ff;
    gap: 12px;
}

.evento-ver-mas i {
    transition: transform 0.3s ease;
}

.evento-ver-mas:hover i {
    transform: translateX(5px);
}

/* Eventos CTA Section */
.eventos-cta-section {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 100%);
    padding: 60px 20px;
    text-align: center;
}

.eventos-cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.eventos-cta-content h2 {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.eventos-cta-content p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin-bottom: 25px;
}

.eventos-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 35px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.eventos-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 200, 0.4);
    color: white;
}

.eventos-cta-btn i {
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .eventos-title {
        font-size: 1.8rem;
    }

    .eventos-grid {
        grid-template-columns: 1fr;
    }

    .evento-card-image {
        height: 200px;
    }
}
</style>
@endpush
