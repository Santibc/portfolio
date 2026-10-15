{{-- Botón flotante de WhatsApp: el globo de ayuda aparece unos segundos al entrar y al pasar el mouse --}}
<div x-data="{ globo: false }"
     x-init="setTimeout(() => globo = true, 3500); setTimeout(() => globo = false, 11000)"
     class="fixed bottom-5 right-5 z-50 flex items-center gap-3 sm:bottom-7 sm:right-7">
    <div x-cloak x-show="globo"
         x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="hidden rounded-2xl rounded-br-md bg-white px-4 py-3 shadow-soft-lg sm:block dark:bg-cream-900">
        <p class="text-sm font-semibold text-cream-950 dark:text-cream-50">¿Hacemos tu pedido?</p>
        <p class="text-xs text-cream-600 dark:text-cream-400">Escríbenos por WhatsApp</p>
    </div>

    <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener"
       @mouseenter="globo = true" @mouseleave="globo = false"
       aria-label="Escríbenos por WhatsApp al {{ $negocio->telefonoVisible() }}"
       class="relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-whatsapp text-white shadow-[0_14px_30px_-8px_rgb(37_211_102/0.75)] transition-transform duration-300 hover:scale-110 hover:bg-whatsapp-600 sm:h-16 sm:w-16">
        <span aria-hidden="true" class="pulso-wa absolute inset-0 rounded-full bg-whatsapp"></span>
        @include('landing.partials.icono-whatsapp', ['clase' => 'relative h-7 w-7 sm:h-8 sm:w-8'])
    </a>
</div>
