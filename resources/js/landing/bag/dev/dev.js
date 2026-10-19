// Arnés de desarrollo del paquete 3D: imita la integración de la landing
// (Lenis + ScrollTrigger + pin 'arma') y expone window.__bag para pruebas.
import '@fontsource/lilita-one/400.css';
import '@fontsource-variable/archivo/wdth.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { initBag } from '../index.js';

gsap.registerPlugin(ScrollTrigger);
const params = new URLSearchParams(location.search);

const lenis = new Lenis();
lenis.on('scroll', ScrollTrigger.update);
gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);

const pasos = gsap.utils.toArray('.paso');
ScrollTrigger.create({
    id: 'arma',
    trigger: '#arma',
    pin: true,
    start: 'top top',
    end: '+=300%',
    scrub: true,
    onUpdate(self) {
        const k = Math.min(pasos.length - 1, Math.floor(self.progress * pasos.length));
        pasos.forEach((el, i) => el.classList.toggle('activo', i === k));
    },
});

document.getElementById('tema').addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    window.dispatchEvent(new CustomEvent('dorilokos:theme-changed', { detail: { theme: dark ? 'dark' : 'light' } }));
});

// Tareas largas del hilo principal durante la carga (para medir el costo de iniciar).
const longTasks = [];
try {
    new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) longTasks.push(Math.round(entry.duration));
    }).observe({ type: 'longtask', buffered: true });
} catch (error) {
    // navegador sin Long Tasks API
}

let readyAt = 0;
window.addEventListener('dorilokos:bag-ready', () => {
    readyAt = performance.now();
    document.documentElement.dataset.bag = 'ready';
}, { once: true });

const t0 = performance.now();
let bag;
try {
    bag = await initBag({
        canvas: document.getElementById('bag-canvas'),
        gsap,
        ScrollTrigger,
        lowPower: params.has('low'),
    });
} catch (error) {
    document.documentElement.dataset.bag = 'error';
    console.error(error);
}
const initMs = performance.now() - t0;
document.fonts.ready.then(() => ScrollTrigger.refresh());

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

window.__lenis = lenis;
window.__ST = ScrollTrigger;
window.__bag = {
    debug: () => bag._debug,
    snapshot: (options) => bag.snapshot(options),
    destroy: () => bag.destroy(),
    stops: () => bag._debug.stops,
    state: () => bag._debug.state,
    tone: (name) => bag._debug.setToneMapping(name),
    timings: () => ({ initMs: Math.round(initMs), readyMs: Math.round(readyAt - t0), longTasks: [...longTasks] }),
    scrollTo(y) {
        lenis.scrollTo(y, { immediate: true, force: true });
        ScrollTrigger.update();
    },
    // Sin amortiguación: para capturas que no dependan de cuántos cuadros alcanzó a dibujar.
    settle: () => bag._debug.settle(),
    // Escala de progreso del pin 'arma' a scroll absoluto.
    armaScroll(p) {
        const st = ScrollTrigger.getById('arma');
        return st.start + (st.end - st.start) * p;
    },
    // FPS durante un scroll automático de toda la página.
    async fps(seconds = 8) {
        lenis.scrollTo(0, { immediate: true, force: true });
        await wait(400);
        const frames = [];
        let last = performance.now();
        let running = true;
        const loop = (t) => {
            frames.push(t - last);
            last = t;
            if (running) requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
        lenis.scrollTo(ScrollTrigger.maxScroll(window), { duration: seconds, easing: (t) => t, force: true });
        await wait(seconds * 1000 + 300);
        running = false;
        frames.shift();
        const sorted = [...frames].sort((a, b) => a - b);
        const avg = frames.reduce((s, v) => s + v, 0) / frames.length;
        return {
            frames: frames.length,
            avgFps: Math.round(1000 / avg),
            p95ms: +sorted[Math.floor(sorted.length * 0.95)].toFixed(1),
            worstMs: +sorted[sorted.length - 1].toFixed(1),
            over20ms: frames.filter((v) => v > 20).length,
        };
    },
};

if (params.has('debug')) {
    const hud = document.getElementById('hud');
    gsap.ticker.add(() => {
        const s = bag && bag._debug.state;
        if (!s) return;
        hud.textContent = `scroll ${Math.round(window.scrollY)}\np ${s.p.toFixed(3)}  hero ${s.hero.toFixed(2)}  burst ${s.burst.toFixed(2)}\nh ${Math.round(s.h)}  ry ${s.ry.toFixed(2)}`;
    });
}
