// Nachos: triángulo de esquinas redondeadas, levemente curvado, con grosor y
// textura de maíz con condimento. Orbitan en el hero y explotan en el cierre.
import {
    BufferGeometry,
    CanvasTexture,
    DynamicDrawUsage,
    Euler,
    Float32BufferAttribute,
    InstancedMesh,
    LinearMipmapLinearFilter,
    Matrix4,
    MeshStandardMaterial,
    NoColorSpace,
    Quaternion,
    SRGBColorSpace,
    Vector2,
    Vector3,
} from 'three';
import { TAU, blurField, clamp, createCanvas, easeOutCubic, heightToNormal, lerp, noise2, range, rng, smoothstep } from './util.js';

// Contorno de un triángulo equilátero redondeado (circunradio total 0.5).
function outline(corner, cornerSegs, edgeSegs) {
    const rInner = 0.5 - corner;
    const pts = [];
    for (let k = 0; k < 3; k++) {
        const th = Math.PI / 2 + (k * TAU) / 3;
        const cx = Math.cos(th) * rInner;
        const cy = Math.sin(th) * rInner;
        for (let s = 0; s <= cornerSegs; s++) {
            const a = th - Math.PI / 3 + ((2 * Math.PI) / 3) * (s / cornerSegs);
            pts.push([cx + Math.cos(a) * corner, cy + Math.sin(a) * corner]);
        }
        const thN = th + TAU / 3;
        const nx = Math.cos(thN) * rInner;
        const ny = Math.sin(thN) * rInner;
        const a0 = th + Math.PI / 3;
        const ex0 = cx + Math.cos(a0) * corner;
        const ey0 = cy + Math.sin(a0) * corner;
        const ex1 = nx + Math.cos(a0) * corner;
        const ey1 = ny + Math.sin(a0) * corner;
        for (let s = 1; s < edgeSegs; s++) {
            const t = s / edgeSegs;
            pts.push([ex0 + (ex1 - ex0) * t, ey0 + (ey1 - ey0) * t]);
        }
    }
    return pts;
}

export function createChipGeometry({ corner = 0.1, cornerSegs = 6, edgeSegs = 6, rings = 4, thickness = 0.034, bend = 0.26 } = {}) {
    const ring = outline(corner, cornerSegs, edgeSegs);
    const m = ring.length;
    const bump = noise2(5);
    const surfaceZ = (x, y) => bend * (x * x + y * y * 0.8) - bend * 0.1 + 0.006 * bump(x * 10 + 3, y * 10);
    const pos = [];
    const uv = [];
    const index = [];
    const vertex = (x, y, z) => {
        pos.push(x, y, z);
        uv.push(x + 0.5, y + 0.5);
        return pos.length / 3 - 1;
    };
    // caras superior e inferior: anillos concéntricos desde el centro
    const faces = [];
    for (const side of [1, -1]) {
        const center = vertex(0, 0, surfaceZ(0, 0) + (side * thickness) / 2);
        const ids = [];
        for (let r = 1; r <= rings; r++) {
            const s = r / rings;
            const row = [];
            for (let j = 0; j < m; j++) {
                const x = ring[j][0] * s;
                const y = ring[j][1] * s;
                row.push(vertex(x, y, surfaceZ(x, y) + (side * thickness) / 2));
            }
            ids.push(row);
        }
        for (let j = 0; j < m; j++) {
            const j1 = (j + 1) % m;
            if (side > 0) index.push(center, ids[0][j], ids[0][j1]);
            else index.push(center, ids[0][j1], ids[0][j]);
        }
        for (let r = 0; r < rings - 1; r++) {
            for (let j = 0; j < m; j++) {
                const j1 = (j + 1) % m;
                const a = ids[r][j];
                const b = ids[r][j1];
                const c = ids[r + 1][j];
                const d = ids[r + 1][j1];
                if (side > 0) index.push(a, c, d, a, d, b);
                else index.push(a, d, c, a, b, d);
            }
        }
        faces.push(ids[rings - 1]);
    }
    // canto redondeado: fila intermedia empujada hacia afuera
    const mid = [];
    for (let j = 0; j < m; j++) {
        const x = ring[j][0];
        const y = ring[j][1];
        const len = Math.hypot(x, y);
        const push = 1 + (thickness * 0.45) / len;
        mid.push(vertex(x * push, y * push, surfaceZ(x, y)));
    }
    const top = faces[0];
    const bottom = faces[1];
    for (let j = 0; j < m; j++) {
        const j1 = (j + 1) % m;
        index.push(top[j], mid[j], mid[j1], top[j], mid[j1], top[j1]);
        index.push(mid[j], bottom[j], bottom[j1], mid[j], bottom[j1], mid[j1]);
    }
    const geometry = new BufferGeometry();
    geometry.setIndex(index);
    geometry.setAttribute('position', new Float32BufferAttribute(pos, 3));
    geometry.setAttribute('uv', new Float32BufferAttribute(uv, 2));
    geometry.computeVertexNormals();
    geometry.computeBoundingSphere();
    return geometry;
}

