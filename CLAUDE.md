# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**DorilokosMix** — sistema de gestión de restaurante sobre Laravel 9 (derivado de la plantilla "Sopas y Sopitas"): caja/ventas por turno, mercado (compras), nómina, gastos y un dashboard consolidado, sobre un app-shell (sidebar + header) con dark/light theming y una librería curada de componentes Tailwind/Preline. Locale `es`, timezone `America/Bogota`, moneda COP.

**Repo multi-cliente:** el remoto `origin` aloja un proyecto distinto por rama (`sopas`, `montano`, `sinden`, `agromarket`, `dorilokos`…). La carpeta local se llama `agromarket`, pero la app de esta rama es **DorilokosMix** (rama `dorilokos`, derivada de `sopas`). Lo que veas con nombre "sopas"/"montano" (seeders, `.claude/`, `.playwright-mcp/`) es herencia de otras ramas.

**Tech Stack:**
- Laravel 9 (PHP 8.0+), Vite 4 (todo JS/CSS bundleado; la única carga externa es la fuente de Bunny Fonts importada en `resources/css/app.css`)
- Tailwind CSS 3.4 + `@tailwindcss/forms` + `@tailwindcss/typography` + Preline UI 4 (vanilla JS components)
- Alpine.js for ligero reactivity, Lucide icons (curated subset), ApexCharts (import eager, via `window.makeChart`), ScrollReveal, TomSelect, SweetAlert2
- En `composer.json` hay paquetes heredados **sin uso** en el código: livewire, yajra/laravel-datatables, maatwebsite/excel, barryvdh/laravel-dompdf, intervention/image
- MySQL, XAMPP (Windows local)
- Laravel Breeze (auth), Spatie Laravel-Permission (roles)
- Deploy target: **Hostinger shared hosting** — only PHP runtime, all JS/CSS must compile via `npm run build`

---

## RULE 0 — NUNCA borres la base de datos ni datos del usuario (crítico)

**Está terminantemente prohibido ejecutar cualquier comando o acción que borre, vacíe o recree la base de datos `dorilokos` (valor de `DB_DATABASE` en `.env`) (o cualquier otra base local como `sopas_prod_*`, o cualquier dato del usuario) sin autorización explícita y por escrito del usuario para esa ejecución puntual.**

Prohibido sin permiso explícito:
- `php artisan migrate:fresh`, `migrate:fresh --seed`, `migrate:refresh`, `migrate:reset`, `db:wipe`.
- `php artisan test` / `phpunit` **mientras la suite use la conexión real** (los tests con `RefreshDatabase` ejecutan `migrate:fresh` contra la base configurada). Antes de correr tests, **verifica** que `phpunit.xml` fuerce una base de pruebas aislada (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) y que NO esté comentada. Si no lo está, **no corras tests** — arréglalo primero o pregunta. (Hoy `phpunit.xml` líneas 24–25 sí lo fuerzan; verifica que siga así.)
- `DROP`, `TRUNCATE`, `DELETE` masivos vía tinker/SQL directo.
- Cualquier seeder o script que limpie tablas existentes.

Reglas operativas:
1. Si una tarea parece requerir resetear la base, **PREGUNTA primero** y explica el impacto. No asumas que los datos son "solo demo".
2. Para verificar cambios de modelo/migración, usa una base de pruebas aislada (SQLite en memoria) o una copia, nunca la base de desarrollo del usuario.
3. Si vas a correr tests, confirma el aislamiento de la base de tests **antes** de ejecutar.
4. Ante la duda, no borres. Es infinitamente más barato preguntar que perder datos.

---

## Commands

```bash
# Development
/dev-start                              # OJO: .claude/commands/dev-start.md apunta al proyecto montano — revisar antes de usar
/dev-stop                               # Mata TODOS los procesos php/node/Apache/MySQL
php artisan serve --host=127.0.0.1 --port=8000
npm run dev                             # Vite hot-reload
npm run build                           # Compile assets to public/build/ (se versiona; no hay build en el servidor)

# Database (RULE 0: migrate:fresh solo con permiso explícito)
php artisan migrate                     # Aplicar migraciones nuevas (aditivas)
php artisan db:seed                     # Seeders base idempotentes (updateOrCreate): no borran, pero restauran valores sembrados
php artisan migrate:fresh --seed        # Wipe + seed base: rol admin, dias_semana, tipos_menu_item, metodos_pago, conceptos_gasto_fijo
php artisan db:seed --class=DemoSeeder  # Datos demo de todos los módulos — solo en BD de pruebas; descarga imágenes de Wikipedia (requiere red)

# Testing (phpunit.xml usa SQLite :memory:)
php artisan test
php artisan test --filter=NominaTest                  # una clase
php artisan test tests/Unit/CalculadoraNominaTest.php # un archivo

# Code formatting
# Ojo: el código existente NO sigue Pint (CRLF + `=>` alineados). Correr pint sobre un archivo
# existente reformatea todo el archivo; sigue el estilo del archivo que estás tocando.
./vendor/bin/pint --test <archivo>      # Solo revisar

# Cache (after config / route / view edits)
php artisan config:clear && php artisan route:clear && php artisan view:clear
```

---

## Despliegue a producción (Hostinger shared hosting)

