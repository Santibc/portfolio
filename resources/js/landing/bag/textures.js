// Arte del empaque dibujado en canvas (frente, reverso), mapas de rugosidad/metalicidad,
// normal maps (arrugas del foil y estrías de los sellos) y materiales del paquete.
import {
    BackSide,
    CanvasTexture,
    LinearFilter,
    LinearMipmapLinearFilter,
    MeshPhysicalMaterial,
    MeshStandardMaterial,
    NoColorSpace,
    RepeatWrapping,
    SRGBColorSpace,
    Vector2,
} from 'three';
import logoUrl from '../../../images/landing/logo-dorilokos-mix-1024.webp';
import { BAG } from './geometry.js';
import { TAU, blurField, createCanvas, heightToNormal, range, rng, yieldToMain } from './util.js';

// Espacio de diseño: 1412 x 2048 px (proporción real del paquete, 1 : 1.45).
const DW = Math.round(2048 / BAG.H);
const DH = 2048;
const PX = DH / BAG.H;
const yPx = (y) => (BAG.yTop - y) * PX;
const SEAL_TOP = yPx(BAG.ySealTop);
const TEAR = yPx(BAG.yTear);
const SEAL_BOTTOM = yPx(BAG.ySealBottom);

const C = {
    purple: '#5f306a',
    deep: '#46234f',
    dark: '#2a152f',
    black: '#1b0d1f',
    lavender: '#bd97ca',
    orange: '#f39030',
    gold: '#f5c020',
    red: '#e3262a',
    cream: '#f5f2f7',
    ink: '#19131e',
};

// Canal G = rugosidad, canal B = metalicidad (convención de three.js).
const ORM = {
    foil: 'rgb(255,62,255)',
    seal: 'rgb(255,70,255)',
    fire: 'rgb(255,78,92)',
    ribbon: 'rgb(255,98,60)',
    white: 'rgb(255,124,0)',
    ink: 'rgb(255,118,0)',
    sticker: 'rgb(255,104,20)',
    window: 'rgb(255,30,0)',
    panel: 'rgb(255,138,0)',
};

const FONT_SAMPLE = 'ÁÉÍÓÚáéíóúÑñ¿¡AaZz0123456789$#·@:';

async function loadFonts() {
    if (!document.fonts || !document.fonts.load) return;
    await Promise.all([
        document.fonts.load('400 64px "Lilita One"', FONT_SAMPLE),
        document.fonts.load('800 64px "Archivo Variable"', FONT_SAMPLE),
    ]).catch(() => {});
}

async function loadImage(url) {
    const img = new Image();
    // con los assets en otro origen (Vite en desarrollo, CDN) el canvas quedaría contaminado
    // y getImageData/texImage2D lanzarían SecurityError
    img.crossOrigin = 'anonymous';
    img.decoding = 'async';
    img.src = url;
    await img.decode();
    return img;
}

function setFont(ctx, { family = 'archivo', weight = 800, size = 40, stretch = 'normal', spacing = 0 }) {
    const fam = family === 'lilita' ? '"Lilita One"' : '"Archivo Variable"';
    ctx.font = `${family === 'lilita' ? 400 : weight} ${size}px ${fam}, system-ui, sans-serif`;
    if ('fontStretch' in ctx) ctx.fontStretch = family === 'lilita' ? 'normal' : stretch;
    if ('letterSpacing' in ctx) ctx.letterSpacing = `${spacing}px`;
}

// Escribe texto y lo comprime en horizontal si no cabe (sirve también donde
// el navegador no soporta fontStretch en canvas).
function fitText(ctx, text, x, y, maxWidth, mode = 'fill') {
    const width = ctx.measureText(text).width;
    if (!maxWidth || width <= maxWidth) {
        if (mode === 'stroke') ctx.strokeText(text, x, y);
        else ctx.fillText(text, x, y);
        return;
    }
    ctx.save();
    ctx.translate(x, y);
    ctx.scale(maxWidth / width, 1);
    if (mode === 'stroke') ctx.strokeText(text, 0, 0);
    else ctx.fillText(text, 0, 0);
    ctx.restore();
}

function outlinedText(ctx, text, x, y, { fill, strokes = [], maxWidth }) {
    ctx.lineJoin = 'round';
    ctx.miterLimit = 2;
    for (const [color, width] of strokes) {
        ctx.strokeStyle = color;
        ctx.lineWidth = width;
        fitText(ctx, text, x, y, maxWidth, 'stroke');
    }
    ctx.fillStyle = fill;
    fitText(ctx, text, x, y, maxWidth);
}

function roundedTrianglePath(ctx, cx, cy, radius, rot, corner) {
    const pts = [];
    for (let k = 0; k < 3; k++) {
        const a = rot + Math.PI / 2 + (k * TAU) / 3;
        pts.push([cx + Math.cos(a) * radius, cy - Math.sin(a) * radius]);
    }
    ctx.beginPath();
    for (let k = 0; k < 3; k++) {
        const p0 = pts[k];
        const p1 = pts[(k + 1) % 3];
        const p2 = pts[(k + 2) % 3];
        const mx = (p0[0] + p1[0]) / 2;
        const my = (p0[1] + p1[1]) / 2;
        if (k === 0) ctx.moveTo(mx, my);
        const nx = (p1[0] + p2[0]) / 2;
        const ny = (p1[1] + p2[1]) / 2;
        ctx.arcTo(p1[0], p1[1], nx, ny, corner);
    }
    ctx.closePath();
}

