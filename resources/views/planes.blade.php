@extends('layouts.public')

@section('title', 'Planes - Betogether')
@section('title-suffix', ' | Planes')
@section('body-id', 'planes-body')

@section('content')
{{-- Hero Section --}}
<section class="planes-hero">
    <div class="planes-hero-content">
        <p class="planes-subtitle">{{ $pagina->contenido['hero_subtitle'] ?? 'NUESTROS PLANES' }}</p>
        <h1 class="planes-title">{{ $pagina->contenido['hero_title'] ?? 'Elige el plan perfecto para tu negocio' }}</h1>
        <p class="planes-description">
            {!! $pagina->contenido['hero_description'] ?? 'Desde emprendedores que inician hasta marcas consolidadas. <strong>Todos incluyen tienda online y pasarela de pagos.</strong>' !!}
        </p>
    </div>
</section>

{{-- Pricing Section --}}
<section class="pricing-section">
    <div class="pricing-container">
        <div class="plans-grid">
            @foreach($planes as $index => $plan)
            <div class="plan-card @if($index == 1) featured @endif">
                @if($index == 1)
                <div class="popular-badge">
                    <span>Más Popular</span>
                </div>
                @endif

                <h3 class="plan-name">{{ $plan->nombre }}</h3>
                <div class="plan-price {{ $plan->precio == 0 ? 'free' : '' }}">
                    @if($plan->precio == 0)
                    GRATIS
                    @else
                    ${{ number_format($plan->precio, 0) }}<small>/mes</small>
                    @endif
                </div>
                @if($plan->limite_transacciones)
                <p class="plan-limit">Hasta {{ $plan->limite_transacciones }} transacciones/mes</p>
                @endif

                <ul class="plan-features">
                    @if($plan->caracteristicas)
                        @foreach($plan->caracteristicas as $caracteristica)
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ $caracteristica }}</span>
                        </li>
                        @endforeach
                    @endif
                </ul>

                <div class="commission-box">
                    <strong>{{ $plan->porcentaje_comision }}% + ${{ number_format($plan->comision_fija, 0) }}</strong>
                    <span class="commission-label">Por transacción exitosa</span>
                </div>

                @auth
                    <a href="{{ route('membresias.index') }}" class="plan-cta">
                        Ver Planes
                    </a>
                @else
                    <a href="{{ route('register') }}" class="plan-cta">
                        {{ $plan->precio == 0 ? 'Empezar Gratis' : 'Elegir Plan' }}
                    </a>
                @endauth
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="planes-cta-section">
    <div class="planes-cta-content">
        <h2>¿Tienes dudas sobre qué plan elegir?</h2>
        <p>Contáctanos y te ayudamos a encontrar el plan ideal para tu negocio</p>
        <a href="{{ route('contacto') }}" class="planes-cta-btn">
            <i class="bi bi-chat-dots"></i>
            Contáctanos
        </a>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Planes Hero */
.planes-hero {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 50%, #1c1c2e 100%);
    padding: 80px 20px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.planes-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.planes-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.planes-subtitle {
    color: #ff00c8;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 3px;
    margin-bottom: 15px;
    text-transform: uppercase;
}

.planes-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.planes-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.planes-description strong {
    color: white;
}

/* Pricing Section Override */
.pricing-section {
    padding: 60px 20px;
    background: #f8f9fa;
}

.pricing-container {
    max-width: 1200px;
    margin: 0 auto;
}

.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.plan-card {
    background: white;
    border-radius: 20px;
    padding: 30px 25px;
    border: 2px solid #eee;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 520px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.plan-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border-color: #ff00c8;
}

.plan-card.featured {
    border-color: #ff00c8;
    transform: scale(1.02);
    box-shadow: 0 10px 40px rgba(255, 0, 200, 0.15);
}

.plan-card.featured:hover {
    transform: scale(1.02) translateY(-10px);
}

.popular-badge {
    position: absolute;
    top: 15px;
    right: -35px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    padding: 6px 40px;
    font-size: 0.75rem;
    font-weight: 600;
    transform: rotate(45deg);
    box-shadow: 0 2px 10px rgba(255, 0, 200, 0.3);
}

.plan-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 10px;
}

.plan-price {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ff00c8;
    margin-bottom: 5px;
}

.plan-price.free {
    color: #25D366;
}

.plan-price small {
    font-size: 0.9rem;
    font-weight: 400;
    color: #666;
}

.plan-limit {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 auto 0;
    flex-grow: 1;
}

.plan-features li {
    padding: 10px 0;
    color: #444;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.plan-features li i {
    color: #25D366;
    font-size: 1.1rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.commission-box {
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-top: 20px;
}

.commission-box strong {
    display: block;
    font-size: 1.2rem;
    margin-bottom: 5px;
}

.commission-label {
    font-size: 0.8rem;
    opacity: 0.9;
}

.plan-cta {
    display: block;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
    text-decoration: none;
    text-align: center;
}

.plan-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 0, 200, 0.4);
    color: white;
}

/* CTA Section */
.planes-cta-section {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 100%);
    padding: 60px 20px;
    text-align: center;
}

.planes-cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.planes-cta-content h2 {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.planes-cta-content p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin-bottom: 25px;
}

.planes-cta-btn {
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

.planes-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 200, 0.4);
    color: white;
}

.planes-cta-btn i {
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .planes-title {
        font-size: 1.8rem;
    }

    .plan-card.featured {
        transform: none;
    }

    .plan-card.featured:hover {
        transform: translateY(-10px);
    }
}
</style>
@endpush
