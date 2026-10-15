@php
    // Se arma sola con los productos visibles + el lema (nada que editar aparte).
    $especialidades = $productos->pluck('titulo')->push($negocio->lema)->unique()->values();
@endphp
{{-- Cinta infinita: la lista va dos veces y se desplaza -50% para que el bucle no se note --}}
<div class="relative overflow-hidden border-y border-primary-700/30 bg-primary-500 py-5 text-cream-50 dark:border-primary-300/10 dark:bg-primary-800" aria-hidden="true">
    <div class="marquesina flex w-max items-center">
        @foreach ([1, 2] as $_)
            {{-- Con pocos productos se repite la lista para que la cinta llene pantallas anchas --}}
            @foreach ($especialidades->count() < 5 ? $especialidades->concat($especialidades) : $especialidades as $item)
                <span class="flex items-center gap-8 pr-8 font-serif text-2xl italic sm:text-3xl">
                    {{ $item }}
                    <svg class="h-5 w-5 text-gold-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l1.8 6.2L20 10l-6.2 1.8L12 18l-1.8-6.2L4 10l6.2-1.8z" /></svg>
                </span>
            @endforeach
        @endforeach
    </div>
</div>
