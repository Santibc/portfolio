// Geometría procedural del paquete tipo almohada: cuerpo inflado (frente y reverso),
// sellos crimpados con borde en zigzag y la tira superior que se arranca.
import { BufferGeometry, Float32BufferAttribute, Vector3 } from 'three';
import { clamp, lerp, smoothstep, noise2, fbm2 } from './util.js';

const W = 1;
const H = 1.45;
const SEAL = 0.085;
const TEAR_GAP = 0.045;

// Medidas en unidades locales (ancho plano = 1). Origen en el centro del paquete.
export const BAG = {
    W,
    H,
    seal: SEAL,
    yTop: H / 2,
    yBottom: -H / 2,
    ySealTop: H / 2 - SEAL,
    ySealBottom: -H / 2 + SEAL,
    yTear: H / 2 - SEAL - TEAR_GAP,
    T: 0.185,
    pinch: 0.09,
    teeth: 26,
    toothDepth: 0.018,
    sealThickness: 0.0024,
    mouthA: 0.405,
    mouthB: 0.255,
};

function texV(y) {
    return (y - BAG.yBottom) / BAG.H;
}

const noiseA = noise2(11);
const noiseB = noise2(29);

function profileV(y) {
    const t = clamp((y - BAG.ySealBottom) / (BAG.ySealTop - BAG.ySealBottom));
    const s = Math.sin(Math.PI * t);
    return 1 - Math.pow(1 - s, 2.1);
}

function profileU(u) {
    const s = Math.sin(Math.PI * clamp(u));
    return 1 - Math.pow(1 - s, 2.3);
}

// Pliegue diagonal que nace en una esquina del cuerpo (donde el sello plano
// se encuentra con el doblez lateral). Devuelve un desplazamiento en z.
function cornerFolds(u, y) {
    let z = 0;
    const corners = [
        [0, BAG.ySealTop, -1],
        [1, BAG.ySealTop, -1],
        [0, BAG.ySealBottom, 1],
        [1, BAG.ySealBottom, 1],
    ];
    for (let k = 0; k < 4; k++) {
        const [cu, cy, dir] = corners[k];
        const du = (cu === 0 ? u : 1 - u) * W;
        const dy = (y - cy) * dir;
        if (du > 0.36 || dy > 0.36 || dy < 0) continue;
        const along = (du + dy * 1.15) * 0.7071;
        const across = (du - dy * 1.15) * 0.7071;
        const fade = smoothstep(0.0, 0.035, along) * (1 - smoothstep(0.1, 0.34, along));
        // valle principal y una cresta paralela más suave
        z -= 0.03 * Math.exp(-((across / 0.02) ** 2)) * fade;
        z += 0.012 * Math.exp(-(((across - 0.045) / 0.025) ** 2)) * fade;
        z += 0.008 * Math.exp(-(((across + 0.05) / 0.022) ** 2)) * fade;
    }
    return z;
}

function wrinkles(u, y, side, pu, pv) {
    const sample = side > 0 ? noiseA : noiseB;
    // ondulación de baja frecuencia (el foil nunca queda perfecto)
    let z = 0.0075 * fbm2(sample, u * 4.2 + 3.1, y * 3.4 + 7.7, 3) * pu * pv;
    // fruncido vertical junto a los sellos
    const dTop = BAG.ySealTop - y;
    const dBot = y - BAG.ySealBottom;
    const near = Math.exp(-Math.max(dTop, 0) / 0.05) + Math.exp(-Math.max(dBot, 0) / 0.05);
    const phase = sample(u * 6, 2.5) * 2.2;
    z += 0.0055 * near * Math.sin(u * Math.PI * 2 * 12.5 + phase) * Math.sin(Math.PI * u);
    z += cornerFolds(u, y) * pu ** 0.35;
    return z;
}

// Punto del cuerpo cerrado. u en [0,1] a lo ancho del material, y en unidades locales.
function closedPoint(u, y, side, out) {
    const pv = profileV(y);
    const pu = profileU(u);
    const x = (u - 0.5) * W * (1 - BAG.pinch * Math.pow(pv, 1.15));
    const z = BAG.T * pu * pv + wrinkles(u, y, side, pu, pv);
    return out.set(x, y, side * (Math.max(z, 0) + 0.0007));
}

