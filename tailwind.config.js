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
        // Bronce / caramelo del logo El Pan Dorado (primary)
        primary: {
          50:  '#fcf5ee',
          100: '#f8e9d8',
          200: '#eed1b4',
          300: '#e0b185',
          400: '#cb884d',
          500: '#9d612b', // base: "El Pan Dorado" del logo
          600: '#834d21',
          700: '#6d3e18',
          800: '#522f14',
          900: '#3b2311',
          950: '#24140a',
        },

        // Naranja pastelería del logo (accent)
        accent: {
          50:  '#fef6eb',
          100: '#fde9ce',
          200: '#fbd19d',
          300: '#f8b562',
          400: '#f89e30',
          500: '#f7931e', // base: "Panadería & Pastelería" del logo
          600: '#da730b',
          700: '#a0500d',
          800: '#814112',
          900: '#5f3011',
          950: '#341909',
        },

        // Crema / miga / corteza (surfaces)
        cream: {
          50:  '#fffdfa',
          100: '#fbf5e9',
          200: '#f6ead5',
          300: '#eddabb',
          400: '#dbbf94',
          500: '#c1ac90',
          600: '#8f775b',
          700: '#6c5742',
          800: '#493727',
          900: '#342518',
          950: '#1d130c',
        },

        // Oro de la cinta "Calidad & Servicio" (detalles decorativos)
        gold: {
          100: '#faf5d3',
          300: '#f2e689',
          500: '#d4b45a',
          700: '#a88040',
        },

        // Surface base (light/dark)
        surface: {
          DEFAULT: '#fffdfa',
          dark: '#1a1610',
        },
      },

      boxShadow: {
        soft: '0 4px 24px -4px rgb(0 0 0 / 0.08), 0 2px 8px -2px rgb(0 0 0 / 0.04)',
        'soft-lg': '0 12px 40px -8px rgb(0 0 0 / 0.12), 0 4px 16px -4px rgb(0 0 0 / 0.06)',
        glow: '0 0 0 4px rgb(157 97 43 / 0.15)',
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
