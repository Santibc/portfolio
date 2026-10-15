/**
 * Landing pública de El Pan Dorado DC.
 * Bundle propio: Alpine (menú/tema), GSAP + ScrollTrigger + SplitText (animaciones),
 * Lenis (scroll suave) y un subconjunto curado de Lucide.
 */
import Alpine from 'alpinejs';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import Lenis from 'lenis';
import {
    createIcons,
    ArrowRight, ArrowUpRight, Award, CakeSlice, Camera, Clock, Coffee, Croissant, Heart, HeartHandshake,
    Leaf, MapPin, Menu, Moon, Navigation, Phone, Sparkles, Store, Sun, Sunrise, Wheat, X,
} from 'lucide';

gsap.registerPlugin(ScrollTrigger, SplitText);

// Incluye todos los iconos de App\Enums\IconoLanding (tarjetas de "Por qué elegirnos").
createIcons({
    icons: {
        ArrowRight, ArrowUpRight, Award, CakeSlice, Camera, Clock, Coffee, Croissant, Heart, HeartHandshake,
        Leaf, MapPin, Menu, Moon, Navigation, Phone, Sparkles, Store, Sun, Sunrise, Wheat, X,
    },
});

const html = document.documentElement;
const conMovimiento = window.matchMedia('(prefers-reduced-motion: no-preference)').matches;

/* ---------------- Scroll suave (Lenis) sincronizado con ScrollTrigger ---------------- */
let lenis = null;
if (conMovimiento) {
    // anchors: Lenis respeta el scroll-padding-top del CSS (altura del menú fijo).
    lenis = new Lenis({ lerp: 0.1, anchors: true });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((tiempo) => lenis.raf(tiempo * 1000));
    gsap.ticker.lagSmoothing(0);
}

/* ---------------- Nav: menú móvil + tema (misma clave que el panel) ---------------- */
Alpine.data('navLanding', () => ({
    abierto: false,

    // No se detiene Lenis al abrir el menú: el overlay cubre la pantalla y tiene
    // data-lenis-prevent; detenerlo quita la barra de scroll y reacomoda la página,
    // lo que descuadra el destino de los enlaces.
    alternarMenu() {
        this.abierto = !this.abierto;
    },

    cerrar() {
        this.abierto = false;
    },

    irA(destino) {
        this.cerrar();
        requestAnimationFrame(() => {
            const el = document.querySelector(destino);
            if (!el) return;
            lenis ? lenis.scrollTo(el) : el.scrollIntoView();
            history.replaceState(null, '', destino);
        });
    },

    alternarTema() {
        const tema = html.classList.contains('dark') ? 'light' : 'dark';
        html.classList.toggle('dark', tema === 'dark');
        html.setAttribute('data-theme', tema);
        html.setAttribute('data-bs-theme', tema);
        try { localStorage.setItem('sopas-theme', tema); } catch (e) { /* modo privado */ }
    },
}));

window.Alpine = Alpine;
Alpine.start();

const nav = document.querySelector('[data-nav]');
const marcarNav = () => { if (nav) nav.dataset.scrolled = window.scrollY > 24 ? 'true' : 'false'; };
marcarNav();
window.addEventListener('scroll', marcarNav, { passive: true });

/* ---------------- Animaciones ---------------- */
const mm = gsap.matchMedia();

mm.add('(prefers-reduced-motion: no-preference)', () => {
    // Hero: titular por palabras con máscara, luego el resto.
    const titulo = document.querySelector('[data-hero-titulo]');
    const intro = gsap.timeline({ defaults: { ease: 'expo.out' }, delay: 0.15 });

    if (titulo) {
        gsap.set(titulo, { opacity: 1 });
        SplitText.create(titulo, {
            type: 'lines,words',
            mask: 'lines',
            autoSplit: true,
            onSplit: (self) => gsap.from(self.words, { yPercent: 115, duration: 1.3, stagger: 0.06, ease: 'expo.out', delay: 0.15 }),
        });
    }

    intro
        .fromTo('[data-anim-hero="arco"]',
            { opacity: 1, clipPath: 'inset(100% 0% 0% 0%)' },
            { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.6, ease: 'expo.inOut' }, 0)
        .from('[data-anim-hero="arco"] img', { scale: 1.3, duration: 2.2 }, 0)
        .fromTo('[data-anim-hero="fade"]', { opacity: 0, y: 26 }, { opacity: 1, y: 0, duration: 1.1, stagger: 0.12 }, 0.45)
        .fromTo('[data-anim-hero="pop"]', { opacity: 0, scale: 0.7 }, { opacity: 1, scale: 1, duration: 1, stagger: 0.15, ease: 'back.out(1.6)' }, 1.1);

    // Títulos de sección: palabras que suben desde una máscara.
    gsap.utils.toArray('[data-split]').forEach((el) => {
        SplitText.create(el, {
            type: 'lines,words',
            mask: 'lines',
            autoSplit: true,
            onSplit: (self) => gsap.from(self.words, {
                yPercent: 110,
                duration: 1.1,
                stagger: 0.045,
                ease: 'expo.out',
                scrollTrigger: { trigger: el, start: 'top 86%', once: true },
            }),
        });
    });

    // Bloques: aparecen al entrar en pantalla (en grupo, escalonados).
    ScrollTrigger.batch('[data-anim]', {
        start: 'top 90%',
        once: true,
        onEnter: (elementos) => gsap.fromTo(elementos,
            { opacity: 0, y: 48 },
            { opacity: 1, y: 0, duration: 1.1, ease: 'power3.out', stagger: 0.09, overwrite: true }),
    });

    // Parallax suave de imágenes dentro de su marco.
    gsap.utils.toArray('[data-parallax]').forEach((el) => {
        const v = parseFloat(el.dataset.parallax) || 8;
        gsap.fromTo(el, { yPercent: -v }, {
            yPercent: v,
            ease: 'none',
            scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });

    // Contadores.
    gsap.utils.toArray('[data-contador]').forEach((el) => {
        const fin = Number(el.dataset.contador);
        const valor = { n: 0 };
        el.textContent = '0';
        gsap.to(valor, {
            n: fin,
            duration: 2.2,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 92%', once: true },
            onUpdate: () => { el.textContent = Math.round(valor.n); },
        });
    });
});

// "Del horno a tu mesa": en escritorio la sección se fija y las tarjetas avanzan en horizontal.
mm.add('(prefers-reduced-motion: no-preference) and (min-width: 1024px)', () => {
    const seccion = document.querySelector('[data-horizontal]');
    const marco = seccion?.querySelector('[data-pista-marco]');
    const pista = seccion?.querySelector('[data-pista]');
    if (!seccion || !marco || !pista) return;

    marco.style.overflow = 'hidden';
    const distancia = () => Math.max(0, pista.scrollWidth - marco.clientWidth);

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: seccion,
            pin: true,
            scrub: 0.8,
            start: 'top top',
            end: () => '+=' + distancia(),
            invalidateOnRefresh: true,
        },
    });
    tl.to(pista, { x: () => -distancia(), ease: 'none' }, 0)
      .fromTo('[data-progreso]', { scaleX: 0 }, { scaleX: 1, ease: 'none' }, 0);

    return () => { marco.style.overflow = ''; };
});

// Al llegar las fuentes el texto se reacomoda: recalcular dónde se activa cada animación.
document.fonts?.ready.then(() => ScrollTrigger.refresh());

window.__landingLista = true;
