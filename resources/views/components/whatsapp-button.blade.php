{{--
    WhatsApp Floating Button Component

    Uso: @include('components.whatsapp-button', ['empresa' => $empresa])
--}}

@if($empresa->whatsapp)
@php
    // Limpiar el número de teléfono (solo números)
    $whatsappNumber = preg_replace('/[^0-9]/', '', $empresa->whatsapp);

    // Mensaje predeterminado
    $defaultMessage = "Hola Estoy en Tu tienda quisiera hacer una consulta:";
    $whatsappUrl = "https://api.whatsapp.com/send?phone={$whatsappNumber}&text=" . urlencode($defaultMessage);
@endphp

<!-- WhatsApp Floating Button -->
<div class="whatsapp-float-container">
    <!-- Tooltip/Message bubble -->
    <div class="whatsapp-tooltip" id="whatsappTooltip">
        <button class="whatsapp-tooltip-close" onclick="closeWhatsAppTooltip()">&times;</button>
        <div class="whatsapp-tooltip-content">
            <i class="bi bi-info-circle-fill text-primary me-2"></i>
            <span>
                <strong>{{ $empresa->nombre }}</strong> {{ $empresa->descripcion ?? 'Entregamos emociones todos los días' }}
                @if($empresa->horario_atencion)
                    <br><small>
                    @php
                        $horarios = [];
                        $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                        foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia) {
                            if(isset($empresa->horario_atencion[$dia]) && !($empresa->horario_atencion[$dia]['cerrado'] ?? false)) {
                                $horarios['semana'] = ($empresa->horario_atencion[$dia]['apertura'] ?? '09:00') . ' - ' . ($empresa->horario_atencion[$dia]['cierre'] ?? '18:00');
                                break;
                            }
                        }
                        if(isset($empresa->horario_atencion['sabado']) && !($empresa->horario_atencion['sabado']['cerrado'] ?? false)) {
                            $horarios['sabado'] = ($empresa->horario_atencion['sabado']['apertura'] ?? '09:00') . ' - ' . ($empresa->horario_atencion['sabado']['cierre'] ?? '16:00');
                        }
                        if(isset($empresa->horario_atencion['domingo']) && !($empresa->horario_atencion['domingo']['cerrado'] ?? false)) {
                            $horarios['domingo'] = ($empresa->horario_atencion['domingo']['apertura'] ?? '09:00') . ' - ' . ($empresa->horario_atencion['domingo']['cierre'] ?? '15:00');
                        }
                    @endphp
                    @if(isset($horarios['semana']))Lunes a viernes hasta las {{ explode(' - ', $horarios['semana'])[1] }}@endif
                    @if(isset($horarios['sabado'])) Sábado hasta las {{ explode(' - ', $horarios['sabado'])[1] }}@endif
                    @if(isset($horarios['domingo'])) Domingo hasta las {{ explode(' - ', $horarios['domingo'])[1] }}@endif
                    </small>
                @endif
                <br><small>Puedes comprar en <strong>{{ request()->getHost() }}</strong> o al ws <strong>+{{ $whatsappNumber }}</strong></small>
            </span>
        </div>
    </div>

    <!-- WhatsApp Button -->
    <a href="{{ $whatsappUrl }}"
       class="whatsapp-float-btn"
       target="_blank"
       rel="noopener noreferrer"
       title="Chatea con nosotros por WhatsApp"
       aria-label="Contactar por WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="28" height="28">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
        </svg>
    </a>
</div>

<style>
/* WhatsApp Floating Button Styles - Posición IZQUIERDA */
.whatsapp-float-container {
    position: fixed;
    bottom: 25px;
    left: 25px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

.whatsapp-float-btn {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    animation: whatsapp-pulse 2s infinite;
}

.whatsapp-float-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
    color: white;
}

@keyframes whatsapp-pulse {
    0% {
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    }
    50% {
        box-shadow: 0 4px 25px rgba(37, 211, 102, 0.6);
    }
    100% {
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    }
}

/* Tooltip Bubble - Ahora a la derecha del botón */
.whatsapp-tooltip {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    color: white;
    padding: 15px 40px 15px 15px;
    border-radius: 12px;
    max-width: 350px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    position: relative;
    font-size: 0.85rem;
    line-height: 1.4;
    display: none;
}

.whatsapp-tooltip.show {
    display: block;
    animation: tooltip-fade-in 0.3s ease;
}

@keyframes tooltip-fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Flecha apuntando hacia abajo a la izquierda */
.whatsapp-tooltip::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 25px;
    border-width: 10px 10px 0;
    border-style: solid;
    border-color: #f57c00 transparent transparent transparent;
}

.whatsapp-tooltip-close {
    position: absolute;
    top: 5px;
    right: 10px;
    background: rgba(255, 255, 255, 0.3);
    border: none;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.whatsapp-tooltip-close:hover {
    background: rgba(255, 255, 255, 0.5);
}

.whatsapp-tooltip-content {
    display: flex;
    align-items: flex-start;
}

.whatsapp-tooltip-content i {
    flex-shrink: 0;
    margin-top: 2px;
}

/* Mobile Responsive */
@media (max-width: 576px) {
    .whatsapp-float-container {
        bottom: 20px;
        left: 15px;
    }

    .whatsapp-float-btn {
        width: 55px;
        height: 55px;
    }

    .whatsapp-tooltip {
        max-width: 280px;
        font-size: 0.8rem;
        left: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltip = document.getElementById('whatsappTooltip');
    const tooltipShown = sessionStorage.getItem('whatsappTooltipShown');

    // Mostrar tooltip automáticamente después de 3 segundos (solo una vez por sesión)
    if (!tooltipShown) {
        setTimeout(function() {
            tooltip.classList.add('show');
            sessionStorage.setItem('whatsappTooltipShown', 'true');

            // Auto-ocultar después de 10 segundos
            setTimeout(function() {
                tooltip.classList.remove('show');
            }, 10000);
        }, 3000);
    }
});

function closeWhatsAppTooltip() {
    document.getElementById('whatsappTooltip').classList.remove('show');
}
</script>
@endif
