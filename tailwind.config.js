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
        brand: ['Caveat', 'cursive'],
      },

      colors: {
        // Naranja Papas del Alma (primary) — fondo del logo
        primary: {
          50:  '#fff5ed',
          100: '#ffe8d4',
          200: '#fecca8',
          300: '#fda571',
          400: '#f87a38',
          500: '#e4550a', // base
          600: '#c9460a',
          700: '#a6360c',
          800: '#852e12',
          900: '#6c2912',
          950: '#3b1207',
        },

        // Dorado papa / caramelo (accent)
        accent: {
          50:  '#fcf8ef',
          100: '#f8eed6',
          200: '#f0dcab',
          300: '#e7c57c',
          400: '#dfb266',
          500: '#d8a654', // base
          600: '#c08937',
          700: '#a4672a',
          800: '#865227',
          900: '#6e4424',
          950: '#3d2311',
        },

        // Crema / café tostado (surfaces y texto)
        cream: {
          50:  '#fffaf3',
          100: '#fcf2e3',
          200: '#f5e6cd',
          300: '#ecd6b3',
          400: '#dcbf98',
          500: '#c9ad91',
          600: '#9e8068',
          700: '#735a48',
          800: '#4f3a2c',
          900: '#3a2718',
          950: '#22160c',
        },

        // Surface base (light/dark)
        surface: {
          DEFAULT: '#fffaf3',
          dark: '#1b120a',
        },
      },

      boxShadow: {
        soft: '0 4px 24px -4px rgb(0 0 0 / 0.08), 0 2px 8px -2px rgb(0 0 0 / 0.04)',
        'soft-lg': '0 12px 40px -8px rgb(0 0 0 / 0.12), 0 4px 16px -4px rgb(0 0 0 / 0.06)',
        glow: '0 0 0 4px rgb(228 85 10 / 0.18)',
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