> 🚧 **PENDIENTE para DorilokosMix.** Este proyecto aún no está desplegado: usará el
> mismo servidor SSH pero con **dominio, ruta y BD nuevos (por definir)**, y trabaja en
> la rama `dorilokos`. Los datos de abajo son los de **Sopas y Sopitas** (proyecto
> origen): **NO** usarlos para desplegar DorilokosMix — harías `git pull` sobre la app
> de Sopas en producción. Actualizar esta sección cuando se configure el dominio.

Producción corre en Hostinger por SSH. El deploy es **`git pull`** sobre la rama
`sopas` (no hay build en el servidor: los assets ya van compilados en `public/build/`,
y la mayoría de cambios de UI viven en Blade inline). El repo en el servidor está en
la rama `sopas`, igual que el desarrollo.

**Las credenciales SSH/BD y la plantilla `askpass` están en `CLAUDE.local.md`**
(archivo gitignored, cargado automáticamente en contexto). NUNCA escribir esas
contraseñas en archivos versionados. En este checkout `CLAUDE.local.md` **no existe**
todavía — si se necesita, pedir las credenciales al usuario.

### Datos de conexión (no sensibles)
- Servidor: `212.85.6.28`, puerto SSH `65002`, usuario `u454224791`.
- Ruta del proyecto: `/home/u454224791/domains/sopasysopitaspms.com/portfolio`.
- BD producción: `u454224791_sopas` (host `localhost` desde el servidor).
- URL: https://sopasysopitaspms.com

### Procedimiento "commit, push y subir a producción"

Cuando el usuario diga *"haz commit, push y pull en producción"* (o similar):

1. **Local:** `git add <archivos> && git commit && git push origin sopas`
   (mensaje de commit en español; el repo trabaja en la rama `sopas`).
2. **Producción (vía SSH, usando el helper askpass de `CLAUDE.local.md`):**
   ```bash
   cd /home/u454224791/domains/sopasysopitaspms.com/portfolio
   git pull origin sopas
   php artisan view:clear          # siempre tras cambios de Blade
   php artisan config:clear        # si tocaste config/.env
   php artisan route:clear         # si tocaste rutas
   php artisan migrate --force     # SOLO si hay migraciones nuevas (preguntar antes)
   ```
3. Verificar que el `HEAD` remoto quedó en el commit esperado (`git log -1 --oneline`).

> ⚠️ `migrate --force` modifica la BD de producción. Aplica **RULE 0**: no lo corras
> sin confirmación explícita del usuario para esa ejecución puntual.

### Traer copia de la BD de producción a local

```bash
# En el servidor: dump comprimido por la red (sin dejar archivo en el server).
# (usar el helper askpass de CLAUDE.local.md para el ssh)
ssh ... 'MYSQL_PWD="<pass>" mysqldump --no-tablespaces --single-transaction \
  --skip-lock-tables --routines --events -u u454224791_ssh u454224791_sopas | gzip -c' \
  > backup_produccion_sopasysopitaspms_<fecha>.sql.gz

gunzip -kf backup_produccion_sopasysopitaspms_<fecha>.sql.gz
# El dump de MariaDB 11.8 trae una directiva sandbox que MariaDB 10.4 (XAMPP) no
# entiende — quitarla antes de importar:
sed -i '/enable the sandbox mode/d' backup_produccion_sopasysopitaspms_<fecha>.sql

# Importar a una BD local nueva (no pisar la que esté en uso) y apuntar el .env ahí.
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE \`sopas_prod_<fecha>\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -h 127.0.0.1 sopas_prod_<fecha> < backup_produccion_sopasysopitaspms_<fecha>.sql
# Editar .env -> DB_DATABASE=sopas_prod_<fecha> ; luego php artisan config:clear
```

Los `backup_produccion_*.sql[.gz]` están gitignored — no se versionan.

---

## Architecture

### Roles & autorización (estado real)
- Single role: `admin` (seeded in `database/seeders/RolesAndPermissionsSeeder.php`). Default user: `admin@admin.com` / `12345678`. No hay ruta de registro.
- `User` usa `HasRoles` (Spatie) + helper `isAdmin()`.
- **La autorización hoy es solo a nivel de ruta.** `routes/web.php` tiene dos grupos:
  - `middleware('auth')` — Caja, Mercado, Lista de mercado, Trabajadores de turno, Gastos, Pagos de ahorro, Perfil, `/components`.
  - `middleware(['auth', 'role:admin'])` — Nómina (empleados, nómina, pagos, prestaciones, ahorros), Gastos fijos y `/consolidado`.
- **No existen Policies** (`AuthServiceProvider::$policies = []`, no hay `app/Policies/`) y todos los Form Requests retornan `authorize(): true`. El sidebar no oculta links por rol: un usuario sin `admin` ve Nómina/Gastos fijos/Consolidado y recibe 403. Los tests de módulos admin hacen `Role::findOrCreate('admin','web')` + `assignRole('admin')` y verifican `assertForbidden()` para no-admin.

### Módulos de negocio (cómo se conectan)
Flujo típico: Controller → Service (`app/Services/`) → Model; estados como backed enums en `app/Enums/` (con `label()`/`color()`).

