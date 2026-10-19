---
name: Dorilokos Mix
description: Landing pública con el lenguaje de un empaque de snacks (foil, sellos crimpados, tabla nutricional) sobre la paleta morado + fuego que comparte con la app interna.
colors:
  morado-logo: "#5f306a"
  morado-hondo: "#46234f"
  tinta-foil: "#1b0d1f"
  naranja-fuego: "#f39030"
  dorado-maiz: "#f5c020"
  naranja-tostado: "#b65112"
  naranja-foco: "#dc6f14"
  ambar-foco: "#f5a524"
  rojo-mix: "#e3262a"
  rojo-pedido: "#c01a1e"
  rojo-pedido-hondo: "#9f171b"
  rojo-sangre: "#7a1316"
  plata-suelo: "#f3eff6"
  plata-baja: "#e2d9e8"
  plata-sello: "#e4dbea"
  lila-triangulo: "#ece1f0"
  papel-panel: "#ffffff"
  lavanda-tenue: "#5c5066"
  noche-baja: "#10071a"
  noche-sello: "#2a152f"
  morado-reflejo: "#6e3f7c"
  humo-claro: "#f5f2f7"
  humo-suave: "#e4d8eb"
  humo-tenue: "#c3b6cc"
  crema-app: "#fcfbfd"
  noche-app: "#140f18"
typography:
  display:
    fontFamily: "Lilita One, Archivo Variable, system-ui, sans-serif"
    fontSize: "clamp(3.3rem, 9.2vw, 9.25rem)"
    fontWeight: 400
    lineHeight: 0.86
    letterSpacing: "-0.005em"
  headline:
    fontFamily: "Lilita One, Archivo Variable, system-ui, sans-serif"
    fontSize: "clamp(2.4rem, 5.2vw, 4.5rem)"
    fontWeight: 400
    lineHeight: 0.92
  title:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "2.05rem"
    fontWeight: 850
    lineHeight: 0.95
    letterSpacing: "-0.01em"
    fontVariation: "\"wdth\" 72"
  body:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.6
  lead:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 400
    lineHeight: 1.625
  label:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "0.8rem"
    fontWeight: 800
    letterSpacing: "0.06em"
    fontVariation: "\"wdth\" 78"
  legal:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.07em"
    fontVariation: "\"wdth\" 85"
  price:
    fontFamily: "Archivo Variable, Archivo, system-ui, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 800
    letterSpacing: "-0.025em"
    fontFeature: "\"tnum\", \"lnum\""
rounded:
  none: "0px"
  sm: "4px"
  xl: "12px"
  2xl: "16px"
  ventana: "28px"
  full: "9999px"
spacing:
  margen-movil: "20px"
  margen-tablet: "32px"
  margen-escritorio: "48px"
  seccion: "80px"
  seccion-lg: "112px"
components:
  button-pedido:
    backgroundColor: "{colors.rojo-pedido}"
    textColor: "{colors.papel-panel}"
    rounded: "{rounded.full}"
    padding: "10px 16px"
  button-pedido-hover:
    backgroundColor: "{colors.rojo-pedido-hondo}"
  button-pedido-xl:
    backgroundColor: "{colors.rojo-pedido}"
    textColor: "{colors.papel-panel}"
    rounded: "{rounded.xl}"
    padding: "16px 28px"
  button-contorno:
    textColor: "{colors.tinta-foil}"
    rounded: "{rounded.full}"
    padding: "12px 24px"
  panel-nutri:
    backgroundColor: "{colors.papel-panel}"
    textColor: "{colors.tinta-foil}"
    rounded: "{rounded.none}"
    padding: "20px"
  etiqueta-dorada:
    backgroundColor: "{colors.dorado-maiz}"
    textColor: "{colors.tinta-foil}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "2px 6px"
  pastilla-opcion:
    textColor: "{colors.tinta-foil}"
    rounded: "{rounded.full}"
    padding: "4px 12px"
  sello-nav:
    backgroundColor: "{colors.plata-sello}"
    textColor: "{colors.tinta-foil}"
    height: "68px"
  ventana:
    backgroundColor: "{colors.plata-baja}"
    rounded: "{rounded.ventana}"
  button-primary-app:
    backgroundColor: "{colors.morado-logo}"
    textColor: "{colors.papel-panel}"
    rounded: "{rounded.xl}"
    padding: "10px 16px"
