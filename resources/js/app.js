import './bootstrap';

// Alpine.js (interactividad ligera)
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Preline UI (componentes vanilla JS sobre Tailwind)
// El import side-effect deja window.HSStaticMethods disponible.
// Importamos tambien por nombre para forzar a Vite a NO tree-shake los side effects.
import { HSStaticMethods } from 'preline';
window.HSStaticMethods = HSStaticMethods;

// SweetAlert2 (alerts/toasts)
import Swal from 'sweetalert2';
window.Swal = Swal;

// TomSelect (selects bonitos)
import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

// ApexCharts — eager (lo necesitamos disponible para el componente <x-chart>)
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

// Helper global para crear graficas con paleta del proyecto + reactividad al tema
window.makeChart = (selector, options) => {
    const el = document.querySelector(selector);
    if (!el) return null;

    const isDark = () => document.documentElement.classList.contains('dark');
    if (isDark()) {
        options.theme = { ...(options.theme || {}), mode: 'dark' };
        options.chart = { ...(options.chart || {}), background: 'transparent' };
        options.grid = { ...(options.grid || {}), borderColor: '#75605a' };
    }

    const chart = new ApexCharts(el, options);
    chart.render();

    // Re-medir el ancho cuando la card cambia de tamaño (resize de ventana,
    // toggle del sidebar, breakpoints). Evita que ApexCharts mantenga un ancho
    // fijo en px medido antes de que el layout se asentara y desborde la card.
    if ('ResizeObserver' in window) {
        const container = el.parentElement || el;
        let raf = null;
        let lastWidth = container.clientWidth;
        const ro = new ResizeObserver(() => {
            const width = container.clientWidth;
            if (width === lastWidth) return;
            lastWidth = width;
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                chart.updateOptions({ chart: { width: '100%' } }, false, false);
            });
        });
        ro.observe(container);
    }

    window.addEventListener('sopas:theme-changed', (e) => {
        const dark = e.detail.theme === 'dark';
        chart.updateOptions({
            theme: { mode: dark ? 'dark' : 'light' },
            chart: { background: 'transparent' },
            grid: { borderColor: dark ? '#75605a' : '#efdfc0' },
        }, false, false);
    });

    return chart;
};

// Lucide icons — import curado
import {
    createIcons,
    Activity, AlertCircle, AlertTriangle, Archive, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight,
    Banknote, BarChart3, Bell,
    Calculator, Calendar, Camera, ChefHat, Check, CheckCircle, CheckCircle2, ChevronDown, ChevronLeft, ChevronRight, ChevronUp,
    ClipboardList, Clock, Cloud, Coffee, Component, Copy, CreditCard,
    DollarSign, Download,
    Edit, ExternalLink, Eye, EyeOff,
    Gauge,
    Heart, Home,
    Image, Inbox, Info,
    Key,
    Layers, List, ListChecks, LoaderCircle, Lock, LogIn, LogOut,
    Mail, MapPin, Menu, MessageCircle, Minus, Moon, MousePointerClick,
    Phone, PieChart, PiggyBank, Play, Plus,
    Receipt, RotateCcw,
    Save, Search, Send, Settings, ShieldCheck, ShoppingBag, ShoppingBasket, ShoppingCart, SkipForward, Soup, Sparkles, Square, SquareStack, Star, Sun,
    Table, Tag, TextCursorInput, Trash2, TrendingDown, TrendingUp, Type,
    Unlock, Upload, User, UserCheck, UserCog, UserPlus, Users, UtensilsCrossed,
    Wallet, Wifi,
    X,
    Zap,
} from 'lucide';

const usedIcons = {
    Activity, AlertCircle, AlertTriangle, Archive, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight,
    Banknote, BarChart3, Bell,
    Calculator, Calendar, Camera, ChefHat, Check, CheckCircle, CheckCircle2, ChevronDown, ChevronLeft, ChevronRight, ChevronUp,
    ClipboardList, Clock, Cloud, Coffee, Component, Copy, CreditCard,
    DollarSign, Download,
    Edit, ExternalLink, Eye, EyeOff,
    Gauge,
    Heart, Home,
    Image, Inbox, Info,
    Key,
    Layers, List, ListChecks, LoaderCircle, Lock, LogIn, LogOut,
    Mail, MapPin, Menu, MessageCircle, Minus, Moon, MousePointerClick,
    Phone, PieChart, PiggyBank, Play, Plus,
    Receipt, RotateCcw,
    Save, Search, Send, Settings, ShieldCheck, ShoppingBag, ShoppingBasket, ShoppingCart, SkipForward, Soup, Sparkles, Square, SquareStack, Star, Sun,
    Table, Tag, TextCursorInput, Trash2, TrendingDown, TrendingUp, Type,
    Unlock, Upload, User, UserCheck, UserCog, UserPlus, Users, UtensilsCrossed,
    Wallet, Wifi,
    X,
    Zap,
};

// ScrollReveal (animaciones on-scroll)
import ScrollReveal from 'scrollreveal';

// Theme toggle (light/dark/auto + persistencia BD + localStorage)
import './theme-toggle';

