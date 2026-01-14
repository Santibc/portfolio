{{-- Indicador de pasos del wizard - 3 pasos --}}
<div class="steps-indicator">
    <div class="steps-container">
        @for($num = 1; $num <= 3; $num++)
            <div class="step-item {{ $num < $pasoActual ? 'completed' : '' }} {{ $num == $pasoActual ? 'active' : '' }} {{ $num > $pasoActual ? 'pending' : '' }}">
                <div class="step-circle">
                    @if($num < $pasoActual)
                        <i class="bi bi-check-lg"></i>
                    @else
                        <span>{{ $num }}</span>
                    @endif
                </div>
            </div>
            @if($num < 3)
                <div class="step-line {{ $num < $pasoActual ? 'completed' : '' }}"></div>
            @endif
        @endfor
    </div>
</div>

<style>
.steps-indicator {
    margin-bottom: 40px;
}

.steps-container {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 600px;
    margin: 0 auto;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
}

.step-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
    border: 2px solid var(--flores-border);
    color: var(--flores-text-light);
}

.step-item.active .step-circle {
    background: var(--flores-primary);
    border-color: var(--flores-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.step-item.completed .step-circle {
    background: var(--flores-primary);
    border-color: var(--flores-primary);
    color: #fff;
}

.step-line {
    flex: 1;
    height: 2px;
    background: var(--flores-border);
    margin: 0 8px;
    transition: background 0.3s ease;
}

.step-line.completed {
    background: var(--flores-primary);
}

@media (max-width: 576px) {
    .step-label {
        font-size: 0.7rem;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        font-size: 0.85rem;
    }
}
</style>