---

# Design System: Dorilokos Mix

## Overview

**Creative North Star: "El Paquete"**

La landing pública (`/`) se lee como el empaque de un snack: el frente es foil metalizado, los bordes de cada sección son sellos crimpados en zigzag, la información se imprime en paneles tipo tabla nutricional con reglas gruesas, y la acción se anuncia con la muesca "Abre aquí" (tijera más línea punteada). Un solo objeto está vivo: el paquete 3D de Dorilokos Mix, que baja por la página siguiendo anclas en el HTML; todo lo demás es impreso y quieto, y la página se lee completa sin él (póster estático cuando no hay WebGL o el usuario pide menos movimiento).

La densidad es la de una etiqueta: titulares gigantes en Lilita One con contorno, cuerpo en Archivo de ancho variable, cifras tabulares, mayúsculas condensadas para rótulos y letra legal. El tema claro es el reverso plateado del paquete y el oscuro es el frente morado; cambiar de tema cambia la rampa de tinta, nunca la geometría.

**Dos mundos, una paleta.** El repositorio tiene dos superficies con sistemas distintos que comparten los tokens de `tailwind.config.js` (`primary`, `accent`, `flame`, `cream`) y la fuente de marca Lilita One:

| | Landing pública (modo Persuade) | App interna (modo Operate) |
|---|---|---|
| Fuente de cuerpo | Archivo Variable (autohospedada) | Plus Jakarta Sans (Bunny Fonts) |
| Superficie | Variables `--suelo`, `--tinta`, `--sello`… en `:root` / `:root.dark` | Utilidades Tailwind con par `dark:` (`bg-white dark:bg-cream-900`), `surface` (`crema-app` / `noche-app`) |
| Contenedores | Panel nutricional cuadrado con borde de 3px, ventanas troqueladas | `<x-card>` redondeado con `shadow-soft` |
| Acción principal | `<x-button variant="pedido">` rojo, en pastilla | `<x-button variant="primary">` morado, `rounded-xl` |
| Movimiento | Scroll (Lenis + GSAP ScrollTrigger), paquete 3D | Transiciones cortas de estado, `fade-in-up` |
| Bundle | `landing.css` + `landing.js` con `tailwind.landing.config.js` | `app.css` + `app.js` |

Los materiales del paquete (foil, sellos, panel nutricional, corte, paquete 3D) son exclusivos de la landing. La app interna sigue RULE 1 a RULE 3 de `CLAUDE.md` y su librería `resources/views/components/`. Las variantes `pedido` y `contorno` del botón y el tamaño `xl` nacieron para la landing; viven en el componente compartido pero no forman parte del vocabulario de la app.

**Key Characteristics:**
- Material de empaque: foil con grano y reflejo, sellos con estrías y dientes, reglas de tabla nutricional.
- Un solo objeto vivo (el paquete 3D); el resto es impreso, legible sin JS.
- Rampa de tinta por tema en variables CSS; la geometría no cambia entre claro y oscuro.
- Lilita One en mayúsculas con contorno para titulares; Archivo condensado para rótulos, tablas y letra legal.
- El rojo como relleno de botón solo significa "pedir".

## Colors

Morado profundo y metálico como tinta y suelo, cruzado por el fuego del logo (dorado, naranja, rojo); los neutros son plata con tinte lavanda.

### Primary
- **Morado del Logo** (`morado-logo`): el morado de marca (`primary-500`). En la landing aparece en el foil, el paquete 3D y el triángulo de la cinta de sabores; en la app es el color de CTA, links activos e ítem activo del sidebar.
- **Morado Hondo** (`morado-hondo`): relleno de los titulares y tinta suave del tema claro.
- **Tinta Foil** (`tinta-foil`): el morado casi negro (`primary-950`). Tinta principal del tema claro (incluido el marco del mapa), suelo del tema oscuro y color de todas las reglas del panel nutricional (en ambos temas, porque el panel siempre es claro).

