// Solo desarrollo: renderiza la placa de foil arrugado del fondo del inicio con el mismo material
// (metal + clearcoat) y el mismo estudio de reflejos que el paquete. Se exporta como imagen fija
// (public/images/landing/foil-*.webp): la página no paga este cálculo.
// Uso: con `npm run dev`, abrir /resources/js/landing/bag/dev/foil.html y llamar
// window.__foil.render({ width, height, tema: 'oscuro' | 'claro', seed }) -> dataURL PNG.
import {
    Color,
    DirectionalLight,
    Mesh,
    MeshPhysicalMaterial,
    NeutralToneMapping,
    PerspectiveCamera,
    PlaneGeometry,
    SRGBColorSpace,
    Scene,
    Vector2,
    WebGLRenderer,
    CanvasTexture,
} from 'three';
import { createStudioEnvironment } from '../environment.js';
import { blurField, heightToNormal, rng } from '../util.js';

// Arrugado de papel metalizado: suma de pliegues rectos en V (cada uno con su largo, su ancho de
// influencia y su signo: cresta o valle). Donde se cruzan quedan facetas planas con quiebres
// nítidos, como el foil de un paquete estrujado; encima, un grano fino muy suave.
function campoArrugado(w, h, seed) {
    const random = rng(seed);
    const campo = new Float32Array(w * h);
    const diagonal = Math.hypot(w, h);
    const pliegues = [];
    for (let i = 0; i < 140; i++) {
        const angulo = random() * Math.PI;
        pliegues.push({
            x: random() * w,
            y: random() * h,
            nx: -Math.sin(angulo),
            ny: Math.cos(angulo),
            tx: Math.cos(angulo),
            ty: Math.sin(angulo),
            mitad: diagonal * (0.12 + random() * 0.4),
            alcance: 40 + random() * 160,
            fuerza: (random() < 0.5 ? -1 : 1) * (0.25 + random() * 0.75),
        });
    }
    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            let altura = 0;
            for (const p of pliegues) {
                const dx = x - p.x;
                const dy = y - p.y;
                const largo = Math.abs(dx * p.tx + dy * p.ty);
                if (largo > p.mitad * 1.3) continue;
                const lejos = Math.abs(dx * p.nx + dy * p.ny);
                // V que se aplana fuera de su alcance y se desvanece en los extremos del pliegue
                const caida = largo < p.mitad ? 1 : 1 - (largo - p.mitad) / (p.mitad * 0.3);
                altura += p.fuerza * Math.min(lejos, p.alcance) * caida;
            }
            campo[y * w + x] = altura * 0.01;
        }
    }
    const grano = rng(seed + 99);
    for (let i = 0; i < campo.length; i++) campo[i] += (grano() - 0.5) * 0.004;
    return campo;
}

function render({ width = 1600, height = 1000, tema = 'oscuro', seed = 7, relieve = 2.2 } = {}) {
    const oscuro = tema === 'oscuro';
    const canvas = document.createElement('canvas');
    const renderer = new WebGLRenderer({ canvas, antialias: true, preserveDrawingBuffer: true });
    renderer.setPixelRatio(1);
    renderer.setSize(width, height, false);
    renderer.outputColorSpace = SRGBColorSpace;
    renderer.toneMapping = NeutralToneMapping;
    renderer.toneMappingExposure = oscuro ? 0.8 : 1;

    const scene = new Scene();
    const entorno = createStudioEnvironment(renderer);
    scene.environment = entorno.texture;
    scene.background = new Color(oscuro ? 0x1b0d1f : 0xf3eff6);

    const nw = 1024;
    const nh = Math.round((nw * height) / width);
    const campo = campoArrugado(nw, nh, seed);
    blurField(campo, nw, nh, 1);
    const normal = new CanvasTexture(heightToNormal(campo, nw, nh, relieve));

    const material = new MeshPhysicalMaterial({
        color: oscuro ? 0x4a2456 : 0xddd5e4,
        metalness: 1,
        roughness: oscuro ? 0.34 : 0.38,
        clearcoat: 0.5,
        clearcoatRoughness: 0.22,
        normalMap: normal,
        normalScale: new Vector2(1, 1),
        envMapIntensity: oscuro ? 0.5 : 0.95,
    });
    const camera = new PerspectiveCamera(30, width / height, 0.1, 100);
    camera.position.set(0, 0, 10);
    const altoPlano = 2 * 10 * Math.tan((15 * Math.PI) / 180) * 1.08;
    const plano = new Mesh(new PlaneGeometry((altoPlano * width) / height, altoPlano), material);
    plano.rotation.x = -0.1;
    scene.add(plano);
    const luz = new DirectionalLight(0xffffff, oscuro ? 0.55 : 1.1);
    luz.position.set(-3, 4, 6);
    scene.add(luz);

    renderer.render(scene, camera);
    const url = canvas.toDataURL('image/png');

    normal.dispose();
    material.dispose();
    plano.geometry.dispose();
    entorno.dispose();
    renderer.dispose();
    return url;
}

window.__foil = { render };