- **Caja** — `TurnoCajaService` abre/cierra el `TurnoCaja` (uno activo a la vez); `VentaService` registra `Venta` → `VentaItem` (→ `MenuItem`) + `VentaPago` (→ `MetodoPago`, multi-método, con cambio en efectivo). `MenuDiaService` define qué `MenuItem` se ofrecen cada día (`menu_dia` ↔ `dias_semana`). El cuadre vive en accessors de `TurnoCaja` (`efectivo_esperado = base + efectivo − gastos − ahorros − mercado pagado desde caja`).
- **Mercado** — catálogo `ProductoMercado`/`TipoProductoMercado`; cada compra es un `RegistroMercado` (con `metodo_pago_id` y `turno_caja_id` opcional si se pagó con la caja abierta). La **lista de mercado** (`ListaMercado` plantilla → sesión `Mercado` con `MercadoItem`s pendiente/registrado/saltado) la orquesta `MercadoSessionService`; `RegistroMercadoObserver` (registrado en `AppServiceProvider::boot`) devuelve el item a "pendiente" si se borra su registro.
- **Inventario** (une Mercado → Caja) — `ProductoMercado.controla_inventario` marca qué se cuenta. El stock **no se guarda**: es `SUM(cantidad)` del kardex `movimientos_inventario` (compra +, venta −, ajuste ±), y **toda** escritura pasa por `InventarioService` (nunca queda negativo; bloquea con `lockForUpdate`). Compras: `RegistroMercadoService` / `MercadoSessionService::registrarItem`. Ventas: un `MenuItem` tiene `componentes` (cantidad + 1..N `opciones` = productos con inventario); con 2+ opciones el cajero elige (ej. "Sabor de Doritos") y llega como `items[i][opciones][componente_id] = producto_id`. `VentaService` descuenta al crear, revierte+re-descuenta al editar y devuelve al borrar. UI del POS compartida en `resources/views/caja/_pos-inventario-script.blade.php` (`window.posInventario`). Ajustes manuales solo `role:admin`. Los ajustes no son `RegistroMercado`, así que no afectan el consolidado.
- **Gastos / Trabajadores de turno / Ahorro** — `GastoService` (gastos de turno con `valor` + `ahorro` del trabajador), `PagoAhorroService` (devolución del ahorro acumulado).
- **Nómina** (independiente de Caja; solo comparte `metodos_pago`) — `LiquidacionNominaService` genera `Nomina` + `NominaDetalle` (snapshots congelados: nombre, salario, %); el saldo/estado de pago se deriva de `Σ pagos_nomina`, no se almacena. Toda la matemática está en `App\Services\CalculadoraNomina` (estática, pura, enteros COP, redondeo half-up; básico = salario × días / 30, salud/pensión sobre el **básico**) con parámetros legales en `config/nomina.php`. Re-liquidar un periodo hace `forceDelete` de nóminas soft-deleted del mismo rango (índice único `fecha_inicio, fecha_fin`).
- **Gastos fijos** — `GastoFijoService` + catálogo `ConceptoGastoFijo`.
- **Consolidado** — `ConsolidadoContableService::resumen()` cruza todo por método de pago: ingresos = ventas de caja (cambio descontado del efectivo); egresos = gastos de caja (valor + ahorro) + `RegistroMercado` + `PagoNomina` + `GastoFijo`, cada uno una sola vez por su fecha. **Un módulo nuevo que genere egresos debe sumarse aquí** (y en `tests/Feature/DashboardConsolidadoTest.php`).

Deuda conocida: varios controllers de catálogo (`ProductoMercadoController`, `EmpleadoController`, `TipoMenuItemController`, `MetodoPagoController`…) y los dashboards (`DashboardMercadoController`, `DashboardCajaController`, `DashboardNominaController`) escriben/agregan con Eloquent directo sin service. No los tomes como patrón; los módulos de referencia son Nómina y Gastos fijos.

### Frontend
- Layouts: `resources/views/layouts/app.blade.php` (autenticado, sidebar drawer en mobile + fijo en desktop), `guest.blade.php` (login)
- Sidebar nav: `resources/views/layouts/navigation-vertical.blade.php`
- Vite entries: `resources/css/app.css`, `resources/js/app.js`, `resources/images/logoico.png`
- Theme toggle source: `resources/js/theme-toggle.js` (bundleado; emite el evento `dorilokos:theme-changed`)
- Globals en `window`: `Alpine`, `HSStaticMethods` (Preline), `Swal`, `TomSelect`, `ApexCharts`, `makeChart(selector, opts)` (theme-aware, lo usa `<x-chart>`), `renderIcons()`, `tableEnhanced(cfg)` (factory Alpine de `<x-table-enhanced>`), `_`, `axios`. `swalConfirm(form, opts)` y `showToast(icon, title)` se definen dentro de `DOMContentLoaded`.
- Un `MutationObserver` global re-renderiza iconos Lucide y re-ejecuta `HSStaticMethods.autoInit()` cuando se inserta DOM; `select[data-tom-select]` se auto-inicializa con TomSelect.

### Database
MySQL en 127.0.0.1:3306 (sin password en XAMPP local). Nombre en `.env` (`DB_DATABASE`). Tests: SQLite `:memory:`.

Core tables: `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`, `password_resets`, `failed_jobs`, `personal_access_tokens`. Las tablas de módulos usan soft deletes en casi todos los modelos; `metodos_pago` es el lookup compartido entre Caja, Mercado, Gastos y Nómina. Migraciones nuevas deben ser **aditivas** (la BD local tiene datos reales).

