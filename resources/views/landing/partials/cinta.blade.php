{{-- Cinta de sabores del empaque. Repite los ingredientes (ya listados en la página), por eso es decorativa. --}}
<div class="relative z-20 -mt-4 -mb-2 overflow-hidden py-3" aria-hidden="true">
    <div class="cinta -rotate-[1.4deg] -mx-4">
        <div class="flex w-max items-center gap-7 whitespace-nowrap py-3 pl-7 font-brand text-[clamp(1.2rem,2.3vw,1.85rem)] uppercase leading-none will-change-transform" data-cinta>
            @for ($vuelta = 0; $vuelta < 4; $vuelta++)
                @foreach ($ingredientes as $ingrediente)
                    <span>{{ $ingrediente['nombre'] }}</span>
                    <svg class="h-4 w-4 shrink-0 text-primary-500" viewBox="0 0 20 20" focusable="false"><polygon points="2,3 18,10 5,18" fill="currentColor" /></svg>
                @endforeach
            @endfor
        </div>
    </div>
</div>