function starPath(ctx, cx, cy, rOut, rIn, points, rot) {
    ctx.beginPath();
    for (let i = 0; i < points * 2; i++) {
        const r = i % 2 === 0 ? rOut : rIn;
        const a = rot + (i * Math.PI) / points;
        const x = cx + Math.cos(a) * r;
        const y = cy + Math.sin(a) * r;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.closePath();
}

function superEllipsePath(ctx, cx, cy, rx, ry, n) {
    ctx.beginPath();
    const steps = 96;
    for (let i = 0; i <= steps; i++) {
        const t = (i / steps) * TAU;
        const c = Math.cos(t);
        const s = Math.sin(t);
        const x = cx + rx * Math.sign(c) * Math.abs(c) ** (2 / n);
        const y = cy + ry * Math.sign(s) * Math.abs(s) ** (2 / n);
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.closePath();
}

function scissors(ctx, x, y, size, color) {
    ctx.save();
    ctx.translate(x, y);
    ctx.scale(size / 100, size / 100);
    ctx.strokeStyle = color;
    ctx.fillStyle = color;
    ctx.lineCap = 'round';
    ctx.lineWidth = 9;
    ctx.beginPath();
    ctx.ellipse(-30, -19, 17, 12, -0.45, 0, TAU);
    ctx.stroke();
    ctx.beginPath();
    ctx.ellipse(-30, 19, 17, 12, 0.45, 0, TAU);
    ctx.stroke();
    ctx.lineWidth = 10;
    ctx.beginPath();
    ctx.moveTo(-15, -12);
    ctx.lineTo(48, 12);
    ctx.moveTo(-15, 12);
    ctx.lineTo(48, -12);
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(10, 0, 6, 0, TAU);
    ctx.fill();
    ctx.restore();
}

function fireGradient(ctx, x0, y0, x1, y1) {
    const g = ctx.createLinearGradient(x0, y0, x1, y1);
    g.addColorStop(0, C.gold);
    g.addColorStop(0.5, C.orange);
    g.addColorStop(1, C.red);
    return g;
}

// ---------------------------------------------------------------- foil y sellos

function paintFoil(ctx, m, mirror) {
    if (m === 'orm') {
        ctx.fillStyle = ORM.foil;
        ctx.fillRect(0, 0, DW, DH);
        // pequeñas variaciones de rugosidad: el foil real no es un espejo uniforme
        const r = rng(mirror ? 77 : 41);
        ctx.globalAlpha = 0.07;
        for (let i = 0; i < 40; i++) {
            ctx.fillStyle = r() > 0.5 ? 'rgb(255,110,255)' : 'rgb(255,30,255)';
            ctx.beginPath();
            ctx.ellipse(r() * DW, r() * DH, range(r, 80, 260), range(r, 40, 140), r() * TAU, 0, TAU);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
        return;
    }
    const g = ctx.createLinearGradient(mirror ? DW : 0, 0, mirror ? 0 : DW, DH * 0.45);
    g.addColorStop(0, '#2c1334');
    g.addColorStop(0.2, '#4c2459');
    g.addColorStop(0.4, '#6e3783');
    g.addColorStop(0.52, '#86509b');
    g.addColorStop(0.64, '#69347d');
    g.addColorStop(0.86, '#40204c');
    g.addColorStop(1, '#2b1332');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, DW, DH);
    const v = ctx.createLinearGradient(0, 0, 0, DH);
    v.addColorStop(0, 'rgba(20,6,24,0.35)');
    v.addColorStop(0.18, 'rgba(20,6,24,0)');
    v.addColorStop(0.75, 'rgba(20,6,24,0)');
    v.addColorStop(1, 'rgba(20,6,24,0.4)');
    ctx.fillStyle = v;
    ctx.fillRect(0, 0, DW, DH);
    const glow = ctx.createRadialGradient(DW * (mirror ? 0.38 : 0.62), DH * 0.36, 0, DW * 0.5, DH * 0.4, DW * 0.9);
    glow.addColorStop(0, 'rgba(189,151,202,0.22)');
    glow.addColorStop(1, 'rgba(189,151,202,0)');
    ctx.fillStyle = glow;
    ctx.fillRect(0, 0, DW, DH);
}

function paintSeals(ctx, m) {
    for (const [y0, y1] of [[0, SEAL_TOP], [SEAL_BOTTOM, DH]]) {
        if (m === 'orm') {
            ctx.fillStyle = ORM.seal;
            ctx.fillRect(0, y0, DW, y1 - y0);
            continue;
        }
        const g = ctx.createLinearGradient(0, 0, DW, 0);
        g.addColorStop(0, '#5b2c6b');
        g.addColorStop(0.5, '#9a6aae');
        g.addColorStop(1, '#5b2c6b');
        ctx.fillStyle = g;
        ctx.fillRect(0, y0, DW, y1 - y0);
        ctx.fillStyle = 'rgba(10,3,12,0.35)';
        ctx.fillRect(0, y0 === 0 ? y1 - 4 : y0, DW, 4);
    }
}

function paintTearLine(ctx, m, withLabel) {
    const color = m === 'orm' ? ORM.fire : C.gold;
    ctx.save();
    ctx.strokeStyle = color;
    ctx.lineWidth = 5;
    ctx.setLineDash([24, 15]);
    ctx.beginPath();
    ctx.moveTo(withLabel ? 132 : 34, TEAR);
    ctx.lineTo(DW - 34, TEAR);
    ctx.stroke();
    ctx.setLineDash([]);
    scissors(ctx, 76, TEAR, 82, color);
    if (withLabel) {
        setFont(ctx, { weight: 800, size: 30, stretch: 'semi-condensed', spacing: 7 });
        ctx.fillStyle = m === 'orm' ? ORM.fire : C.gold;
        ctx.textBaseline = 'alphabetic';
        ctx.textAlign = 'left';
        fitText(ctx, 'ABRE AQUÍ', 136, TEAR - 16, 260);
    }
    ctx.restore();
}

// Franja de fuego (llamas) sobre el sello inferior.
function paintFireStripe(ctx, m, top, seed) {
    const r = rng(seed);
    const base = SEAL_BOTTOM;
    const flames = (yTop, amp, step) => {
        ctx.beginPath();
        ctx.moveTo(0, base);
        ctx.lineTo(0, yTop + amp * 0.6);
        let x = 0;
        while (x < DW) {
            const w = step * range(r, 0.7, 1.3);
            const peak = yTop + range(r, -amp * 0.35, amp * 0.25);
            const valley = yTop + amp * range(r, 0.55, 0.95);
            ctx.quadraticCurveTo(x + w * 0.15, peak + amp * 0.2, x + w * 0.45, peak);
            ctx.quadraticCurveTo(x + w * 0.55, valley - amp * 0.4, x + w, valley);
            x += w;
        }
        ctx.lineTo(DW, base);
        ctx.closePath();
    };
    if (m === 'orm') {
        ctx.fillStyle = ORM.fire;
        flames(top, 64, 120);
        ctx.fill();
        return;
    }
    const g = ctx.createLinearGradient(0, top - 40, 0, base);
    g.addColorStop(0, C.gold);
    g.addColorStop(0.45, C.orange);
    g.addColorStop(1, C.red);
    ctx.fillStyle = g;
    flames(top, 64, 120);
    ctx.fill();
    const g2 = ctx.createLinearGradient(0, top + 30, 0, base);
    g2.addColorStop(0, 'rgba(255,226,120,0.85)');
    g2.addColorStop(1, 'rgba(243,144,48,0)');
    ctx.fillStyle = g2;
    flames(top + 46, 46, 90);
    ctx.fill();
}

// ---------------------------------------------------------------- frente

function paintBigTriangle(ctx, m) {
    const pts = [[-300, 250], [1560, 1000], [-40, 1830]];
    ctx.save();
    ctx.lineJoin = 'round';
    ctx.beginPath();
    ctx.moveTo(pts[0][0], pts[0][1]);
    ctx.lineTo(pts[1][0], pts[1][1]);
    ctx.lineTo(pts[2][0], pts[2][1]);
    ctx.closePath();
    if (m === 'color') {
        ctx.fillStyle = 'rgba(30,10,36,0.32)';
        ctx.fill();
        ctx.strokeStyle = fireGradient(ctx, 0, 300, DW, 1800);
        ctx.globalAlpha = 0.9;
    } else {
        ctx.strokeStyle = ORM.fire;
    }
    ctx.lineWidth = 46;
    ctx.stroke();
    ctx.restore();
}

function paintLogo(ctx, m, assets) {
    const size = 930;
    const x = (DW - size) / 2;
    const y = 236;
    ctx.save();
    if (m === 'color') {
        ctx.shadowColor = 'rgba(14,4,18,0.6)';
        ctx.shadowBlur = 46;
        ctx.shadowOffsetY = 14;
        ctx.drawImage(assets.logo, x, y, size, size);
    } else {
        ctx.drawImage(assets.logoOrm, x, y, size, size);
    }
    ctx.restore();
}

function paintRibbon(ctx, m) {
    ctx.save();
    ctx.translate(DW / 2, 1222);
    ctx.rotate(-0.055);
    const w = 1180;
    const h = 138;
    const notch = 46;
    ctx.beginPath();
    ctx.moveTo(-w / 2, -h / 2);
    ctx.lineTo(w / 2, -h / 2);
    ctx.lineTo(w / 2 - notch, 0);
    ctx.lineTo(w / 2, h / 2);
    ctx.lineTo(-w / 2, h / 2);
    ctx.lineTo(-w / 2 + notch, 0);
    ctx.closePath();
    if (m === 'color') {
        ctx.shadowColor = 'rgba(14,4,18,0.45)';
        ctx.shadowBlur = 24;
        ctx.shadowOffsetY = 8;
        const g = ctx.createLinearGradient(-w / 2, 0, w / 2, 0);
        g.addColorStop(0, C.gold);
        g.addColorStop(0.55, C.orange);
        g.addColorStop(1, '#ec6a2c');
        ctx.fillStyle = g;
    } else {
        ctx.fillStyle = ORM.ribbon;
    }
    ctx.fill();
    ctx.shadowColor = 'transparent';
    ctx.strokeStyle = m === 'color' ? C.dark : ORM.ink;
    ctx.lineWidth = 6;
    ctx.beginPath();
    ctx.moveTo(-w / 2 + 70, -h / 2 + 14);
    ctx.lineTo(w / 2 - 70, -h / 2 + 14);
    ctx.moveTo(-w / 2 + 70, h / 2 - 14);
    ctx.lineTo(w / 2 - 70, h / 2 - 14);
    ctx.stroke();
    setFont(ctx, { family: 'lilita', size: 92 });
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = m === 'color' ? C.dark : ORM.ink;
    fitText(ctx, 'SABOR BIEN LOKO', 0, 6, w - 220);
    ctx.restore();
}

function paintChip(ctx, x, y, r, rot, random) {
    ctx.save();
    ctx.shadowColor = 'rgba(10,2,10,0.55)';
    ctx.shadowBlur = 18;
    ctx.shadowOffsetY = 8;
    roundedTrianglePath(ctx, x, y, r, rot, r * 0.22);
    const g = ctx.createLinearGradient(x - r, y - r, x + r, y + r);
    g.addColorStop(0, '#f8c55a');
    g.addColorStop(0.5, '#f0a032');
    g.addColorStop(1, '#d4701f');
    ctx.fillStyle = g;
    ctx.fill();
    ctx.shadowColor = 'transparent';
    ctx.clip();
    // condimento: puntos rojos y naranja tostado
    for (let i = 0; i < 70; i++) {
        const a = random() * TAU;
        const d = Math.sqrt(random()) * r;
        ctx.fillStyle = random() > 0.35 ? 'rgba(200,45,22,0.85)' : 'rgba(150,60,15,0.7)';
        ctx.beginPath();
        ctx.arc(x + Math.cos(a) * d, y + Math.sin(a) * d, range(random, 1.5, 4.5), 0, TAU);
        ctx.fill();
    }
    const hl = ctx.createLinearGradient(x - r, y - r, x + r * 0.2, y + r * 0.2);
    hl.addColorStop(0, 'rgba(255,240,200,0.45)');
    hl.addColorStop(1, 'rgba(255,240,200,0)');
    ctx.fillStyle = hl;
    ctx.fillRect(x - r, y - r, r * 2, r * 2);
    ctx.restore();
}

function paintWindow(ctx, m) {
    const cx = 690;
    const cy = 1512;
    const rx = 450;
    const ry = 182;
    ctx.save();
    if (m === 'orm') {
        superEllipsePath(ctx, cx, cy, rx, ry, 3.4);
        ctx.fillStyle = ORM.window;
        ctx.fill();
        ctx.strokeStyle = ORM.white;
        ctx.lineWidth = 12;
        ctx.stroke();
        ctx.restore();
        return;
    }
    superEllipsePath(ctx, cx, cy, rx, ry, 3.4);
    const g = ctx.createLinearGradient(0, cy - ry, 0, cy + ry);
    g.addColorStop(0, '#140810');
    g.addColorStop(1, '#2d1436');
    ctx.fillStyle = g;
    ctx.fill();
    ctx.save();
    ctx.clip();
    const random = rng(1234);
    const chips = [
        [cx - 330, cy + 60, 150, 0.3],
        [cx - 150, cy - 40, 175, -0.5],
        [cx + 40, cy + 70, 165, 0.9],
        [cx + 230, cy - 30, 180, 0.15],
        [cx + 380, cy + 80, 150, -0.7],
        [cx - 40, cy - 150, 140, 2.1],
        [cx + 300, cy - 170, 130, 1.4],
        [cx - 300, cy - 150, 130, 2.6],
    ];
    for (const [x, y, r, rot] of chips) paintChip(ctx, x, y, r, rot, random);
    // brillo de la película transparente
    const film = ctx.createLinearGradient(cx - rx, cy - ry, cx + rx * 0.3, cy + ry);
    film.addColorStop(0, 'rgba(255,255,255,0.28)');
    film.addColorStop(0.35, 'rgba(255,255,255,0.04)');
    film.addColorStop(0.5, 'rgba(255,255,255,0)');
    ctx.fillStyle = film;
    ctx.fillRect(cx - rx, cy - ry, rx * 2, ry * 2);
    ctx.restore();
    superEllipsePath(ctx, cx, cy, rx, ry, 3.4);
    ctx.strokeStyle = C.cream;
    ctx.lineWidth = 12;
    ctx.stroke();
    superEllipsePath(ctx, cx, cy, rx + 16, ry + 16, 3.4);
    ctx.strokeStyle = C.gold;
    ctx.lineWidth = 4;
    ctx.stroke();
    ctx.restore();
}

function paintStar(ctx, m) {
    const cx = 1150;
    const cy = 1352;
    ctx.save();
    starPath(ctx, cx, cy, 158, 124, 16, 0.12);
    if (m === 'color') {
        ctx.shadowColor = 'rgba(14,4,18,0.5)';
        ctx.shadowBlur = 22;
        ctx.shadowOffsetY = 8;
        ctx.fillStyle = C.gold;
        ctx.fill();
        ctx.shadowColor = 'transparent';
        ctx.strokeStyle = C.red;
        ctx.lineWidth = 10;
        ctx.lineJoin = 'round';
        ctx.stroke();
    } else {
        ctx.fillStyle = ORM.sticker;
        ctx.fill();
    }
    ctx.translate(cx, cy);
    ctx.rotate(-0.24);
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    setFont(ctx, { family: 'lilita', size: 58 });
    ctx.fillStyle = m === 'color' ? C.dark : ORM.ink;
    fitText(ctx, '¡CARGADO!', 0, 6, 222);
    ctx.restore();
}

function paintFrontLegal(ctx, m) {
    ctx.save();
    setFont(ctx, { weight: 800, size: 36, stretch: 'semi-condensed', spacing: 1 });
    ctx.textAlign = 'left';
    ctx.textBaseline = 'alphabetic';
    ctx.fillStyle = m === 'color' ? C.dark : ORM.ink;
    fitText(ctx, 'Contenido neto: 1 antojo grande', 64, SEAL_BOTTOM - 30, 640);
    ctx.textAlign = 'right';
    fitText(ctx, 'Hecho al momento', DW - 64, SEAL_BOTTOM - 30, 420);
    ctx.restore();
}

function paintFront(ctx, m, assets) {
    paintFoil(ctx, m, false);
    paintBigTriangle(ctx, m);
    paintLogo(ctx, m, assets);
    paintFireStripe(ctx, m, 1745, 9);
    paintWindow(ctx, m);
    paintRibbon(ctx, m);
    paintStar(ctx, m);
    paintFrontLegal(ctx, m);
    paintSeals(ctx, m);
    paintTearLine(ctx, m, true);
}

// ---------------------------------------------------------------- reverso

const INGREDIENTES = [
    'Frijol',
    'Carne desmechada',
    'Chicharrón carnudo',
    'Plátano maduro',
    'Queso rayado',
    'Maíz dulce',
    'Guacamole',
    'Pico de gallo',
    'Salsa de la casa',
];

function check(ctx, x, y, s) {
    ctx.save();
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.lineWidth = s * 0.2;
    ctx.beginPath();
    ctx.moveTo(x - s * 0.42, y);
    ctx.lineTo(x - s * 0.1, y + s * 0.32);
    ctx.lineTo(x + s * 0.46, y - s * 0.36);
    ctx.stroke();
    ctx.restore();
}

function paintWordmark(ctx, m, cx, cy, scale) {
    ctx.save();
    ctx.translate(cx, cy);
    ctx.scale(scale, scale);
    ctx.textAlign = 'center';
    ctx.textBaseline = 'alphabetic';
    setFont(ctx, { family: 'lilita', size: 150 });
    if (m === 'color') {
        outlinedText(ctx, 'Dorilokos', 0, 0, { fill: '#ffffff', strokes: [[C.black, 34], [C.red, 16]] });
    } else {
        outlinedText(ctx, 'Dorilokos', 0, 0, { fill: ORM.white, strokes: [[ORM.fire, 34]] });
    }
    ctx.translate(250, 92);
    ctx.rotate(-0.12);
    setFont(ctx, { family: 'lilita', size: 104 });
    if (m === 'color') outlinedText(ctx, 'MIX', 0, 0, { fill: C.red, strokes: [[C.black, 30], [C.gold, 14]] });
    else outlinedText(ctx, 'MIX', 0, 0, { fill: ORM.fire, strokes: [[ORM.fire, 30]] });
    ctx.restore();
}

function paintTable(ctx, m) {
    const x0 = 118;
    const x1 = DW - 118;
    const y0 = 488;
    const pad = 46;
    const inkC = m === 'color' ? C.ink : ORM.panel;
    const rows = INGREDIENTES.length;
    const rowH = 58;
    const y1 = y0 + 300 + rows * rowH + 96;
    ctx.save();
    if (m === 'color') {
        ctx.shadowColor = 'rgba(14,4,18,0.5)';
        ctx.shadowBlur = 30;
        ctx.shadowOffsetY = 10;
    }
    ctx.fillStyle = m === 'color' ? C.cream : ORM.panel;
    ctx.fillRect(x0, y0, x1 - x0, y1 - y0);
    ctx.shadowColor = 'transparent';
    ctx.strokeStyle = inkC;
    ctx.lineWidth = 12;
    ctx.strokeRect(x0 + 6, y0 + 6, x1 - x0 - 12, y1 - y0 - 12);
    if (m === 'orm') {
        ctx.restore();
        return y1;
    }
    const L = x0 + pad;
    const R = x1 - pad;
    ctx.fillStyle = C.ink;
    ctx.textBaseline = 'alphabetic';
    ctx.textAlign = 'left';
    setFont(ctx, { weight: 900, size: 86, stretch: 'condensed', spacing: -1 });
    fitText(ctx, 'Información antojicional', L, y0 + pad + 70, R - L);
    let y = y0 + pad + 92;
    ctx.fillRect(L, y, R - L, 22);
    y += 22 + 54;
    setFont(ctx, { weight: 700, size: 40, stretch: 'semi-condensed' });
    ctx.fillText('Porción', L, y);
    ctx.textAlign = 'right';
    setFont(ctx, { weight: 800, size: 40, stretch: 'semi-condensed' });
    ctx.fillText('1 paquete cargado', R, y);
    y += 20;
    ctx.fillRect(L, y, R - L, 9);
    y += 9 + 40;
    setFont(ctx, { weight: 800, size: 25, stretch: 'semi-condensed', spacing: 4 });
    ctx.textAlign = 'left';
    ctx.fillText('INGREDIENTE', L, y);
    ctx.textAlign = 'right';
    ctx.fillText('¿LLEVA?', R, y);
    y += 14;
    ctx.fillRect(L, y, R - L, 3);
    ctx.strokeStyle = C.ink;
    for (let i = 0; i < rows; i++) {
        const base = y + rowH * i + 42;
        setFont(ctx, { weight: i === rows - 1 ? 800 : 600, size: 38, stretch: 'semi-condensed' });
        ctx.textAlign = 'left';
        ctx.fillText(INGREDIENTES[i], L, base);
        check(ctx, R - 22, base - 12, 34);
        if (i < rows - 1) {
            ctx.fillStyle = 'rgba(25,19,30,0.28)';
            ctx.fillRect(L, y + rowH * (i + 1), R - L, 2);
            ctx.fillStyle = C.ink;
        }
    }
    y += rowH * rows + 6;
    ctx.fillRect(L, y, R - L, 14);
    setFont(ctx, { weight: 500, size: 25, stretch: 'semi-condensed' });
    ctx.textAlign = 'left';
    fitText(ctx, '*Porción generosa. Se arma al momento, frente a ti.', L, y + 48, R - L);
    ctx.restore();
    return y1;
}

const EAN_L = ['0001101', '0011001', '0010011', '0111101', '0100011', '0110001', '0101111', '0111011', '0110111', '0001011'];

// Datos del reverso: la página los pone en #bag-stage desde config/landing.php (así no quedan
// quemados aquí); los valores de abajo solo sirven para el arnés de desarrollo.
function datosNegocio() {
    const datos = document.getElementById('bag-stage')?.dataset ?? {};
    return {
        telefono: datos.telefono || '314 279 8769',
        direccion: datos.direccion || 'Calle 65 #11-73',
        barrio: datos.barrio || 'Chapinero',
        ciudad: datos.ciudad || 'Bogotá',
        instagram: datos.instagram || '@dorilokosmix',
    };
}

function paintBarcode(ctx, m, x, y, w, h) {
    ctx.save();
    ctx.fillStyle = m === 'color' ? '#ffffff' : ORM.panel;
    ctx.beginPath();
    if (ctx.roundRect) {
        ctx.roundRect(x, y, w, h, 16);
    } else {
        ctx.rect(x, y, w, h);
    }
    ctx.fill();
    if (m === 'orm') {
        ctx.restore();
        return;
    }
    const telefono = datosNegocio().telefono;
    const digits = telefono.replace(/\D/g, '').slice(0, 10).padEnd(10, '0');
    let bits = '101';
    for (let i = 0; i < digits.length; i++) {
        bits += EAN_L[+digits[i]];
        if (i === 4) bits += '01010';
    }
    bits += '101';
    const mod = (w - 60) / bits.length;
    const bx = x + 30;
    ctx.fillStyle = C.ink;
    for (let i = 0; i < bits.length; i++) {
        if (bits[i] !== '1') continue;
        const guard = i < 3 || i >= bits.length - 3;
        ctx.fillRect(bx + i * mod, y + 22, mod + 0.4, h - (guard ? 62 : 76));
    }
    setFont(ctx, { weight: 600, size: 30, stretch: 'normal', spacing: 5 });
    ctx.textAlign = 'center';
    ctx.textBaseline = 'alphabetic';
    fitText(ctx, telefono, x + w / 2, y + h - 18, w - 40);
    ctx.restore();
}

function paintBackInfo(ctx, m, yStart) {
    const L = 118;
    const white = m === 'color' ? '#ffffff' : ORM.white;
    const gold = m === 'color' ? C.gold : ORM.fire;
    ctx.save();
    ctx.textAlign = 'left';
    ctx.textBaseline = 'alphabetic';
    let y = yStart + 70;
    setFont(ctx, { weight: 800, size: 34, stretch: 'semi-condensed', spacing: 5 });
    ctx.fillStyle = gold;
    fitText(ctx, 'MODO DE PREPARACIÓN', L, y, 700);
    y += 72;
    setFont(ctx, { family: 'lilita', size: 64 });
    ctx.fillStyle = white;
    fitText(ctx, 'ábrelo, cárgalo, cómetelo.', L, y, DW - 2 * L);
    y += 54;
    const boxW = 420;
    const boxH = 214;
    const boxX = DW - L - boxW;
    paintBarcode(ctx, m, boxX, y, boxW, boxH);
    setFont(ctx, { weight: 700, size: 33, stretch: 'semi-condensed' });
    ctx.fillStyle = white;
    const textW = boxX - L - 40;
    const negocio = datosNegocio();
    fitText(ctx, `Hecho al momento en ${negocio.barrio}`, L, y + 50, textW);
    fitText(ctx, `${negocio.direccion} · ${negocio.ciudad}`, L, y + 96, textW);
    setFont(ctx, { family: 'lilita', size: 70 });
    if (m === 'color') outlinedText(ctx, negocio.instagram, L, y + 190, { fill: C.gold, strokes: [[C.black, 12]], maxWidth: textW });
    else outlinedText(ctx, negocio.instagram, L, y + 190, { fill: ORM.fire, strokes: [[ORM.fire, 12]], maxWidth: textW });
    ctx.restore();
}

function paintBack(ctx, m) {
    paintFoil(ctx, m, true);
    paintFireStripe(ctx, m, 1830, 31);
    paintWordmark(ctx, m, DW / 2 - 60, 380, 0.92);
    const yEnd = paintTable(ctx, m);
    paintBackInfo(ctx, m, yEnd);
    paintSeals(ctx, m);
    paintTearLine(ctx, m, false);
}

// ---------------------------------------------------------------- mapas derivados

// Clasifica los píxeles del logo: tinta blanca mate y no metálica, morado metalizado,
// fuego y rojo semimetálicos.
function makeLogoOrm(img) {
    const w = img.naturalWidth;
    const h = img.naturalHeight;
    const canvas = createCanvas(w, h);
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(img, 0, 0);
    const data = ctx.getImageData(0, 0, w, h);
    const px = data.data;
    for (let i = 0; i < px.length; i += 4) {
        if (px[i + 3] === 0) continue;
        const r = px[i];
        const g = px[i + 1];
        const b = px[i + 2];
        const mn = Math.min(r, g, b);
        const mx = Math.max(r, g, b);
        let rough = 0.3;
        let metal = 0.5;
        if (mn > 165 && mx - mn < 70) {
            rough = 0.46;
            metal = 0;
        } else if (b > g && r < 170) {
            rough = 0.28;
            metal = 0.8;
        }
        px[i] = 255;
        px[i + 1] = rough * 255;
        px[i + 2] = metal * 255;
    }
    ctx.putImageData(data, 0, 0);
    return canvas;
}

// Arrugas del foil: pliegues largos y suaves, arrugas finas, fruncido junto a los
// sellos y pliegues diagonales en las esquinas.
function makeCrinkleNormal(w, h) {
    const canvas = createCanvas(w, h);
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const random = rng(4242);
    ctx.fillStyle = 'rgb(128,128,128)';
    ctx.fillRect(0, 0, w, h);
    ctx.lineCap = 'round';
    const k = w / 1024;
    const line = (x, y, len, ang, width, light, alpha) => {
        ctx.strokeStyle = light ? `rgba(255,255,255,${alpha})` : `rgba(0,0,0,${alpha})`;
        ctx.lineWidth = width * k;
        ctx.beginPath();
        ctx.moveTo(x - Math.cos(ang) * len * 0.5, y - Math.sin(ang) * len * 0.5);
        ctx.lineTo(x + Math.cos(ang) * len * 0.5, y + Math.sin(ang) * len * 0.5);
        ctx.stroke();
    };
    for (let i = 0; i < 90; i++) {
        line(random() * w, random() * h, range(random, 160, 520) * k, range(random, -0.9, 0.9) + (random() > 0.5 ? Math.PI / 2 : 0), range(random, 10, 26), random() > 0.5, range(random, 0.05, 0.1));
    }
    for (let i = 0; i < 240; i++) {
        line(random() * w, random() * h, range(random, 30, 160) * k, random() * Math.PI, range(random, 2.5, 6), random() > 0.5, range(random, 0.05, 0.11));
    }
    const sealT = (SEAL_TOP / DH) * h;
    const sealB = (SEAL_BOTTOM / DH) * h;
    for (const [y0, dir] of [[sealT, 1], [sealB, -1]]) {
        for (let x = 0; x < w; x += range(random, 10, 26) * k) {
            const len = range(random, 30, 110) * k;
            line(x, y0 + (dir * len) / 2, len, Math.PI / 2 + range(random, -0.2, 0.2), range(random, 3, 7), random() > 0.5, range(random, 0.1, 0.2));
        }
    }
    for (const [cx, cy, ang] of [[0, sealT, 0.8], [w, sealT, Math.PI - 0.8], [0, sealB, -0.8], [w, sealB, Math.PI + 0.8]]) {
        for (let i = 0; i < 9; i++) {
            const len = range(random, 80, 260) * k;
            const a = ang + range(random, -0.35, 0.35);
            line(cx + Math.cos(a) * len * 0.5, cy + Math.sin(a) * len * 0.5, len, a, range(random, 4, 12), random() > 0.5, range(random, 0.1, 0.22));
        }
    }
    const data = ctx.getImageData(0, 0, w, h).data;
    const field = new Float32Array(w * h);
    for (let i = 0; i < field.length; i++) field[i] = data[i * 4] / 255;
    blurField(field, w, h, Math.max(1, Math.round(2 * k)));
    return heightToNormal(field, w, h, 7);
}

// Estrías del crimpado: crestas verticales finas, repetidas a lo ancho del sello.
function makeCrimpNormal() {
    const w = 64;
    const h = 16;
    const field = new Float32Array(w * h);
    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            const t = (x / w) * 2;
            const s = Math.abs(Math.sin(Math.PI * t));
            field[y * w + x] = Math.pow(s, 0.7);
        }
    }
    return heightToNormal(field, w, h, 1.6);
}