---

## RULE 1 — Reuse the Blade component library (mandatory)

**Every new view, module, partial or page MUST be built from the components in `resources/views/components/`.** No re-creating buttons, inputs, modals, cards or alerts with raw HTML. The component library is the design system — bypassing it fragments the look and breaks dark-mode consistency.

**Available components** (anonymous Blade — `<x-name />`, no PHP class needed):

| Categoria | Componentes |
|---|---|
| Layout | `<x-page-header>`, `<x-section>`, `<x-card>`, `<x-breadcrumb>` |
| Acciones | `<x-button>` (variants: primary, secondary, ghost, danger, success, link / sizes: xs, sm, md, lg) |
| Forms | `<x-input>`, `<x-textarea>`, `<x-select>` (con `tomselect` flag), `<x-checkbox>`, `<x-radio>`, `<x-toggle>`, `<x-input-currency>` (RULE 7), `<x-quantity-stepper>` |
| Datos | `<x-stat-card>`, `<x-data-table>` (Alpine: search/sort/paginate cliente), `<x-table-enhanced>` + `<x-th-sort>`, `<x-chart>` (ApexCharts vía `window.makeChart`), `<x-progress>`, `<x-spinner>` |
| Feedback | `<x-alert>` (info/success/warning/danger, dismissible), `<x-badge>`, `<x-empty-state>`, `<x-tooltip>` |
| Overlays | `<x-modal>` (Preline), `<x-dropdown>` + `<x-dropdown-item>` |
| Nav | `<x-tabs>`, `<x-accordion>` |
| Misc | `<x-icon name="...">` (Lucide), `<x-avatar>` |

**Live showcase:** ver todo renderizado en `/components` (autenticado). Ahí esta el contrato visual y los nombres exactos de variants/props.

**Rules when reusing:**
1. Si necesitas algo que no existe, **primero extiende un componente** vía slots/atributos antes de crear uno nuevo. Si genuinamente falta, **crealo en `resources/views/components/`** siguiendo el mismo patrón (props + `$attributes->merge()` + variantes light/dark) y registralo en el showcase `resources/views/components-showcase.blade.php` para que quede visible.
2. Si estas tentado a copiar Bootstrap classes (`form-control`, `btn`, `card`, `alert-*`, `badge`, `bg-primary`...): **NO**. Bootstrap fue removido. Usa `<x-button>`, `<x-input>`, `<x-card>`, `<x-alert>`, etc.
3. Para iconos usa **siempre** `<x-icon name="kebab-case">` (Lucide). Si el icono que necesitas no esta en `resources/js/app.js`, agregalo en **los dos lugares**: el named import `import { ... } from 'lucide'` **y** el objeto `const usedIcons = { ... }` (PascalCase en ambos). Si falta en cualquiera, el icono no se renderiza.
4. Iconos: respeta el set ya importado. Antes de inventar nuevos, revisa los disponibles en el showcase.
5. Tablas — dos opciones, ambas en cliente:
   - `<x-data-table>`: la tabla se define por props/columnas (hasta ~1000 filas).
   - `<x-table-enhanced>` (la más usada en los módulos): envuelve una `<table>` Blade ya renderizada y le agrega búsqueda/filtros/orden/paginación. Marca `<tbody data-enhance>`, cada `<tr data-row>`, y usa `<x-th-sort :col="N">` en los `<th>` ordenables; los `<tfoot>` de totales quedan intactos. Ejemplo: `resources/views/nomina/index.blade.php`.
   - No hay DataTables JS (yajra está en composer pero sin uso). Para datasets grandes, paginación server-side con los estilos de estos componentes.
6. `<x-table-enhanced>`, `<x-th-sort>` y `<x-quantity-stepper>` aún **no** están en el showcase — regístralos si los tocas.

---

## RULE 2 — Theming (light / dark) required for all new code

Every new module, view and component MUST work correctly in both modes. The layout already wires el mecanismo — el codigo nuevo debe respetarlo, no reinventarlo.

**Como se aplica el tema:**
- El script anti-FOUC en `layouts/app.blade.php` y `layouts/guest.blade.php` lee la preferencia (`users.theme` + `localStorage['dorilokos-theme']`) y setea en `<html>`:
  - `data-theme="light|dark"` (atributo custom)
  - `data-bs-theme="light|dark"` (legacy compat)
  - `class="dark"` (Tailwind `darkMode: 'class'`)
- El boton `#themeToggleBtn` en el header dispara `theme-toggle.js`, alterna los flags y hace `PATCH /profile/theme` para persistir.

**Reglas al agregar vistas/componentes:**
1. Nunca hardcodear `bg-*`, `text-*`, `border-*` que solo se vean bien en un modo. Cada color tiene contraparte `dark:`.
2. Usa los tokens Tailwind (`primary-*`, `accent-*`, `cream-*` definidos en `tailwind.config.js`) en lugar de hex inline.
3. Para utilities Tailwind: pair light + dark — `class="bg-white text-cream-900 dark:bg-cream-900 dark:text-cream-50"`.
4. Para CSS custom: scope con `[data-theme="dark"] .your-class { ... }`. No crees una hoja oscura aparte.
5. **Verifica en ambos modos** antes de marcar la tarea completada — toggle del header, revisa contrast, hovers, modales, tablas, forms, estados disabled. Si no puedes toggle en navegador, dilo explicitamente.
6. Imagenes/iconos: usa SVG con `stroke="currentColor"` o `fill="currentColor"` para que hereden color (los Lucide ya hacen esto). Si necesitas una imagen distinta para dark, swap via `[data-theme="dark"] img { content: url(...) }`.
7. JS dinamico que inserta DOM debe usar las mismas clases Tailwind/componentes — no leer `localStorage['dorilokos-theme']` para ramificar estilos en JS, que la cascada CSS lo resuelva.

