/**
 * "Así se arma": fija la sección mientras el paquete 3D se abre y se llena.
 * El paquete lee este mismo ScrollTrigger (id 'arma'); aquí solo se marca el paso activo,
 * con la misma división en partes iguales que usa el 3D.
 */
export function iniciarArmado(ScrollTrigger) {
    const seccion = document.getElementById('como-se-arma');
    const pasos = [...document.querySelectorAll('[data-paso]')];
    if (!seccion || pasos.length === 0) return;

    let activo = -1;
    const marcar = (indice) => {
        if (indice === activo) return;
        activo = indice;
        pasos.forEach((paso, i) => {
            paso.classList.toggle('activo', i === indice);
            if (i === indice) {
                paso.setAttribute('aria-current', 'step');
            } else {
                paso.removeAttribute('aria-current');
            }
        });
    };

    ScrollTrigger.create({
        id: 'arma',
        trigger: seccion,
        pin: true,
        start: 'top top',
        end: '+=300%',
        anticipatePin: 1,
        onUpdate: (st) => marcar(Math.min(pasos.length - 1, Math.floor(st.progress * pasos.length))),
    });
    marcar(0);
}
