/** La cinta de sabores avanza con el scroll (no se mueve sola: nada que pausar). */
export function iniciarCinta(gsap) {
    const pista = document.querySelector('[data-cinta]');
    if (!pista) return;

    gsap.fromTo(pista, { xPercent: 0 }, {
        xPercent: -28,
        ease: 'none',
        scrollTrigger: { trigger: pista, start: 'top bottom', end: 'bottom top', scrub: true },
    });
}
