# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Visitantes de la landing pública (`/`)** — gente con antojo en Bogotá que llega desde Instagram, Google Maps o Rappi y decide en segundos si pide. Confirmado por el cliente (2026-10-18): universitarios y jóvenes de Chapinero, oficinistas de la zona (almuerzo o antojo rápido) y personas que piden domicilio en Bogotá. Llegan sobre todo desde el celular.
- **Equipo del restaurante (app interna)** — administrador (acceso total) y cajeros con rol `ventas` (solo caja). Operan turnos de caja, ventas, mercado, inventario, nómina, gastos y el consolidado.

## Product Purpose

Dorilokos Mix es un restaurante de comida rápida en Chapinero (Bogotá) que vende "dorilokos": un paquete de Doritos abierto y cargado de ingredientes salados, que se come en el mismo paquete. La landing existe para que un visitante nuevo entienda qué es, se le antoje y **empiece un pedido por WhatsApp** (acción principal, confirmada por el cliente); Rappi y visitar el local son caminos secundarios. Éxito = pedidos de WhatsApp iniciados desde la página.

La app interna (resto de este repositorio) sostiene la operación diaria del restaurante.

## Positioning

La versión salada y bogotana de los dorilocos mexicanos: el paquete es el plato, cargado hasta arriba. Producto real publicado en Rappi: "carne desmechada, chicharrón carnudo, plátano maduro, queso rayado, maíz dulce, frijol, pico de gallo, guacamole y salsa de la casa" — $ 25.900.

## Operating Context

- Pedidos por WhatsApp al +57 314 279 8769 (número de domicilios publicado en Instagram).
- Tienda en Rappi: "Dorilokos Mix Chapinero".
- Local: Calle 65 #11-73, Chapinero, Bogotá (bio de Instagram). Google Business lo publica como "Cl. 65 #1175" (por confirmar si es 11-73 o 11-75).
- Instagram: https://www.instagram.com/dorilokosmix/
- Google Business (oct. 2026): 4,8 ★ con 24 opiniones, "Restaurante de comida rápida", $1–40.000 por persona, abre a las 11 a.m. (lunes).

## Capabilities and Constraints

- Laravel 9 (Blade) + Vite 4 + Tailwind 3.4. Despliegue en hosting compartido de Hostinger: solo PHP en el servidor; todo JS/CSS se compila con `npm run build` y se versiona en `public/build`. Sin Node en producción, sin `storage:link`.
- La landing vive en `/` (ruta `welcome`), pública; la app sigue detrás de `/login`.
- Toda vista debe funcionar en modo claro y oscuro (reglas del proyecto en CLAUDE.md).
- Pendiente del cliente (no inventar): fotos reales del local y de los productos, misión, visión y valores, horario completo, menú completo con precios, número exacto de la dirección y dominio público.

## Brand Commitments

- Nombre: "Dorilokos Mix" (logo: palabra "Dorilokos" + "MIX" sobre un triángulo tipo nacho morado con borde de fuego). Fuente: `resources/images/dorilokos-mix-original.png`; derivado transparente en `public/images/logo.png`.
- Paleta de marca: morado #5f306a, naranja fuego #f39030, dorado #f5c020, rojo "MIX" #e3262a (tokens en `tailwind.config.js`).
- Fuente de marca: Lilita One.
- Lema en uso: "¡Sabor bien loko!".
- El paquete 3D de la landing lleva la marca Dorilokos Mix, nunca el logo ni la marca registrada Doritos (decisión del cliente, 2026-10-18).
- Voz: parcera y divertida, con jerga bogotana suave, pero clara y directa para quien está en la oficina o pidiendo desde la casa.

## Evidence on Hand

- Reales: calificación de Google (4,8 ★, 24 opiniones), producto y precio de Rappi ($ 25.900), dirección, WhatsApp, Instagram, logo.
- Ausentes (no se pueden fabricar): testimonios o citas de clientes, cifras de ventas o clientes, premios, precios distintos al confirmado, horario completo, fotos del local. Las fotos y textos institucionales pendientes se muestran como contenido de ejemplo claramente marcado hasta que el cliente los envíe.

## Product Principles

1. Antojo primero: mostrar la comida y el paquete antes de explicar.
2. Pedir en un toque: WhatsApp siempre a un toque, con el pedido ya escrito.
3. Verdad de barrio: solo datos reales; lo pendiente se marca como ejemplo.
4. Pensado para el celular: rápido y legible en un teléfono de gama media con datos móviles.

## Accessibility & Inclusion

- Todo el contenido y las acciones deben estar disponibles sin el efecto 3D (sin WebGL, con `prefers-reduced-motion` o con JS lento).
- Contraste AA en ambos temas; navegación completa por teclado.