const renderIcons = () => createIcons({ icons: usedIcons, attrs: { 'stroke-width': 1.75 } });
// Exponer para que los componentes (p.ej. <x-data-table>) fuercen el re-render de iconos
// despues de paginar/filtrar/ordenar, sin depender solo del MutationObserver global.
window.renderIcons = renderIcons;

const initTomSelect = (root = document) => {
    root.querySelectorAll('select[data-tom-select]').forEach((el) => {
        if (el.tomselect) return;
        // Si hay <option value=""> con texto, usamos ese texto como placeholder y lo quitamos como opcion
        const emptyOpt = el.querySelector('option[value=""]');
        let placeholder = el.dataset.placeholder || '';
        if (emptyOpt && !placeholder) {
            placeholder = emptyOpt.textContent.trim();
        }
        new TomSelect(el, {
            placeholder: placeholder || undefined,
            allowEmptyOption: false,
            create: false,
            dropdownParent: 'body',  // escapar overflow de cards/modales
        });
    });
};

// Surface JS errors visibly (en consola, no romper UI silenciosamente)
window.addEventListener('error', (e) => {
    console.error('[sopas] error global:', e.error || e.message);
});
window.addEventListener('unhandledrejection', (e) => {
    console.error('[sopas] promise rechazada:', e.reason);
});

const initPreline = () => {
    if (window.HSStaticMethods?.autoInit) {
        try { window.HSStaticMethods.autoInit(); }
        catch (err) { console.error('[sopas] Preline autoInit fallo:', err); }
    } else {
        console.warn('[sopas] window.HSStaticMethods no esta disponible');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    renderIcons();
    initTomSelect();
    initPreline();

    // ScrollReveal: entrada suave para data-reveal
    ScrollReveal({
        distance: '20px',
        duration: 600,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        origin: 'bottom',
        reset: false,
        opacity: 0,
        viewFactor: 0.1,
    }).reveal('[data-reveal]', { interval: 80 });

    // Re-renderizar iconos + re-init Preline cuando se inserten nodos dinamicamente.
    // Debounced + reentry-safe: evita loops cuando Alpine y el observer se pelean
    // por los mismos nodos (p.ej. <template x-for> que re-monta items con <i data-lucide>).
    let scheduled = false;
    let running = false;
    let needsIcons = false;
    let needsPreline = false;
    let pendingRescan = false;

    // Marca needsIcons/needsPreline segun los nodos insertados. Setear flags es idempotente
    // y barato, asi que es seguro escanear siempre (incluso mutaciones del propio flush).
    const scanNodes = (mutations) => {
        for (const m of mutations) {
            for (const n of m.addedNodes) {
                if (n.nodeType !== 1) continue;
                if (!needsIcons && (n.querySelector?.('[data-lucide]') || n.matches?.('[data-lucide]'))) {
                    needsIcons = true;
                }
                if (!needsPreline && (n.querySelector?.('[data-hs-overlay], [class*="hs-"]') || n.matches?.('[data-hs-overlay]'))) {
                    needsPreline = true;
                }
                if (needsIcons && needsPreline) break;
            }
            if (needsIcons && needsPreline) break;
        }
    };

    const schedule = () => {
        if ((needsIcons || needsPreline) && !scheduled) {
            scheduled = true;
            requestAnimationFrame(flush);
        }
    };

    const flush = () => {
        scheduled = false;
        if (running) return;
        running = true;
        try {
            if (needsIcons) { needsIcons = false; renderIcons(); }
            if (needsPreline && window.HSStaticMethods?.autoInit) {
                needsPreline = false;
                window.HSStaticMethods.autoInit();
            }
        } finally {
            running = false;
        }
        // Re-procesar mutaciones llegadas mientras corria el flush (p.ej. Alpine repintando
        // una pagina nueva con <i data-lucide>) en vez de descartarlas. Converge porque
        // renderIcons/autoInit son idempotentes y no dejan nodos pendientes nuevos.
        if (pendingRescan) {
            pendingRescan = false;
            const queued = observer.takeRecords();
            if (queued.length) scanNodes(queued);
            schedule();
        }
    };

    const observer = new MutationObserver((mutations) => {
        if (running) {
            // No descartar: re-escanear al terminar el flush en curso.
            pendingRescan = true;
            return;
        }
        scanNodes(mutations);
        schedule();
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Confirmacion global con SweetAlert: reemplaza confirm() nativo
    window.swalConfirm = (form, opts = {}) => {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: opts.title || '¿Continuar?',
            text: opts.text || '',
            icon: opts.icon || 'question',
            showCancelButton: true,
            confirmButtonText: opts.confirmButtonText || 'Sí, continuar',
            cancelButtonText: opts.cancelButtonText || 'Cancelar',
            confirmButtonColor: opts.confirmButtonColor || '#aab808',
            cancelButtonColor: '#75605a',
            background: isDark ? '#1a1610' : '#fffdfa',
            color: isDark ? '#fbf5e9' : '#3e2723',
            customClass: { popup: 'rounded-2xl' },
        }).then((result) => {
            if (result.isConfirmed && form) form.submit();
        });
        return false;
    };

    // Toast helper global
    window.showToast = (icon, title) => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1a1610' : '#fffdfa',
            color: document.documentElement.classList.contains('dark') ? '#fbf5e9' : '#3e2723',
        });
        Toast.fire({ icon, title });
    };
});

