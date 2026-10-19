@php
    // Código de barras tipo EAN a partir del número de domicilios (decorativo: el número va escrito debajo).
    $digitos = str_split(preg_replace('/\D/', '', $numero));
    $codigoL = ['0001101', '0011001', '0010011', '0111101', '0100011', '0110001', '0101111', '0111011', '0110111', '0001011'];
    $mitad = intdiv(count($digitos), 2);
    $modulos = '101';
    foreach ($digitos as $i => $digito) {
        $codigo = $codigoL[(int) $digito];
        $modulos .= $i < $mitad ? $codigo : strtr($codigo, '01', '10');
        if ($i === $mitad - 1) {
            $modulos .= '01010';
        }
    }
    $modulos .= '101';
    $guardas = [0, 1, 2, 3 + 7 * $mitad, 4 + 7 * $mitad, 5 + 7 * $mitad, 6 + 7 * $mitad, 7 + 7 * $mitad];
    $total = strlen($modulos);
    $guardas = array_merge($guardas, [$total - 3, $total - 2, $total - 1]);
@endphp

<div>
    <svg class="h-14 w-full max-w-[12rem]" viewBox="0 0 {{ $total }} 46" preserveAspectRatio="none" aria-hidden="true" focusable="false">
        @foreach (str_split($modulos) as $x => $bit)
            @if ($bit === '1')
                <rect x="{{ $x }}" y="0" width="1" height="{{ in_array($x, $guardas, true) ? 46 : 40 }}" fill="currentColor" />
            @endif
        @endforeach
    </svg>
    <p class="mt-1 font-semibold tracking-[0.3em] [font-variant-numeric:tabular-nums]">{{ $numero }}</p>
</div>