### Secondary
- **Naranja Fuego** (`naranja-fuego`): fondo de la cinta de sabores y paso medio del degradado de fuego.
- **Dorado Maíz** (`dorado-maiz`): etiquetas "El más vendido", círculo del paso activo de "Así se arma", el sticker de 4,8 ★, la selección de texto, el relleno del acento del titular y la muesca de corte en oscuro.
- **Naranja Tostado** (`naranja-tostado`): contorno de los titulares y color de la tijera y la línea de corte en el tema claro.
- **Naranja Foco / Ámbar Foco** (`naranja-foco` claro, `ambar-foco` oscuro): anillo de foco de teclado (3px, separación 3px).

### Tertiary
- **Rojo MIX** (`rojo-mix`): el rojo del logo como tinta de fuego: extremo del degradado del triángulo, contorno del sticker, relleno de la palabra acento del titular en claro y contorno de titulares en oscuro.
- **Rojo Pedido** (`rojo-pedido`, `flame-600`): relleno del botón `pedido`. Más oscuro que el Rojo MIX para que el texto blanco pase AA. Hover en **Rojo Pedido Hondo** (`rojo-pedido-hondo`), activo en `flame-800`.
- **Rojo Sangre** (`rojo-sangre`): contorno de la palabra acento roja del titular en claro.

### Neutral
- **Plata Suelo** (`plata-suelo`) / **Plata Baja** (`plata-baja`): suelo y fondo inferior del foil en claro; Plata Baja también es el fondo de ventanas y mapa.
- **Plata Sello** (`plata-sello`) / **Noche Sello** (`noche-sello`): cuerpo de los sellos crimpados y del pie en claro / oscuro.
- **Lila Triángulo** (`lila-triangulo`): relleno del triángulo gigante del inicio y del sello del corte de capas en claro (en oscuro pasa a Morado Hondo).
- **Papel Panel** (`papel-panel`): fondo del panel nutricional (en oscuro, `crema-app`); texto de los botones rojos.
- **Lavanda Tenue** (`lavanda-tenue`): tinta tenue en claro y tinta tenue dentro del panel en ambos temas.
- **Noche Baja** (`noche-baja`) / **Morado Reflejo** (`morado-reflejo`): fondo inferior del foil y reflejo del foil en oscuro.
- **Humo Claro / Suave / Tenue** (`humo-claro`, `humo-suave`, `humo-tenue`): la rampa de tinta del tema oscuro (principal, suave, tenue). Humo Claro es también el marco del mapa y el trazo de los botones `contorno` en oscuro.
- **Crema App / Noche App** (`crema-app`, `noche-app`): fondo de cuerpo de la app interna en claro / oscuro.

Los ingredientes del corte de capas tienen colores planos propios en `config/landing.php`; son contenido ilustrativo, no tokens del sistema.

### Named Rules
**The Una Señal Viva Rule.** El rojo como relleno de un control significa una sola cosa: pedir (`<x-button variant="pedido">`). Ningún otro botón, link o fondo interactivo es rojo; las salidas secundarias (Instagram, cómo llegar, Rappi) usan `contorno`.

**The Rampa, No Geometría Rule.** Claro y oscuro solo cambian las variables de `:root` / `:root.dark`. Ninguna regla de la landing cambia tamaño, posición o forma según el tema.

**The Panel Siempre Claro Rule.** El panel nutricional es papel en ambos temas, con tinta y reglas de Tinta Foil; dentro del panel se usan `--panel-*` y `--regla`, nunca `--tinta-*`. Al revés, lo que se dibuja sobre el suelo (marco del mapa, contornos, líneas de los pasos) usa `--tinta`, nunca `--regla`, porque `--regla` no cambia con el tema y en oscuro es igual al suelo.

## Typography

**Display Font:** Lilita One (con Archivo Variable y system-ui de respaldo), autohospedada, subconjunto latino precargado.
**Body Font:** Archivo Variable con eje de ancho (`@fontsource-variable/archivo/wdth.css`).
**Label Font:** Archivo Variable condensado (eje `wdth` 72–85%).

**Character:** Lilita One es la letra gorda del empaque y del logo; Archivo es la letra impresa de la etiqueta, que se condensa para rótulos y tablas igual que en un paquete real.

