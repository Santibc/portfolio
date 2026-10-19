// Relleno del paquete: nachos que enmarcan la boca, un montículo de frijol que sube y se
// cubre de carne y queso, piezas instanciadas que caen con gravedad y la salsa en hilo.
// Todo es función pura del progreso p (0..1) y de una semilla, así que al devolver el
// scroll se deshace exactamente.
import {
    BufferGeometry,
    CanvasTexture,
    CatmullRomCurve3,
    Color,
    CylinderGeometry,
    DataTexture,
    DoubleSide,
    DynamicDrawUsage,
    Euler,
    Float32BufferAttribute,
    Group,
    IcosahedronGeometry,
    InstancedMesh,
    LatheGeometry,
    LinearFilter,
    LinearMipmapLinearFilter,
    Matrix4,
    Mesh,
    MeshPhysicalMaterial,
    MeshStandardMaterial,
    NoColorSpace,
    Quaternion,
    RGBAFormat,
    SRGBColorSpace,
    Shape,
    ShapeGeometry,
    SphereGeometry,
    TubeGeometry,
    UnsignedByteType,
    Vector2,
    Vector3,
    BoxGeometry,
} from 'three';
import { mergeGeometries, mergeVertices } from 'three/addons/utils/BufferGeometryUtils.js';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import { BAG, mouthSection } from './geometry.js';
import { TAU, blurField, clamp, createCanvas, easeOutCubic, fbm2, heightToNormal, lerp, noise2, range, rng, smoothstep } from './util.js';

const RIM = BAG.yTear;

// ---------------------------------------------------------------- montículo

const SECTION_SAMPLES = 48;
const SECTION_DEPTH = 0.5;
const sectionTable = [];
for (let i = 0; i <= SECTION_SAMPLES; i++) {
    sectionTable.push(mouthSection(RIM - SECTION_DEPTH + (SECTION_DEPTH * i) / SECTION_SAMPLES));
}

// Semiejes interiores (con margen) de la bolsa abierta a la altura y.
function sectionAt(y) {
    const t = clamp((y - (RIM - SECTION_DEPTH)) / SECTION_DEPTH) * SECTION_SAMPLES;
    const i = Math.min(SECTION_SAMPLES - 1, Math.floor(t));
    const f = t - i;
    const s0 = sectionTable[i];
    const s1 = sectionTable[i + 1];
    return { a: lerp(s0.a, s1.a, f) * 0.9, b: lerp(s0.b, s1.b, f) * 0.82 };
}

// El frijol sube casi todo en su ventana (cada capa se ve apilarse); después la comida
// sigue subiendo un poco y las piezas ya asentadas suben con ella.
const LATE_RISE = 0.04;

function lateRise(p) {
    return LATE_RISE * smoothstep(0.4, 0.8, p);
}

function moundLevel(p) {
    return lerp(RIM - 0.26, RIM - 0.075, smoothstep(0.2, 0.36, p)) + lateRise(p);
}

function moundDome(p) {
    return lerp(0.012, 0.19, smoothstep(0.2, 0.38, p));
}

const moundNoise = noise2(73);

function moundHeight(x, z, p) {
    const level = moundLevel(p);
    const { a, b } = sectionAt(level);
    const r2 = (x / a) ** 2 + (z / b) ** 2;
    const dome = moundDome(p) * Math.pow(Math.max(0, 1 - r2), 0.72);
    const rough = fbm2(moundNoise, x * 7 + 2.3, z * 7 + 5.1, 4) * 0.03 + fbm2(moundNoise, x * 19 + 9, z * 19 - 3, 2) * 0.009;
    return level + dome + rough * smoothstep(0.18, 0.36, p) * (1 - 0.5 * r2);
}

// Frijol refrito: base rojiza mate con grumos y algunos granos enteros. Devuelve el color
// y un campo de alturas para el normal map (los grumos tienen relieve).
function paintBeans(size) {
    const canvas = createCanvas(size, size);
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const random = rng(515);
    const k = size / 512;
    ctx.fillStyle = '#5a2a1a';
    ctx.fillRect(0, 0, size, size);
    const blob = (fill, r, alpha, squash = 0.75) => {
        ctx.globalAlpha = alpha;
        ctx.fillStyle = fill;
        ctx.beginPath();
        ctx.ellipse(random() * size, random() * size, r * k, r * k * range(random, squash * 0.7, squash), random() * TAU, 0, TAU);
        ctx.fill();
    };
    const tones = ['#4c2214', '#6b3321', '#552717', '#763c25', '#62301d'];
    for (let i = 0; i < 420; i++) blob(tones[Math.floor(random() * tones.length)], range(random, 8, 24), 0.5);
    for (let i = 0; i < 1100; i++) blob(random() > 0.5 ? '#43190e' : '#7e452b', range(random, 2, 5), 0.55);
    // granos enteros: riñones granate con un brillo pequeño
    for (let i = 0; i < 110; i++) {
        const x = random() * size;
        const y = random() * size;
        const a = random() * TAU;
        const r = range(random, 7, 10) * k;
        ctx.globalAlpha = 0.9;
        ctx.fillStyle = random() > 0.5 ? '#5c2216' : '#6d2a1c';
        ctx.beginPath();
        ctx.ellipse(x, y, r, r * 0.62, a, 0, TAU);
        ctx.fill();
        ctx.globalAlpha = 0.45;
        ctx.fillStyle = '#9a5a44';
        ctx.beginPath();
        ctx.ellipse(x - Math.cos(a) * r * 0.3, y - Math.sin(a) * r * 0.3, r * 0.35, r * 0.16, a, 0, TAU);
        ctx.fill();
    }
    ctx.globalAlpha = 1;
    const px = ctx.getImageData(0, 0, size, size).data;
    const field = new Float32Array(size * size);
    for (let i = 0; i < field.length; i++) field[i] = (px[i * 4] * 0.5 + px[i * 4 + 1] * 0.35 + px[i * 4 + 2] * 0.15) / 255;
    blurField(field, size, size, 1);
    return { color: canvas, normal: heightToNormal(field, size, size, 6) };
}