function canvasTexture(canvas, { srgb = false, anisotropy = 1, repeat = false } = {}) {
    const texture = new CanvasTexture(canvas);
    texture.colorSpace = srgb ? SRGBColorSpace : NoColorSpace;
    texture.anisotropy = anisotropy;
    texture.minFilter = LinearMipmapLinearFilter;
    texture.magFilter = LinearFilter;
    texture.generateMipmaps = true;
    if (repeat) {
        texture.wrapS = RepeatWrapping;
        texture.wrapT = RepeatWrapping;
    }
    texture.needsUpdate = true;
    return texture;
}

function paintToCanvas(height, painter) {
    const scale = height / DH;
    const canvas = createCanvas(Math.round(DW * scale), height);
    const ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);
    painter(ctx);
    return canvas;
}

export async function createBagTextures({ renderer, lowPower = false }) {
    const t0 = performance.now();
    const timings = [];
    const mark = (label) => timings.push([label, Math.round(performance.now() - t0)]);
    const [, logo] = await Promise.all([loadFonts(), loadImage(logoUrl)]);
    mark('fuentes+logo');
    const assets = { logo, logoOrm: makeLogoOrm(logo) };
    mark('logo orm');
    await yieldToMain();
    const height = lowPower ? 1536 : 2048;
    const anisotropy = renderer.capabilities.getMaxAnisotropy();

    const frontMap = canvasTexture(paintToCanvas(height, (ctx) => paintFront(ctx, 'color', assets)), { srgb: true, anisotropy });
    const frontOrm = canvasTexture(paintToCanvas(height / 2, (ctx) => paintFront(ctx, 'orm', assets)), { anisotropy });
    mark('frente');
    await yieldToMain();
    const backMap = canvasTexture(paintToCanvas(height, (ctx) => paintBack(ctx, 'color')), { srgb: true, anisotropy });
    const backOrm = canvasTexture(paintToCanvas(height / 2, (ctx) => paintBack(ctx, 'orm')), { anisotropy });
    mark('reverso');
    await yieldToMain();
    const crinkleH = lowPower ? 900 : 1200;
    const crinkle = canvasTexture(makeCrinkleNormal(Math.round(crinkleH / BAG.H), crinkleH), { anisotropy });
    mark('arrugas');
    const crimp = canvasTexture(makeCrimpNormal(), { anisotropy, repeat: true });
    crimp.repeat.set(58, 1);

    const all = [frontMap, frontOrm, backMap, backOrm, crinkle, crimp];
    return {
        timings,
        frontMap,
        frontOrm,
        backMap,
        backOrm,
        crinkle,
        crimp,
        all,
        // Sube las texturas a la GPU y libera los canvas (ya no se vuelven a leer).
        upload() {
            for (const texture of all) {
                renderer.initTexture(texture);
                const img = texture.image;
                if (img && img.width > 64) {
                    img.width = 1;
                    img.height = 1;
                }
            }
        },
        dispose() {
            for (const texture of all) texture.dispose();
        },
    };
}