---

## RULE 3 — Brand palette (morado + fuego, del logo DorilokosMix)

Paleta del proyecto, definida en `tailwind.config.js` `theme.extend.colors`. **Usa los tokens, no hex inline.**

| Token | Light | Dark | Uso |
|---|---|---|---|
| `primary-500` | `#5f306a` (morado logo) | usar `primary-400`/`300` | CTAs, links activos, sidebar item activo, badges primary |
| `primary-600/700` | `#532a5d` / `#46234f` | — | hover/active de primary, degradados |
| `primary-300` | `#bd97ca` | — | textos/links en dark, ilustraciones |
| `accent-500` | `#f39030` (naranja fuego) | — | acentos, punto activo del sidebar, CTAs sobre fondo morado |
| `accent-300` | `#f5c020` (dorado) | — | taglines sobre morado, degradados fuego |
| `flame-500` | `#e3262a` (rojo "MIX") | — | resaltes, franja degradada de marca (`from-primary-500 via-flame-500 to-accent-400`) |
| `cream-50/100/200` | neutros claros con tinte lavanda | — | surfaces, cards, backgrounds |
| `cream-900/950` | morado-gris muy oscuro | — | textos sobre fondo claro / fondos en dark |
| `surface.DEFAULT` | `#fcfbfd` | `#140f18` | body background |

Fuente de marca: `font-brand` / `.brand-script` = **Lilita One** (solo peso 400). Logo fuente en `resources/images/dorilokos-mix-original.png`; derivados en `public/images/` y `public/favicon.ico`.

Tailwind tip: para tonos complementarios reach for `amber-*`, `orange-*`, `violet-*`. Para semantica: `emerald` (success), `rose` (danger), `amber` (warning), `sky` (info).

---

## RULE 4 — Database normalization (mandatory before any DDL)

**Antes de escribir cualquier migration o crear cualquier tabla, normaliza el modelo de datos a por lo menos 3NF.** Saltarse este paso casi siempre cuesta 10x mas refactorizar despues. La revision es parte del trabajo, no un nice-to-have.

**Checklist de normalizacion (3NF baseline):**

1. **1NF** — atomicidad. Sin listas/CSV en columnas (`tags = "a,b,c"` ❌), sin arrays JSON cuando vas a filtrar/joinear por sus elementos (usa tabla pivote). Cada celda = un valor.
2. **2NF** — sin dependencias parciales. Si una tabla tiene PK compuesta, todas las columnas no-clave dependen de la **clave completa**, no de un subconjunto. (Si encuentras dependencia parcial, parte la tabla en dos.)
3. **3NF** — sin dependencias transitivas. Si `A → B` y `B → C`, entonces `C` no vive en la tabla de `A`. Ejemplo clasico: `pedido.cliente_id` + `pedido.cliente_nombre` ❌ — el nombre vive en `clientes`, no se duplica.
4. **Boyce-Codd (BCNF)** cuando aplique. Si tienes determinantes que no son superclaves, divide la tabla.

**Reglas operativas:**
- **Foreign keys explicitas siempre** — `$table->foreignId('user_id')->constrained()->cascadeOnDelete()` o `restrictOnDelete()`. Nada de `unsignedBigInteger` huerfano.
- **Naming consistente** — tablas en plural snake_case (`pedidos`, `pedido_items`), modelos PascalCase singular (`Pedido`, `PedidoItem`), columnas snake_case, FKs `<modelo>_id`, timestamps siempre (`$table->timestamps()`).
- **Tablas pivot** para relaciones many-to-many — `pedido_producto` con FKs a ambos lados + columnas extra de la relacion (cantidad, precio_unitario_en_compra). No JSON arrays.
- **Soft deletes** (`$table->softDeletes()`) cuando el negocio necesita recuperar registros borrados. Sino, hard delete con confirmacion.
- **Indices** en columnas que se filtran o joinean frecuentemente — `$table->index('estado')`, FK ya viene indexada por `constrained()`.
- **Constraints** — `unique` cuando aplique (email, slug, codigo), `nullable()` solo si el negocio lo permite, defaults sensibles, check constraints para enums (mejor: ENUM nativo o lookup table).
- **Lookup tables** para enums que pueden crecer — `estados_pedido`, `tipos_pago` con `id` + `nombre` + `codigo` — en lugar de strings sueltas. Asi cambiar/agregar valores no requiere migration.
- **Evita columnas calculadas almacenadas** — `total = subtotal + impuesto` se calcula en el accessor del modelo o en query. Solo persiste si lo vas a indexar/agregar masivamente.
- **Auditoria** — para entidades importantes considera `created_by`, `updated_by` (FK a `users.id`) y eventos del modelo o `spatie/laravel-activitylog`.