// Hebras de carne y tiritas de queso que se revelan sobre el frijol durante su ventana.
// Por canal: R = umbral de aparición × máscara (carne), G = máscara (carne); B y A igual
// para el queso. Dividir R/G deshace el filtrado, así los mipmaps no adelantan la carne.
function paintFillData(size) {
    const random = rng(733);
    const k = size / 512;
    const layer = (n, widths, lengths, bend) => {
        const canvas = createCanvas(size, size);
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, size, size);
        ctx.globalCompositeOperation = 'lighten';
        ctx.lineCap = 'round';
        for (let i = 0; i < n; i++) {
            const v = Math.round(range(random, 0.06, 1) * 255);
            ctx.strokeStyle = `rgb(${v},255,0)`;
            ctx.lineWidth = range(random, widths[0], widths[1]) * k;
            const x = random() * size;
            const y = random() * size;
            const a = random() * TAU;
            const len = range(random, lengths[0], lengths[1]) * k;
            const b = bend * (random() > 0.5 ? 1 : -1);
            ctx.beginPath();
            ctx.moveTo(x, y);
            ctx.quadraticCurveTo(x + Math.cos(a + b) * len * 0.5, y + Math.sin(a + b) * len * 0.5, x + Math.cos(a) * len, y + Math.sin(a) * len);
            ctx.stroke();
        }
        return ctx.getImageData(0, 0, size, size).data;
    };
    const meat = layer(950, [3, 6], [16, 48], 0.45);
    const cheese = layer(1500, [2.2, 3.6], [8, 20], 0.25);
    const data = new Uint8Array(size * size * 4);
    for (let i = 0; i < size * size; i++) {
        data[i * 4] = meat[i * 4];
        data[i * 4 + 1] = meat[i * 4 + 1];
        data[i * 4 + 2] = cheese[i * 4];
        data[i * 4 + 3] = cheese[i * 4 + 1];
    }
    const texture = new DataTexture(data, size, size, RGBAFormat, UnsignedByteType);
    texture.colorSpace = NoColorSpace;
    texture.minFilter = LinearMipmapLinearFilter;
    texture.magFilter = LinearFilter;
    texture.generateMipmaps = true;
    texture.anisotropy = 4;
    texture.needsUpdate = true;
    return texture;
}

// Colores lineales de la carne desmechada (#8a5530 → #a8693c) y del queso rayado.
const FILL_SHADER = /* glsl */ `
{
    vec4 fillData = texture2D( fillMap, vMapUv );
    float meatThr = fillData.r / max( fillData.g, 0.004 );
    float meatOn = fillData.g * smoothstep( meatThr, meatThr + 0.05, meatAmount );
    vec3 meatColor = mix( vec3( 0.254, 0.091, 0.030 ), vec3( 0.392, 0.141, 0.045 ), fract( meatThr * 37.0 ) );
    diffuseColor.rgb = mix( diffuseColor.rgb, meatColor * ( 0.6 + 0.4 * fillData.g ), meatOn );
    float cheeseThr = fillData.b / max( fillData.a, 0.004 );
    float cheeseOn = fillData.a * smoothstep( cheeseThr, cheeseThr + 0.05, cheeseAmount );
    vec3 cheeseColor = mix( vec3( 0.888, 0.716, 0.352 ), vec3( 0.965, 0.863, 0.584 ), fract( cheeseThr * 23.0 ) );
    diffuseColor.rgb = mix( diffuseColor.rgb, cheeseColor * ( 0.75 + 0.25 * fillData.a ), cheeseOn );
}
`;

function createMound() {
    const NR = 16;
    const NS = 72;
    const count = 1 + NR * NS + NS;
    const geometry = new BufferGeometry();
    const pos = new Float32Array(count * 3);
    const col = [];
    const uv = [];
    const polar = [];
    // sombreado de oclusión hacia el borde (el color lo pone la textura)
    const pushColor = (r, ang) => {
        const shade = 0.5 + 0.5 * (1 - smoothstep(0.55, 1, r));
        col.push(shade, shade, shade);
        uv.push(0.5 + Math.cos(ang) * r * 0.5, 0.5 + Math.sin(ang) * r * 0.32);
    };
    polar.push([0, 0, 0]);
    pushColor(0, 0);
    for (let r = 1; r <= NR; r++) {
        for (let s = 0; s < NS; s++) {
            const ang = (s / NS) * TAU;
            polar.push([r / NR, ang, 0]);
            pushColor(r / NR, ang);
        }
    }
    for (let s = 0; s < NS; s++) {
        const ang = (s / NS) * TAU;
        polar.push([1, ang, 1]);
        pushColor(1, ang);
    }
    const index = [];
    for (let s = 0; s < NS; s++) index.push(0, 1 + ((s + 1) % NS), 1 + s);
    // anillo r (base 1) empieza en 1 + (r - 1) * NS; se une con el anillo siguiente
    for (let r = 1; r < NR; r++) {
        for (let s = 0; s < NS; s++) {
            const s1 = (s + 1) % NS;
            const a = 1 + (r - 1) * NS + s;
            const b = 1 + (r - 1) * NS + s1;
            const c = 1 + r * NS + s;
            const d = 1 + r * NS + s1;
            index.push(a, b, d, a, d, c);
        }
    }
    const rimStart = 1 + (NR - 1) * NS;
    const skirtStart = 1 + NR * NS;
    for (let s = 0; s < NS; s++) {
        const s1 = (s + 1) % NS;
        index.push(rimStart + s, rimStart + s1, skirtStart + s1, rimStart + s, skirtStart + s1, skirtStart + s);
    }
    geometry.setIndex(index);
    geometry.setAttribute('position', new Float32BufferAttribute(pos, 3));
    geometry.setAttribute('color', new Float32BufferAttribute(col, 3));
    geometry.setAttribute('uv', new Float32BufferAttribute(uv, 2));

    const beans = paintBeans(512);
    const map = new CanvasTexture(beans.color);
    map.colorSpace = SRGBColorSpace;
    map.anisotropy = 4;
    const normalMap = new CanvasTexture(beans.normal);
    normalMap.colorSpace = NoColorSpace;
    normalMap.anisotropy = 4;
    const fillMap = paintFillData(512);
    const meatAmount = { value: 0 };
    const cheeseAmount = { value: 0 };
    // comida mate: nada de clearcoat (con brillo parecía ganache)
    const material = new MeshStandardMaterial({
        map,
        normalMap,
        normalScale: new Vector2(0.9, 0.9),
        vertexColors: true,
        roughness: 0.86,
        metalness: 0,
    });
    material.onBeforeCompile = (shader) => {
        shader.uniforms.fillMap = { value: fillMap };
        shader.uniforms.meatAmount = meatAmount;
        shader.uniforms.cheeseAmount = cheeseAmount;
        shader.fragmentShader = shader.fragmentShader
            .replace('#include <common>', '#include <common>\nuniform sampler2D fillMap;\nuniform float meatAmount;\nuniform float cheeseAmount;')
            .replace('#include <map_fragment>', `#include <map_fragment>\n${FILL_SHADER}`);
    };
    const mesh = new Mesh(geometry, material);
    mesh.frustumCulled = false;

    let last = -1;
    function update(p) {
        mesh.visible = p > 0.19;
        if (!mesh.visible || Math.abs(p - last) < 1e-5) return;
        last = p;
        meatAmount.value = smoothstep(0.27, 0.42, p) * 1.06;
        cheeseAmount.value = smoothstep(0.6, 0.76, p) * 1.06;
        const level = moundLevel(p);
        const { a, b } = sectionAt(level);
        const arr = geometry.getAttribute('position').array;
        for (let i = 0; i < polar.length; i++) {
            const [r, ang, skirt] = polar[i];
            const x = Math.cos(ang) * r * a;
            const z = Math.sin(ang) * r * b;
            const k = skirt ? 0.45 : 1;
            arr[i * 3] = x * k;
            arr[i * 3 + 1] = skirt ? level - 0.22 : moundHeight(x, z, p);
            arr[i * 3 + 2] = z * k;
        }
        geometry.getAttribute('position').needsUpdate = true;
        geometry.computeVertexNormals();
    }
    return { mesh, update, textures: [map, normalMap, fillMap], dispose() {
        geometry.dispose();
        material.dispose();
        map.dispose();
        normalMap.dispose();
        fillMap.dispose();
    } };
}

