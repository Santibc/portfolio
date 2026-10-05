{{-- Badge de stock en la tarjeta de un item del POS (solo si descuenta inventario). Requiere `item` en scope Alpine. --}}
<template x-if="unidadesDisponibles(item) !== null">
    <span class="absolute top-1.5 left-1.5 inline-flex items-center font-semibold rounded-full text-[9px] px-1.5 py-0.5 shadow-soft"
          :class="itemAgotado(item)
              ? 'bg-rose-600 text-white'
              : (unidadesDisponibles(item) <= 5 ? 'bg-amber-400 text-amber-950' : 'bg-white/95 text-cream-900 dark:bg-cream-900/95 dark:text-cream-100')"
          x-text="itemAgotado(item) ? 'Agotado' : unidadesDisponibles(item) + ' disp.'"></span>
</template>
