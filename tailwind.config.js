/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './node_modules/preline/dist/*.js',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
        display: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
        brand: ['"Lilita One"', '"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },

      colors: {
        // Morado DorilokosMix (primary) — 500 = morado del logo
        primary: {
          50:  '#f6f1f8',
          100: '#ece1f0',
          200: '#d9c2e1',
          300: '#bd97ca',
          400: '#8f5aa3',
          500: '#5f306a', // base
          600: '#532a5d',
          700: '#46234f',
          800: '#381c3f',
          900: '#2a152f',
          950: '#1b0d1f',
        },

        // Fuego naranja / dorado (accent) — 500 = naranja del logo
        accent: {
          50:  '#fff8eb',
          100: '#ffecc6',
          200: '#fdd889',
          300: '#f5c020',
          400: '#f5a524',
          500: '#f39030', // base
          600: '#dc6f14',
          700: '#b65112',
          800: '#934016',
          900: '#783616',
          950: '#451a07',
        },

        // Rojo "MIX" (resaltes, badges llamativos, degradado fuego)
        flame: {
          50:  '#fef2f2',
          100: '#fde3e3',
          200: '#fbcbcc',
          300: '#f7a4a6',
          400: '#f06d70',
          500: '#e3262a', // base
          600: '#c01a1e',
          700: '#9f171b',
          800: '#83181c',
          900: '#6d191c',
          950: '#3b080a',
        },

        // Neutros con tinte lavanda (surfaces, textos, bordes)
        cream: {
          50:  '#fcfbfd',
          100: '#f5f2f7',
          200: '#ebe5ef',
          300: '#ddd4e3',
          400: '#c3b6cc',
          500: '#a497ae',
          600: '#7d7087',
          700: '#5c5066',
          800: '#3e3447',
          900: '#2a2231',
          950: '#19131e',
        },

        // Surface base (light/dark)
        surface: {
          DEFAULT: '#fcfbfd',
          dark: '#140f18',
        },
      },

      boxShadow: {
        soft: '0 4px 24px -4px rgb(0 0 0 / 0.08), 0 2px 8px -2px rgb(0 0 0 / 0.04)',
        'soft-lg': '0 12px 40px -8px rgb(0 0 0 / 0.12), 0 4px 16px -4px rgb(0 0 0 / 0.06)',
        glow: '0 0 0 4px rgb(243 144 48 / 0.25)',
      },

      borderRadius: {
        '4xl': '2rem',
      },

      keyframes: {
        'fade-in-up': {
          '0%': { opacity: '0', transform: 'translateY(8px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'soft-pop': {
          '0%': { opacity: '0', transform: 'scale(0.96)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
      },
      animation: {
        'fade-in-up': 'fade-in-up 0.45s ease-out both',
        'soft-pop': 'soft-pop 0.35s ease-out both',
      },
    },
  },

  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),

    // Variantes de Preline UI 4 (Tailwind v3 — replicadas manualmente porque
    // preline/variants.css usa la sintaxis nueva @custom-variant de Tailwind v4)
    require('tailwindcss/plugin')(function ({ addVariant }) {
      // Overlay / modal
      addVariant('hs-overlay-open', ['&.open', '.open &']);
      addVariant('hs-overlay-backdrop-open', ['&.open', '.open &']);
      addVariant('hs-overlay-layout-open', ['&.opened', '.opened &']);

      // Dropdown
      addVariant('hs-dropdown-open', ['&.open', '.open &']);

      // Accordion
      addVariant('hs-accordion-active', ['&.active', '.active &']);
      addVariant('hs-accordion-selected', ['&.selected', '.selected &']);

      // Tabs
      addVariant('hs-tab-active', ['&.active', '.active &']);

      // Collapse
      addVariant('hs-collapse-open', ['&.open', '.open &']);

      // Tooltip
      addVariant('hs-tooltip-shown', ['&.show', '.show &']);

      // Combobox / select / range slider
      addVariant('hs-combo-box-has-value', ['&.has-value', '.has-value &']);
      addVariant('hs-combo-box-tab-active', ['&.active', '.active &']);
      addVariant('hs-select-disabled', ['&.disabled', '.disabled &']);

      // Stepper
      addVariant('hs-stepper-active', ['&.active', '.active &']);
      addVariant('hs-stepper-success', ['&.success', '.success &']);
      addVariant('hs-stepper-completed', ['&.completed', '.completed &']);
      addVariant('hs-stepper-error', ['&.error', '.error &']);
      addVariant('hs-stepper-processed', ['&.processed', '.processed &']);
      addVariant('hs-stepper-skipped', ['&.skipped', '.skipped &']);
      addVariant('hs-stepper-disabled', ['&.disabled', '.disabled &']);

      // Pin input / strong password / file upload
      addVariant('hs-pin-input-completed', ['&.completed', '.completed &']);
      addVariant('hs-strong-password-active', ['&.active', '.active &']);
      addVariant('hs-strong-password-accepted', ['&.accepted', '.accepted &']);
      addVariant('hs-file-upload-complete', ['&.complete', '.complete &']);
      addVariant('hs-file-upload-pending', ['&.pending', '.pending &']);

      // Removing element / drag
      addVariant('hs-removing', ['&.hs-removing', '.hs-removing &']);
      addVariant('hs-dragged', ['&.dragged', '.dragged &']);
    }),
  ],
};