### Hierarchy
- **Display** (Lilita 400, `clamp(3.3rem, 9.2vw, 9.25rem)`, interlínea 0.86, mayúsculas): titular del inicio y del cierre. Contorno de 0.075em pintado detrás del relleno y sombra suave teñida. Admite una palabra acento con otro relleno y contorno.
- **Headline** (Lilita 400, `clamp(2.4rem, 5.2vw, 4.5rem)`, interlínea 0.92, mayúsculas, contorno 0.06em): un título por sección. También en mayúsculas sin contorno para nombres de producto (1.75rem) y el menú móvil (2.6rem).
- **Title** (Archivo 850, ancho 72%, 1.6–2.05rem, interlínea 0.95): título de cada panel nutricional ("Información antojicional", "Combos", "Horario").
- **Body** (Archivo 400, 1.0625rem, interlínea 1.6): texto corrido. Entradillas a 1.125–1.25rem en tinta suave, con medida máxima de 46–62ch.
- **Label** (Archivo 800, ancho 78%, mayúsculas, espaciado 0.06em, 0.68–0.95rem): navegación de escritorio, encabezados de tabla, etiquetas de producto, nombres de columna en celular, títulos de columna del pie.
- **Legal** (Archivo 600, ancho 85%, 0.75rem, espaciado 0.07em, mayúsculas, tinta tenue): letra legal del inicio y del pie, notas de precio, "Foto pendiente".
- **Price** (Archivo 800, 1.5–2.4rem, espaciado apretado, cifras tabulares): precios en COP con formato `$ 23.000`.

### Named Rules
**The Contorno Rule.** Lilita One siempre va en mayúsculas; cuando es titular (Display o Headline) lleva contorno pintado detrás del relleno (`paint-order: stroke fill`), nunca un contorno encima de la letra.

**The Cifras de Etiqueta Rule.** Todo número dentro de un panel nutricional usa cifras tabulares y alineadas para que los precios caigan en columna.

## Layout

Un contenedor de hasta 1440px con márgenes de 20px (móvil), 32px (≥640px) y 48px (≥1024px). Las secciones respiran 80px arriba y abajo (112px desde 1024px) y se separan con un sello crimpado de 28px de alto en vez de con espacio en blanco o líneas.

Escritorio usa una retícula de 12 columnas donde cada sección reserva una columna vacía (`data-bag-anchor`) para el paquete 3D: el inicio reparte 5 / 4 / 3 (titular, paquete, panel), "¿Qué es?" 4 / 8, "Así se arma" 7 / 5, Ubicación 5 / 7. Sin 3D, esas columnas desaparecen y el texto ocupa todo el ancho (`html:not(.con-3d)`), así que nunca queda un hueco.

En celular (diseño primero para celular) todo cae a una columna; el paquete se estaciona fuera de cuadro en las secciones densas, la tabla del menú se convierte en bloques por producto con el nombre de cada columna como rótulo, y aparece un botón flotante de pedido cuando el botón principal sale de pantalla. Breakpoints: 420px (`xs`, solo para mostrar el nombre junto al logo), 640, 768, 1024 y 1280px.

"Así se arma" se fija (pin) a pantalla completa mientras el paquete se abre; cada paso es una fila completa con número, foto, texto y medida, y solo la fila activa se resalta.

## Elevation & Depth

La profundidad viene del material más que de sombras: el foil tiene grano y un reflejo diagonal, los sellos tienen estrías verticales, y una capa fija de 3D (z 30) corre por detrás del contenido impreso (z 31). Las sombras que existen son difusas y teñidas, nunca desplazadas en bloque. Se escriben como `rgb(var(--x) / α)` con canales RGB sueltos (no `color-mix()`): `--sombra` cambia con el tema (canales de `primary-950` en claro, negro en oscuro, derivados del token con el helper `canales` de `tailwind.landing.config.js`) y `--sombra-honda` (canales de Noche Baja, `16 7 26`) es fija en ambos temas para las sombras proyectadas que deben verse sobre el suelo oscuro.

