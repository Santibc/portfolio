/** Menú móvil en <dialog> modal: atrapa el foco, cierra con Esc y devuelve el foco al botón. */
export function iniciarMenuMovil(lenis) {
    const dialogo = document.getElementById('menu-movil');
    const abrir = document.querySelector('[data-menu-abrir]');
    if (!dialogo || !abrir || typeof dialogo.showModal !== 'function') return;

    abrir.addEventListener('click', () => {
        dialogo.showModal();
        abrir.setAttribute('aria-expanded', 'true');
        lenis?.stop();
    });

    dialogo.querySelector('[data-menu-cerrar]')?.addEventListener('click', () => dialogo.close());

    dialogo.addEventListener('close', () => {
        abrir.setAttribute('aria-expanded', 'false');
        lenis?.start();
    });

    dialogo.querySelectorAll('[data-menu-enlace]').forEach((enlace) => {
        enlace.addEventListener('click', (evento) => {
            const destino = document.querySelector(enlace.getAttribute('href'));
            if (!destino) return;
            evento.preventDefault();
            dialogo.close();
            // scroll-padding-top del <html> deja el destino debajo de la barra fija.
            if (lenis) {
                lenis.scrollTo(destino);
            } else {
                destino.scrollIntoView();
            }
        });
    });
}
