/** El botón flotante de WhatsApp se esconde mientras haya otro botón de pedido a la vista (inicio, menú, cierre). */
export function iniciarBotonFlotante() {
    const boton = document.querySelector('[data-wa-flotante]');
    const vigilados = [document.getElementById('cta-inicio'), document.querySelector('#menu .panel-nutri'), document.querySelector('#pedir .corte')].filter(Boolean);
    if (!boton || vigilados.length === 0 || !('IntersectionObserver' in window)) return;

    const visibles = new Set();
    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach((entrada) => {
            if (entrada.isIntersecting) {
                visibles.add(entrada.target);
            } else {
                visibles.delete(entrada.target);
            }
        });
        boton.classList.toggle('oculto', visibles.size > 0);
    });

    vigilados.forEach((elemento) => observador.observe(elemento));
}