// Lleva un punto del cuerpo cerrado a su posición con la boca abierta (objetivo de morph).
function applyOpen(u, y, side, out) {
    const w = Math.pow(smoothstep(BAG.yTear - 0.66, BAG.yTear, y), 1.35);
    if (w <= 0) return out;
    const lip = smoothstep(BAG.yTear - 0.16, BAG.yTear, y);
    const a = BAG.mouthA * (1 + 0.05 * lip);
    const b = BAG.mouthB * (1 + 0.1 * lip);
    const ang = Math.PI * u;
    // el borde rasgado nunca queda perfectamente parejo
    const wobble = 0.006 * noiseA(u * 9 + side * 5, 3.3) * lip;
    const xo = -Math.cos(ang) * a;
    const zo = side * (Math.sin(ang) * b + 0.004 * noiseB(u * 7, y * 5 + side));
    out.x = lerp(out.x, xo, w);
    out.y += wobble;
    out.z = lerp(out.z, zo, w);
    return out;
}

function openPoint(u, y, side, out) {
    return applyOpen(u, y, side, closedPoint(u, y, side, out));
}

// Sección horizontal de la boca abierta a la altura y (semiejes de la elipse interior).
export function mouthSection(y) {
    const p = new Vector3();
    openPoint(0.0, Math.min(y, BAG.yTear), 1, p);
    const a = Math.abs(p.x);
    openPoint(0.5, Math.min(y, BAG.yTear), 1, p);
    const b = Math.abs(p.z);
    return { a, b };
}

const _a = new Vector3();
const _b = new Vector3();
const _c = new Vector3();
const _d = new Vector3();
const _du = new Vector3();
const _dy = new Vector3();

function surfaceNormal(fn, u, y, side, out) {
    const h = 1e-4;
    fn(Math.min(u + h, 1), y, side, _a);
    fn(Math.max(u - h, 0), y, side, _b);
    fn(u, y + h, side, _c);
    fn(u, y - h, side, _d);
    _du.subVectors(_a, _b);
    _dy.subVectors(_c, _d);
    out.crossVectors(_du, _dy).normalize();
    if (side < 0) out.negate();
    return out;
}

// Reparte más filas/columnas cerca de los bordes, donde hay más curvatura.
function cluster(s, k) {
    return lerp(s, 0.5 - 0.5 * Math.cos(Math.PI * s), k);
}

function interiorShade(y) {
    return lerp(0.14, 1, smoothstep(BAG.yTear - 0.85, BAG.yTear - 0.015, y));
}

// Normales de una grilla de posiciones por diferencias centrales (unilaterales en los
// bordes). La fila `rows` es fantasma: solo sirve para la derivada de la última fila real.
function gridNormals(grid, cols, rows, side, out) {
    const du = new Vector3();
    const dv = new Vector3();
    const n = new Vector3();
    const at = (i, j) => (j * cols + i) * 3;
    for (let j = 0; j < rows; j++) {
        for (let i = 0; i < cols; i++) {
            const il = at(Math.max(0, i - 1), j);
            const ir = at(Math.min(cols - 1, i + 1), j);
            const jd = at(i, Math.max(0, j - 1));
            const ju = at(i, j + 1);
            du.set(grid[ir] - grid[il], grid[ir + 1] - grid[il + 1], grid[ir + 2] - grid[il + 2]);
            dv.set(grid[ju] - grid[jd], grid[ju + 1] - grid[jd + 1], grid[ju + 2] - grid[jd + 2]);
            n.crossVectors(du, dv).normalize();
            if (side < 0) n.negate();
            out.push(n.x, n.y, n.z);
        }
    }
}