// ---------------------------------------------------------------- piezas

function paintVertices(g, fn) {
    const pos = g.getAttribute('position');
    const col = [];
    const v = new Vector3();
    const c = new Color();
    for (let i = 0; i < pos.count; i++) {
        v.fromBufferAttribute(pos, i);
        fn(v, c, i);
        col.push(c.r, c.g, c.b);
    }
    g.setAttribute('color', new Float32BufferAttribute(col, 3));
    return g;
}

// Quita normales y uv para soldar costuras y vuelve a calcular normales suaves.
function weld(g) {
    g.deleteAttribute('normal');
    g.deleteAttribute('uv');
    return mergeVertices(g);
}

function makeBean() {
    const g = new SphereGeometry(0.5, 12, 8);
    g.scale(1, 0.6, 0.68);
    const pos = g.getAttribute('position');
    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        pos.setY(i, pos.getY(i) - 0.12 * (1 - 4 * x * x));
    }
    g.computeVertexNormals();
    return g;
}

// Carne desmechada: haz de tres fibras finas y onduladas.
function makeStrand() {
    const random = rng(61);
    const parts = [];
    for (let f = 0; f < 3; f++) {
        const pts = [];
        const oy = range(random, -0.035, 0.035);
        const oz = range(random, -0.05, 0.05);
        const len = range(random, 0.78, 1.04);
        for (let k = 0; k <= 6; k++) {
            const t = k / 6;
            pts.push(new Vector3((t - 0.5) * len, oy + 0.045 * Math.sin(t * 6.3 + f * 2.1), oz + 0.05 * Math.cos(t * 5.1 + f)));
        }
        parts.push(new TubeGeometry(new CatmullRomCurve3(pts), 12, range(random, 0.028, 0.042), 5, false));
    }
    const g = mergeGeometries(parts);
    parts.forEach((p) => p.dispose());
    g.scale(1, 0.75, 1);
    return g;
}

// Chicharrón carnudo: bloque irregular con la piel frita reventada en burbujas arriba,
// una franja de grasa y la carne rojiza abajo.
function makeChunk(lowPower) {
    const g = weld(new IcosahedronGeometry(0.5, lowPower ? 4 : 7));
    const pos = g.getAttribute('position');
    const low = noise2(17);
    const bubbles = noise2(23);
    const grain = noise2(29);
    const bubble = new Float32Array(pos.count);
    const v = new Vector3();
    for (let i = 0; i < pos.count; i++) {
        v.fromBufferAttribute(pos, i).multiplyScalar(2);
        // de esfera a trozo: esquinas más llenas (superelipsoide) y forma irregular
        const sx = Math.sign(v.x) * Math.abs(v.x) ** 0.6;
        const sy = Math.sign(v.y) * Math.abs(v.y) ** 0.6;
        const sz = Math.sign(v.z) * Math.abs(v.z) ** 0.6;
        v.set(sx * 0.58, sy * 0.36, sz * 0.46);
        const k = 1 + 0.2 * fbm2(low, v.x * 2.4 + 1, v.z * 2.4 - v.y * 2, 2);
        v.multiplyScalar(k);
        // ampollas de la piel frita (arriba) y grano de la carne (abajo)
        const skin = smoothstep(0.0, 0.16, v.y);
        const b = smoothstep(-0.1, 0.3, fbm2(bubbles, v.x * 11 + 3, v.z * 11 + v.y * 5, 3));
        bubble[i] = b * skin;
        const n = v.clone().normalize();
        v.addScaledVector(n, 0.07 * b * skin + 0.028 * grain(v.x * 23, v.z * 23 + v.y * 11) * (1 - skin));
        if (v.y < -0.2) v.y = -0.2 + (v.y + 0.2) * 0.3;
        pos.setXYZ(i, v.x, v.y, v.z);
    }
    const crack = new Color('#6e3c16');
    const skinColor = new Color('#c47c35');
    const puff = new Color('#e9bd70');
    const fat = new Color('#e8cba0');
    const meat = new Color('#9a5434');
    const meatLight = new Color('#b46a44');
    paintVertices(g, (p, c, i) => {
        if (p.y > 0.06) {
            const b = bubble[i];
            if (b < 0.5) c.copy(crack).lerp(skinColor, b * 2);
            else c.copy(skinColor).lerp(puff, (b - 0.5) * 2);
        } else if (p.y > -0.04) {
            c.copy(fat).lerp(skinColor, smoothstep(0.02, 0.06, p.y));
        } else {
            c.copy(meat).lerp(meatLight, 0.5 + 0.5 * grain(p.x * 13, p.z * 13));
        }
    });
    g.computeVertexNormals();
    return g;
}

