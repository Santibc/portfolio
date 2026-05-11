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
        // Oliva / mostaza (primary)
        primary: {
          50:  '#f7faea',
          100: '#ecf2c1',
          200: '#d6e285',
          300: '#c8d62e',
          400: '#bbcb1c',
          500: '#aab808', // base
          600: '#949f06',
          700: '#838c00',
          800: '#5d6716',
          900: '#3d4310',
          950: '#2a2e0a',
        },

        // Camel / wheat (accent)
        accent: {
          50:  '#fbf6ed',
          100: '#f4ead0',
          200: '#e2caa1',
          300: '#d2b27c',
          400: '#c2a07d',
          500: '#b89875', // base
          600: '#a07e5b',
          700: '#85664a',
          800: '#6b513c',
          900: '#574232',
          950: '#2f2218',
        },

        // Cream / cafe / beige (surfaces)
        cream: {
          50:  '#fffdfa',
          100: '#fbf5e9',
          200: '#f5e9d2',
          300: '#efdfc0',
          400: '#e2caa1',
          500: '#d7ccc8',
          600: '#a1887f',
          700: '#75605a',
          800: '#4e342e',
          900: '#3e2723',
          950: '#241410',
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
        glow: '0 0 0 4px rgb(170 184 8 / 0.15)',
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