// Cuerpo: frente (grupo 0), reverso (grupo 1) e interior plateado (grupo 2, BackSide).
// La boca abierta va como morph target 0.
export function createBodyGeometry({ nu = 88, nv = 128 } = {}) {
    const pos = [];
    const nor = [];
    const uv = [];
    const col = [];
    const openPos = [];
    const openNor = [];
    const index = [];
    const groups = [];
    const p = new Vector3();
    const cols = nu + 1;
    const ys = [];
    for (let j = 0; j <= nv; j++) ys.push(lerp(BAG.ySealBottom, BAG.yTear, cluster(j / nv, 0.4)));
    ys.push(2 * ys[nv] - ys[nv - 1]);
    const us = [];
    for (let i = 0; i <= nu; i++) us.push(cluster(i / nu, 0.55));
    let base = 0;

    for (const side of [1, -1]) {
        const start = index.length;
        const closedGrid = new Float32Array(cols * (nv + 2) * 3);
        const openGrid = new Float32Array(cols * (nv + 2) * 3);
        for (let j = 0; j <= nv + 1; j++) {
            for (let i = 0; i <= nu; i++) {
                const k = (j * cols + i) * 3;
                closedPoint(us[i], ys[j], side, p);
                closedGrid[k] = p.x;
                closedGrid[k + 1] = p.y;
                closedGrid[k + 2] = p.z;
                applyOpen(us[i], ys[j], side, p);
                openGrid[k] = p.x;
                openGrid[k + 1] = p.y;
                openGrid[k + 2] = p.z;
            }
        }
        const real = cols * (nv + 1) * 3;
        for (let k = 0; k < real; k++) {
            pos.push(closedGrid[k]);
            openPos.push(openGrid[k]);
        }
        gridNormals(closedGrid, cols, nv + 1, side, nor);
        gridNormals(openGrid, cols, nv + 1, side, openNor);
        for (let j = 0; j <= nv; j++) {
            const shade = interiorShade(ys[j]);
            for (let i = 0; i <= nu; i++) {
                uv.push(side > 0 ? us[i] : 1 - us[i], texV(ys[j]));
                col.push(shade, shade, shade);
            }
        }
        for (let j = 0; j < nv; j++) {
            for (let i = 0; i < nu; i++) {
                const a = base + j * cols + i;
                const b = a + 1;
                const c = a + cols;
                const d = c + 1;
                if (side > 0) index.push(a, b, d, a, d, c);
                else index.push(a, d, b, a, c, d);
            }
        }
        groups.push([start, index.length - start]);
        base += cols * (nv + 1);
    }

    const geometry = new BufferGeometry();
    geometry.setIndex(index);
    geometry.setAttribute('position', new Float32BufferAttribute(pos, 3));
    geometry.setAttribute('normal', new Float32BufferAttribute(nor, 3));
    geometry.setAttribute('uv', new Float32BufferAttribute(uv, 2));
    geometry.setAttribute('color', new Float32BufferAttribute(col, 3));
    geometry.morphAttributes.position = [new Float32BufferAttribute(openPos, 3)];
    geometry.morphAttributes.normal = [new Float32BufferAttribute(openNor, 3)];
    geometry.addGroup(groups[0][0], groups[0][1], 0);
    geometry.addGroup(groups[1][0], groups[1][1], 1);
    geometry.addGroup(0, index.length, 2);
    geometry.computeBoundingSphere();
    geometry.boundingSphere.radius *= 1.25;
    return geometry;
}

// Constructor incremental de mallas con grupos de material.
class MeshBuilder {
    constructor() {
        this.pos = [];
        this.nor = [];
        this.uv = [];
        this.col = [];
        this.index = [];
        this.groups = [];
        this.count = 0;
    }

    vertex(p, n, u, v, shade = 1) {
        this.pos.push(p.x, p.y, p.z);
        this.nor.push(n.x, n.y, n.z);
        this.uv.push(u, v);
        this.col.push(shade, shade, shade);
        return this.count++;
    }

    group(start, materialIndex) {
        this.groups.push([start, this.index.length - start, materialIndex]);
    }

    // Cuadrilátero con normal plana; ordena los vértices para que mire hacia `normal`.
    quad(p0, p1, p2, p3, normal, uvs) {
        _du.subVectors(p1, p0);
        _dy.subVectors(p2, p0);
        const flip = _c.crossVectors(_du, _dy).dot(normal) < 0;
        const ids = [p0, p1, p2, p3].map((p, k) => this.vertex(p, normal, uvs[k][0], uvs[k][1]));
        if (flip) this.index.push(ids[0], ids[2], ids[1], ids[1], ids[2], ids[3]);
        else this.index.push(ids[0], ids[1], ids[2], ids[1], ids[3], ids[2]);
    }