**Proceso obligatorio antes de crear una tabla:**

1. Lista las **entidades** del dominio y sus atributos (en lenguaje natural).
2. Identifica **relaciones** (1-1, 1-N, N-N) y **cardinalidad** (obligatoria/opcional, en cada lado).
3. Aplica el checklist 1NF→2NF→3NF arriba — si una columna falla, divide.
4. Dibuja un mini-ERD textual (en el commit message o en `docs/` si es grande) con tablas, FKs, e indices clave.
5. Escribe la migration. Cada FK tiene `constrained()` y politica de `onDelete` explicita. Cada timestamp/softDelete decidido a proposito.
6. Crea seeder con datos coherentes (FK validas) — confirma con `migrate:fresh --seed`.

Si la informacion del dominio es ambigua, **pregunta antes de inventar**. Es mas barato preguntar 5 minutos que migrar datos despues.

---

## RULE 5 — SOLID + architecture patterns (mandatory)

Toda funcionalidad nueva debe seguir SOLID y los patrones que ya estan en uso. Codigo que viole estos principios se devuelve.

### SOLID

- **S — Single Responsibility.** Una clase = una razon para cambiar. Controllers delgados (solo HTTP: validar, llamar servicio, devolver respuesta). Modelos solo persistencia + relaciones + scopes + accessors. Logica de negocio en **Services** (`app/Services/`). Reglas de validacion en **Form Requests** (`app/Http/Requests/`). Politicas de autorizacion en **Policies** (`app/Policies/`).
- **O — Open/Closed.** Extiende via composicion/herencia/strategy, no editando codigo estable. Para variantes (ej. distintos tipos de pago/notificacion) usa interfaces + strategy/factory.
- **L — Liskov Substitution.** Subclases respetan el contrato de la base. Si `EnviarNotificacion` interfaz dice "no lanza", la implementacion concreta tampoco.
- **I — Interface Segregation.** Interfaces pequeñas y especificas. Mejor `LeerCatalogo` + `EditarCatalogo` que `CrudCatalogo` con 12 metodos.
- **D — Dependency Inversion.** Depende de abstracciones (interfaces), no implementaciones. Bind en `AppServiceProvider::register()` con `$this->app->bind(Interface::class, Concrete::class)`. Inyecta via constructor.

### Patrones recomendados (usalos cuando aplican, no a la fuerza)

> Los ejemplos `Pedido*` de esta tabla son **nombres ilustrativos**, no archivos existentes. Hoy existen `Services/`, `Enums/`, `Observers/` y `Http/Requests/`; **no** existen `Policies/`, `Actions/`, `Data/`, `Queries/`, `Events/`, `Listeners/` ni `Http/Resources/`, y `AppServiceProvider::register()` no tiene bindings. Créalos cuando el patrón aplique.

| Patron | Cuando usar | Ejemplo (nombre ilustrativo) |
|---|---|---|
| **Service layer** | Logica de negocio reusable, transacciones, orquestacion. **Default para cualquier feature no-trivial.** | `app/Services/PedidoService.php` |
| **Repository** (opcional) | Cuando quieres aislar Eloquent del codigo de negocio o tener fallbacks (cache, alternativos). En proyectos pequeños es over-engineering — **prefiere Eloquent directo en el service**. | Solo si la complejidad lo justifica |
| **Action / Single-method service** | Operaciones unicas e independientes. Un archivo, una clase, un metodo `__invoke()`. | `app/Actions/CrearPedido.php` |
| **DTO / Value Object** | Pasar datos entre capas sin acoplar a Request/Eloquent. PHP 8 readonly classes ideales. | `app/Data/PedidoData.php` |
| **Form Request** | Validacion + autorizacion de cada endpoint. Una por accion. | `app/Http/Requests/StorePedidoRequest.php` |
| **Policy** | Autorizacion por modelo. Registrar en `AuthServiceProvider::policies`. Usar `$this->authorize()` en controllers. | `app/Policies/PedidoPolicy.php` |
| **Resource (API)** | Transformar modelos a JSON. Si expones API. | `app/Http/Resources/PedidoResource.php` |
| **Observer** | Side effects al crear/actualizar/borrar (audit log, notificaciones). | `app/Observers/PedidoObserver.php` |
| **Event + Listener** | Side effects desacoplados, asincronos via queue. | `PedidoCreado` + `EnviarConfirmacion` |
| **Strategy / Factory** | Familia de algoritmos intercambiables (ej. tipos de pago, calculadoras de impuesto). | `App\Pagos\PagoEfectivoStrategy` |
| **Specification / Query object** | Filtros complejos reutilizables sobre Eloquent. | `app/Queries/PedidosPendientes.php` |

### Reglas operativas concretas