// Tajada de maduro: corte diagonal largo, centro dorado y borde caramelizado oscuro.
function makeSlice() {
    // de abajo hacia arriba: así LatheGeometry deja las caras mirando hacia afuera
    const profile = [
        [0, -0.06], [0.2, -0.06], [0.36, -0.058], [0.45, -0.05], [0.5, -0.02],
        [0.5, 0.02], [0.45, 0.05], [0.36, 0.058], [0.2, 0.06], [0, 0.06],
    ].map(([x, y]) => new Vector2(x, y));
    const g = weld(new LatheGeometry(profile, 36));
    g.scale(2.3, 1, 1);
    const center = new Color('#f0a83c');
    const mid = new Color('#e08a2a');
    const edge = new Color('#9a4a14');
    const n = noise2(12);
    paintVertices(g, (v, c) => {
        const r = Math.hypot(v.x / 2.3, v.z) * 2;
        c.copy(center).lerp(mid, smoothstep(0.25, 0.7, r)).lerp(edge, smoothstep(0.78, 0.98, r));
        // manchas caramelizadas de la sartén
        c.lerp(edge, 0.55 * smoothstep(0.25, 0.6, n(v.x * 4 + 2, v.z * 7)));
    });
    // la tajada se curva un poco, como el plátano
    const pos = g.getAttribute('position');
    for (let i = 0; i < pos.count; i++) pos.setY(i, pos.getY(i) + 0.05 * (pos.getX(i) / 1.15) ** 2);
    g.computeVertexNormals();
    return g;
}

function makeKernel() {
    const g = new SphereGeometry(0.5, 9, 7);
    g.scale(1, 0.78, 0.88);
    const pos = g.getAttribute('position');
    for (let i = 0; i < pos.count; i++) {
        const y = pos.getY(i);
        if (y < -0.2) pos.setY(i, -0.2 - (y + 0.2) * 0.35);
    }
    g.computeVertexNormals();
    return g;
}

function makeShred() {
    const g = new BoxGeometry(1, 0.08, 0.13, 10, 1, 1);
    const pos = g.getAttribute('position');
    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);
        const z = pos.getZ(i);
        const tw = x * 0.9;
        pos.setXYZ(i, x, y * Math.cos(tw) - z * Math.sin(tw) + 0.16 * (x * x * 4 - 0.5) * 0.5, y * Math.sin(tw) + z * Math.cos(tw));
    }
    g.computeVertexNormals();
    return g;
}

// Cucharada de guacamole: baja y redondeada, con un piquito torcido arriba y grumos de
// aguacate machacado (con vetas en espiral regulares parecía helado).
function makeDollop() {
    const profile = [new Vector2(0, -0.04)];
    for (let k = 0; k <= 16; k++) {
        const h = k / 16;
        const r = 0.5 * Math.sqrt(Math.max(0, 1 - h * h)) * (1 + 0.1 * Math.sin(Math.PI * Math.min(1, h * 2.4)));
        profile.push(new Vector2(r, -0.04 + 0.32 * h + 0.07 * smoothstep(0.7, 1, h)));
    }
    const g = weld(new LatheGeometry(profile, 40));
    const pos = g.getAttribute('position');
    const n = noise2(8);
    const chunk = noise2(14);
    const lump = new Float32Array(pos.count);
    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);
        const z = pos.getZ(i);
        const h = clamp((y + 0.04) / 0.39);
        const ang = Math.atan2(z, x);
        // contorno irregular de cuchara y grumos de aguacate machacado
        lump[i] = fbm2(chunk, x * 9 + 2, z * 9 + y * 6, 2);
        const k = 1 + 0.1 * n(Math.cos(ang) * 1.6 + 4, Math.sin(ang) * 1.6) + 0.035 * lump[i] + 0.04 * Math.sin(ang * 2 + h * 6) * h;
        pos.setXYZ(i, x * k + 0.06 * h * h * h, y + 0.012 * lump[i], z * k);
    }
    const base = new Color('#8fb04e');
    const light = new Color('#a4c25e');
    const dark = new Color('#6b8a35');
    paintVertices(g, (v, c, i) => {
        c.copy(base).lerp(lump[i] > 0 ? light : dark, Math.min(1, Math.abs(lump[i]) * 1.6));
    });
    g.computeVertexNormals();
    return g;
}

// Dado irregular para el pico de gallo (tomate y cebolla): caja redondeada con vértices
// movidos. Con flesh, una cara muestra la pulpa más clara (multiplica el color de instancia).
function makeDice(seed, flesh = null) {
    const g = weld(new RoundedBoxGeometry(1, 1, 1, 2, 0.24));
    const pos = g.getAttribute('position');
    const n = noise2(seed);
    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);
        const z = pos.getZ(i);
        pos.setXYZ(i, x * (1 + 0.2 * n(y * 3 + 1, z * 3)), y * (1 + 0.2 * n(z * 3 + 5, x * 3)), z * (1 + 0.2 * n(x * 3 - 2, y * 3)));
    }
    if (flesh) paintVertices(g, (v, c) => c.setRGB(1, 1, 1).lerp(flesh, smoothstep(0.25, 0.45, v.y)));
    g.computeVertexNormals();
    return g;
}

// Hojita de cilantro: tres lóbulos planos con una curvatura leve.
function makeLeaf() {
    const s = new Shape();
    s.moveTo(0, -0.5);
    s.bezierCurveTo(-0.32, -0.38, -0.56, -0.02, -0.3, 0.14);
    s.bezierCurveTo(-0.46, 0.42, -0.12, 0.56, 0, 0.36);
    s.bezierCurveTo(0.12, 0.56, 0.46, 0.42, 0.3, 0.14);
    s.bezierCurveTo(0.56, -0.02, 0.32, -0.38, 0, -0.5);
    const g = new ShapeGeometry(s, 6);
    g.rotateX(-Math.PI / 2);
    const pos = g.getAttribute('position');
    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const z = pos.getZ(i);
        pos.setY(i, 0.12 * (x * x + z * z));
    }
    g.computeVertexNormals();
    return g;
}

const physical = (opts) => new MeshPhysicalMaterial({ roughness: 0.4, metalness: 0, ...opts });
const standard = (opts) => new MeshStandardMaterial({ roughness: 0.6, metalness: 0, ...opts });

