{{--
    Equipo con teclado físico (se marca en Caja → Impresora y queda guardado en este Chrome): los
    campos se marcan con inputmode="none" al tocarlos para que Chrome no abra el teclado de la
    pantalla. En una tablet Chrome lo abre aunque haya un teclado conectado.
--}}
<script>
    window.tecladoFisico = (() => {
        const CLAVE = 'equipo-teclado-fisico';
        const CAMPOS = 'input, textarea, [contenteditable]';
        const activo = () => { try { return localStorage.getItem(CLAVE) === '1'; } catch (e) { return false; } };

        const marcar = (evento) => {
            const campo = evento.target.closest?.(CAMPOS);
            if (!campo || !activo() || campo.hasAttribute('data-inputmode-antes')) return;
            campo.setAttribute('data-inputmode-antes', campo.getAttribute('inputmode') ?? '');
            campo.setAttribute('inputmode', 'none');
        };
        // pointerdown: antes de que el campo tome el foco. focusin: los que se enfocan sin tocarlos.
        document.addEventListener('pointerdown', marcar, true);
        document.addEventListener('focusin', marcar, true);

        return {
            activo,
            guardar(conTeclado) {
                try { conTeclado ? localStorage.setItem(CLAVE, '1') : localStorage.removeItem(CLAVE); } catch (e) {}
                if (conTeclado) return;
                document.querySelectorAll('[data-inputmode-antes]').forEach((campo) => {
                    const antes = campo.getAttribute('data-inputmode-antes');
                    antes ? campo.setAttribute('inputmode', antes) : campo.removeAttribute('inputmode');
                    campo.removeAttribute('data-inputmode-antes');
                });
            },
        };
    })();
</script>