### Shadow Vocabulary
- **Panel apoyado** (`box-shadow: 0 28px 56px -30px rgb(var(--sombra) / 0.55)`): bajo cada panel nutricional, como una etiqueta despegada del foil.
- **Sombra de titular** (`text-shadow: 0 0.05em 0.14em rgb(var(--sombra) / 0.28)`): solo en el Display.
- **Sello flotante** (`drop-shadow(0 10px 16px rgb(var(--sombra-honda) / 0.22))`): bajo la barra de navegación fija.
- **Botón pedido** (`0 12px 28px -12px` Rojo Pedido al 70%, hover `0 16px 34px -12px` al 85%): resplandor rojo bajo la acción viva.
- **Sticker** (`drop-shadow(0 10px 14px rgb(var(--sombra-honda) / 0.35))`): bajo la estrella de calificación.
- **Póster del paquete** (`drop-shadow(0 28px 36px rgb(var(--sombra-honda) / 0.45))`): bajo el paquete estático del inicio.

### Named Rules
**The Impresos Encima Rule.** Todo bloque de lectura (paneles, titulares, párrafos) va por encima del lienzo 3D (`.sobre-3d`, z 31); el paquete y los nachos pasan por detrás del texto, nunca encima.

## Shapes

Dos geometrías conviven a propósito. Lo impreso es recto: el panel nutricional no tiene radio (0px) y se enmarca con un borde de 3px y reglas internas de 10px, 5px y 1px. Lo troquelado es redondo: las ventanas de foto tienen esquinas de 28px y una línea punteada exterior de 2px a 7px de distancia, como el hueco transparente de un empaque. El mapa comparte las esquinas de 28px pero lleva un borde sólido de 3px en `--tinta`.

Los bordes del empaque son zigzag: dientes de 8–9px hechos con máscaras de gradiente cónico, abajo en la barra de navegación, arriba en el pie y a ambos lados en los separadores. Los botones de acción y las opciones son pastillas (9999px); los botones grandes de la muesca de corte y la app interna usan 12px. Las etiquetas doradas tienen 4px. La estrella de calificación es un polígono de 16 puntas girado −6°.

## Components

### Buttons
Contundentes y táctiles: se levantan al pasar el puntero y se hunden al presionar.
- **Shape:** pastilla (9999px) en navegación, menú, ubicación y botón flotante; esquinas de 12px en el tamaño `xl` de la muesca de corte.
- **Pedido:** relleno Rojo Pedido, texto blanco, borde `flame-700` de 1px, mayúsculas con espaciado amplio, ícono de WhatsApp a la izquierda. Tamaños usados: `sm` (barra), `md` (filas del menú), `lg` (ubicación, flotante), `xl` (inicio, cierre, menú móvil).
- **Hover / Focus:** sube 2px, se oscurece a Rojo Pedido Hondo y crece el resplandor; foco con anillo de 3px en naranja/ámbar foco. Transición de 200ms.
- **Contorno:** transparente, borde de 2px en `currentColor`, hereda la tinta del fondo; hover con un velo del 10%. Para Instagram, TikTok, "Cómo llegar" y Rappi.
- **App interna:** `primary` morado, `secondary`, `ghost`, `danger`, `success`, `link`, con `rounded-xl` y `shadow-soft`; no se usan en la landing.

### Chips
- **Etiqueta dorada:** fondo Dorado Maíz, tinta Tinta Foil, rótulo de 0.68–0.7rem, radio de 4px ("El más vendido").
- **Pastilla de opción:** borde de 2px en el color de regla, texto en negrita de 0.875rem, sin relleno (bases de Doritos, valores de la casa). Informativa, no seleccionable.
- **Insignia "Texto de ejemplo":** `<x-badge variant="warning">` ámbar (forzado a ámbar claro en oscuro) para marcar contenido pendiente del cliente.

### Cards / Containers
- **Panel nutricional:** papel, borde de 3px en Tinta Foil, sin radio, relleno de 20–32px, título condensado, regla gruesa bajo el título, filas separadas por reglas finas, precio grande en la esquina inferior. Es el único contenedor de información de la landing (resumen del inicio, menú, horario, ficha de la casa).
- **Ventana troquelada:** hueco de foto con radio de 28px y línea punteada exterior. Vacía muestra rayas diagonales, un ícono de cámara, el título en Lilita y "Foto pendiente" en letra legal.
- **Mapa:** ventana con borde sólido de 3px en `--tinta` (Tinta Foil en claro, Humo Claro en oscuro), sobre fondo `--suelo-bajo`; en oscuro el mapa se invierte de color.

