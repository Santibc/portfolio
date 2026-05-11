/**
 * Sopas y Sopitas - Theme Toggle (Dark Mode)
 *
 * Mecanismo:
 *   - data-theme="dark" en <html>: activa CSS variables custom y overrides Tailwind
 *   - data-bs-theme="dark" en <html>: activa Bootstrap 5.3 dark mode nativo
 *   - clase "dark" en <html>: activa Tailwind darkMode: 'class'
 *
 * Persistencia:
 *   - localStorage 'sopas-theme': 'light' | 'dark' | 'auto'
 *   - PATCH /profile/theme: sincroniza con BD (solo autenticado)
 *
 * El bootstrap inicial (anti-FOUC) ya se aplica desde un <script> inline
 * en el <head> del layout. Este archivo gestiona el toggle y la sincronización.
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'sopas-theme';
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

    /**
     * Aplica el tema al <html> (los 3 mecanismos a la vez).
     * @param {'light'|'dark'} resolved Tema resuelto efectivo (no 'auto').
     */
    function applyTheme(resolved) {
        const html = document.documentElement;

        if (resolved === 'dark') {
            html.setAttribute('data-theme', 'dark');
            html.setAttribute('data-bs-theme', 'dark');
            html.classList.add('dark');
        } else {
            html.setAttribute('data-theme', 'light');
            html.setAttribute('data-bs-theme', 'light');
            html.classList.remove('dark');
        }

        // Habilitar/deshabilitar tema oscuro de Flatpickr
        const flatpickrDark = document.getElementById('flatpickrDarkTheme');
        if (flatpickrDark) {
            flatpickrDark.disabled = (resolved !== 'dark');
        }

        // Notificar a otros componentes (Swal, etc.) por si necesitan reaccionar
        window.dispatchEvent(new CustomEvent('sopas:theme-changed', {
            detail: { theme: resolved }
        }));
    }

    /**
     * Resuelve la preferencia (light/dark/auto) a un tema efectivo (light/dark).
     */
    function resolveTheme(preference) {
        if (preference === 'light' || preference === 'dark') return preference;
        return mediaQuery.matches ? 'dark' : 'light';
    }

    /**
     * Lee la preferencia actual desde localStorage.
     * Si no existe, retorna 'auto'.
     */
    function getPreference() {
        return localStorage.getItem(STORAGE_KEY) || 'auto';
    }

    /**
     * Guarda preferencia en localStorage y opcionalmente en BD.
     */
    function setPreference(preference) {
        localStorage.setItem(STORAGE_KEY, preference);
        applyTheme(resolveTheme(preference));
        syncToServer(preference);
    }

    /**
     * Sincroniza la preferencia con el servidor (si el usuario está autenticado).
     */
    function syncToServer(preference) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) return; // No autenticado o pantalla guest

        fetch('/profile/theme', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ theme: preference }),
            credentials: 'same-origin',
        }).catch(function () {
            // Silenciar - localStorage ya guardó la preferencia
        });
    }

    /**
     * Toggle: light -> dark -> light. Mantenemos simple (sin estado 'auto' en el ciclo).
     */
    function toggleTheme() {
        const current = getPreference();
        const resolved = resolveTheme(current);
        const next = resolved === 'dark' ? 'light' : 'dark';
        setPreference(next);
    }

    // Reaccionar a cambios del SO solo si la preferencia es 'auto'
    mediaQuery.addEventListener('change', function () {
        if (getPreference() === 'auto') {
            applyTheme(resolveTheme('auto'));
        }
    });

    // Bind del botón y sincronización inicial de Flatpickr al DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('themeToggleBtn');
        if (btn) {
            btn.addEventListener('click', toggleTheme);
        }
        // Sincronizar Flatpickr (el link recién existe ahora)
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const flatpickrDark = document.getElementById('flatpickrDarkTheme');
        if (flatpickrDark) flatpickrDark.disabled = !isDark;
    });

    // Exponer API para uso programático
    window.SopasTheme = {
        toggle: toggleTheme,
        set: setPreference,
        get: getPreference,
        resolved: function () { return resolveTheme(getPreference()); },
    };
})();
