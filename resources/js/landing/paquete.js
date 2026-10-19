// Si el módulo 3D no existe (o falla), la página se queda con el póster sin romperse.
const modulos3d = import.meta.glob('./bag/index.js');

/** WebGL2 disponible y sin ahorro de datos activado. */
export function puedeMostrar3d() {
    if (navigator.connection?.saveData) return false;
    try {
        const gl = document.createElement('canvas').getContext('webgl2');
        gl?.getExtension('WEBGL_lose_context')?.loseContext();
        return Boolean(gl);
    } catch {
        return false;
    }
}

// El póster es idéntico a la primera pose del 3D, así que no hay apuro: three.js (~170 KB gzip) se
// carga con la primera interacción o, si nadie toca nada, un rato después del load. En celular la
// espera es mayor para no pelear con la carga inicial en equipos modestos.
const EVENTOS_ARRANQUE = ['pointerdown', 'pointermove', 'touchstart', 'wheel', 'keydown', 'scroll'];

/** Carga three.js y el paquete cuando la página ya terminó de cargar (no compite con el LCP). */
export function cargarPaquete({ gsap, ScrollTrigger, alFallar }) {
    const canvas = document.getElementById('bag-canvas');
    const importar = modulos3d['./bag/index.js'];
    if (!canvas || !importar) {
        alFallar();
        return;
    }

    window.addEventListener('dorilokos:bag-ready', () => document.documentElement.classList.add('bag-listo'), { once: true });
    window.addEventListener('dorilokos:bag-lost', () => document.documentElement.classList.remove('bag-listo'));

    const lowPower = window.matchMedia('(max-width: 767px)').matches || (navigator.hardwareConcurrency || 8) <= 4;
    let iniciado = false;
    const iniciar = () => {
        if (iniciado) return;
        iniciado = true;
        EVENTOS_ARRANQUE.forEach((evento) => window.removeEventListener(evento, iniciar));
        importar()
            .then(({ initBag }) => initBag({ canvas, gsap, ScrollTrigger, lowPower }))
            .catch((error) => {
                console.warn('Paquete 3D no disponible, se mantiene el póster.', error);
                alFallar();
            });
    };
    const programar = () => {
        EVENTOS_ARRANQUE.forEach((evento) => window.addEventListener(evento, iniciar, { passive: true }));
        window.setTimeout(() => {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(iniciar, { timeout: 2000 });
            } else {
                iniciar();
            }
        }, lowPower ? 6000 : 2500);
    };

    if (document.readyState === 'complete') {
        programar();
    } else {
        window.addEventListener('load', programar, { once: true });
    }
}