// Ventanas del progreso: [0.2,0.4) frijol y carne; [0.4,0.6) chicharrón, maduro y maíz;
// [0.6,0.8) queso, guacamole y pico; [0.8,1] salsa.
// rest: altura del centro sobre la superficie (en tamaños); top y foot: alto y radio de la
// pieza ya asentada, para el mapa de alturas sobre el que se apoya la salsa.
const TYPES = [
    {
        key: 'frijol', from: 0.2, to: 0.32, count: 90, size: [0.03, 0.04], rest: 0.3, flat: true, top: 0.3, foot: 0.45,
        make: makeBean, material: () => physical({ roughness: 0.45, clearcoat: 0.25, clearcoatRoughness: 0.45 }),
        colors: [['#6e2a1e', 1], ['#7d3424', 1], ['#5e2318', 1], ['#8a4030', 0.6]],
    },
    {
        key: 'carne', from: 0.27, to: 0.4, count: 110, size: [0.1, 0.15], rest: 0.05, flat: true, top: 0.06, foot: 0.3,
        make: makeStrand, material: () => standard({ roughness: 0.74 }),
        colors: [['#a8693c', 1], ['#8a5530', 1], ['#b8743f', 0.7], ['#94502c', 1]],
    },
    {
        key: 'chicharron', from: 0.4, to: 0.52, count: 22, size: [0.062, 0.08], rest: 0.22, top: 0.45, foot: 0.6,
        make: makeChunk, material: () => standard({ vertexColors: true, roughness: 0.74 }),
        colors: [['#ffffff', 1], ['#f6ede2', 1]],
    },
    {
        key: 'maduro', from: 0.44, to: 0.56, count: 16, size: [0.045, 0.058], rest: 0.08, flat: true, tilt: 0.14, top: 0.1, foot: 1.0,
        make: makeSlice, material: () => physical({ vertexColors: true, roughness: 0.42, clearcoat: 0.3, clearcoatRoughness: 0.3 }),
        colors: [['#ffffff', 1], ['#fff3e2', 1]],
    },
    {
        key: 'maiz', from: 0.48, to: 0.6, count: 80, size: [0.017, 0.022], rest: 0.3, top: 0.4, foot: 0.4,
        make: makeKernel, material: () => physical({ roughness: 0.36, clearcoat: 0.35, clearcoatRoughness: 0.3 }),
        colors: [['#f5c020', 1], ['#f2b417', 1], ['#f8cf3c', 1], ['#e8a815', 1]],
    },
    {
        key: 'queso', from: 0.6, to: 0.74, count: 230, size: [0.05, 0.075], rest: 0.06, flat: true, top: 0.06, foot: 0.25,
        make: makeShred, material: () => standard({ roughness: 0.62 }),
        colors: [['#f6e7b4', 1], ['#fbefc9', 1], ['#f0d98f', 1], ['#fff6dc', 1]],
    },
    {
        key: 'guacamole', from: 0.64, to: 0.78, count: 13, size: [0.09, 0.13], rest: 0.05, flat: true, tilt: 0.12, top: 0.4, foot: 0.55,
        make: makeDollop, material: () => standard({ vertexColors: true, roughness: 0.72 }),
        colors: [['#ffffff', 1], ['#f1f6e6', 1]],
    },
    {
        key: 'tomate', from: 0.67, to: 0.8, count: 60, size: [0.022, 0.03], scale: [0.6, 1.4], rest: 0.3, top: 0.4, foot: 0.4,
        make: () => makeDice(31, new Color(1.35, 3.6, 3.4)),
        material: () => physical({ vertexColors: true, roughness: 0.35, clearcoat: 0.35, clearcoatRoughness: 0.25 }),
        colors: [['#c9382a', 1], ['#b82e22', 0.8], ['#d84a33', 0.6]],
    },
    {
        key: 'cebolla', from: 0.68, to: 0.8, count: 36, size: [0.014, 0.02], scale: [0.6, 1.4], squash: 0.45, rest: 0.22, top: 0.3, foot: 0.35,
        make: () => makeDice(47), material: () => physical({ roughness: 0.3, clearcoat: 0.45, clearcoatRoughness: 0.2 }),
        colors: [['#efe9dc', 1], ['#f6f2e8', 1], ['#e4dccb', 0.7]],
    },
    {
        key: 'cilantro', from: 0.7, to: 0.8, count: 30, size: [0.032, 0.045], rest: 0.05, flat: true, top: 0.05, foot: 0.3,
        make: makeLeaf, material: () => standard({ roughness: 0.55, side: DoubleSide }),
        colors: [['#2f6b2a', 1], ['#3d7d33', 1], ['#285c24', 1]],
    },
];

function pickColor(random, palette) {
    const total = palette.reduce((s, [, w]) => s + w, 0);
    let r = random() * total;
    for (const [hex, w] of palette) {
        r -= w;
        if (r <= 0) return hex;
    }
    return palette[palette.length - 1][0];
}

function randomQuat(random, flat, tilt = 0.35) {
    const e = flat
        ? new Euler(range(random, -tilt, tilt), random() * TAU, range(random, -tilt, tilt))
        : new Euler(random() * TAU, random() * TAU, random() * TAU);
    return new Quaternion().setFromEuler(e);
}

const _m = new Matrix4();
const _p = new Vector3();
const _q = new Quaternion();
const _qs = new Quaternion();
const _s = new Vector3();

