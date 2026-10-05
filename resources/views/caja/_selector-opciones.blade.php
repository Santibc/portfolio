{{-- Selector de opciones (ej. sabor de Doritos). Debe ir dentro del x-data que usa posInventario(). --}}
<div x-show="picker.open" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
     @keydown.escape.window="picker.open && cerrarPicker()">
    <div class="flex min-h-full items-end sm:items-center justify-center p-4" @click.self="cerrarPicker()">
        <div class="w-full max-w-md bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg border border-cream-200 dark:border-cream-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-wide text-cream-500 dark:text-cream-400">Elige para</p>
                    <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50 truncate" x-text="picker.item ? picker.item.nombre : ''"></h3>
                </div>
                <button type="button" @click="cerrarPicker()" aria-label="Cerrar"
                        class="size-8 inline-flex justify-center items-center rounded-full text-cream-600 hover:bg-cream-100 dark:text-cream-300 dark:hover:bg-cream-800 transition-colors">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <div class="p-5 space-y-5">
                <template x-for="comp in elegibles(picker.item)" :key="comp.id">
                    <div>
                        <p class="text-sm font-semibold text-cream-800 dark:text-cream-200 mb-2" x-text="comp.nombre"></p>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="op in comp.opciones" :key="op.id">
                                <button type="button" @click="elegirOpcion(comp, op.id)" :disabled="opcionAgotada(comp, op.id)"
                                        :class="picker.sel[comp.id] == op.id
                                            ? 'bg-primary-500 border-primary-500 text-white shadow-soft dark:bg-primary-400 dark:border-primary-400 dark:text-primary-950'
                                            : (opcionAgotada(comp, op.id)
                                                ? 'bg-cream-100 border-cream-200 text-cream-400 cursor-not-allowed dark:bg-cream-900/60 dark:border-cream-800 dark:text-cream-600'
                                                : 'bg-white border-cream-300 text-cream-900 hover:border-primary-400 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 dark:hover:border-primary-400')"
                                        class="px-3 py-3 rounded-xl border text-left transition-all active:scale-[0.98]">
                                    <span class="block text-sm font-semibold leading-tight" x-text="op.nombre"></span>
                                    <span class="block mt-0.5 text-xs opacity-80"
                                          x-text="opcionAgotada(comp, op.id) ? 'Agotado' : 'Quedan ' + fmtCant(disponible(op.id))"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
