@php
    // Corte del paquete abierto: una banda de color plano por ingrediente, de abajo hacia arriba.
    // Las capas de arriba hacen montaña por encima del borde ("cargado hasta arriba").
    $alturas = [54, 34, 36, 30, 26, 20, 22, 26, 22, 12];
    $base = 392;
    $bordeAnterior = null;
    $bandas = [];

    foreach ($ingredientes as $i => $ingrediente) {
        $base -= $alturas[$i] ?? 22;
        $montana = $i >= 6 ? ($i - 5) * 9 : 0;
        $borde = [];
        for ($x = 20; $x <= 300; $x += 10) {
            $ola = 3.5 * sin($x * 0.11 + $i * 1.7);
            $domo = $montana * max(0, 1 - (($x - 160) / 122) ** 2);
            $borde[] = [$x, round($base - $ola - $domo, 1)];
        }
        $debajo = $bordeAnterior ?? array_map(fn ($p) => [$p[0], 404], $borde);
        $puntos = array_merge($borde, array_reverse($debajo));
        $d = 'M' . implode(' L', array_map(fn ($p) => $p[0] . ',' . $p[1], $puntos)) . ' Z';
        $bandas[] = ['d' => $d, 'color' => $ingrediente['color']];
        $bordeAnterior = $borde;
    }
@endphp

<figure class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-5 sm:gap-7" aria-labelledby="titulo-capas">
    <svg class="w-full max-w-[280px]" viewBox="0 0 320 430" aria-hidden="true" focusable="false">
        <defs>
            <clipPath id="interior-paquete">
                <path d="M58,394 C44,300 42,180 46,98 L46,0 L274,0 L274,98 C278,180 276,300 262,394 Z" />
            </clipPath>
        </defs>
        <g clip-path="url(#interior-paquete)">
            @foreach ($bandas as $banda)
                <path d="{{ $banda['d'] }}" fill="{{ $banda['color'] }}" />
            @endforeach
            {{-- Nachos asomando en la base --}}
            <g fill="#c26112" opacity=".55">
                <polygon points="72,384 104,372 84,352" /><polygon points="128,388 162,378 140,358" />
                <polygon points="186,386 222,380 198,356" /><polygon points="232,384 256,368 238,350" />
            </g>
        </g>
        {{-- Paredes del paquete con el borde rasgado arriba y el sello crimpado abajo --}}
        <g fill="none" stroke="var(--tinta)" stroke-width="7" stroke-linejoin="round" stroke-linecap="round">
            <path d="M58,394 C44,300 42,180 46,98 L40,90 L48,82 L42,74" />
            <path d="M262,394 C276,300 278,180 274,98 L280,90 L272,82 L278,74" />
        </g>
        <path d="M50,396 L270,396 L270,414 L261,422 L252,414 L243,422 L234,414 L225,422 L216,414 L207,422 L198,414 L189,422 L180,414 L171,422 L162,414 L153,422 L144,414 L135,422 L126,414 L117,422 L108,414 L99,422 L90,414 L81,422 L72,414 L63,422 L54,414 L50,414 Z"
              style="fill: var(--triangulo)" stroke="var(--tinta)" stroke-width="4" stroke-linejoin="round" />
    </svg>

    <div>
        <figcaption id="titulo-capas" class="rotulo texto-tenue text-sm">Lo que lleva, capa por capa</figcaption>
        <ol class="mt-3 space-y-1.5 text-[0.98rem] font-semibold" reversed>
            @foreach (array_reverse($ingredientes) as $ingrediente)
                <li class="flex items-center gap-2.5">
                    <span class="h-3.5 w-3.5 shrink-0 rounded-[4px] ring-1 ring-black/20" style="background: {{ $ingrediente['color'] }}" aria-hidden="true"></span>
                    {{ $ingrediente['nombre'] }}
                </li>
            @endforeach
        </ol>
    </div>
</figure>
