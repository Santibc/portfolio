{{-- Abre la configuración de la impresora de recibos; el punto muestra su estado en este equipo. --}}
<x-button variant="secondary" size="sm" icon="printer" class="shrink-0 !py-2"
          x-data="impresoraEstado()" x-bind:title="etiqueta" data-hs-overlay="#modal-impresora">
    <span class="hidden sm:inline">Impresora</span>
    <span class="w-2 h-2 rounded-full" :class="punto"></span>
</x-button>
