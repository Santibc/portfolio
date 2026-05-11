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
    BarChart3, Bell,
    Calendar, Camera, ChefHat, Check, CheckCircle2, ChevronDown, ChevronLeft, ChevronRight, ChevronUp,
    Clock, Cloud, Coffee, Component, Copy, CreditCard,
    DollarSign, Download,
    Edit, ExternalLink, Eye, EyeOff,
    Gauge,
    Heart, Home,
    Image, Inbox, Info,
    Key,
    Layers, List, LoaderCircle, Lock, LogIn, LogOut,
    Mail, MapPin, Menu, MessageCircle, Minus, Moon, MousePointerClick,
    Phone, PieChart, Plus,
    Save, Search, Send, Settings, ShieldCheck, ShoppingBag, ShoppingBasket, ShoppingCart, Soup, Sparkles, Square, SquareStack, Star, Sun,
    Table, Tag, TextCursorInput, Trash2, TrendingDown, TrendingUp, Type,
    Upload, User, UserCheck, UserCog, UserPlus, Users, UtensilsCrossed,
    Wifi,
    X,
    Zap,
} from 'lucide';

const usedIcons = {
    Activity, AlertCircle, AlertTriangle, Archive, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight,
    BarChart3, Bell,
    Calendar, Camera, ChefHat, Check, CheckCircle2, ChevronDown, ChevronLeft, ChevronRight, ChevronUp,
    Clock, Cloud, Coffee, Component, Copy, CreditCard,
    DollarSign, Download,
    Edit, ExternalLink, Eye, EyeOff,
    Gauge,
    Heart, Home,
    Image, Inbox, Info,
    Key,
    Layers, List, LoaderCircle, Lock, LogIn, LogOut,
    Mail, MapPin, Menu, MessageCircle, Minus, Moon, MousePointerClick,
    Phone, PieChart, Plus,
    Save, Search, Send, Settings, ShieldCheck, ShoppingBag, ShoppingBasket, ShoppingCart, Soup, Sparkles, Square, SquareStack, Star, Sun,
    Table, Tag, TextCursorInput, Trash2, TrendingDown, TrendingUp, Type,
    Upload, User, UserCheck, UserCog, UserPlus, Users, UtensilsCrossed,
    Wifi,
    X,
    Zap,
};

// ScrollReveal (animaciones on-scroll)
import ScrollReveal from 'scrollreveal';

// Theme toggle (light/dark/auto + persistencia BD + localStorage)
import './theme-toggle';

const renderIcons = () => createIcons({ icons: usedIcons, attrs: { 'stroke-width': 1.75 } });

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
    // Re-init defensivo despues del primer paint, por si Alpine inserta nodos tarde
    setTimeout(initPreline, 200);

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

    // Re-renderizar iconos + re-init Preline cuando se inserten nodos dinamicamente
    const observer = new MutationObserver((mutations) => {
        let needsIcons = false;
        let needsPreline = false;
        for (const m of mutations) {
            for (const n of m.addedNodes) {
                if (n.nodeType !== 1) continue;
                if (n.querySelector?.('[data-lucide]') || n.matches?.('[data-lucide]')) needsIcons = true;
                if (n.querySelector?.('[data-hs-overlay], [class*="hs-"]') || n.matches?.('[data-hs-overlay]')) needsPreline = true;
            }
        }
        if (needsIcons) renderIcons();
        if (needsPreline && window.HSStaticMethods?.autoInit) window.HSStaticMethods.autoInit();
    });
    observer.observe(document.body, { childList: true, subtree: true });

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

Alpine.start();