### Navigation
- **Barra:** sello crimpado fijo arriba (68px, 76px en escritorio) con dientes abajo; logo y nombre en Lilita, enlaces en rótulo de 0.95rem con subrayado de 3px al pasar el puntero, botón de tema y "Pedir" en pastilla.
- **Celular:** `<dialog>` a pantalla completa sobre foil, cabecera de sello, enlaces en Lilita de 2.6rem, acción de pedido `xl` a todo el ancho y dirección en letra legal.
- **Pie:** sello con dientes arriba, columnas en rótulo tenue, código de barras con el número de WhatsApp y letra legal final.

### Muesca de corte "Abre aquí"
La acción principal: tijera de 28px y línea punteada de 3px que lleva al botón `pedido xl`. Al pasar el puntero o enfocar, la tijera avanza por la línea y gira −8° (500ms, `cubic-bezier(0.16, 1, 0.3, 1)`). En celular la línea se estira y el botón ocupa todo el ancho.

### Sello crimpado
Banda con estrías verticales (2px de tinta al 18% cada 6px) sobre el color de sello, recortada en zigzag. Funciona como barra de navegación, pie y separador de 28px entre secciones.

### Sticker de calificación
Estrella dorada de 136px con borde Rojo MIX, girada −6°, con la calificación real de Google en Lilita y el número de opiniones. Al pasar el puntero gira a +8° y crece 5%. Es un enlace a Google Maps.

### Paquete 3D
Paquete procedural (three.js) en un lienzo fijo, decorativo (`aria-hidden`). Sigue anclas `data-bag-anchor` cuya caja define posición y alto, con `data-bag-mode` (`hero`, `back`, `arma`, `park`, `pin`, `burst`) y `data-bag-rot`. Solo se carga con WebGL2 y sin preferencia de menos movimiento; si no, se ve el póster estático del inicio y las demás anclas se ocultan.

## Do's and Don'ts

### Do:
- **Do** pintar la landing con las variables de rampa (`--suelo`, `--tinta`, `--tinta-suave`, `--tinta-tenue`, `--sello`) y dejar que `:root.dark` las cambie; `--regla`, `--panel-tinta` y `--panel-tenue` valen lo mismo en ambos temas porque son del panel, y no se usan sobre el suelo.
- **Do** poner cada dato estructurado (precios, horario, lo que lleva, ficha) en un panel nutricional con reglas de 10px, 5px y 1px y cifras tabulares.
- **Do** separar secciones con el sello crimpado de 28px, no con líneas ni fondos alternos.
- **Do** usar `<x-button variant="pedido">` con el ícono de WhatsApp para toda acción de pedir y `contorno` para las salidas secundarias.
- **Do** mantener cada bloque de texto por encima del lienzo 3D (z 31) y reservar en escritorio una columna vacía con `data-bag-anchor` para el paquete.
- **Do** usar `cubic-bezier(0.16, 1, 0.3, 1)` para el movimiento de la landing y desactivarlo con `prefers-reduced-motion`.
- **Do** mostrar lo pendiente como pendiente: ventanas vacías con "Foto pendiente" e insignia "Texto de ejemplo".
- **Do** construir las vistas de la app interna con `resources/views/components/`, Plus Jakarta Sans, `<x-card>` y pares `dark:` de Tailwind (RULE 1 a 3 de `CLAUDE.md`).

### Don't:
- **Don't** usar rojo como relleno de un control que no sea pedir.
- **Don't** cambiar tamaños, posiciones o formas según el tema; solo cambia la rampa.
- **Don't** presentar el menú como una fila de tarjetas iguales; es una tabla con filas por producto.
- **Don't** poner Lilita One en minúsculas ni como texto de párrafo.
- **Don't** usar sombras desplazadas sin desenfoque; las sombras de la landing son difusas y teñidas.
- **Don't** llevar foil, sellos, panel nutricional, muesca de corte ni el paquete 3D a la app interna, ni usar `pedido`/`contorno` en sus pantallas.
- **Don't** traer a la landing `<x-card>`, `shadow-soft`, Plus Jakarta Sans ni clases que dependan de `app.js`; la landing tiene su propio bundle.
- **Don't** hacer que algún contenido dependa del 3D o de JS para leerse.
