---
version: 1
slug: "resources-views-landing-index-blade-php"
primary_target: "resources/views/landing/index.blade.php"
related_targets: ["resources/views/layouts/landing.blade.php","resources/js/landing.js"]
---

# Landing pública Dorilokos Mix (`/`, ruta `welcome`)

- **Modo:** Persuade.
- **Audiencia:** universitarios y jóvenes de Chapinero, oficinistas de la zona y gente que pide domicilio en Bogotá; casi todos desde el celular.
- **Acción:** iniciar un pedido por WhatsApp (+57 314 279 8769) con el mensaje ya escrito. Secundarias: Rappi, cómo llegar, Instagram.
- **Prueba real:** 4,8 ★ en Google (24 opiniones), producto y precio de Rappi ($ 25.900), dirección real, Instagram @dorilokosmix.
- **Restricciones:** paleta y Lilita One de marca; claro y oscuro; sin consultas a BD (la landing lee `config/landing.php`); bundle propio (sin `app.js`); todo funciona sin WebGL y con `prefers-reduced-motion`.
- **Momento memorable:** el paquete 3D se abre por la línea "Abre aquí" y le caen los ingredientes mientras haces scroll.

## Direction contract

THESIS: La página habla el idioma del empaque de snacks (foil, sellos crimpados, tabla nutricional, muesca de corte) y el paquete 3D de Dorilokos Mix es el único objeto vivo: baja por toda la página y se abre para mostrar lo que lleva. Rechaza el hero partido de comida rápida (foto + titular + botón + fila de tarjetas iguales).

OWN-WORLD: Frente = foil morado metalizado (#1b0d1f→#5f306a con reflejo lavanda) cruzado por el triángulo de fuego del logo (dorado→naranja→rojo) como trazo. Reverso = panel plateado claro con tinta morado-negra y reglas gruesas de tabla nutricional. Sellos crimpados en zigzag como bordes de sección, estrellas-sticker para la prueba, línea punteada "Abre aquí" con tijera como gesto de acción, código de barras y letra legal en el pie. Lilita One (titulares con contorno rojo) + Archivo de ancho variable (cuerpo y tablas, cifras tabulares). El rojo MIX solo marca lo vivo (pedir).

STORY: Ver el paquete cargado → entender qué es un dorilokos y qué lleva → verlo armarse (el paquete se abre y caen los ingredientes) → elegir en la tabla del menú → confiar (4,8 ★ Google, local real en Chapinero, mapa) → pedir por WhatsApp en un toque.

FIRST VIEWPORT: Sello crimpado superior con logo, navegación y "Pedir". Foil a sangre con el triángulo de fuego gigante saliendo por la izquierda; titular "EL PAQUETE ES EL PLATO" en Lilita One arriba a la izquierda; gancho de una línea y la acción roja "Abre aquí · Pide por WhatsApp" sobre la línea punteada con tijera; paquete 3D al centro-derecha, inclinado, con nachos alrededor; a la derecha la tabla "Información antojicional" (lo que lleva + $ 25.900 de Rappi); sticker 4,8 ★ Google junto al paquete; sello inferior con letra legal (contenido neto, dirección, @dorilokosmix).

FORM: El Paquete (lenguaje de empaque de snacks), candidato 7 de mi lista ordenada por resonancia; seed:5da4cfb20345f283.

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance

## Raises (de los retadores declinados)

- Noche de neón: una sola señal viva; el rojo MIX se reserva para lo activo (pedir ahora, abierto).
- Retratos de datos (Du Bois): el contenido se dibuja, no se lista; las capas de ingredientes se ven como un corte del paquete en campos de color plano.
- Salón de azulejos: lo pendiente se ve pendiente; las fotos que faltan son ventanas vacías del empaque esperando su contenido.
- Forja: una fila por paso; cada paso del armado ocupa una fila completa con su medida.
- Tablero de salidas: el menú es una tabla real (filas = productos, columnas = qué lleva y cómo pedir), nunca una fila de tarjetas iguales.
- Escena ASCII: una rampa de tinta; claro y oscuro cambian la rampa (reverso plateado / frente morado), nunca la geometría.

## Signature interaction y gramática de movimiento

- **El viaje del paquete** (Three.js + GSAP ScrollTrigger + Lenis): hero (flota, gira lento, se inclina con el puntero) → "¿Qué es?" (gira y muestra el reverso) → "Así se arma" (sección fija: el sello superior se rasga por "Abre aquí" y caen los ingredientes al ritmo de las filas de pasos) → menú (se estaciona junto a la tabla) → ubicación (cae sobre el pin del mapa) → cierre (explota en nachos junto al CTA final).
- Movimiento ligado al scroll; fuera del hero no hay animaciones automáticas. Easing exponencial `cubic-bezier(0.16, 1, 0.3, 1)`. El contenido es visible por defecto; nada depende del JS para leerse.
- `prefers-reduced-motion` o sin WebGL: paquete estático (render pre-hecho) en el hero, sin viaje ni scrub; los pasos quedan como lista fija.

## Pendiente del cliente

Fotos reales (fachada, interior, equipo, producto), misión, visión, valores, horario completo, menú completo con precios, número exacto de la dirección (11-73 vs 11-75) y dominio.