function createPieces(type, lowPower, random) {
    const n = Math.ceil(type.count * (lowPower ? 0.5 : 1));
    const geometry = type.make(lowPower);
    const material = type.material();
    const mesh = new InstancedMesh(geometry, material, n);
    mesh.instanceMatrix.setUsage(DynamicDrawUsage);
    mesh.frustumCulled = false;
    const pieces = [];
    const top = type.from >= 0.6;
    for (let i = 0; i < n; i++) {
        const df = range(random, 0.038, 0.055);
        const ts = lerp(type.from, type.to - df, random());
        const land = ts + df;
        const sec = sectionAt(moundLevel(land));
        const ang = random() * TAU;
        const rad = Math.sqrt(random()) * (top ? 0.95 : 0.88);
        const xl = Math.cos(ang) * rad * sec.a;
        const zl = Math.sin(ang) * rad * sec.b;
        const size = range(random, type.size[0], type.size[1]);
        const sc = type.scale
            ? new Vector3(range(random, ...type.scale), range(random, ...type.scale) * (type.squash || 1), range(random, ...type.scale))
            : new Vector3(1, 1, 1);
        pieces.push({
            ts,
            df,
            land,
            size,
            sc,
            // nacen cerca de la boca (no a media pantalla) y aparecen creciendo
            start: new Vector3(xl * 0.35 + range(random, -0.06, 0.06), RIM + range(random, 0.25, 0.45), zl * 0.3 + range(random, -0.04, 0.04)),
            end: new Vector3(xl, moundHeight(xl, zl, land) + size * type.rest * sc.y, zl),
            q0: randomQuat(random, false),
            q1: randomQuat(random, Boolean(type.flat), type.tilt),
            axis: new Vector3(range(random, -1, 1), range(random, -1, 1), range(random, -1, 1)).normalize(),
            spin: range(random, 3, 8),
            color: new Color(pickColor(random, type.colors)),
            lastT: -1,
            lastLate: -1,
        });
    }
    pieces.sort((a, b) => a.ts - b.ts);
    pieces.forEach((piece, i) => mesh.setColorAt(i, piece.color));
    mesh.count = 0;
    mesh.visible = false;

    function update(p) {
        let n2 = 0;
        const late = lateRise(p);
        let changed = false;
        for (const piece of pieces) {
            if (p < piece.ts) break;
            const t = clamp((p - piece.ts) / piece.df);
            // una pieza ya asentada conserva su matriz en el buffer mientras la comida no suba
            if (t === 1 && piece.lastT === 1 && piece.lastLate === late) {
                n2++;
                continue;
            }
            piece.lastT = t;
            piece.lastLate = late;
            changed = true;
            const settle = easeOutCubic(t);
            const endY = piece.end.y + late - lateRise(piece.land);
            _p.set(
                lerp(piece.start.x, piece.end.x, t),
                lerp(piece.start.y, endY, t * t),
                lerp(piece.start.z, piece.end.z, t),
            );
            _q.slerpQuaternions(piece.q0, piece.q1, settle);
            _qs.setFromAxisAngle(piece.axis, (1 - settle) * piece.spin);
            _q.multiply(_qs);
            const s = piece.size * smoothstep(0, 0.2, t);
            _s.copy(piece.sc).multiplyScalar(s);
            _m.compose(_p, _q, _s);
            mesh.setMatrixAt(n2++, _m);
        }
        mesh.count = n2;
        mesh.visible = n2 > 0;
        if (changed) mesh.instanceMatrix.needsUpdate = true;
    }

    // Posición final (p = 1) para el mapa de alturas de la salsa.
    function settled(fn) {
        for (const piece of pieces) {
            const y = piece.end.y + LATE_RISE - lateRise(piece.land);
            const s = piece.size;
            const base = y - s * type.rest * piece.sc.y;
            fn(piece.end.x, piece.end.z, y + s * type.top * piece.sc.y, s * type.foot * Math.max(piece.sc.x, piece.sc.z), base);
        }
    }
    return { mesh, update, settled, geometry, material };
}

// Mapa de alturas de la comida terminada: montículo más las piezas asentadas encima.
function createSurface(types) {
    const level = moundLevel(1);
    const { a, b } = sectionAt(level);
    const NX = 72;
    const NZ = 44;
    const hf = new Float32Array((NX + 1) * (NZ + 1));
    const gx = (i) => -a + (2 * a * i) / NX;
    const gz = (j) => -b + (2 * b * j) / NZ;
    for (let j = 0; j <= NZ; j++) {
        for (let i = 0; i <= NX; i++) hf[j * (NX + 1) + i] = moundHeight(gx(i), gz(j), 1);
    }
    for (const t of types) {
        // cada pieza suma una cúpula de su alto sobre su apoyo
        t.settled((x, z, top, r, base) => {
            const i0 = Math.max(0, Math.floor(((x - r + a) / (2 * a)) * NX));
            const i1 = Math.min(NX, Math.ceil(((x + r + a) / (2 * a)) * NX));
            const j0 = Math.max(0, Math.floor(((z - r + b) / (2 * b)) * NZ));
            const j1 = Math.min(NZ, Math.ceil(((z + r + b) / (2 * b)) * NZ));
            for (let j = j0; j <= j1; j++) {
                for (let i = i0; i <= i1; i++) {
                    const d = Math.hypot(gx(i) - x, gz(j) - z) / r;
                    if (d >= 1) continue;
                    const k = j * (NX + 1) + i;
                    hf[k] = Math.max(hf[k], base + (top - base) * Math.sqrt(1 - d * d));
                }
            }
        });
    }
    return (x, z) => {
        const fi = clamp((x + a) / (2 * a)) * NX;
        const fj = clamp((z + b) / (2 * b)) * NZ;
        const i = Math.min(NX - 1, Math.floor(fi));
        const j = Math.min(NZ - 1, Math.floor(fj));
        const u = fi - i;
        const v = fj - j;
        const at = (ii, jj) => hf[jj * (NX + 1) + ii];
        return lerp(lerp(at(i, j), at(i + 1, j), u), lerp(at(i, j + 1), at(i + 1, j + 1), u), v);
    };
}

// ---------------------------------------------------------------- nachos de base

// Corona de nachos que enmarca la boca: abiertos hacia afuera y hundidos, para no tapar
// el interior mientras se llena.
function createChipPile({ chipGeometry, chipMaterial, lowPower, random }) {
    const n = lowPower ? 6 : 11;
    const mesh = new InstancedMesh(chipGeometry, chipMaterial, n);
    mesh.instanceMatrix.setUsage(DynamicDrawUsage);
    mesh.frustumCulled = false;
    const sec = sectionAt(RIM - 0.08);
    const chips = [];
    for (let i = 0; i < n; i++) {
        const ang = (i / n) * TAU + range(random, -0.18, 0.18);
        // un nacho es plano: si va muy pegado a la pared curva, sus esquinas la atraviesan
        const r = range(random, 0.62, 0.8);
        const x = Math.cos(ang) * r * sec.a;
        const z = Math.sin(ang) * r * sec.b;
        const outward = Math.atan2(x, z);
        const lean = range(random, 0.9, 1.2);
        const yaw = outward + range(random, -0.5, 0.5);
        const q = new Quaternion().setFromEuler(new Euler(lean, yaw, range(random, -0.45, 0.45), 'YXZ'));
        const y = RIM + range(random, -0.03, 0.01);
        chips.push({ pos: new Vector3(x, y, z), q, size: range(random, 0.24, 0.3) });
    }
    let last = -1;
    function update(open) {
        mesh.visible = open > 0.3;
        if (!mesh.visible || Math.abs(open - last) < 1e-5) return;
        last = open;
        const grow = smoothstep(0.3, 0.95, open);
        chips.forEach((c, i) => {
            // con la boca a medio abrir las paredes están más juntas: los nachos van más adentro
            _p.set(c.pos.x * (0.7 + 0.3 * open), c.pos.y - (1 - grow) * 0.14, c.pos.z * open * open);
            const s = c.size * grow;
            _s.set(s, s, s);
            _m.compose(_p, c.q, _s);
            mesh.setMatrixAt(i, _m);
        });
        mesh.instanceMatrix.needsUpdate = true;
    }
    return { mesh, update };
}