- **Controllers de maximo ~7 metodos resource (`index, create, store, show, edit, update, destroy`).** Sin logica de negocio dentro — delega al service.
- **Validacion siempre en Form Request**, nunca con `$request->validate()` inline (excepto endpoints triviales).
- **Autorizacion siempre via Policy** (`$this->authorize('update', $pedido)` o middleware `can:`). No hagas `if ($user->id === $pedido->user_id)` en controllers.
- **Transacciones DB** para operaciones multi-tabla — `DB::transaction(fn() => ...)`. Si falla a la mitad, rollback automatico.
- **Inyeccion por constructor** o promoted properties: `public function __construct(private PedidoService $svc) {}`. Nada de `new Service()` ni `app(Service::class)` salvo en factories.
- **Type hints estrictos**: argumentos y returns siempre tipados (`int`, `string`, `?Pedido`, `Collection`, etc.). PHP 8.1 union types y readonly cuando aplican.
- **Strict types** opcional pero deseable: `declare(strict_types=1);` al inicio de clases nuevas.
- **No magic strings** — para estados, tipos, etc., usa enums (`enum EstadoPedido: string { case Pendiente = 'pendiente'; ... }`). Laravel 9 soporta backed enums en migrations y casts.
- **Eager loading** para evitar N+1: `Pedido::with('cliente', 'items.producto')->get()`. Detecta con `\Illuminate\Database\Eloquent\Builder::query()` debug o con `barryvdh/laravel-debugbar` en local.
- **Configuracion via `.env` / `config/`** — nunca hardcodear API keys, URLs, limites en clases. Lee con `config('servicio.clave')`.
- **Nombres expresivos** — clases, metodos y variables se leen como prosa. `crearPedidoConItems()` ✓, `procesar()` ✗.
- **Comentarios solo cuando el "porque" no es obvio** — un workaround, una restriccion de negocio, una decision contraintuitiva. NO documentes lo que el codigo ya dice.

### Estructura de carpetas a respetar / crear cuando aplique

```
app/
├── Actions/                # Single-purpose actions (opcional)
├── Data/                   # DTOs / Value Objects
├── Enums/                  # Backed enums tipados
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/        # Delgados
│   ├── Middleware/
│   ├── Requests/           # Form Requests (validacion + auth)
│   └── Resources/          # API transformers
├── Listeners/
├── Models/                 # Solo persistencia + relaciones + scopes
├── Observers/
├── Policies/               # Autorizacion por modelo
├── Providers/
├── Queries/                # Query objects reutilizables (opcional)
├── Services/               # Logica de negocio
└── View/Components/        # Blade components con clase PHP (cuando necesitan logica)
```

---

## RULE 6 — File uploads (mandatory): public/ only, never storage/

El servidor de produccion (Hostinger shared hosting) **no soporta** `php artisan storage:link` — no se puede crear el symlink `public/storage` → `storage/app/public`. Por eso **todos los uploads van directamente a `public/uploads/{modulo}/`** y la BD guarda solo el nombre del archivo. El modelo expone un accessor que construye la URL con `asset()`.

**Reglas concretas:**

1. **Carpeta destino**: `public_path('uploads/{modulo}/')` — `kebab-case` plural (ej. `profile-photos`, `productos-mercado`).
2. **Crear directorio si no existe**: `File::makeDirectory($path, 0755, true)` (recursivo).
3. **Nombrar el archivo unico**: `{prefijo}_{id}_{time()}.{ext}`.
4. **BD guarda solo el nombre** (string). Nunca el path completo, nunca `/storage/...`.
5. **Eliminar archivo viejo** antes de subir uno nuevo: `File::exists()` + `File::delete()`.
6. **Validar siempre**: `mimes:jpeg,png,jpg,gif,webp,avif, max:2048` (o el equivalente para PDFs/docs). NO uses la regla `image` de Laravel 9 — rechaza AVIF aunque agregues `avif` a `mimes`. La regla `mimes` ya valida tanto la extensión como el MIME real del archivo.
7. **Accessor en el modelo**: `getXxxUrlAttribute(): string` retorna `asset('uploads/{modulo}/'.$this->columna)` o `''`.
8. **Prohibido** `Storage::disk('public')`, `Storage::put`, `$file->storeAs(...)`, ni cualquier API que dependa de `storage:link`.

**Patron de referencia:** `App\Http\Controllers\ProfileController::updatePhoto` y `App\Models\User::getProfilePhotoUrlAttribute`. Tambien `App\Http\Controllers\ProductoMercadoController::saveImagen` y `App\Models\ProductoMercado::getImagenUrlAttribute`.

**Ejemplo minimo:**

```php
// Controller
if ($request->hasFile('imagen')) {
    $path = public_path('uploads/foos');
    if (!File::exists($path)) File::makeDirectory($path, 0755, true);

    $file = $request->file('imagen');
    $name = 'foo_' . $foo->id . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->move($path, $name);

    $foo->update(['imagen' => $name]);
}

// Model
public function getImagenUrlAttribute(): string
{
    return $this->imagen ? asset('uploads/foos/' . $this->imagen) : '';
}
```

**Git:** `.gitignore` ya ignora `public/uploads/*` (los binarios no se versionan); cuando crees una carpeta nueva, agrega un `.gitkeep` para que la carpeta exista en el repo aunque este vacia.

---

## RULE 7 — Money/currency inputs (mandatory): use `<x-input-currency>`

Cualquier input que capture un valor monetario (precio, costo, total, valor, monto, etc.) DEBE usar `<x-input-currency>`. **No** uses `<x-input type="number">` para dinero — los usuarios esperan ver `$ 100.000` mientras escriben, no `100000` sin formato. Todos los valores en pesos deben mostrarse con separadores de miles en formato es-CO (`.` como separador de miles).

