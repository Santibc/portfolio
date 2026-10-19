// Paquete 3D de Dorilokos Mix: recorre la landing con el scroll y se abre en "Así se arma".
// La página lo carga con import() dinámico solo si hay WebGL y no hay prefers-reduced-motion.
import {
    ACESFilmicToneMapping,
    AgXToneMapping,
    CanvasTexture,
    DirectionalLight,
    Group,
    HemisphereLight,
    MathUtils,
    Mesh,
    MeshBasicMaterial,
    NeutralToneMapping,
    PerspectiveCamera,
    PlaneGeometry,
    SRGBColorSpace,
    Scene,
    WebGLRenderer,
} from 'three';
import { BAG, createBodyGeometry, createBottomSealGeometry, createStripGeometry, peelStrip } from './geometry.js';
import { createBagMaterials, createBagTextures } from './textures.js';
import { CHIP_RADIUS, ORBIT_REACH, createChipGeometry, createChipMaterial, createChips } from './chips.js';
import { createFilling } from './ingredients.js';
import { createStudioEnvironment } from './environment.js';
import { TAU, clamp, createCanvas, damp, lerp, smootherstep, smoothstep, yieldToMain } from './util.js';

// Rotaciones por defecto (rx, ry, rz) si el ancla no trae data-bag-rot.
const DEFAULT_ROT = {
    hero: [0.1, -0.5, 0.13],
    back: [0.06, 0.34, -0.06],
    arma: [0.22, -0.1, 0.02],
    park: [0.08, -0.4, 0.1],
    pin: [0.03, -0.28, 0.0],
    burst: [0.12, -0.42, -0.08],
};
const MODES = Object.keys(DEFAULT_ROT);
// En 'arma', al abrirse se inclina hacia la cámara al menos esto para mostrar la boca.
const ARMA_LOOK_RX = 0.6;
// Tramo de la transición hacia 'pin' en que empieza la caída y en que aterriza.
const PIN_FALL = 0.45;
const PIN_LAND = 0.86;
// Modo por defecto según el nombre del ancla, si la página no trae data-bag-mode.
const MODE_BY_ANCHOR = { hero: 'hero', que: 'back', arma: 'arma', menu: 'park', ubicacion: 'pin', cierre: 'burst' };

const THEMES = {
    dark: { exposure: 1.0, env: 1.0, key: 2.1, hemi: 0.42, rim: 2.0, warm: 0.9, shadow: 0.42 },
    light: { exposure: 1.06, env: 1.12, key: 2.5, hemi: 0.75, rim: 1.2, warm: 0.7, shadow: 0.26 },
};

// Sombra suave detrás del paquete: le da peso sobre el fondo de la página.
function createShadow() {
    const canvas = createCanvas(128, 128);
    const ctx = canvas.getContext('2d');
    const g = ctx.createRadialGradient(64, 64, 0, 64, 64, 64);
    g.addColorStop(0, 'rgba(255,255,255,1)');
    g.addColorStop(0.45, 'rgba(255,255,255,0.55)');
    g.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, 128, 128);
    const map = new CanvasTexture(canvas);
    const mesh = new Mesh(
        new PlaneGeometry(1, 1),
        new MeshBasicMaterial({ map, color: 0x12041a, transparent: true, depthWrite: false, toneMapped: false }),
    );
    mesh.position.set(0.07, -0.1, -0.7);
    mesh.scale.set(1.45, 1.85, 1);
    mesh.renderOrder = -1;
    return { mesh, map };
}

const TONE_MAPPINGS = { neutral: NeutralToneMapping, aces: ACESFilmicToneMapping, agx: AgXToneMapping };

// Alto opcional que la página reserva arriba (header fijo) en --bag-top-inset (px o rem).
function readTopInset() {
    const raw = getComputedStyle(document.documentElement).getPropertyValue('--bag-top-inset').trim();
    const value = parseFloat(raw);
    if (!Number.isFinite(value)) return 0;
    if (raw.endsWith('rem')) return value * (parseFloat(getComputedStyle(document.documentElement).fontSize) || 16);
    return value;
}

function parseRot(attr, mode) {
    const base = DEFAULT_ROT[mode] || DEFAULT_ROT.park;
    let rot = base;
    if (attr) {
        const parts = attr.split(',').map((v) => parseFloat(v));
        if (parts.length === 3 && parts.every(Number.isFinite)) rot = parts;
    }
    const [rx, ry, rz] = rot;
    return { rx, ry: mode === 'back' ? Math.PI + ry : ry, rz };
}