function makeChipCanvases(size) {
    const random = rng(808);
    const color = createCanvas(size, size);
    const ctx = color.getContext('2d');
    const k = size / 512;
    const g = ctx.createRadialGradient(size * 0.45, size * 0.42, 0, size / 2, size / 2, size * 0.62);
    g.addColorStop(0, '#f6b546');
    g.addColorStop(0.65, '#ec9a32');
    g.addColorStop(1, '#d97a22');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, size, size);
    const height = createCanvas(size, size);
    const hctx = height.getContext('2d', { willReadFrequently: true });
    hctx.fillStyle = 'rgb(128,128,128)';
    hctx.fillRect(0, 0, size, size);
    const dot = (c, x, y, r, fill, alpha) => {
        c.globalAlpha = alpha;
        c.fillStyle = fill;
        c.beginPath();
        c.arc(x, y, r, 0, TAU);
        c.fill();
    };
    for (let i = 0; i < 170; i++) {
        const x = random() * size;
        const y = random() * size;
        const r = range(random, 6, 26) * k;
        dot(ctx, x, y, r, random() > 0.5 ? '#f9c862' : '#c8691d', range(random, 0.08, 0.18));
        dot(hctx, x, y, r, random() > 0.5 ? '#fff' : '#000', 0.12);
    }
    // burbujas tostadas del maíz frito
    for (let i = 0; i < 40; i++) {
        const x = random() * size;
        const y = random() * size;
        const r = range(random, 3, 9) * k;
        dot(ctx, x, y, r * 1.25, '#b85c18', 0.35);
        dot(ctx, x - r * 0.2, y - r * 0.2, r, '#fbd27a', 0.55);
        dot(hctx, x, y, r, '#fff', 0.5);
    }
    // condimento: polvo rojo y naranja
    for (let i = 0; i < 1500; i++) {
        const x = random() * size;
        const y = random() * size;
        const r = range(random, 0.8, 2.8) * k;
        const pick = random();
        const fill = pick < 0.5 ? '#c42f17' : pick < 0.8 ? '#d9531d' : pick < 0.93 ? '#9b2410' : '#6b2a0c';
        dot(ctx, x, y, r, fill, range(random, 0.55, 0.95));
        dot(hctx, x, y, r, '#fff', 0.45);
    }
    ctx.globalAlpha = 1;
    hctx.globalAlpha = 1;
    const data = hctx.getImageData(0, 0, size, size).data;
    const field = new Float32Array(size * size);
    for (let i = 0; i < field.length; i++) field[i] = data[i * 4] / 255;
    blurField(field, size, size, 1);
    return { color, normal: heightToNormal(field, size, size, 3.2) };
}

export function createChipMaterial({ renderer, lowPower = false }) {
    const { color, normal } = makeChipCanvases(lowPower ? 256 : 512);
    const anisotropy = Math.min(8, renderer.capabilities.getMaxAnisotropy());
    const map = new CanvasTexture(color);
    map.colorSpace = SRGBColorSpace;
    map.anisotropy = anisotropy;
    map.minFilter = LinearMipmapLinearFilter;
    const normalMap = new CanvasTexture(normal);
    normalMap.colorSpace = NoColorSpace;
    normalMap.anisotropy = anisotropy;
    const material = new MeshStandardMaterial({
        map,
        normalMap,
        normalScale: new Vector2(0.8, 0.8),
        roughness: 0.58,
        metalness: 0,
    });
    return { material, textures: [map, normalMap] };
}

const _m = new Matrix4();
const _p = new Vector3();
const _q = new Quaternion();
const _s = new Vector3();
const _e = new Euler();

// Anillo de la órbita del hero: inclinado para que el arco delantero pase por debajo de
// la ventana del empaque y el trasero quede escondido detrás del paquete (nunca sobre el logo).
const ORBIT_TILT = 0.9;
export const ORBIT_REACH = 1.06 * 0.98;
// Radio aparente de un nacho en unidades del paquete por unidad de tamaño (con perspectiva).
export const CHIP_RADIUS = 0.6 * 1.15;

