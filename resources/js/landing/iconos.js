import {
    createIcons,
    ArrowDown,
    ArrowUpRight,
    Bike,
    Camera,
    Check,
    Clock,
    Flame,
    Menu,
    Moon,
    Navigation,
    Scissors,
    Sparkles,
    Star,
    Sun,
    X,
} from 'lucide';

/** Solo los íconos que usa la landing (<x-icon> renderiza <i data-lucide="...">). */
export function iniciarIconos() {
    createIcons({
        icons: { ArrowDown, ArrowUpRight, Bike, Camera, Check, Clock, Flame, Menu, Moon, Navigation, Scissors, Sparkles, Star, Sun, X },
        attrs: { 'stroke-width': 1.75 },
    });
}
