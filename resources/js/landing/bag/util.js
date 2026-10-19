// Utilidades numéricas compartidas por el paquete 3D (sin dependencias).

export const TAU = Math.PI * 2;

export function clamp(x, a = 0, b = 1) {
    return x < a ? a : x > b ? b : x;
}

export function lerp(a, b, t) {
    return a + (b - a) * t;
}

export function smoothstep(e0, e1, x) {
    const t = clamp((x - e0) / (e1 - e0));
    return t * t * (3 - 2 * t);
}

export function smootherstep(t) {
    t = clamp(t);
    return t * t * t * (t * (t * 6 - 15) + 10);
}

export function easeOutCubic(t) {
    t = clamp(t);
    return 1 - (1 - t) * (1 - t) * (1 - t);
}

// Amortiguación exponencial independiente del framerate.
export function damp(current, target, lambda, dt) {
    return lerp(current, target, 1 - Math.exp(-lambda * dt));
}

// PRNG determinista (mulberry32): mismas semillas, mismo paquete en cada carga.
export function rng(seed) {
    let s = seed >>> 0;
    return function next() {
        s = (s + 0x6d2b79f5) >>> 0;
        let t = s;
        t = Math.imul(t ^ (t >>> 15), t | 1);
        t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

export function range(random, a, b) {
    return a + (b - a) * random();
}

// Ruido de valor 2D suave en [-1, 1].
export function noise2(seed = 1) {
    const hash = (x, y) => {
        let h = Math.imul(x, 374761393) + Math.imul(y, 668265263) + Math.imul(seed, 1442695041);
        h = Math.imul(h ^ (h >>> 13), 1274126177);
        h ^= h >>> 16;
        return ((h >>> 0) / 4294967295) * 2 - 1;
    };
    return function sample(x, y) {
        const x0 = Math.floor(x);
        const y0 = Math.floor(y);
        const fx = x - x0;
        const fy = y - y0;
        const sx = fx * fx * (3 - 2 * fx);
        const sy = fy * fy * (3 - 2 * fy);
        const a = hash(x0, y0);
        const b = hash(x0 + 1, y0);
        const c = hash(x0, y0 + 1);
        const d = hash(x0 + 1, y0 + 1);
        return lerp(lerp(a, b, sx), lerp(c, d, sx), sy);
    };
}

// Ruido fractal (3 octavas) a partir de noise2.
export function fbm2(sample, x, y, octaves = 3) {
    let amp = 0.5;
    let freq = 1;
    let sum = 0;
    let norm = 0;
    for (let i = 0; i < octaves; i++) {
        sum += amp * sample(x * freq, y * freq);
        norm += amp;
        amp *= 0.5;
        freq *= 2.03;
    }
    return sum / norm;
}

export function createCanvas(width, height) {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    return canvas;
}

// Cede el hilo principal entre pasos pesados de la carga (evita tareas largas).
export function yieldToMain() {
    const scheduler = globalThis.scheduler;
    if (scheduler && typeof scheduler.yield === 'function') return scheduler.yield();
    return new Promise((resolve) => setTimeout(resolve, 0));
}

// Desenfoque de caja separable (4 pasadas ~ gaussiano) sobre un campo de alturas.
export function blurField(field, w, h, radius) {
    const tmp = new Float32Array(field.length);
    const norm = 1 / (radius * 2 + 1);
    const horizontal = (src, dst) => {
        for (let y = 0; y < h; y++) {
            const row = y * w;
            let acc = 0;
            for (let k = -radius; k <= radius; k++) acc += src[row + Math.min(w - 1, Math.max(0, k))];
            for (let x = 0; x < w; x++) {
                dst[row + x] = acc * norm;
                acc += src[row + Math.min(w - 1, x + radius + 1)] - src[row + Math.max(0, x - radius)];
            }
        }
    };
    const vertical = (src, dst) => {
        for (let x = 0; x < w; x++) {
            let acc = 0;
            for (let k = -radius; k <= radius; k++) acc += src[Math.min(h - 1, Math.max(0, k)) * w + x];
            for (let y = 0; y < h; y++) {
                dst[y * w + x] = acc * norm;
                acc += src[Math.min(h - 1, y + radius + 1) * w + x] - src[Math.max(0, y - radius) * w + x];
            }
        }
    };
    horizontal(field, tmp);
    vertical(tmp, field);
    horizontal(field, tmp);
    vertical(tmp, field);
}

export function heightToNormal(field, w, h, strength) {
    const canvas = createCanvas(w, h);
    const ctx = canvas.getContext('2d');
    const img = ctx.createImageData(w, h);
    const px = img.data;
    for (let y = 0; y < h; y++) {
        const up = (y > 0 ? y - 1 : y) * w;
        const down = (y < h - 1 ? y + 1 : y) * w;
        const row = y * w;
        for (let x = 0; x < w; x++) {
            const xl = x > 0 ? x - 1 : x;
            const xr = x < w - 1 ? x + 1 : x;
            // flipY: la v de la textura crece hacia arriba de la imagen
            const nx = -(field[row + xr] - field[row + xl]) * strength;
            const ny = (field[down + x] - field[up + x]) * strength;
            const inv = 1 / Math.sqrt(nx * nx + ny * ny + 1);
            const i = (row + x) * 4;
            px[i] = (nx * inv * 0.5 + 0.5) * 255;
            px[i + 1] = (ny * inv * 0.5 + 0.5) * 255;
            px[i + 2] = (inv * 0.5 + 0.5) * 255;
            px[i + 3] = 255;
        }
    }
    ctx.putImageData(img, 0, 0);
    return canvas;
}
