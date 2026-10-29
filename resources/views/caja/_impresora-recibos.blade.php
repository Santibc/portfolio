{{--
    Impresora de recibos de la caja. La configuración es POR EQUIPO (localStorage del Chrome de
    la caja), porque solo el equipo POS tiene la impresora conectada:
      - activa:         al cobrar, el recibo se imprime solo.
      - desactivada:    se cobra sin imprimir y sin preguntar.
      - sin-configurar: (por defecto) al cobrar pregunta si seguir sin imprimir.
    Una página web no puede ver ni elegir las impresoras de Windows: el recibo sale por la
    predeterminada. Para que salga sin la ventana de impresión, Chrome debe abrirse con
    --kiosk-printing, y eso lo hace el ícono que crea el instalador (InstaladorCajaService).
--}}
<x-modal id="modal-impresora" title="Impresora de recibos">
    <div x-data="impresoraModal(@js(route('caja.recibo.prueba')))" class="space-y-5">

        <div class="space-y-2">
            <div x-show="estado === 'activa'">
                <x-alert variant="success" title="Lista en este equipo">
                    Al cobrar, el recibo sale solo por la impresora.
                </x-alert>
            </div>
            <div x-show="estado === 'sin-configurar'">
                <x-alert variant="warning" title="Sin configurar en este equipo">
                    Al cobrar se pregunta si quieres seguir sin imprimir el recibo.
                </x-alert>
            </div>
            <div x-show="estado === 'desactivada'" x-cloak>
                <x-alert variant="info" title="Este equipo no imprime recibos">
                    Se cobra sin imprimir y sin preguntar. Para usar la impresora, haz la prueba de abajo.
                </x-alert>
            </div>
            <p class="text-xs text-cream-600 dark:text-cream-400">
                Esto se guarda solo en este equipo: configúralo en el equipo que tiene la impresora conectada.
            </p>
        </div>

        <section class="space-y-2">
            <h4 class="text-sm font-semibold text-cream-900 dark:text-cream-50">1. Prepara el equipo (una sola vez)</h4>
            <p class="text-sm text-cream-700 dark:text-cream-300">
                Conecta la impresora por USB y préndela. Luego descarga el instalador y ábrelo: deja la impresora
                lista y crea el ícono <strong>{{ $accesoCaja }}</strong>,
                que abre la caja en pantalla completa al prender el equipo e imprime sin mostrar la ventana de
                impresión (para cerrarla: Alt+F4 con un teclado).
            </p>
            <x-button variant="secondary" size="sm" icon="download" :href="route('caja.instalador')">
                Descargar instalador
            </x-button>
            <p class="text-xs text-cream-600 dark:text-cream-400">
                Si Chrome avisa del archivo, elige «Conservar». Si Windows lo bloquea, toca «Más información» y
                «Ejecutar de todas formas». Al terminar pide reiniciar: la caja se abre sola y ahí sigues con el paso 2.
            </p>
        </section>

        <section class="space-y-3">
            <h4 class="text-sm font-semibold text-cream-900 dark:text-cream-50">2. Imprime una prueba</h4>
            <p class="text-sm text-cream-700 dark:text-cream-300">Para rollo de 58 mm (Xprinter XP-58 y similares).</p>
            <x-button variant="primary" icon="printer" @click="probar()" x-bind:disabled="paso === 'probando'">
                Imprimir prueba
            </x-button>

            <div x-show="paso === 'preguntar'" x-cloak
                 class="space-y-2 rounded-xl border border-cream-200 p-3 dark:border-cream-800">
                <p class="text-sm font-medium text-cream-900 dark:text-cream-50">
                    ¿Salió el recibo de prueba sin que apareciera la ventana de impresión?
                </p>
                <div class="flex flex-wrap gap-2">
                    <x-button variant="success" size="sm" icon="check" @click="confirmar(true)">Sí, salió bien</x-button>
                    <x-button variant="secondary" size="sm" @click="confirmar(false)">No</x-button>
                </div>
            </div>

            <div x-show="paso === 'ayuda'" x-cloak>
                <x-alert variant="warning" title="Revisemos">
                    <ul class="list-disc space-y-1 pl-4">
                        <li><strong>Apareció la ventana de impresión:</strong> cierra Chrome y abre la caja con el ícono {{ $accesoCaja }} del escritorio. Si no lo tienes, haz el paso 1.</li>
                        <li><strong>No salió nada:</strong> revisa que la impresora esté prendida, con papel y conectada, y abre de nuevo el instalador.</li>
                        <li><strong>Salió cortado o muy pequeño:</strong> revisa que el rollo sea de 58 mm y prueba otra vez.</li>
                    </ul>
                </x-alert>
            </div>

            <div x-show="paso === 'error'" x-cloak>
                <x-alert variant="danger">No se pudo cargar el recibo de prueba. Revisa la conexión y vuelve a intentarlo.</x-alert>
            </div>
        </section>

        <section class="space-y-2 border-t border-cream-200 pt-4 dark:border-cream-800">
            <h4 class="text-sm font-semibold text-cream-900 dark:text-cream-50">Teclado de este equipo</h4>
            {{-- Queda en este Chrome (layouts/_teclado-fisico), también para la pantalla de login. --}}
            <x-toggle id="equipo-teclado-fisico" label="Este equipo tiene teclado"
                      description="No abre el teclado de la pantalla al tocar un campo."
                      x-init="on = window.tecladoFisico.activo()"
                      x-on:change="window.tecladoFisico.guardar($event.target.checked)" />
        </section>

        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-cream-200 pt-4 dark:border-cream-800">
            <x-button variant="ghost" size="sm" icon="rotate-ccw" x-show="ultimo" x-cloak @click="reimprimir()">
                Reimprimir último recibo
            </x-button>
            <x-button variant="ghost" size="sm" class="ml-auto" x-show="estado !== 'desactivada'" @click="desactivar()">
                No usar impresora en este equipo
            </x-button>
        </div>
    </div>
