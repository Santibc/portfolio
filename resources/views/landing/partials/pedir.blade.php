{{-- Cierre: el paquete explota en nachos junto a la acción --}}
<section id="pedir" class="relative overflow-hidden pb-24 pt-16 lg:pb-32" aria-labelledby="titulo-pedir">
    <div class="contenedor flex flex-col items-center text-center">
        <div class="h-[38vh] w-full max-w-md lg:h-[46vh]" data-bag-anchor="cierre" data-bag-mode="burst" data-bag-rot="0.05,-0.3,-0.08"></div>

        <h2 id="titulo-pedir" class="titular sobre-3d mt-4 text-[clamp(3rem,8vw,7.25rem)]">¿Se te <span class="acento">antojó?</span></h2>
        <p class="texto-suave sobre-3d mt-6 max-w-[46ch] text-lg sm:text-xl">Escríbenos por WhatsApp y te lo llevamos en {{ $negocio['barrio'] }}, o pasa por el local.</p>

        <div class="corte sobre-3d mt-10 w-full max-w-xl flex-wrap justify-center sm:w-auto sm:flex-nowrap">
            <x-icon name="scissors" class="corte__tijera h-7 w-7" />
            <span class="corte__linea !flex-1 sm:!flex-none" aria-hidden="true"></span>
            <x-button variant="pedido" size="xl" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" class="w-full sm:w-auto sm:whitespace-nowrap">
                <x-brand-icon name="whatsapp" class="h-6 w-6" />
                <span class="sm:hidden">Pide por WhatsApp</span>
                <span class="hidden sm:inline">Abre aquí · Pide por WhatsApp</span>
            </x-button>
        </div>

        <p class="texto-tenue sobre-3d mt-7 text-base">
            WhatsApp {{ $negocio['whatsapp_visible'] }}
            @if ($enlaces['rappi'])
                · <a href="{{ $enlaces['rappi'] }}" target="_blank" rel="noopener" class="enlace">También en Rappi</a>
            @endif
        </p>
    </div>
</section>
