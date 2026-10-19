// Estudio fotográfico procedural para los reflejos del foil (PMREM).
// Un metal sin entorno se ve negro: estas cajas de luz son lo que el paquete refleja.
import {
    BackSide,
    Color,
    DoubleSide,
    Float32BufferAttribute,
    LinearSRGBColorSpace,
    Mesh,
    MeshBasicMaterial,
    PMREMGenerator,
    PlaneGeometry,
    Scene,
    SphereGeometry,
} from 'three';
import { smoothstep } from './util.js';

function panel(scene, geometry, { position, size, color, intensity }) {
    const material = new MeshBasicMaterial({ side: DoubleSide });
    const c = new Color(color);
    material.color.setRGB(c.r * intensity, c.g * intensity, c.b * intensity, LinearSRGBColorSpace);
    const mesh = new Mesh(geometry, material);
    mesh.position.set(...position);
    mesh.scale.set(size[0], size[1], 1);
    mesh.lookAt(0, 0, 0);
    scene.add(mesh);
    return mesh;
}

export function createStudioEnvironment(renderer) {
    const scene = new Scene();
    const sphere = new SphereGeometry(40, 48, 24);
    const pos = sphere.getAttribute('position');
    const colors = [];
    const top = new Color(0.22, 0.17, 0.26);
    const mid = new Color(0.085, 0.06, 0.1);
    const bottom = new Color(0.03, 0.018, 0.035);
    const c = new Color();
    for (let i = 0; i < pos.count; i++) {
        const h = pos.getY(i) / 40;
        if (h > 0) c.copy(mid).lerp(top, smoothstep(0, 0.9, h));
        else c.copy(mid).lerp(bottom, smoothstep(0, 0.7, -h));
        colors.push(c.r, c.g, c.b);
    }
    sphere.setAttribute('color', new Float32BufferAttribute(colors, 3));
    scene.add(new Mesh(sphere, new MeshBasicMaterial({ side: BackSide, vertexColors: true })));

    const plane = new PlaneGeometry(1, 1);
    // caja de luz principal arriba-izquierda-frente
    panel(scene, plane, { position: [-11, 10, 16], size: [14, 9], color: 0xfff6ee, intensity: 6.5 });
    // franja cálida a la derecha (reflejo de fuego de la marca)
    panel(scene, plane, { position: [19, 1, 7], size: [3.2, 20], color: 0xffa860, intensity: 4.2 });
    // contraluz lavanda atrás a la izquierda: separa la silueta del fondo oscuro
    panel(scene, plane, { position: [-18, 3, -10], size: [4, 18], color: 0xd6b8ff, intensity: 5 });
    // cenital suave
    panel(scene, plane, { position: [2, 22, 2], size: [16, 16], color: 0xffffff, intensity: 2.2 });
    // relleno frontal bajo: da tonos medios a la mitad inferior del foil
    panel(scene, plane, { position: [4, -8, 19], size: [26, 8], color: 0xe6d2f2, intensity: 1.9 });
    // rebote del piso morado
    panel(scene, plane, { position: [0, -20, 4], size: [30, 16], color: 0x9a68ad, intensity: 1.1 });

    const pmrem = new PMREMGenerator(renderer);
    const target = pmrem.fromScene(scene, 0.03);
    pmrem.dispose();
    scene.traverse((o) => {
        if (o.isMesh) o.material.dispose();
    });
    sphere.dispose();
    plane.dispose();
    return target;
}