**Como funciona el componente:**
- El input visible muestra el valor formateado en vivo (`100.000`) con un `$` decorativo a la izquierda.
- Internamente un `<input type="hidden" name="...">` envia solo digitos crudos (`100000`) al backend.
- El backend recibe un integer valido — la validacion `'valor' => ['required', 'integer', 'min:1']` pasa sin transformacion adicional. **No hagas parseo manual en el controller** — el componente ya hace la conversion antes del submit.
- Despues de cada cambio dispara un evento `currency-changed` con el valor numerico (`$event.detail`) para que un Alpine padre lo pueda escuchar (ej. para calcular un total en vivo).

**Uso:**

```blade
<x-input-currency
    label="Valor total (COP)"
    name="valor"
    :value="old('valor', $registro->valor ?? null)"
    required
/>
```

Para usar el valor en un `x-data` padre (calculo en vivo, validacion, etc.):

```blade
<div x-data="{ valor: 0 }" x-on:currency-changed="valor = $event.detail">
    <x-input-currency name="valor" />
    <p x-show="valor > 0" x-text="'Recibes: $ ' + (valor * 0.97).toLocaleString('es-CO')"></p>
</div>
```

**Reglas operativas:**

1. **Columna BD**: `unsignedInteger` (COP no usa decimales reales, max ~4.2 mil M). Si el negocio requiere centavos/multimoneda, documentalo y usa `unsignedBigInteger` con multiplicador ×100, o columnas separadas `valor` + `moneda`.
2. **Validacion en Form Request**: `['required', 'integer', 'min:1']`. NO uses `numeric` ni `decimal` para COP.
3. **Mostrar valores guardados** (lista, reporte, exportacion, PDF, email): usa el accessor del modelo `getXxxFormateadoAttribute()` que retorna `'$ ' . number_format($valor, 0, ',', '.')`. Centraliza el formato en una sola fuente. Si el modulo no lo tiene, agregalo — no formatees inline en el blade.
4. **Calculos en vivo en el frontend** (totales, descuentos, unitarios): usa `Intl.NumberFormat('es-CO').format(...)` en JS. Coincide con el formato del componente (separadores `.`).
5. **Multiples campos en el mismo formulario**: cada `<x-input-currency>` dispara su propio evento `currency-changed`. Distinguelos con `name` o usando refs Alpine si necesitas reaccionar a uno especifico.
6. **Restablecer valor (reset/old)**: el componente lee `old($name, $value)` automaticamente; en errores de validacion el usuario no pierde lo que escribio. No reimpliementes esto.

**Patron de referencia:** `resources/views/components/input-currency.blade.php` y su uso en `resources/views/registro-mercado/create.blade.php`.

---

## Adding a new module — checklist

Cuando vayas a agregar un modulo (ej. "Pedidos", "Productos"), recorre **en este orden** y no salgas hasta cumplir cada paso:

1. **Diseño de datos** — aplica RULE 4 (normalizacion). Mini-ERD textual antes de cualquier `php artisan make:migration`.
2. **Migrations** — FKs `constrained()`, indices, soft deletes / timestamps por decision, no por copia.
3. **Modelos** — relaciones, casts (enums, dates, arrays), scopes nombrados, accessors. Sin logica de negocio.
4. **Form Requests** — validacion + autorizacion (`authorize()` que llama Policy).
5. **Policies** — `viewAny`, `view`, `create`, `update`, `delete`, etc. Registrar en `AuthServiceProvider::policies`.
6. **Service / Action** — logica de negocio. Inyectada en controller. Usa transacciones cuando toca multiples tablas.
7. **Controller** — delgado, resource standard. Llama service, retorna view/redirect/JSON.
8. **Routes** — `routes/web.php`, dentro del grupo `['auth', 'role:admin']` salvo que el módulo sea operativo diario (como Caja/Mercado, que están solo en `auth`) — pregunta si no está claro. Resource routes preferido.
9. **Vistas** — `resources/views/<modulo>/`. **Reutiliza componentes** (RULE 1). Theme aware (RULE 2). Responsive mobile-first.
9.5. **Uploads** — si el modulo recibe archivos (imagenes, PDFs, etc.), sigue **RULE 6**: directo a `public/uploads/{modulo}/`. Nunca `Storage::`.
9.6. **Inputs de moneda** — si el modulo captura valores en pesos (precio, total, costo, valor), sigue **RULE 7**: usa `<x-input-currency>` y un accessor `getXxxFormateadoAttribute()` para mostrarlo. Nunca `<x-input type="number">` para dinero.
10. **Sidebar** — agrega link en `resources/views/layouts/navigation-vertical.blade.php` con icono Lucide.
11. **Seeder** — datos demo coherentes en `database/seeders/`. Catálogos de referencia: seeder idempotente (`updateOrCreate`) llamado desde `DatabaseSeeder`; datos demo: llamarlo desde `DemoSeeder`. Verifica en SQLite/tests o en una BD de prueba — `migrate:fresh --seed` sobre la BD local solo con permiso (RULE 0).
12. **Tests** — al menos un Feature test del happy path + autorizacion (`tests/Feature/<Modulo>Test.php`).
13. **Verificacion manual** — abrir el modulo en navegador, recorrer create/edit/delete, modo claro y oscuro, mobile (DevTools 375px) y desktop. Lighthouse mobile > 90.
14. **Build** — `npm run build` antes de cerrar la tarea, confirmar bundle no inflado.
