import colors from 'tailwindcss/colors';
import base from './tailwind.config.js';

// Canales RGB sueltos de un color ("27 13 31") para las sombras con alfa variable de
// landing.css: rgb(var(--sombra) / 0.3). Así --sombra sigue al token en vez de copiarlo.
const canales = (hex) => {
  const h = hex.replace('#', '').replace(/^(.)(.)(.)$/, '$1$1$2$2$3$3');
  return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16)).join(' ');
};

/**
 * Tailwind solo para la landing pública (resources/css/landing.css lo carga con @config).
 * Hereda tokens de marca y plugins de la app, pero escanea únicamente las vistas de la
 * landing y los componentes Blade, así su CSS no arrastra las clases del panel.
 */
export default {
  ...base,
  content: [
    './resources/views/landing/**/*.blade.php',
    './resources/views/layouts/landing.blade.php',
    // Solo los componentes que usa la landing (los demás traen clases de Preline/Alpine).
    './resources/views/components/{button,icon,brand-icon,badge}.blade.php',
    './resources/js/landing.js',
    './resources/js/landing/*.js',
  ],
  theme: {
    ...base.theme,
    extend: {
      ...base.theme.extend,
      fontFamily: {
        ...base.theme.extend.fontFamily,
        sans: ['"Archivo Variable"', 'Archivo', 'system-ui', 'sans-serif'],
        brand: ['"Lilita One"', '"Archivo Variable"', 'system-ui', 'sans-serif'],
      },
      screens: {
        xs: '420px',
      },
      canales: {
        'primary-950': canales(base.theme.extend.colors.primary[950]),
        black: canales(colors.black),
      },
    },
  },
};
