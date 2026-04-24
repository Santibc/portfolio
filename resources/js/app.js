import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

// Theme toggle logic — se ejecuta ANTES de Alpine para evitar flash
(function () {
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = stored || (prefersDark ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', theme === 'dark');
})();

// Alpine data store global para el tema
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        current: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        toggle() {
            this.current = this.current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.current);
            document.documentElement.classList.toggle('dark', this.current === 'dark');
        },
    });

    Alpine.store('sidebar', {
        open: window.innerWidth >= 1024,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    });
});

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();