    build() {
        const geometry = new BufferGeometry();
        geometry.setIndex(this.index);
        geometry.setAttribute('position', new Float32BufferAttribute(this.pos, 3));
        geometry.setAttribute('normal', new Float32BufferAttribute(this.nor, 3));
        geometry.setAttribute('uv', new Float32BufferAttribute(this.uv, 2));
        geometry.setAttribute('color', new Float32BufferAttribute(this.col, 3));
        for (const [start, count, material] of this.groups) geometry.addGroup(start, count, material);
        geometry.computeBoundingSphere();
        return geometry;
    }
}

// Sello plano (dos caras + cantos) con el borde exterior en zigzag.
// Grupos: cara frontal -> matFront, cara trasera -> matBack, cantos -> matFront.
function addSeal(builder, edge, matFront, matBack) {
    const t = BAG.sealThickness;
    const top = edge === 'top';
    const yInner = top ? BAG.ySealTop : BAG.ySealBottom;
    const yTip = top ? BAG.yTop : BAG.yBottom;
    const yValley = yTip + (top ? -1 : 1) * BAG.toothDepth;
    const nK = BAG.teeth * 2;
    const rows = 3;
    const edgeY = (k) => (k % 2 === 1 ? yTip : yValley);
    const p = new Vector3();
    const n = new Vector3();

    for (const side of [1, -1]) {
        const start = builder.index.length;
        const first = builder.count;
        n.set(0, 0, side);
        for (let k = 0; k <= nK; k++) {
            const x = -W / 2 + (W * k) / nK;
            for (let r = 0; r <= rows; r++) {
                const y = lerp(edgeY(k), yInner, r / rows);
                p.set(x, y, side * t);
                const u = (x + W / 2) / W;
                builder.vertex(p, n, side > 0 ? u : 1 - u, texV(y));
            }
        }
        for (let k = 0; k < nK; k++) {
            for (let r = 0; r < rows; r++) {
                const a = first + k * (rows + 1) + r;
                const b = a + rows + 1;
                const c = a + 1;
                const d = b + 1;
                // orientar según el lado y si el sello está arriba o abajo
                const ccw = (side > 0) === top;
                if (ccw) builder.index.push(a, c, b, b, c, d);
                else builder.index.push(a, b, c, b, d, c);
            }
        }
        builder.group(start, side > 0 ? matFront : matBack);
    }

    // cantos del zigzag y de los costados
    const start = builder.index.length;
    const outward = new Vector3();
    for (let k = 0; k < nK; k++) {
        const x0 = -W / 2 + (W * k) / nK;
        const x1 = -W / 2 + (W * (k + 1)) / nK;
        const y0 = edgeY(k);
        const y1 = edgeY(k + 1);
        const dx = x1 - x0;
        const dy = y1 - y0;
        if (top) outward.set(-dy, dx, 0).normalize();
        else outward.set(dy, -dx, 0).normalize();
        const v = texV(top ? yTip : yTip);
        builder.quad(
            new Vector3(x0, y0, t), new Vector3(x1, y1, t),
            new Vector3(x0, y0, -t), new Vector3(x1, y1, -t),
            outward, [[(x0 + 0.5), v], [(x1 + 0.5), v], [(x0 + 0.5), v], [(x1 + 0.5), v]],
        );
    }
    for (const sx of [-1, 1]) {
        const x = (sx * W) / 2;
        const yE = yValley;
        outward.set(sx, 0, 0);
        const u = sx < 0 ? 0 : 1;
        builder.quad(
            new Vector3(x, yE, t), new Vector3(x, yInner, t),
            new Vector3(x, yE, -t), new Vector3(x, yInner, -t),
            outward, [[u, texV(yE)], [u, texV(yInner)], [u, texV(yE)], [u, texV(yInner)]],
        );
    }
    builder.group(start, matFront);
}

// Sello inferior. Materiales: 0 = sello frente, 1 = sello reverso.
export function createBottomSealGeometry() {
    const builder = new MeshBuilder();
    addSeal(builder, 'bottom', 0, 1);
    return builder.build();
}

