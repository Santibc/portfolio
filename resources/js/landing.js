/**
 * Landing pública de Dorilokos Mix (bundle propio: no carga app.js).
 * Scroll suave con Lenis + ScrollTrigger, menú móvil, cinta de sabores, botón flotante
 * y carga diferida del paquete 3D (resources/js/landing/bag).
 */
import './theme-toggle';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { iniciarIconos } from './landing/iconos';
import { iniciarMenuMovil } from './landing/menu-movil';
import { iniciarCinta } from './landing/cinta';
import { iniciarArmado } from './landing/armado';
import { iniciarBotonFlotante } from './landing/boton-flotante';
import { cargarPaquete, puedeMostrar3d } from './landing/paquete';

gsap.registerPlugin(ScrollTrigger);
ScrollTrigger.config({ ignoreMobileResize: true });

const html = document.documentElement;
const menosMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

iniciarIconos();

let lenis = null;
if (!menosMovimiento) {
    lenis = new Lenis({ anchors: true, stopInertiaOnNavigate: true });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((tiempo) => lenis.raf(tiempo * 1000));
    gsap.ticker.lagSmoothing(0);
    iniciarCinta(gsap);
}

iniciarMenuMovil(lenis);
iniciarBotonFlotante();

// El <head> ya marcó .con-3d si no hay reduced motion; aquí se confirma que el equipo puede con WebGL.
if (html.classList.contains('con-3d') && puedeMostrar3d()) {
    iniciarArmado(ScrollTrigger);
    cargarPaquete({
        gsap,
        ScrollTrigger,
        alFallar() {
            html.classList.remove('con-3d', 'bag-listo');
            ScrollTrigger.getById('arma')?.kill(true);
            ScrollTrigger.refresh();
        },
    });
} else {
    html.classList.remove('con-3d');
}

// Las fuentes y las imágenes cambian alturas: recalcular las posiciones del scroll.
document.fonts?.ready.then(() => ScrollTrigger.refresh());
window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