// Componente reutilizable para realzar una tabla Blade existente (con sus modales, forms y
// footer intactos) agregando busqueda global, filtros por columna, orden y paginacion EN
// CLIENTE, operando sobre las <tr> reales del DOM: pagina ocultando filas (style.display) y
// ordena moviendo nodos (appendChild) — nunca re-renderiza, asi se conservan listeners, forms
// DELETE y los iconos ya convertidos. Marca en la tabla del modulo: tbody[data-enhance],
// tr[data-row] en las filas de datos, y @click="sortBy(i)" en los <th> ordenables (i = indice
// de columna, base 0). Footers en <tfoot> (fuera del tbody) quedan intactos.
window.tableEnhanced = (cfg = {}) => ({
    search: '',
    page: 1,
    perPage: cfg.perPage ?? 5,
    sortIdx: null,
    sortAsc: true,
    filterDefs: cfg.filters || [],   // [{ col: 2, label: 'Estado' }]
    activeFilters: {},
    rowsMeta: [],
    tbody: null,
    _reordered: false,
    _norm(s) { return (s || '').replace(/\s+/g, ' ').trim(); },
    // Parsea numeros es-CO ('.' miles, ',' decimal) tolerando '$'/unidades; NaN para texto/fechas.
    _num(s) {
        const t = String(s).trim();
        if (!/\d/.test(t) || !/^-?\s*\$?\s*[\d.,]+\s*[a-zA-Z%º°]{0,5}\.?$/.test(t)) return NaN;
        const c = t.replace(/[^\d.,-]/g, '').replace(/\./g, '').replace(',', '.');
        return (c === '' || c === '-') ? NaN : parseFloat(c);
    },
    init() {
        this.tbody = this.$root.querySelector('tbody[data-enhance]');
        if (!this.tbody) return;
        const trs = [...this.tbody.querySelectorAll(':scope > tr[data-row]')];
        this.rowsMeta = trs.map((tr) => ({
            el: tr,
            text: this._norm(tr.textContent).toLowerCase(),
            cells: [...tr.children].map((td) => this._norm(td.textContent)),
        }));
        for (const f of this.filterDefs) {
            if (!(f.col in this.activeFilters)) this.activeFilters[f.col] = '';
        }
        this.$watch('search', () => { this.page = 1; this.apply(); });
        this.$watch('perPage', () => { this.page = 1; this.apply(); });
        this.$watch('activeFilters', () => { this.page = 1; this.apply(); });
        this.apply();
    },
    get filterOptions() {
        const out = {};
        for (const f of this.filterDefs) {
            const set = new Set();
            this.rowsMeta.forEach((r) => { const v = r.cells[f.col]; if (v) set.add(v); });
            out[f.col] = [...set].sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));
        }
        return out;
    },
    get visibleRows() {
        const q = this.search.toLowerCase();
        return this.rowsMeta.filter((r) =>
            (!q || r.text.includes(q)) &&
            this.filterDefs.every((f) => !this.activeFilters[f.col] || r.cells[f.col] === this.activeFilters[f.col]));
    },
    get effPerPage() { return this.perPage === 0 ? Math.max(this.visibleRows.length, 1) : this.perPage; },
    get pages() { return Math.max(1, Math.ceil(this.visibleRows.length / this.effPerPage)); },
    goTo(p) { this.page = Math.min(this.pages, Math.max(1, p)); this.apply(); },
    sortBy(idx) {
        if (this.sortIdx === idx) this.sortAsc = !this.sortAsc;
        else { this.sortIdx = idx; this.sortAsc = true; }
        this.page = 1;
        this.apply();
    },
    apply() {
        if (!this.tbody) return;
        if (this.page > this.pages) this.page = this.pages;
        let ordered = this.visibleRows;
        if (this.sortIdx !== null) {
            const k = this.sortIdx, dir = this.sortAsc ? 1 : -1;
            ordered = ordered.slice().sort((a, b) => {
                const x = a.cells[k] ?? '', y = b.cells[k] ?? '';
                const nx = this._num(x), ny = this._num(y);
                if (!isNaN(nx) && !isNaN(ny)) return (nx - ny) * dir;
                return String(x).localeCompare(String(y), 'es', { numeric: true }) * dir;
            });
            ordered.forEach((r) => this.tbody.appendChild(r.el));
            this._reordered = true;
        } else if (this._reordered) {
            // Restaurar el orden original de captura (solo si antes se reordeno)
            this.rowsMeta.forEach((r) => this.tbody.appendChild(r.el));
            this._reordered = false;
        }
        const start = (this.page - 1) * this.effPerPage;
        const pageSet = new Set(ordered.slice(start, start + this.effPerPage));
        this.rowsMeta.forEach((r) => { r.el.style.display = pageSet.has(r) ? '' : 'none'; });
    },
});

Alpine.start();
