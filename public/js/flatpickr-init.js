/**
 * Inicializador global de Flatpickr.
 * Reemplaza todos los input[type="date"] por un calendario unificado con locale es,
 * valor Y-m-d para el backend y display d/m/Y. Sincroniza el tema oscuro con html.dark.
 */
(function () {
    'use strict';

    if (typeof flatpickr === 'undefined') {
        console.warn('[flatpickr-init] Flatpickr no est\u00e1 cargado.');
        return;
    }

    if (flatpickr.l10ns && flatpickr.l10ns.es) {
        flatpickr.localize(flatpickr.l10ns.es);
    }

    var FP_ATTR = 'data-fp-initialized';

    function buildConfig(input) {
        var cfg = {
            locale: 'es',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            disableMobile: true
        };
        if (input.getAttribute('min')) cfg.minDate = input.getAttribute('min');
        if (input.getAttribute('max')) cfg.maxDate = input.getAttribute('max');
        return cfg;
    }

    function initAll(root) {
        var scope = root || document;
        var inputs = scope.querySelectorAll('input[type="date"]:not([' + FP_ATTR + '])');
        inputs.forEach(function (input) {
            try {
                flatpickr(input, buildConfig(input));
                input.setAttribute(FP_ATTR, '1');
            } catch (e) {
                console.error('[flatpickr-init] error inicializando input', input, e);
            }
        });
    }

    function syncDarkTheme() {
        var link = document.getElementById('flatpickrDarkTheme');
        if (!link) return;
        var isDark = document.documentElement.classList.contains('dark');
        link.disabled = !isDark;
    }

    window.initFlatpickrAll = initAll;

    function onReady() {
        initAll();
        syncDarkTheme();

        var htmlEl = document.documentElement;
        var observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                if (mutations[i].attributeName === 'class') {
                    syncDarkTheme();
                    break;
                }
            }
        });
        observer.observe(htmlEl, { attributes: true, attributeFilter: ['class'] });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }
})();
