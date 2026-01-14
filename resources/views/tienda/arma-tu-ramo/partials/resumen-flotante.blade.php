{{-- Resumen flotante en la parte inferior --}}
<div class="resumen-flotante">
    <div class="resumen-flotante-inner">
        <div class="resumen-info">
            @if($ramo->estilo)
                <div class="resumen-item">
                    <span class="resumen-label">Estilo</span>
                    <span class="resumen-value">{{ $ramo->estilo->nombre }}</span>
                </div>
            @endif

            <div class="resumen-item">
                <span class="resumen-label">Flores</span>
                <span class="resumen-value" id="resumen-flores">{{ $ramo->total_flores }} seleccionadas</span>
            </div>

            @if($ramo->envoltura)
                <div class="resumen-item">
                    <span class="resumen-label">Envoltura</span>
                    <span class="resumen-value">{{ $ramo->envoltura->nombre }}</span>
                </div>
            @endif
        </div>

        <div class="resumen-total">
            <span class="resumen-label">Total</span>
            <span class="resumen-value resumen-total-value" id="resumen-total">
                ${{ number_format($ramo->total, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>