// Ancho visible del documento y alto del viewport grande (100lvh). En móvil la barra de URL no
// cambia ese alto, así que el lienzo cubre la pantalla con o sin barra y no hay que redimensionar.
function medirViewport() {
    const sonda = document.createElement('div');
    sonda.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:100lvh;visibility:hidden;pointer-events:none';
    document.body.appendChild(sonda);
    const lvh = sonda.getBoundingClientRect().height;
    sonda.remove();
    const html = document.documentElement;
    return {
        w: html.clientWidth || window.innerWidth,
        h: Math.max(html.clientHeight || 0, lvh || 0) || window.innerHeight,
    };
}

export async function initBag({ canvas, gsap, ScrollTrigger, lowPower = false }) {
    if (!canvas || !gsap || !ScrollTrigger) throw new Error('initBag: faltan canvas, gsap o ScrollTrigger');
    const t0 = performance.now();
    const timeline = [];
    const mark = (label) => timeline.push([label, Math.round(performance.now() - t0)]);

    let renderer;
    try {
        renderer = new WebGLRenderer({
            canvas,
            alpha: true,
            antialias: !lowPower && (window.devicePixelRatio || 1) < 2,
            premultipliedAlpha: true,
            stencil: false,
            powerPreference: 'default',
        });
    } catch (error) {
        throw new Error(`WebGL no disponible: ${error.message}`);
    }
    if (!renderer.getContext()) throw new Error('WebGL no disponible');

    renderer.setClearColor(0x000000, 0);
    // Comprobar shaders cuesta llamadas síncronas a la GPU: solo en desarrollo.
    renderer.debug.checkShaderErrors = Boolean(import.meta.env && import.meta.env.DEV);
    renderer.outputColorSpace = SRGBColorSpace;
    renderer.toneMapping = NeutralToneMapping;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, lowPower ? 1.5 : 2));
    let { w: cw, h: ch } = medirViewport();
    renderer.setSize(cw, ch, true);

    const camera = new PerspectiveCamera(26, cw / ch, 0.1, 100);
    camera.position.set(0, 0, 14);
    const visibleHeight = 2 * camera.position.z * Math.tan(MathUtils.degToRad(camera.fov / 2));

    const scene = new Scene();
    const envTarget = createStudioEnvironment(renderer);
    mark('entorno');
    scene.environment = envTarget.texture;

    const hemi = new HemisphereLight(0xfff2fb, 0x2a152f, 0.4);
    const key = new DirectionalLight(0xffffff, 2);
    key.position.set(-4, 6, 8);
    const rim = new DirectionalLight(0xd8b8ff, 2);
    rim.position.set(5, 3, -6);
    const warm = new DirectionalLight(0xffb070, 0.9);
    warm.position.set(7, -3, 4);
    scene.add(hemi, key, rim, warm);

    // rig = posición/escala de pantalla; pivot = rotación del paquete.
    const rig = new Group();
    const pivot = new Group();
    pivot.rotation.order = 'ZXY';
    rig.add(pivot);
    scene.add(rig);
    const shadow = createShadow();
    rig.add(shadow.mesh);

    let textures;
    try {
        textures = await createBagTextures({ renderer, lowPower });
    } catch (error) {
        // sin arte no hay paquete: se libera el contexto y la página conserva el póster
        envTarget.dispose();
        renderer.dispose();
        renderer.forceContextLoss();
        throw error;
    }
    mark('texturas');
    const mats = createBagMaterials(textures);

    const bodyGeometry = createBodyGeometry(lowPower ? { nu: 64, nv: 92 } : {});
    mark('cuerpo');
    await yieldToMain();
    const body = new Mesh(bodyGeometry, [mats.printFront, mats.printBack, mats.interior]);
    const bottomSeal = new Mesh(createBottomSealGeometry(), [mats.sealFront, mats.sealBack]);
    const stripGeometry = createStripGeometry(lowPower ? { nu: 64 } : {});
    const strip = new Mesh(stripGeometry, [mats.printFront, mats.printBack, mats.sealFront, mats.sealBack, mats.interior]);
    strip.frustumCulled = false;
    const stripBase = {
        pos: Float32Array.from(stripGeometry.getAttribute('position').array),
        nor: Float32Array.from(stripGeometry.getAttribute('normal').array),
    };
    const stripCenterY = (BAG.yTear + BAG.yTop) / 2;
    const stripHolder = new Group();
    stripHolder.position.y = stripCenterY;
    strip.position.y = -stripCenterY;
    stripHolder.add(strip);
    pivot.add(body, bottomSeal, stripHolder);

    const chipGeometry = createChipGeometry();
    const chipMat = createChipMaterial({ renderer, lowPower });
    const chips = createChips({
        count: lowPower ? 12 : 24,
        orbitCount: lowPower ? 4 : 6,
        geometry: chipGeometry,
        material: chipMat.material,
    });
    rig.add(chips.mesh);
    mark('nachos');
    await yieldToMain();

    const filling = createFilling({ chipGeometry, chipMaterial: chipMat.material, lowPower });
    mark('relleno');
    await yieldToMain();
    pivot.add(filling.group);

    // ------------------------------------------------------------ tema

    let theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    let needsRender = true;
    function applyTheme(name) {
        theme = name === 'light' ? 'light' : 'dark';
        const t = THEMES[theme];
        renderer.toneMappingExposure = t.exposure;
        scene.environmentIntensity = t.env;
        key.intensity = t.key;
        hemi.intensity = t.hemi;
        rim.intensity = t.rim;
        warm.intensity = t.warm;
        shadow.mesh.material.opacity = t.shadow;
        needsRender = true;
    }
    applyTheme(theme);
    const onTheme = (event) => {
        const detail = event && event.detail && event.detail.theme;
        applyTheme(detail || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'));
    };
    window.addEventListener('dorilokos:theme-changed', onTheme);

    // ------------------------------------------------------------ paradas

    const triggers = new Map();
    let stops = [];
    let armaRange = null;
    let topInset = 0;

    function anchorElements() {
        return Array.from(document.querySelectorAll('[data-bag-anchor]'));
    }

    function ensureTriggers(elements) {
        for (const el of elements) {
            if (triggers.has(el)) continue;
            // refreshPriority -1: se calculan después de los pines de la página
            triggers.set(el, ScrollTrigger.create({ trigger: el, start: 'center center', refreshPriority: -1 }));
        }
    }

    function computeStops() {
        const elements = anchorElements();
        ensureTriggers(elements);
        const scrollNow = window.scrollY;
        const maxScroll = Math.max(0, ScrollTrigger.maxScroll(window));
        const armaST = ScrollTrigger.getById('arma');
        const anchoVisible = document.documentElement.clientWidth || window.innerWidth;
        const list = [];
        armaRange = null;
        topInset = readTopInset();
        for (const el of elements) {
            const rect = el.getBoundingClientRect();
            if (rect.width < 1 || rect.height < 1) continue;
            const key = el.dataset.bagAnchor;
            let mode = el.dataset.bagMode || MODE_BY_ANCHOR[key];
            if (!MODES.includes(mode)) mode = 'park';
            const rot = parseRot(el.dataset.bagRot, mode);
            const x = rect.left + rect.width / 2;
            // 'park' dentro del cuadro: el paquete descansa sobre el contenido y se va con él
            const glue = mode === 'park' && rect.left >= 0 && rect.right <= anchoVisible;
            const box = { x, h: rect.height, w: rect.width, ...rot, mode, key, glue };
            if (mode === 'arma' && armaST) {
                // durante el pin la sección queda en top 0: y = offset del ancla dentro de ella
                const section = armaST.pin || armaST.trigger;
                const sRect = section.getBoundingClientRect();
                const y = rect.top - sRect.top + rect.height / 2;
                const start = clamp(armaST.start, 0, maxScroll);
                const end = clamp(armaST.end, start, maxScroll);
                list.push({ S: start, y, ...box });
                list.push({ S: end, y, ...box });
                armaRange = { start, end };
                continue;
            }
            const st = triggers.get(el);
            const docTop = rect.top + scrollNow;
            let S = st && Number.isFinite(st.start) ? st.start : docTop + rect.height / 2 - window.innerHeight / 2;
            S = clamp(S, 0, maxScroll);
            list.push({ S, y: docTop - S + rect.height / 2, ...box });
        }
        list.sort((a, b) => a.S - b.S);
        // giros continuos: cada parada elige la vuelta que sigue el sentido del recorrido
        for (let i = 1; i < list.length; i++) {
            const prev = list[i - 1].ry;
            let ry = list[i].ry;
            while (ry - prev >= Math.PI * 1.5) ry -= TAU;
            while (ry - prev < -Math.PI * 0.5) ry += TAU;
            list[i].ry = ry;
        }
        stops = list;
        needsRender = true;
    }

    const onRefresh = () => computeStops();
    ScrollTrigger.addEventListener('refresh', onRefresh);
    computeStops();

    // ------------------------------------------------------------ pose

    // shift: lo que el scroll movió el paquete más allá de la primera o la última parada
    // glue: 1 mientras el paquete va pegado al contenido (lo sigue sin retraso)
    // floor: y más baja que puede tener su base (el borde inferior del ancla en que se apoya)
    const target = { x: 0, y: 0, h: 0, w: 0, rx: 0, ry: 0, rz: 0, p: 0, hero: 0, burst: 0, float: 0, park: 0, squash: 0, shift: 0, glue: 0, floor: Infinity };
    const cur = { ...target };
    const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    let first = true;
    // y objetivo del fotograma anterior y paradas con que se calculó (ver update)
    let lastTargetY = 0;
    let lastStops = null;

    function armaProgress(scroll, i, t) {
        if (armaRange) {
            const span = armaRange.end - armaRange.start;
            if (span <= 0) return scroll >= armaRange.start ? 1 : 0;
            return clamp((scroll - armaRange.start) / span);
        }
        const k = stops.findIndex((s) => s.mode === 'arma');
        if (k < 0) return 0;
        if (scroll >= stops[k].S) return 1;
        if (i === k - 1) return t;
        return 0;
    }

    function computeTarget(scroll) {
        const n = stops.length;
        const o = target;
        let a;
        let b;
        let t = 0;
        let shift = 0;
        let i = 0;
        if (scroll <= stops[0].S) {
            a = b = stops[0];
            shift = stops[0].S - scroll;
        } else if (scroll >= stops[n - 1].S) {
            a = b = stops[n - 1];
            i = n - 1;
            shift = stops[n - 1].S - scroll;
        } else {
            while (i < n - 2 && scroll >= stops[i + 1].S) i++;
            a = stops[i];
            b = stops[i + 1];
            const span = b.S - a.S;
            t = span > 0 ? clamp((scroll - a.S) / span) : 1;
        }
        // blend: peso de la parada b en la pose (y en los modos)
        let blend = smootherstep(t);
        let y = lerp(a.y, b.y, blend) + shift;
        o.squash = 0;
        o.glue = 0;

        if (b.mode === 'pin' && a !== b) {
            // Primero viaja con su sección anterior (sube con el scroll, no se queda flotando
            // encima del contenido); ya fuera de pantalla espera arriba y cae derecho sobre la
            // posición viva del pin, que viene subiendo, con un rebote pegado a él.
            const glued = a.y + (a.S - scroll);
            const live = b.y + (b.S - scroll);
            const above = -(b.h * 0.62 + 40);
            let fall = above;
            let squash = 0;
            if (t >= PIN_LAND) {
                const f = (t - PIN_LAND) / (1 - PIN_LAND);
                fall = live - Math.sin(Math.PI * f) * b.h * 0.07;
                squash = Math.exp(-f * 7) * 0.09;
            } else if (t > PIN_FALL) {
                const f = (t - PIN_FALL) / (PIN_LAND - PIN_FALL);
                fall = lerp(above, live, f * f);
            }
            const band = b.h * 0.35;
            blend = smoothstep(-band, band, fall - glued);
            y = lerp(glued, fall, blend);
            o.squash = squash * blend;
        } else if (a.mode === 'pin' && a !== b) {
            // Al salir del mapa sigue pegado al pin mientras el mapa sube (no se queda flotando
            // encima de él) y después vuela a la parada siguiente.
            const glued = a.y + (a.S - scroll);
            blend = smoothstep(0.35, 0.9, t);
            y = lerp(glued, b.y, blend);
        } else if (a.glue && a !== b) {
            // Parado sobre el contenido (el menú): se va con su sección en vez de quedarse quieto
            // en la pantalla mientras el panel sube y lo tapa. Cuando ya salió del cuadro por
            // arriba, primero se corre de lado y después baja a la parada siguiente.
            const glued = a.y + (a.S - scroll);
            const span = b.S - a.S;
            const out = span > 0 ? clamp((a.y + a.h) / span, 0, 0.6) : 0;
            const u = clamp((t - out) / (1 - out));
            blend = smootherstep(clamp(u * 2));
            y = lerp(glued, b.y, smootherstep(clamp(u * 2 - 1)));
            o.glue = 1;
        }

        // Llegando a una parada pegada, o pegado a ella, la base no baja del borde inferior del
        // ancla: si el alto o la y vienen atrasados por un scroll rápido, el contenido lo empuja
        // en vez de taparle la base.
        const seat = a === b ? null : a.glue && blend === 0 ? a : b.glue ? b : null;
        o.floor = seat ? seat.y + (seat.S - scroll) + seat.h / 2 : Infinity;

        o.x = lerp(a.x, b.x, blend);
        o.y = y;
        o.shift = shift;
        o.h = lerp(a.h, b.h, blend);
        o.w = lerp(a.w, b.w, blend);
        o.rx = lerp(a.rx, b.rx, blend);
        o.ry = lerp(a.ry, b.ry, blend);
        o.rz = lerp(a.rz, b.rz, blend);

        const weight = (mode) => (a.mode === mode ? 1 - blend : 0) + (b.mode === mode ? blend : 0);
        o.float = weight('hero');
        o.park = weight('park');
        if (a.mode === 'hero' && b.mode === 'hero') o.hero = 1;
        else if (a.mode === 'hero') o.hero = 1 - smoothstep(0, 0.28, t);
        else if (b.mode === 'hero') o.hero = smoothstep(0.72, 1, t);
        else o.hero = 0;
        if (b.mode === 'burst' && a !== b) o.burst = smoothstep(0.55, 1, t);
        else if (a.mode === 'burst' && a === b) o.burst = 1;
        else if (a.mode === 'burst') o.burst = 1 - smoothstep(0, 0.35, t);
        else o.burst = 0;
        o.p = armaProgress(scroll, i, t);
        // al abrirse se inclina hacia la cámara para que se vea el interior
        const look = smoothstep(0.04, 0.22, o.p) * weight('arma');
        if (look > 0) o.rx = lerp(o.rx, Math.max(o.rx, ARMA_LOOK_RX), look);
    }

    // ------------------------------------------------------------ estado del paquete (función de p)

    let lastP = -1;
    let lastTear = -1;
    function applyProgress(p) {
        if (Math.abs(p - lastP) < 1e-6) return false;
        lastP = p;
        const tear = smoothstep(0.004, 0.11, p);
        const fly = smoothstep(0.1, 0.17, p);
        const open = smootherstep((p - 0.09) / 0.11);
        if (Math.abs(tear - lastTear) > 1e-6) {
            lastTear = tear;
            // el rasgado arranca en la tijera de "Abre aquí" (izquierda) y avanza a la derecha
            const xFront = -BAG.W / 2 - 0.002 + tear * (BAG.W + 0.004);
            peelStrip(stripGeometry, stripBase.pos, stripBase.nor, xFront, {
                radius: lerp(0.22, 0.15, tear),
                fold: 0.62 * smoothstep(0, 0.4, tear),
            });
        }
        // la tira suelta cae hacia el costado donde terminó el rasgado, girando, y se encoge
        // sin salir de la caja del paquete
        stripHolder.visible = fly < 0.999;
        const f2 = fly * fly;
        stripHolder.position.set(0.32 * fly, stripCenterY - 0.3 * f2, 0.25 * fly);
        stripHolder.rotation.set(0.6 * fly, -0.3 * fly, 0.8 * fly);
        stripHolder.scale.setScalar(1 - smoothstep(0.15, 0.85, fly));
        body.morphTargetInfluences[0] = open;
        mats.interior.visible = tear > 0;
        filling.update(p, open);
        return true;
    }

    // ------------------------------------------------------------ eventos

    function onResize() {
        const { w, h } = medirViewport();
        if (w === cw && h === ch) return;
        cw = w;
        ch = h;
        renderer.setSize(cw, ch, true);
        camera.aspect = cw / ch;
        camera.updateProjectionMatrix();
        computeStops();
    }
    const onPointer = (event) => {
        pointer.tx = (event.clientX / cw) * 2 - 1;
        pointer.ty = (event.clientY / ch) * 2 - 1;
    };
    const onVisibility = () => {
        needsRender = true;
    };
    let contextLost = false;
    const onLost = (event) => {
        event.preventDefault();
        contextLost = true;
        window.dispatchEvent(new CustomEvent('dorilokos:bag-lost'));
    };
    window.addEventListener('resize', onResize);
    window.addEventListener('pointermove', onPointer, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    canvas.addEventListener('webglcontextlost', onLost);

    // ------------------------------------------------------------ precarga de GPU

    mark('paradas');
    textures.upload();
    for (const texture of [...chipMat.textures, ...filling.textures]) renderer.initTexture(texture);
    mark('subida');
    await yieldToMain();
    // compileAsync no bloquea el hilo principal mientras el driver enlaza los shaders
    filling.prime();
    mats.interior.visible = true;
    chips.update({ time: 0, hero: 1, burst: 1 });
    if (renderer.compileAsync) await renderer.compileAsync(scene, camera);
    else renderer.compile(scene, camera);
    mark('compilar');
    filling.update(0, 0);
    applyProgress(0);

    // ------------------------------------------------------------ bucle

    let destroyed = false;
    let ready = false;
    let offscreenRendered = false;
    const prev = { x: 0, y: 0, h: 0, rx: 0, ry: 0, rz: 0 };

    function onScreen() {
        const hh = cur.h * 0.75;
        const spread = cur.hero > 0.01 || cur.burst > 0.01 ? cur.h * 1.15 : 0;
        const hw = cur.h * 0.55 + spread;
        const above = cur.p > 0 && cur.p < 1 ? cur.h * 0.6 : 0;
        return cur.x + hw > 0 && cur.x - hw < cw && cur.y + hh + spread > 0 && cur.y - hh - spread - above < ch;
    }

    // Límites de pantalla (viewport menos el inset superior) en unidades locales del
    // paquete, para que la explosión de nachos no se salga ni invada el header.
    const bounds = { left: 0, right: 0, top: 0, bottom: 0, cx: 0, cy: 0, persp: 0 };
    function updateBounds(y, pxPerUnit, s) {
        const margin = 10;
        bounds.left = (margin - cur.x) / pxPerUnit;
        bounds.right = (cw - margin - cur.x) / pxPerUnit;
        bounds.top = (y - topInset - margin) / pxPerUnit;
        bounds.bottom = -(ch - margin - y) / pxPerUnit;
        bounds.cx = (cur.x - cw / 2) / pxPerUnit;
        bounds.cy = -(y - ch / 2) / pxPerUnit;
        bounds.persp = s / camera.position.z;
        return bounds;
    }

    function update(time, dt) {
        if (!stops.length) {
            if (rig.visible) {
                rig.visible = false;
                renderer.render(scene, camera);
            }
            return;
        }
        rig.visible = true;
        computeTarget(window.scrollY);
        const k = first ? 1e9 : 10;
        for (const prop of ['x', 'h', 'w', 'rx', 'ry', 'rz', 'squash', 'shift']) cur[prop] = damp(cur[prop], target[prop], k, dt);
        if (target.glue && !first) {
            // Pegado al contenido no puede quedarse atrás (el panel que sube lo taparía): copia lo
            // que se movió el objetivo desde el fotograma anterior (el scroll) y solo amortigua el
            // desfase que traía al llegar o el salto de unas paradas recalculadas.
            const ref = lastStops === stops ? lastTargetY : target.y;
            cur.y = target.y + damp(cur.y - ref, 0, 60, dt);
        } else {
            cur.y = damp(cur.y, target.y, k, dt);
        }
        lastTargetY = target.y;
        lastStops = stops;
        // la base tampoco se hunde en el contenido en que se apoya (floor, en computeTarget)
        cur.y = Math.min(cur.y, target.floor - cur.h / 2);
        for (const prop of ['burst', 'float', 'park']) cur[prop] = damp(cur[prop], target[prop], first ? 1e9 : 9, dt);
        // los nachos del hero crecen al arrancar (el póster no los trae) en vez de aparecer de golpe
        cur.hero = damp(cur.hero, target.hero, 7, dt);
        cur.p = damp(cur.p, target.p, first ? 1e9 : 12, dt);
        if (Math.abs(cur.p - target.p) < 1e-4) cur.p = target.p;
        pointer.x = damp(pointer.x, pointer.tx, 4, dt);
        pointer.y = damp(pointer.y, pointer.ty, 4, dt);

        // flotación del hero (y suave en park), inclinación con el puntero
        const fl = cur.float;
        const pk = cur.park;
        const y = cur.y + Math.sin(time * 1.1) * 0.022 * cur.h * fl + Math.sin(time * 0.9 + 1) * 0.014 * cur.h * pk;
        const rx = cur.rx + (Math.sin(time * 0.6) * 0.03 + pointer.y * 0.15) * fl;
        const ry = cur.ry + (Math.sin(time * 0.45) * 0.12 + pointer.x * 0.15) * fl + Math.sin(time * 0.35) * 0.06 * pk;
        const rz = cur.rz + Math.sin(time * 0.7) * 0.025 * fl;

        const worldPerPx = visibleHeight / ch;
        const s = (cur.h * worldPerPx) / BAG.H;
        rig.position.set((cur.x - cw / 2) * worldPerPx, -(y - ch / 2) * worldPerPx, 0);
        rig.scale.set(s * (1 + cur.squash * 0.6), s * (1 - cur.squash), s);
        pivot.rotation.set(rx, ry, rz);

        const progressed = applyProgress(cur.p);
        const animated = fl > 0.001 || pk > 0.001 || cur.hero > 0.001;
        // la órbita se cierra para que los nachos (con su radio y la perspectiva) no se
        // salgan del viewport ni se alejen más de medio paquete del ancla
        const pxPerUnit = cur.h / BAG.H;
        const sizeK = lerp(0.8, 1, clamp((cw - 360) / 640));
        const chipR = 0.34 * sizeK * CHIP_RADIUS;
        const room = Math.min(Math.min(cur.x, cw - cur.x) - 12, cur.w / 2 + pxPerUnit * 0.5) / pxPerUnit;
        const orbit = clamp((room - chipR) / ORBIT_REACH, 0.45, 1);
        // los límites se toman en la pose de la parada: pasado el final, los nachos se van con el paquete
        chips.update({ time, hero: cur.hero, burst: cur.burst, orbit, sizeK, bounds: updateBounds(y - cur.shift, pxPerUnit, s) });

        const moved = Math.abs(cur.x - prev.x) + Math.abs(y - prev.y) + Math.abs(cur.h - prev.h) > 0.05
            || Math.abs(rx - prev.rx) + Math.abs(ry - prev.ry) + Math.abs(rz - prev.rz) > 1e-4;
        prev.x = cur.x;
        prev.y = y;
        prev.h = cur.h;
        prev.rx = rx;
        prev.ry = ry;
        prev.rz = rz;

        if (!onScreen()) {
            // fuera de pantalla: un render para limpiar y se detiene hasta que vuelva
            if (!offscreenRendered) {
                rig.visible = false;
                renderer.render(scene, camera);
                rig.visible = true;
                offscreenRendered = true;
                rendered();
            }
            return;
        }
        if (offscreenRendered) {
            offscreenRendered = false;
            needsRender = true;
        }
        const burstActivo = cur.burst > 0.001 && cur.burst < 0.999;
        if (!(needsRender || moved || progressed || animated || first || burstActivo)) return;
        // En pantallas de 120-240 Hz la flotación ociosa no necesita más de 60 renders por segundo.
        const porScroll = needsRender || progressed || first || burstActivo
            || Math.abs(cur.x - target.x) + Math.abs(cur.y - target.y) + Math.abs(cur.h - target.h) > 0.5;
        if (!porScroll && time - ultimoRender < intervaloOcioso) return;
        ultimoRender = time;
        renderer.render(scene, camera);
        needsRender = false;
        rendered();
    }

    function rendered() {
        first = false;
        if (ready) return;
        mark('primer frame');
        ready = true;
        window.dispatchEvent(new CustomEvent('dorilokos:bag-ready'));
    }

    const stats = { cpu: 0, max: 0, frames: 0 };
    const intervaloOcioso = lowPower ? 1 / 30 : 1 / 60;
    let ultimoRender = -Infinity;
    let frozenTime = null;
    const tick = (time, deltaMs) => {
        if (destroyed || contextLost || document.hidden) return;
        const t0 = performance.now();
        update(frozenTime ?? time, Math.min(Math.max(deltaMs / 1000, 0), 0.1));
        const spent = performance.now() - t0;
        stats.cpu = stats.cpu * 0.95 + spent * 0.05;
        stats.max = Math.max(stats.max, spent);
        stats.frames++;
    };
    gsap.ticker.add(tick);

    // ------------------------------------------------------------ API

    // PNG transparente del frame actual. Con `frame` ({ width, height, bagHeight } en px CSS)
    // renderiza aparte con el paquete centrado (lo usa el póster); chips y shadow en false
    // ocultan los nachos y la sombra.
    function snapshot({ pixelRatio, frame, chips: withChips = true, shadow: withShadow = true } = {}) {
        const before = renderer.getPixelRatio();
        const ratio = pixelRatio || before;
        const position = rig.position.clone();
        const scale = rig.scale.clone();
        if (frame) {
            renderer.setPixelRatio(ratio);
            renderer.setSize(frame.width, frame.height, false);
            camera.aspect = frame.width / frame.height;
            camera.updateProjectionMatrix();
            rig.position.set(0, 0, 0);
            rig.scale.setScalar((frame.bagHeight * visibleHeight) / frame.height / BAG.H);
        } else if (ratio !== before) {
            renderer.setPixelRatio(ratio);
        }
        const chipsVisible = chips.mesh.visible;
        if (!withChips) chips.mesh.visible = false;
        shadow.mesh.visible = withShadow;
        renderer.render(scene, camera);
        const url = canvas.toDataURL('image/png');
        chips.mesh.visible = chipsVisible;
        shadow.mesh.visible = true;
        if (frame || ratio !== before) {
            rig.position.copy(position);
            rig.scale.copy(scale);
            renderer.setPixelRatio(before);
            renderer.setSize(cw, ch, false);
            camera.aspect = cw / ch;
            camera.updateProjectionMatrix();
            renderer.render(scene, camera);
        }
        return url;
    }

    function destroy() {
        if (destroyed) return;
        destroyed = true;
        gsap.ticker.remove(tick);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pointermove', onPointer);
        window.removeEventListener('dorilokos:theme-changed', onTheme);
        document.removeEventListener('visibilitychange', onVisibility);
        canvas.removeEventListener('webglcontextlost', onLost);
        ScrollTrigger.removeEventListener('refresh', onRefresh);
        for (const st of triggers.values()) st.kill();
        triggers.clear();
        const geometries = new Set();
        const materials = new Set();
        scene.traverse((object) => {
            if (object.geometry) geometries.add(object.geometry);
            if (object.material) [].concat(object.material).forEach((m) => materials.add(m));
            if (object.isInstancedMesh) object.dispose();
        });
        filling.dispose();
        geometries.forEach((g) => g.dispose());
        materials.forEach((m) => m.dispose());
        textures.dispose();
        shadow.map.dispose();
        chipMat.textures.forEach((t) => t.dispose());
        envTarget.dispose();
        renderer.setClearColor(0x000000, 0);
        renderer.clear();
        renderer.dispose();
    }

    return {
        destroy,
        snapshot,
        // Solo para el arnés de desarrollo.
        _debug: {
            get stops() {
                return stops.map((s) => ({ ...s }));
            },
            get state() {
                return { ...cur, theme, armaRange };
            },
            stats,
            timeline,
            get textureTimings() {
                return textures.timings;
            },
            // Lleva la pose a la objetivo sin amortiguar y dibuja (capturas deterministas).
            settle() {
                first = true;
                update(frozenTime ?? gsap.ticker.time, 1);
            },
            // Congela el reloj de la flotación/órbita (para el póster) y centra el puntero.
            freeze(time) {
                frozenTime = time;
                pointer.x = pointer.y = pointer.tx = pointer.ty = 0;
                needsRender = true;
            },
            setToneMapping(name) {
                renderer.toneMapping = TONE_MAPPINGS[name] ?? NeutralToneMapping;
                scene.traverse((object) => {
                    if (object.material) [].concat(object.material).forEach((m) => {
                        m.needsUpdate = true;
                    });
                });
                needsRender = true;
            },
            renderer,
            scene,
            camera,
            render() {
                needsRender = true;
            },
        },
    };
}