// ---------------------------------------------------------------- salsa de la casa

// Recorrido del hilo en coordenadas normalizadas de la boca (u = ancho, w = fondo → frente):
// cuatro pasadas sueltas con horquillas y cruces, como sale de un tarro apretado.
const SALSA_PATH = [
    [-0.82, -0.42], [-0.3, -0.36], [0.3, -0.22], [0.78, -0.1],
    [0.88, 0.04], [0.7, 0.12],
    [0.2, 0.2], [-0.4, 0.34], [-0.8, 0.45],
    [-0.9, 0.3], [-0.7, 0.2],
    [-0.2, 0.0], [0.3, -0.25], [0.7, -0.5],
    [0.6, -0.62], [0.4, -0.5],
    [0.1, -0.1], [-0.1, 0.3], [-0.2, 0.62],
];
const SALSA_TURNS = [4, 9, 14];

// Cinta de sección elíptica aplanada (el hilo se asienta sobre la comida) y radio variable.
// Los índices van por segmento para poder dibujarla de a poco con drawRange.
function buildRibbon(points, radii, radial, flatten) {
    const n = points.length;
    const pos = new Float32Array(n * radial * 3);
    const nor = new Float32Array(n * radial * 3);
    const T = new Vector3();
    const B = new Vector3();
    const N = new Vector3();
    const up = new Vector3(0, 1, 0);
    const v = new Vector3();
    for (let i = 0; i < n; i++) {
        T.subVectors(points[Math.min(i + 1, n - 1)], points[Math.max(i - 1, 0)]).normalize();
        B.crossVectors(T, up).normalize();
        N.crossVectors(B, T).normalize();
        const rw = radii[i];
        const rh = radii[i] * flatten;
        for (let k = 0; k < radial; k++) {
            const th = (k / radial) * TAU;
            const c = Math.cos(th);
            const s = Math.sin(th);
            const o = (i * radial + k) * 3;
            pos[o] = points[i].x + B.x * c * rw + N.x * s * rh;
            pos[o + 1] = points[i].y + B.y * c * rw + N.y * s * rh;
            pos[o + 2] = points[i].z + B.z * c * rw + N.z * s * rh;
            v.copy(B).multiplyScalar(c / rw).addScaledVector(N, s / rh).normalize();
            nor[o] = v.x;
            nor[o + 1] = v.y;
            nor[o + 2] = v.z;
        }
    }
    const index = [];
    for (let i = 0; i < n - 1; i++) {
        for (let k = 0; k < radial; k++) {
            const k1 = (k + 1) % radial;
            const a = i * radial + k;
            const b = (i + 1) * radial + k;
            const c = (i + 1) * radial + k1;
            const d = i * radial + k1;
            index.push(a, b, c, a, c, d);
        }
    }
    const geometry = new BufferGeometry();
    geometry.setIndex(index);
    geometry.setAttribute('position', new Float32BufferAttribute(pos, 3));
    geometry.setAttribute('normal', new Float32BufferAttribute(nor, 3));
    return geometry;
}

// Cruces del recorrido (en planta): ahí la salsa se acumula en charquitos.
function findCrossings(pts, minGap) {
    const out = [];
    for (let i = 0; i < pts.length - 1; i++) {
        const p1 = pts[i];
        const p2 = pts[i + 1];
        for (let j = i + minGap; j < pts.length - 1; j++) {
            const p3 = pts[j];
            const p4 = pts[j + 1];
            const den = (p2.x - p1.x) * (p4.z - p3.z) - (p2.z - p1.z) * (p4.x - p3.x);
            if (Math.abs(den) < 1e-12) continue;
            const ta = ((p3.x - p1.x) * (p4.z - p3.z) - (p3.z - p1.z) * (p4.x - p3.x)) / den;
            const tb = ((p3.x - p1.x) * (p2.z - p1.z) - (p3.z - p1.z) * (p2.x - p1.x)) / den;
            if (ta >= 0 && ta <= 1 && tb >= 0 && tb <= 1) {
                out.push({ x: lerp(p1.x, p2.x, ta), z: lerp(p1.z, p2.z, ta), i, j });
            }
        }
    }
    return out;
}