</x-modal>

@push('scripts')
<script>
    window.impresoraRecibos = (() => {
        const CLAVE = 'caja-impresora';           // { estado } de ESTE equipo
        const CLAVE_ULTIMO = 'caja-ultimo-recibo'; // URL del último recibo, para reimprimir
        const leer = () => { try { return JSON.parse(localStorage.getItem(CLAVE)) || {}; } catch (e) { return {}; } };
        const avisar = () => window.dispatchEvent(new CustomEvent('impresora-cambio'));

        return {
            estado() {
                const estado = leer().estado;
                return estado === 'activa' || estado === 'desactivada' ? estado : 'sin-configurar';
            },
            guardar(cambios) {
                try { localStorage.setItem(CLAVE, JSON.stringify(Object.assign(leer(), cambios))); } catch (e) {}
                avisar();
            },
            ultimoRecibo() { try { return localStorage.getItem(CLAVE_ULTIMO); } catch (e) { return null; } },

            // Con el import ESM, Preline no deja HSOverlay en window: se usa el botón que abre el modal.
            abrirConfiguracion() {
                const boton = [...document.querySelectorAll('[data-hs-overlay="#modal-impresora"]')].find((b) => !b.closest('#modal-impresora'));
                boton?.click();
            },

            // Carga el recibo en un iframe oculto y lo imprime. Desde el ícono de la caja
            // (--kiosk-printing) sale directo; en otro Chrome aparece la ventana de impresión.
            imprimir(url) {
                return new Promise((resolve) => {
                    const iframe = document.createElement('iframe');
                    iframe.setAttribute('aria-hidden', 'true');
                    iframe.tabIndex = -1;
                    // Fuera de la pantalla pero con tamaño: con display:none no se maqueta y sale en blanco.
                    iframe.style.cssText = 'position:fixed;left:-10000px;top:0;width:90mm;height:20mm;border:0;';
                    iframe.addEventListener('load', async () => {
                        const win = iframe.contentWindow;
                        let impreso = false;
                        try {
                            // Sin recibo (error, sesión vencida) no se imprime otra página.
                            if (win && win.document.querySelector('[data-recibo]')) {
                                await win.document.fonts.ready;
                                win.focus();
                                win.print();
                                impreso = true;
                            }
                        } catch (e) {}
                        setTimeout(() => iframe.remove(), 2000);
                        resolve(impreso);
                    }, { once: true });
                    iframe.src = url;
                    document.body.appendChild(iframe);
                });
            },

            async imprimirVenta(url) {
                try { localStorage.setItem(CLAVE_ULTIMO, url); } catch (e) {}
                avisar();
                if (this.estado() !== 'activa') return;
                if (!(await this.imprimir(url))) {
                    window.showToast?.('error', 'No se pudo imprimir el recibo. Reimprímelo desde «Impresora».');
                }
            },

            // Antes de cobrar: true = seguir con el cobro.
            async confirmarCobro() {
                if (this.estado() !== 'sin-configurar') return true;

                const respuesta = await window.Swal.fire({
                    icon: 'warning',
                    title: 'La impresora no está configurada',
                    text: 'Este equipo no tiene configurada la impresora de recibos. ¿Quieres cobrar sin imprimir el recibo?',
                    input: 'checkbox',
                    inputValue: 0,
                    inputPlaceholder: 'No volver a preguntar en este equipo',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Cobrar sin imprimir',
                    denyButtonText: 'Configurar impresora',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        // SweetAlert toma fondo y texto (también los de la casilla) de estas variables.
                        popup: 'rounded-2xl [--swal2-background:theme(colors.white)] [--swal2-color:theme(colors.cream.900)] dark:[--swal2-background:theme(colors.cream.900)] dark:[--swal2-color:theme(colors.cream.50)]',
                        actions: 'gap-2',
                        confirmButton: 'inline-flex items-center rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-600',
                        denyButton: 'inline-flex items-center rounded-xl border border-cream-300 bg-white px-4 py-2.5 text-sm font-semibold text-cream-900 hover:bg-cream-100 dark:border-cream-700 dark:bg-cream-800 dark:text-cream-100 dark:hover:bg-cream-700',
                        cancelButton: 'inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-cream-700 hover:bg-cream-100 dark:text-cream-200 dark:hover:bg-cream-800',
                    },
                });

                if (respuesta.isConfirmed) {
                    if (respuesta.value) this.guardar({ estado: 'desactivada' });
                    return true;
                }
                if (respuesta.isDenied) this.abrirConfiguracion();
                return false;
            },
        };
    })();

    // Botón de estado (punto verde / ámbar / gris) junto a la caja.
    window.impresoraEstado = () => ({
        estado: window.impresoraRecibos.estado(),
        init() { window.addEventListener('impresora-cambio', () => { this.estado = window.impresoraRecibos.estado(); }); },
        get etiqueta() {
            return { activa: 'Impresora lista', desactivada: 'Este equipo no imprime recibos', 'sin-configurar': 'Impresora sin configurar' }[this.estado];
        },
        get punto() {
            return { activa: 'bg-emerald-500', desactivada: 'bg-cream-400 dark:bg-cream-600', 'sin-configurar': 'bg-amber-500' }[this.estado];
        },
    });

    window.impresoraModal = (urlPrueba) => ({
        estado: 'sin-configurar',
        ultimo: null,
        paso: null, // probando | preguntar | ayuda | error
        init() {
            this.leer();
            window.addEventListener('impresora-cambio', () => this.leer());
        },
        leer() {
            this.estado = window.impresoraRecibos.estado();
            this.ultimo = window.impresoraRecibos.ultimoRecibo();
        },
        async probar() {
            this.paso = 'probando';
            this.paso = (await window.impresoraRecibos.imprimir(urlPrueba)) ? 'preguntar' : 'error';
        },
        confirmar(salio) {
            if (!salio) {
                this.paso = 'ayuda';
                return;
            }
            window.impresoraRecibos.guardar({ estado: 'activa' });
            this.paso = null;
            window.showToast?.('success', 'Impresora lista: los recibos se imprimen al cobrar.');
        },
        desactivar() {
            window.impresoraRecibos.guardar({ estado: 'desactivada' });
            this.paso = null;
        },
        reimprimir() {
            if (this.ultimo) window.impresoraRecibos.imprimir(this.ultimo);
        },
    });

    @if (session('recibo_venta_id'))
        // Venta recién cobrada: el recibo se imprime al cargar la caja (si este equipo imprime).
        // setTimeout: después de los DOMContentLoaded de app.js (ahí se define showToast).
        document.addEventListener('DOMContentLoaded', () => setTimeout(() => {
            window.impresoraRecibos.imprimirVenta(@js(route('caja.venta.recibo', session('recibo_venta_id'))));
        }));
    @endif
</script>
@endpush