// Encaja un punto (unidades locales del paquete) dentro de los límites de pantalla que da
// index.js, corrigiendo la perspectiva: lo que está más cerca de la cámara se ve más grande
// y más lejos del centro de la pantalla.
function fitInside(p, radius, b) {
    const f = 1 / Math.max(0.2, 1 - p.z * b.persp);
    const r = radius * f;
    const shiftX = b.cx * (f - 1);
    const shiftY = b.cy * (f - 1);
    if (b.right - b.left > 2 * r) p.x = (clamp(p.x * f + shiftX, b.left + r, b.right - r) - shiftX) / f;
    if (b.top - b.bottom > 2 * r) p.y = (clamp(p.y * f + shiftY, b.bottom + r, b.top - r) - shiftY) / f;
}

export function createChips({ count, orbitCount, geometry, material, seed = 99 }) {
    const mesh = new InstancedMesh(geometry, material, count);
    mesh.instanceMatrix.setUsage(DynamicDrawUsage);
    mesh.frustumCulled = false;
    const random = rng(seed);
    // los ángulos de la explosión se reparten en orden salteado para que los nachos del
    // hero no salgan todos hacia el mismo lado
    const slots = Array.from({ length: count }, (_, i) => (i * 7) % count);
    const data = [];
    for (let i = 0; i < count; i++) {
        // explosión: semicírculo superior y costados; nunca hacia abajo, donde va el texto
        const theta = lerp(-0.3, Math.PI + 0.3, slots[i] / Math.max(1, count - 1)) + range(random, -0.1, 0.1);
        const toCamera = i % 6 === 3;
        const reach = toCamera ? range(random, 0.85, 1.15) : range(random, 0.72, 1.28);
        const burst = new Vector3(
            clamp(Math.cos(theta) * reach * 1.15, -1.3, 1.3),
            clamp(Math.sin(theta) * reach + 0.12, -0.6, 1.55),
            toCamera ? range(random, 0.75, 1.15) : range(random, -0.45, 0.3),
        );
        data.push({
            phase: (i / orbitCount) * TAU + range(random, -0.3, 0.3),
            radius: range(random, 0.88, 1.06),
            height: range(random, -0.3, -0.06),
            bob: random() * TAU,
            size: range(random, 0.27, 0.34),
            rx: range(random, -0.9, 0.9),
            ry: range(random, -0.9, 0.9),
            rz: random() * TAU,
            sx: range(random, -0.5, 0.5),
            sy: range(random, -0.7, 0.7),
            sz: range(random, -0.4, 0.4),
            burst,
            lift: range(random, 0, 0.18),
            spin: new Vector3(range(random, -6, 6), range(random, -6, 6), range(random, -4, 4)),
            burstSize: toCamera ? range(random, 0.32, 0.38) : range(random, 0.2, 0.3),
        });
    }
    // Las primeras orbitCount son las del hero; todas participan en la explosión.
    // bounds (opcional): límites de pantalla en unidades locales; ver fitInside.
    function update({ time, hero, burst, orbit = 1, sizeK = 1, bounds = null }) {
        if (hero < 0.001 && burst < 0.001) {
            mesh.visible = false;
            return;
        }
        mesh.visible = true;
        const exploding = burst >= 0.001;
        const n = exploding ? count : orbitCount;
        const e = easeOutCubic(burst);
        const cosT = Math.cos(ORBIT_TILT);
        const sinT = Math.sin(ORBIT_TILT);
        for (let i = 0; i < n; i++) {
            const d = data[i];
            let s;
            if (exploding) {
                _p.copy(d.burst).multiplyScalar(e);
                _p.y += d.lift * Math.sin(Math.PI * clamp(burst));
                _e.set(d.rx + d.spin.x * burst, d.ry + d.spin.y * burst, d.rz + d.spin.z * burst);
                s = d.burstSize * sizeK * smoothstep(0, 0.28, burst);
                if (bounds) fitInside(_p, s * CHIP_RADIUS, bounds);
            } else {
                const a = d.phase + time * 0.3;
                const x = Math.cos(a) * d.radius * 0.98 * orbit;
                const z = Math.sin(a) * d.radius * 0.62;
                const y = d.height + Math.sin(time * 0.8 + d.bob) * 0.04;
                _p.set(x, y * cosT - z * sinT, y * sinT + z * cosT);
                // cabeceo suave (la cara siempre hacia la cámara) y giro libre en el plano
                _e.set(
                    d.rx * 0.45 + 0.3 * Math.sin(time * 0.7 + d.bob),
                    d.ry * 0.5 + 0.4 * Math.sin(time * 0.5 + d.phase),
                    d.rz + time * d.sz,
                );
                s = d.size * sizeK * hero;
            }
            _s.set(s, s, s);
            _q.setFromEuler(_e);
            _m.compose(_p, _q, _s);
            mesh.setMatrixAt(i, _m);
        }
        mesh.count = n;
        mesh.instanceMatrix.needsUpdate = true;
    }
    return { mesh, update };
}