function createSalsa({ lowPower, surface }) {
    const random = rng(4040);
    const sec = sectionAt(moundLevel(1));
    // corrido hacia el frente: en el fondo el hilo quedaría sobre la silueta y parecería flotar
    const ctrl = SALSA_PATH.map(([u, w]) => new Vector3(
        (u + range(random, -0.03, 0.03)) * sec.a * 0.84,
        0,
        ((w + range(random, -0.03, 0.03)) * 0.8 + 0.1) * sec.b * 0.9,
    ));
    const segments = lowPower ? 200 : 320;
    const radial = lowPower ? 6 : 8;
    const pts = new CatmullRomCurve3(ctrl, false, 'centripetal').getSpacedPoints(segments);
    const wobble = noise2(91);
    // grosor irregular (el tarro no aprieta parejo) y puntas finas
    const radii = pts.map((_, i) => {
        const t = i / segments;
        const base = lerp(0.006, 0.012, 0.5 + 0.5 * wobble(t * 11, 0.5));
        return base * (0.35 + 0.65 * smoothstep(0, 0.04, t)) * (0.35 + 0.65 * (1 - smoothstep(0.95, 1, t)));
    });
    // apoya el hilo sobre la comida real (montículo + piezas) y lo suaviza para que caiga
    // como un líquido y no copie cada borde
    const FLAT = 0.45;
    let ys = pts.map((p, i) => surface(p.x, p.z) + radii[i] * FLAT + 0.004);
    ys = ys.map((_, i) => Math.max(...ys.slice(Math.max(0, i - 1), i + 2)));
    ys = ys.map((_, i) => {
        const w = ys.slice(Math.max(0, i - 2), i + 3);
        return w.reduce((s, y) => s + y, 0) / w.length;
    });
    pts.forEach((p, i) => {
        p.y = ys[i];
    });
    const geometry = buildRibbon(pts, radii, radial, FLAT);
    // rojo profundo de salsa casera: brillo de líquido sin parecer plástico
    const material = new MeshPhysicalMaterial({
        color: 0x9e1418,
        roughness: 0.25,
        metalness: 0,
        clearcoat: 0.6,
        clearcoatRoughness: 0.2,
    });
    const tube = new Mesh(geometry, material);
    tube.frustumCulled = false;

    // charquitos en los cruces y en las horquillas (donde el tarro cambia de sentido)
    const spots = [];
    for (const c of findCrossings(pts, 14)) {
        if (spots.some((s) => Math.hypot(s.x - c.x, s.z - c.z) < 0.03)) continue;
        spots.push({ x: c.x, z: c.z, y: Math.max(ys[c.i], ys[c.j]), at: c.j, r: range(random, 0.016, 0.021) });
    }
    for (const turn of SALSA_TURNS) {
        if (spots.length >= 5) break;
        const target = ctrl[turn];
        let best = 0;
        pts.forEach((p, i) => {
            if (Math.hypot(p.x - target.x, p.z - target.z) < Math.hypot(pts[best].x - target.x, pts[best].z - target.z)) best = i;
        });
        spots.push({ x: pts[best].x, z: pts[best].z, y: ys[best], at: best, r: range(random, 0.013, 0.017) });
    }
    spots.length = Math.min(spots.length, 5);
    const dropGeometry = new SphereGeometry(1, 16, 8);
    dropGeometry.scale(1, 0.3, 1);
    const drops = new InstancedMesh(dropGeometry, material, Math.max(1, spots.length));
    drops.frustumCulled = false;
    drops.count = 0;

    // chorro: se adelgaza al caer (como un chorro real que acelera), se desvanece arriba y nace
    // cerca de la boca para no cruzar la barra fija de la página
    const streamGeometry = new CylinderGeometry(0.011, 0.0045, 1, 10, 6, true);
    streamGeometry.translate(0, 0.5, 0);
    const sp = streamGeometry.getAttribute('position');
    const streamColors = [];
    for (let i = 0; i < sp.count; i++) streamColors.push(1, 1, 1, 1 - smoothstep(0.7, 1, sp.getY(i)));
    streamGeometry.setAttribute('color', new Float32BufferAttribute(streamColors, 4));
    const streamMaterial = material.clone();
    streamMaterial.vertexColors = true;
    streamMaterial.transparent = true;
    const stream = new Mesh(streamGeometry, streamMaterial);
    stream.frustumCulled = false;
    const streamTop = RIM + 0.55;
    const perSegment = radial * 6;
    const head = new Vector3();

    function update(p) {
        const ps = clamp((p - 0.8) / 0.2);
        if (ps <= 0) {
            tube.visible = false;
            stream.visible = false;
            drops.visible = false;
            return;
        }
        // el hilo se termina de dibujar en 0.62 y el chorro se retira antes del fotograma asentado
        const fall = 0.1;
        const endPour = 0.62;
        const retract = 0.74;
        const along = smoothstep(fall, endPour, ps) * segments;
        const drawn = Math.floor(along);
        tube.visible = drawn > 0;
        geometry.setDrawRange(0, drawn * perSegment);
        const i0 = Math.min(segments, drawn);
        head.copy(pts[i0]).lerp(pts[Math.min(segments, i0 + 1)], along - drawn);
        // el chorro baja hasta la punta del hilo, la sigue y al final se corta
        let bottom = head.y;
        let topY = streamTop;
        if (ps < fall) bottom = lerp(streamTop, head.y, (ps / fall) ** 2);
        if (ps > endPour) topY = lerp(streamTop, head.y, clamp((ps - endPour) / (retract - endPour)) ** 2);
        const len = topY - bottom;
        stream.visible = len > 0.002 && ps < retract;
        stream.position.set(head.x, bottom, head.z);
        stream.scale.set(1, Math.max(len, 0.0001), 1);
        let count = 0;
        for (const s of spots) {
            const k = smoothstep(s.at, s.at + 10, along);
            if (k <= 0) continue;
            _p.set(s.x, s.y - s.r * 0.12, s.z);
            _s.setScalar(s.r * (0.4 + 0.6 * k));
            _q.identity();
            _m.compose(_p, _q, _s);
            drops.setMatrixAt(count++, _m);
        }
        drops.count = count;
        drops.visible = count > 0;
        if (count > 0) drops.instanceMatrix.needsUpdate = true;
    }
    return {
        meshes: [tube, drops, stream],
        update,
        dispose() {
            geometry.dispose();
            dropGeometry.dispose();
            streamGeometry.dispose();
            material.dispose();
            streamMaterial.dispose();
            drops.dispose();
        },
    };
}

// ---------------------------------------------------------------- conjunto

export function createFilling({ chipGeometry, chipMaterial, lowPower = false, seed = 2026 }) {
    const random = rng(seed);
    const group = new Group();
    group.name = 'relleno';
    const mound = createMound();
    const pile = createChipPile({ chipGeometry, chipMaterial, lowPower, random });
    const types = TYPES.map((type) => createPieces(type, lowPower, random));
    const salsa = createSalsa({ lowPower, surface: createSurface(types) });
    group.add(pile.mesh, mound.mesh, ...types.map((t) => t.mesh), ...salsa.meshes);

    let lastP = -1;
    let lastOpen = -1;
    return {
        group,
        // Para subirlas a la GPU durante la carga (si no, la primera aparición da un tirón).
        textures: mound.textures,
        update(p, open) {
            group.visible = open > 0.001;
            if (!group.visible) return;
            if (Math.abs(open - lastOpen) > 1e-5) {
                lastOpen = open;
                pile.update(open);
            }
            if (Math.abs(p - lastP) < 1e-5) return;
            lastP = p;
            mound.update(p);
            for (const t of types) t.update(p);
            salsa.update(p);
        },
        // Para precompilar shaders: todo visible con al menos una instancia.
        prime() {
            pile.update(1);
            mound.update(1);
            for (const t of types) t.update(1);
            salsa.update(0.9);
            group.visible = true;
            lastP = -1;
            lastOpen = -1;
        },
        dispose() {
            mound.dispose();
            for (const t of types) {
                t.geometry.dispose();
                t.material.dispose();
                t.mesh.dispose();
            }
            pile.mesh.dispose();
            salsa.dispose();
        },
    };
}