export function createBagMaterials(tex) {
    const print = (map, orm) => new MeshPhysicalMaterial({
        map,
        roughnessMap: orm,
        metalnessMap: orm,
        roughness: 0.8,
        metalness: 1,
        normalMap: tex.crinkle,
        normalScale: new Vector2(0.34, 0.34),
        clearcoat: 0.6,
        clearcoatRoughness: 0.1,
    });
    const seal = (map, orm) => new MeshPhysicalMaterial({
        map,
        roughnessMap: orm,
        metalnessMap: orm,
        roughness: 1,
        metalness: 1,
        normalMap: tex.crimp,
        normalScale: new Vector2(1.3, 1.3),
        clearcoat: 0.6,
        clearcoatRoughness: 0.25,
    });
    const materials = {
        printFront: print(tex.frontMap, tex.frontOrm),
        printBack: print(tex.backMap, tex.backOrm),
        sealFront: seal(tex.frontMap, tex.frontOrm),
        sealBack: seal(tex.backMap, tex.backOrm),
        interior: new MeshStandardMaterial({
            color: 0xdcd8e2,
            metalness: 1,
            roughness: 0.3,
            normalMap: tex.crinkle,
            normalScale: new Vector2(0.7, 0.7),
            side: BackSide,
            vertexColors: true,
        }),
    };
    materials.list = Object.values(materials);
    return materials;
}
