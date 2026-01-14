@extends('tienda.layout')

@section('title', 'Términos y Condiciones - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Encabezado --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">Términos y Condiciones</h1>
                <p class="text-muted">Política de Pedidos, Despachos, Devoluciones y Garantías</p>
            </div>

            {{-- Horario de Atención --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 fw-bold mb-3">
                        <i class="bi bi-clock text-primary me-2"></i>
                        Horario de Atención
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Lunes a Viernes:</strong> 10:00 - 19:00 hrs</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Sábado:</strong> 10:30 - 14:00 hrs</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1. Política de Despacho --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 fw-bold mb-4">
                        <span class="badge bg-primary me-2">1</span>
                        Política de Despacho de Productos
                    </h2>

                    <p class="mb-4">
                        <i class="bi bi-flower1 text-primary me-2"></i>
                        {{ config('app.name') }} vende productos físicos, tales como ramos de flores, arreglos florales y complementos,
                        los cuales son enviados a la dirección indicada por el cliente al momento de la compra.
                    </p>

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Áreas de despacho
                    </h5>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-3">
                            <strong class="text-primary">📍 Cobertura directa:</strong>
                            <p class="mb-0 ms-4">Realizamos envíos dentro de la Región de Valparaíso, incluyendo Viña del Mar y Valparaíso.</p>
                        </li>
                        <li class="mb-3">
                            <strong class="text-primary">🌎 Cobertura nacional:</strong>
                            <p class="mb-0 ms-4">También ofrecemos envíos a otras ciudades de Chile en alianza con comercios locales, entre ellas:</p>
                            <p class="mb-0 ms-4 text-muted">Santiago, Limache, Iquique, Antofagasta, Calama, Punta Arenas, Talca, La Serena, entre otras.</p>
                        </li>
                        <li>
                            <strong class="text-primary">📈 Expansión:</strong>
                            <p class="mb-0 ms-4">Constantemente estamos agregando nuevos destinos para la entrega de nuestros productos.</p>
                        </li>
                    </ul>

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-truck text-primary me-2"></i>
                        Tiempos de entrega
                    </h5>
                    <div class="alert alert-info" role="alert">
                        <ul class="mb-0">
                            <li>Los pedidos deben realizarse con al menos <strong>24 horas de anticipación</strong> a la fecha de entrega.</li>
                            <li>En fechas especiales de alta demanda (Ej: San Valentín, Día de la Madre, Día de la Secretaria, Día de la Mujer),
                                los pedidos deben efectuarse con al menos <strong>48 horas de anticipación</strong>.</li>
                            <li>No garantizamos entregas en un horario exacto, pero sí aseguramos que se realizará <strong>dentro del día solicitado</strong>.</li>
                        </ul>
                    </div>

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-cash-stack text-primary me-2"></i>
                        Costos de envío
                    </h5>
                    <ul class="mb-4">
                        <li>Se calculan automáticamente según la comuna o ciudad de despacho y se informan antes de finalizar la compra.</li>
                        <li>En ciudades fuera de la Región de Valparaíso, los costos pueden variar dependiendo del comercio en alianza que realice la entrega.</li>
                    </ul>

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-calendar-event text-primary me-2"></i>
                        Días especiales o festivos
                    </h5>
                    <p>Las entregas en días festivos o fines de semana deben ser confirmadas previamente, pudiendo implicar un costo adicional.</p>
                </div>
            </div>

            {{-- 2. Política de Cambios y Devoluciones --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 fw-bold mb-4">
                        <span class="badge bg-primary me-2">2</span>
                        Política de Cambios y Devoluciones
                    </h2>

                    <div class="alert alert-warning" role="alert">
                        <p class="mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Debido a la naturaleza perecedera de nuestras flores, <strong>no realizamos devoluciones ni reembolsos</strong>,
                            salvo en los siguientes casos:
                        </p>
                    </div>

                    <ul class="mb-3">
                        <li class="mb-2">
                            <i class="bi bi-x-circle text-danger me-2"></i>
                            Error en el despacho atribuible a la empresa (Ej: producto equivocado).
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-emoji-frown text-danger me-2"></i>
                            Producto en mal estado al momento de la entrega.
                        </li>
                    </ul>

                    <div class="alert alert-success" role="alert">
                        <p class="mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            En estos casos, se podrá coordinar el <strong>reemplazo del producto sin costo adicional</strong> para el cliente.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 3. Garantía de Calidad --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 fw-bold mb-4">
                        <span class="badge bg-primary me-2">3</span>
                        Garantía de Calidad
                    </h2>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-patch-check-fill text-success fs-3 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-2">Productos Garantizados</h5>
                                    <p class="mb-0">Nuestros productos físicos están garantizados en calidad y frescura.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-flower3 text-primary fs-3 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-2">Flores Seleccionadas</h5>
                                    <p class="mb-0">Todos los arreglos y ramos se elaboran con flores seleccionadas y en óptimas condiciones.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-shield-check text-info fs-3 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-2">Fuerza Mayor</h5>
                                    <p class="mb-0">En caso de inconvenientes externos que afecten la entrega, garantizamos la reprogramación del envío en acuerdo con el cliente.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-arrow-repeat text-warning fs-3 me-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-2">Disponibilidad de Productos</h5>
                                    <p class="mb-0">Si algún producto no está disponible, podrá ser reemplazado por otro de características similares, siempre manteniendo la calidad y valor del original.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer de la página --}}
            <div class="text-center mt-5 pt-4 border-top">
                <p class="text-muted mb-2">
                    <i class="bi bi-calendar3 me-2"></i>
                    Última actualización: {{ now()->format('d/m/Y') }}
                </p>
                <p class="text-muted">
                    Si tienes alguna pregunta sobre nuestros términos y condiciones,
                    por favor <a href="mailto:{{ config('mail.from.address') }}" class="text-decoration-none">contáctanos</a>.
                </p>
                <a href="{{ route('home') }}" class="btn btn-outline-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>
                    Volver a la Tienda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