// Tira superior que se arranca: franja del cuerpo sobre la línea "Abre aquí" + sello superior.
// Materiales: 0 = frente impreso, 1 = reverso impreso, 2 = sello frente, 3 = sello reverso, 4 = interior.
export function createStripGeometry({ nu = 88, rows = 7 } = {}) {
    const builder = new MeshBuilder();
    const p = new Vector3();
    const n = new Vector3();
    const bandStart = builder.index.length;

    for (const side of [1, -1]) {
        const start = builder.index.length;
        const first = builder.count;
        for (let j = 0; j <= rows; j++) {
            const y = lerp(BAG.yTear, BAG.ySealTop, j / rows);
            for (let i = 0; i <= nu; i++) {
                const u = cluster(i / nu, 0.55);
                closedPoint(u, y, side, p);
                surfaceNormal(closedPoint, u, y, side, n);
                builder.vertex(p, n, side > 0 ? u : 1 - u, texV(y), interiorShade(y));
            }
        }
        for (let j = 0; j < rows; j++) {
            for (let i = 0; i < nu; i++) {
                const a = first + j * (nu + 1) + i;
                const b = a + 1;
                const c = a + nu + 1;
                const d = c + 1;
                if (side > 0) builder.index.push(a, b, d, a, d, c);
                else builder.index.push(a, d, b, a, c, d);
            }
        }
        builder.group(start, side > 0 ? 0 : 1);
    }
    const bandCount = builder.index.length - bandStart;
    addSeal(builder, 'top', 2, 3);
    builder.groups.push([bandStart, bandCount, 4]);
    const geometry = builder.build();
    geometry.boundingSphere.radius *= 3;
    return geometry;
}

const tearNoise = noise2(53);

// Deforma la tira mientras se rasga desde la tijera (izquierda) hacia la derecha. Lo ya
// arrancado (a la izquierda del frente) se levanta con un rizo corto y después cuelga
// hacia adelante y hacia abajo, como foil flexible. El borde del corte queda irregular.
export function peelStrip(geometry, basePositions, baseNormals, xFront, { radius = 0.18, aMax = 0.6, droop = 1.9, fold = 0.6 } = {}) {
    const posAttr = geometry.getAttribute('position');
    const arr = posAttr.array;
    if (xFront <= -W / 2 - 0.001) {
        arr.set(basePositions);
        geometry.getAttribute('normal').array.set(baseNormals);
        geometry.getAttribute('normal').needsUpdate = true;
        posAttr.needsUpdate = true;
        return;
    }
    const r = radius;
    const sCurl = r * aMax;
    const thetaMin = -1.2;
    const sBend = (aMax - thetaMin) / droop;
    const curlX = -r * Math.sin(aMax);
    const curlY = r * (1 - Math.cos(aMax));
    for (let i = 0; i < arr.length; i += 3) {
        const x = basePositions[i];
        const y = basePositions[i + 1];
        const z = basePositions[i + 2];
        const d = xFront - x;
        if (d <= 0) {
            arr[i] = x;
            arr[i + 1] = y;
            arr[i + 2] = z;
            continue;
        }
        let h = y - BAG.yTear;
        if (h < 0.004) h += 0.005 * tearNoise(x * 31, 1.7) * smoothstep(0, 0.04, d);
        let cx;
        let cy;
        let theta;
        if (d <= sCurl) {
            theta = d / r;
            cx = -r * Math.sin(theta);
            cy = r * (1 - Math.cos(theta));
        } else {
            // la tangente baja con la distancia (gravedad) hasta quedar colgando
            const s = d - sCurl;
            const s1 = Math.min(s, sBend);
            theta = aMax - droop * s1;
            cx = curlX - (Math.sin(aMax) - Math.sin(theta)) / droop;
            cy = curlY + (Math.cos(theta) - Math.cos(aMax)) / droop;
            if (s > sBend) {
                cx -= Math.cos(thetaMin) * (s - sBend);
                cy += Math.sin(thetaMin) * (s - sBend);
            }
        }
        // h se mide sobre la normal de la curva: la franja se enrosca entera
        const px = cx + h * Math.sin(theta);
        const py = BAG.yTear + cy + h * Math.cos(theta);
        // pliegue hacia la cámara alrededor de un eje vertical en el frente de rasgado
        const phi = fold * smoothstep(0, 0.35, d);
        const c = Math.cos(phi);
        const sn = Math.sin(phi);
        arr[i] = xFront + px * c + z * sn;
        arr[i + 1] = py;
        arr[i + 2] = -px * sn + z * c;
    }
    posAttr.needsUpdate = true;
    geometry.computeVertexNormals();
}
