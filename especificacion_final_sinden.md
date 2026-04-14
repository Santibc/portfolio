# ESPECIFICACIÓN FUNCIONAL COMPLETA — Sistema de Gestión de Órdenes de Trabajo SINDEN S.A.S.

## CONTEXTO PARA CLAUDE CODE

Este documento describe EXACTAMENTE cómo debe funcionar cada módulo de un sistema de gestión de órdenes para un taller de manufactura de piezas metálicas (corte, doblado, soldadura, acabado de láminas). El proyecto ya tiene el esqueleto base funcional incluyendo autenticación, roles/permisos, y template con componentes reutilizables.

**Negocio:** El taller recibe órdenes de clientes que necesitan piezas fabricadas a partir de láminas metálicas (HR - Hot Rolled, CR - Cold Rolled, INOX - Acero Inoxidable) con distintos calibres (C3mm, C2mm, C1/2, etc.). Cada orden tiene bosquejos/dibujos técnicos de las piezas, y múltiples operarios trabajan secuencialmente en las piezas (uno corta, otro dobla, otro suelda, otro da acabado) hasta completarlas al 100%.

**Diseño visual:** Material Design 3 con fuente Albert Sans. Soporte para modo Light y Dark. La app se usa principalmente en tablets Android pero también en escritorio.

**Convención de nombres:** TODOS los nombres de tablas, campos, enums, valores, relaciones y rutas van en ESPAÑOL. Sin excepciones.

**Roles del sistema (4):**
- **Recepción** — Crea órdenes, gestiona clientes, gestiona entregas
- **Operario** — Ejecuta el trabajo sobre las piezas, reporta avance
- **Contabilidad** — Gestiona pagos, aprueba abonos
- **Administrador** — Configuración general, gestión de usuarios

---

## MÓDULO 1: AUTENTICACIÓN

### 1.1 Pantalla de Login (compartida por todos los roles)

**Campos del formulario:**
- `correo` — Label: "Usuario ó Correo Electrónico". Campo de texto. Validación: requerido, formato email.
- `contrasena` — Label: "Contraseña". Campo password con toggle de visibilidad. Validación: requerido.
- Botón principal: "INICIO DE SESIÓN"
- Enlace debajo: "Recuperación de contraseña" → lleva a pantalla de recuperación.

**Comportamiento post-login:**
- Según el rol del usuario autenticado, redirigir al dashboard correspondiente:
  - `recepcion` → `/recepcion/panel`
  - `operario` → `/operario/panel`
  - `contabilidad` → `/contabilidad/panel`
  - `administrador` → `/admin/configuracion`
- Si el usuario tiene múltiples roles, redirigir al de mayor jerarquía: administrador > recepcion > contabilidad > operario.

### 1.2 Recuperación de Contraseña

- Campo: "Correo Electrónico"
- Botón: "Enviar enlace de recuperación"
- Flujo estándar de recuperación: envía email con link, el usuario establece nueva contraseña.

---

## MÓDULO 2: GESTIÓN DE CLIENTES

### 2.1 Tabla: `clientes`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `nombre` | varchar(255) | requerido | Nombre completo del cliente |
| `direccion` | text | nullable | Dirección |
| `correo` | varchar(255) | nullable, formato email | Correo electrónico |
| `celular_1` | varchar(20) | nullable | Celular principal |
| `celular_2` | varchar(20) | nullable | Celular secundario |
| `activo` | boolean | default:true | Para desactivar sin eliminar |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Reglas de negocio:**
- Los clientes se crean desde el flujo de creación de orden O desde el listado de clientes.
- Un cliente puede tener múltiples órdenes.
- No se eliminan clientes, solo se desactivan (borrado lógico).

### 2.2 Pantalla: Listado de Clientes

**Acceso:** Recepción y Administrador.

**URL:** `/recepcion/clientes`

**Contenido:**
- Título: "CLIENTES"
- Tabla con columnas: Nombre, Dirección, Celular 1, Celular 2, Correo Electrónico, Acciones.
- Buscador superior para filtrar por nombre, celular o correo.
- Paginación.

**Acciones por fila:**
- "VER/EDITAR" — Abre modal o página de edición.

**Acciones globales:**
- Botón "AGREGAR" — Abre formulario de nuevo cliente.
- Botón "IMPRIMIR LISTADO" — Genera PDF y descarga.
- Botón "GENERAR HOJA CALCULO" — Exporta a Excel (.xlsx).

---

## MÓDULO 3: CATÁLOGO DE ITEMS

### 3.1 Tabla: `catalogo_items`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `codigo` | varchar(50) | requerido, único | Código del item. Ej: "SER 1004" |
| `descripcion` | text | requerido | Descripción del producto/servicio |
| `precio_unitario` | decimal(12,2) | requerido, min:0 | Precio unitario sin IVA |
| `porcentaje_iva` | decimal(5,2) | requerido, default:19.00 | Porcentaje de IVA |
| `categoria` | varchar(50) | requerido | Valores: 'servicio', 'material', 'producto_terminado' |
| `activo` | boolean | default:true | Borrado lógico |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Valores de `categoria`:**
| Valor | Label visual | Descripción |
|-------|-------------|-------------|
| `servicio` | "SERVICIO" | Servicios como corte, doblez, soldadura, pintura |
| `material` | "MATERIAL" | Láminas, tubos, ángulos y demás materiales vendidos |
| `producto_terminado` | "PRODUCTO TERMINADO" | Piezas o productos ya fabricados |

**Regla importante:** La categoría se asigna al item en el catálogo, NO cada vez que se agrega un item a una orden. El recepcionista NO elige la categoría al crear la orden — ya viene predefinida.

**Al seleccionar un item del catálogo en la creación de orden**, se auto-llenan código, descripción, precio unitario y la categoría se hereda invisiblemente.

### 3.2 Pantalla: Listado de Items

**Acceso:** Recepción, Contabilidad y Administrador.

**URL:** `/recepcion/items` y `/contabilidad/items`

**Contenido:**
- Título: "LISTA ITEMS"
- Tabla con columnas: Código, Descripción, Categoría, P. Unitario, IVA %, Acciones.
- Buscador para filtrar por código, descripción o categoría.

**Acciones por fila:**
- "EDITAR" — Formulario de edición.
- "Eliminar" — Desactiva. Pide confirmación.

**Acciones globales:**
- Botón "AGREGAR" — Formulario de nuevo item.

---

## MÓDULO 4: BOSQUEJOS MATRIZ (Biblioteca de Plantillas)

### 4.1 Tabla: `grupos_bosquejos`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `nombre` | varchar(255) | requerido | Nombre del grupo. Ej: "Puertas Industriales" |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

### 4.2 Tabla: `plantillas_bosquejos`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `grupo_bosquejo_id` | FK nullable | existe en grupos_bosquejos | Grupo al que pertenece |
| `nombre` | varchar(255) | requerido | Nombre del bosquejo |
| `ruta_archivo` | varchar(500) | requerido | Ruta del archivo de imagen |
| `ruta_miniatura` | varchar(500) | nullable | Miniatura generada |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Relación:** Un grupo tiene muchas plantillas. Una plantilla puede no pertenecer a ningún grupo.

### 4.3 Pantalla: Gestión de Bosquejos Matriz

**Acceso:** Recepción y Administrador.

**URL:** `/recepcion/bosquejos-matriz`

- Vista organizada por grupos. Cada grupo es sección colapsable con bosquejos como tarjetas con miniatura.
- "AGREGAR GRUPO" — Crea grupo (solo nombre).
- Dentro de cada grupo: botón para agregar bosquejo (sube imagen + nombre).
- Cada bosquejo: Ver (expande), Editar (nombre), Eliminar, "DESCARGAR BOSQUEJO".

---

## MÓDULO 5: ÓRDENES DE TRABAJO (Módulo Principal)

### 5.1 Tabla: `ordenes`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `numero_orden` | varchar(20) | único, auto-generado | Consecutivo "#0001". Se genera al GENERAR, NO al guardar borrador |
| `cliente_id` | FK | requerido, existe en clientes | |
| `creado_por` | FK | requerido, existe en usuarios | Recepcionista que creó |
| `estado_trabajo` | varchar(50) | requerido | Estado de ejecución del trabajo |
| `estado_entrega` | varchar(50) | nullable | Estado de entrega al cliente |
| `estado_pago` | varchar(50) | nullable | Estado financiero |
| `fecha_entrega` | date | nullable | Fecha programada de entrega |
| `hora_entrega` | time | nullable | |
| `ruta_firma_cliente` | varchar(500) | nullable | Imagen de firma digital |
| `notas` | text | nullable | Observaciones generales |
| `subtotal` | decimal(12,2) | calculado | Suma de subtotales de items |
| `monto_iva` | decimal(12,2) | calculado | IVA total |
| `total` | decimal(12,2) | calculado | subtotal + monto_iva |
| `total_pagado` | decimal(12,2) | calculado | Suma de pagos aprobados |
| `saldo` | decimal(12,2) | calculado | total - total_pagado |
| `clonada_de_id` | FK nullable | existe en ordenes | Si fue copiada de otra orden |
| `bloqueada_por` | FK nullable | existe en usuarios | Usuario que tiene la orden abierta |
| `bloqueada_en` | timestamp | nullable | Cuándo se bloqueó |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

### Sistema de 3 Estados Independientes

La orden maneja 3 campos de estado que funcionan SIMULTÁNEAMENTE. NUNCA el usuario cambia estos estados manualmente — se calculan automáticamente.

**Valores de `estado_trabajo`:**
| Valor | Label visual | Cuándo se asigna |
|-------|-------------|-----------------|
| `borrador` | "BORRADOR" | Al GUARDAR ORDEN |
| `generada` | "GENERADA" | Al GENERAR ORDEN (sin piezas → pasa directo a 'ejecutada') |
| `en_ejecucion` | "EN EJECUCIÓN" | Alguna pieza tiene progreso > 0% y NO todas están al 100% |
| `ejecutada_parcialmente` | "EJECUTADA PARCIALMENTE" | Al menos una pieza al 100% pero no todas |
| `ejecutada` | "EJECUTADA" | TODAS las piezas al 100%, O la orden no tiene piezas |

**Valores de `estado_entrega`:**
| Valor | Label visual | Cuándo se asigna |
|-------|-------------|-----------------|
| `null` | (no se muestra badge) | Ninguna pieza entregada |
| `entregada_parcialmente` | "ENTREGADA PARCIALMENTE" | Al menos una pieza entregada pero no todas |
| `entregada` | "ENTREGADA" | Todas las piezas entregadas, O orden sin piezas |

**Valores de `estado_pago`:**
| Valor | Label visual | Cuándo se asigna |
|-------|-------------|-----------------|
| `null` | (no se muestra) | Solo borradores |
| `saldo_pendiente` | "SALDO PENDIENTE" | saldo > 0 |
| `pagado` | "PAGADO" | saldo <= 0 |

### Tabla de combinaciones válidas (19 combinaciones + borrador)

| # | estado_trabajo | estado_entrega | estado_pago | Label visual |
|---|---------------|---------------|------------|-------------|
| 1 | borrador | null | null | "Borrador" |
| 2 | generada | null | saldo_pendiente | "Generada · Saldo Pendiente" |
| 3 | generada | null | pagado | "Generada · Pagado" |
| 4 | en_ejecucion | null | saldo_pendiente | "En Ejecución · Saldo Pendiente" |
| 5 | en_ejecucion | null | pagado | "En Ejecución · Pagado" |
| 6 | en_ejecucion | entregada_parcialmente | saldo_pendiente | "En Ejecución · Entregada Parcialmente · Saldo Pendiente" |
| 7 | en_ejecucion | entregada_parcialmente | pagado | "En Ejecución · Entregada Parcialmente · Pagado" |
| 8 | ejecutada_parcialmente | null | saldo_pendiente | "Ejecutada Parcialmente · Saldo Pendiente" |
| 9 | ejecutada_parcialmente | null | pagado | "Ejecutada Parcialmente · Pagado" |
| 10 | ejecutada_parcialmente | entregada_parcialmente | saldo_pendiente | "Ejecutada Parcialmente · Entregada Parcialmente · Saldo Pendiente" |
| 11 | ejecutada_parcialmente | entregada_parcialmente | pagado | "Ejecutada Parcialmente · Entregada Parcialmente · Pagado" |
| 12 | ejecutada_parcialmente | entregada | saldo_pendiente | "Ejecutada Parcialmente · Entregada · Saldo Pendiente" |
| 13 | ejecutada_parcialmente | entregada | pagado | "Ejecutada Parcialmente · Entregada · Pagado" |
| 14 | ejecutada | null | saldo_pendiente | "Ejecutada · Saldo Pendiente" |
| 15 | ejecutada | null | pagado | "Ejecutada · Pagado" |
| 16 | ejecutada | entregada_parcialmente | saldo_pendiente | "Ejecutada · Entregada Parcialmente · Saldo Pendiente" |
| 17 | ejecutada | entregada_parcialmente | pagado | "Ejecutada · Entregada Parcialmente · Pagado" |
| 18 | ejecutada | entregada | saldo_pendiente | "Ejecutada · Entregada · Saldo Pendiente" |
| 19 | ejecutada | entregada | pagado | "Ejecutada · Entregada · Pagado" |

Adicionalmente existe el estado `anulada` para órdenes canceladas manualmente.

### Reglas automáticas de cálculo

**estado_trabajo:**
```
SI es borrador → 'borrador'
SI fue generada Y la orden NO tiene piezas → 'ejecutada' (venta directa sin fabricación)
SI fue generada Y NINGUNA pieza tiene progreso > 0% → 'generada'
SI al menos UNA pieza tiene progreso > 0% Y NO todas al 100% → 'en_ejecucion'
SI al menos UNA pieza al 100% PERO NO todas → 'ejecutada_parcialmente'
SI TODAS las piezas al 100% → 'ejecutada'
```

**estado_entrega:**
```
SI la orden no tiene piezas → 'entregada' al generarla
SI NINGUNA pieza entregada → null
SI al menos UNA entregada PERO NO todas → 'entregada_parcialmente'
SI TODAS entregadas → 'entregada'
```

**estado_pago:**
```
SI es borrador → null
SI saldo > 0 → 'saldo_pendiente'
SI saldo <= 0 → 'pagado'
```

### Visualización de estados en la UI

Se muestran como badges en línea, cada uno con su color:
- **estado_trabajo:** borrador=gris, generada=azul, en_ejecucion=amarillo, ejecutada_parcialmente=naranja, ejecutada=verde
- **estado_entrega:** entregada_parcialmente=cyan, entregada=verde_oscuro, null=no se muestra
- **estado_pago:** saldo_pendiente=rojo, pagado=verde, null=no se muestra

### 5.2 Tabla: `orden_items` (Líneas de factura)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido, existe en ordenes | |
| `catalogo_item_id` | FK nullable | existe en catalogo_items | Null si fue escrito manualmente |
| `codigo` | varchar(50) | nullable | |
| `descripcion` | text | requerido | |
| `cantidad` | decimal(10,2) | requerido, min:0.01 | |
| `precio_unitario` | decimal(12,2) | requerido, min:0 | |
| `porcentaje_iva` | decimal(5,2) | default:19.00 | |
| `categoria` | varchar(50) | requerido | Heredada del catálogo: 'servicio', 'material', 'producto_terminado' |
| `subtotal` | decimal(12,2) | calculado | cantidad × precio_unitario |
| `monto_iva` | decimal(12,2) | calculado | subtotal × (porcentaje_iva/100) |
| `total` | decimal(12,2) | calculado | subtotal + monto_iva |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

### 5.3 Tabla: `orden_bosquejos` (Bosquejos adjuntos a la orden)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | |
| `plantilla_bosquejo_id` | FK nullable | existe en plantillas_bosquejos | Si viene de la biblioteca matriz |
| `tipo_origen` | varchar(50) | requerido | Cómo se insertó |
| `nombre` | varchar(255) | requerido | |
| `ruta_archivo` | varchar(500) | requerido | |
| `ruta_miniatura` | varchar(500) | nullable | |
| `orden_visual` | integer | default:0 | Para ordenar los bosquejos |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Valores de `tipo_origen`:**
| Valor | Descripción |
|-------|------------|
| `archivo_local` | Archivo subido del dispositivo |
| `plantilla` | Seleccionado de bosquejo matriz |
| `grupo_plantillas` | Grupo completo de bosquejos matriz |
| `camara` | Foto tomada con cámara |
| `dibujo_tablet` | Dibujo hecho a mano en tablet |

### 5.4 Tabla: `orden_piezas` (ELEMENTO CENTRAL del flujo multi-operario)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | |
| `orden_bosquejo_id` | FK nullable | existe en orden_bosquejos | |
| `nombre` | varchar(255) | requerido | "Pieza A", "Pieza B", etc. |
| `nombre_automatico` | varchar(255) | nullable | Nombre auto si viene de bosquejo matriz |
| `cantidad` | integer | requerido, min:1 | Ej: 100 unidades |
| `material` | varchar(100) | nullable | "HR", "CR", "INOX", etc. |
| `calibre` | varchar(50) | nullable | "C3mm", "C2mm", "C1/2", etc. |
| `especificacion` | text | nullable | Texto completo: "100 - PIEZA A - C3mm - HR" |
| `porcentaje_avance` | decimal(5,2) | default:0, min:0, max:100 | % actual |
| `operario_actual_id` | FK nullable | existe en usuarios donde rol=operario | Quién tiene la pieza AHORA. NULL = en pool general |
| `estado` | varchar(50) | default:'pendiente' | 'pendiente', 'en_proceso', 'completada', 'entregada' |
| `entregada` | boolean | default:false | |
| `entregada_en` | timestamp | nullable | |
| `entregada_por` | FK nullable | existe en usuarios | |
| `orden_visual` | integer | default:0 | |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Nomenclatura automática:** El campo `especificacion` se genera concatenando: `{cantidad} - {nombre} - {calibre} - {material}`. Ejemplo: "100 - PIEZA A - C3mm - HR".

### 5.5 Tabla: `asignaciones_piezas` (Asignaciones y transferencias entre operarios)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_pieza_id` | FK | requerido | |
| `orden_id` | FK | requerido | Denormalizado para consultas rápidas |
| `asignado_desde_id` | FK nullable | existe en usuarios | Operario anterior. NULL si es asignación inicial |
| `asignado_a_id` | FK | requerido, existe en usuarios rol=operario | Operario que recibe |
| `asignado_por_id` | FK | requerido, existe en usuarios | Quién hizo la asignación |
| `tipo_asignacion` | varchar(50) | requerido | Tipo |
| `porcentaje_al_asignar` | decimal(5,2) | requerido | % en el que estaba la pieza |
| `notas` | text | nullable | |
| `activa` | boolean | default:true | Solo UNA activa por pieza a la vez |
| `creado_en` | timestamp | auto | |

**Valores de `tipo_asignacion`:**
| Valor | Cuándo se usa |
|-------|--------------|
| `inicial` | Recepción asigna el primer operario al crear la orden |
| `transferencia` | Un operario transfiere la pieza a otro operario |
| `complemento` | Un operario toma una pieza del pool de "Complementar" |
| `reasignacion` | Recepción o admin reasigna manualmente |

### 5.6 Tabla: `historial_avances` (Historial de progreso por pieza)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_pieza_id` | FK | requerido | |
| `operario_id` | FK | requerido | Operario que trabajó |
| `porcentaje_desde` | decimal(5,2) | requerido | % al RECIBIR la pieza |
| `porcentaje_hasta` | decimal(5,2) | requerido | % al SOLTAR la pieza |
| `contribucion` | decimal(5,2) | calculado | porcentaje_hasta - porcentaje_desde |
| `notas` | text | nullable | |
| `asignado_en` | timestamp | requerido | Cuándo recibió la pieza |
| `completado_en` | timestamp | nullable | Cuándo terminó su parte |
| `creado_en` | timestamp | auto | |

### 5.7 Tabla: `pagos` (Abonos)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | |
| `monto` | decimal(12,2) | requerido, min:0.01 | |
| `metodo_pago` | varchar(50) | requerido | |
| `referencia_pago` | varchar(255) | nullable | Nro de referencia |
| `registrado_por` | FK | requerido, existe en usuarios | |
| `aprobado_por` | FK nullable | existe en usuarios | Contabilidad aprueba |
| `aprobado` | boolean | default:false | |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Valores de `metodo_pago`:**
| Valor | Label visual |
|-------|-------------|
| `efectivo` | "Efectivo" |
| `nequi` | "Nequi" |
| `transferencia` | "Transferencia" |
| `tarjeta` | "Tarjeta/Datáfono" |
| `otro` | "Otro" |

**Reglas de negocio:**
- Al registrar un abono, se recalcula `ordenes.total_pagado` (solo abonos aprobados) y `ordenes.saldo`.
- Si `saldo <= 0` → `estado_pago = 'pagado'`.
- Si hay abonos aprobados pero `saldo > 0` → `estado_pago = 'saldo_pendiente'`.
- Abonos registrados por Recepción van como `aprobado = false` y necesitan aprobación de Contabilidad. Si el usuario es Contabilidad o Admin, se auto-aprueba.

### 5.8 Tabla: `orden_fotos` (Registro fotográfico)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | |
| `orden_pieza_id` | FK nullable | | Pieza específica o null si es general |
| `tipo_foto` | varchar(50) | requerido | 'avance', 'entrega', 'evidencia' |
| `ruta_archivo` | varchar(500) | requerido | |
| `ruta_miniatura` | varchar(500) | nullable | |
| `subido_por` | FK | requerido | |
| `aprobada` | boolean | default:false | |
| `aprobada_por` | FK nullable | | |
| `creado_en` | timestamp | auto | |

**Flujo de aprobación de foto:** Al adjuntar, se muestra preview: "¿Está bien la foto?" → "Aceptar" / "Repetir". Si acepta → `aprobada = true`. Si repite → descarta y vuelve a captura.

### 5.9 Tabla: `orden_comentarios`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | |
| `usuario_id` | FK | requerido | |
| `contenido` | text | requerido | |
| `creado_en` | timestamp | auto | |

Los comentarios NO se pueden editar ni eliminar. Son un log de comunicación entre roles.

### 5.10 Tabla: `registro_actividades` (Auditoría — INMUTABLE)

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `usuario_id` | FK | requerido | |
| `orden_id` | FK nullable | | |
| `accion` | varchar(100) | requerido | Clave de la acción |
| `descripcion` | text | requerido | Descripción legible |
| `datos_extra` | jsonb | nullable | Datos adicionales en JSON |
| `creado_en` | timestamp | auto | |

**PROTECCIÓN ABSOLUTA:** Esta tabla NO permite UPDATE ni DELETE. Implementar trigger de PostgreSQL:
```sql
CREATE OR REPLACE FUNCTION prevenir_modificacion_registro()
RETURNS TRIGGER AS $$
BEGIN
  RAISE EXCEPTION 'Los registros de actividades no pueden ser modificados ni eliminados';
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER proteger_registro_actividades_actualizar
BEFORE UPDATE ON registro_actividades
FOR EACH ROW EXECUTE FUNCTION prevenir_modificacion_registro();

CREATE TRIGGER proteger_registro_actividades_eliminar
BEFORE DELETE ON registro_actividades
FOR EACH ROW EXECUTE FUNCTION prevenir_modificacion_registro();
```

En el código: el modelo NO debe tener métodos de actualizar ni eliminar. No definir rutas de edición ni eliminación. No hay botones de editar/eliminar en la UI.

**Acciones a registrar:**
| acción | Cuándo |
|--------|--------|
| `orden.creada` | Se guarda o genera una orden |
| `orden.actualizada` | Se edita una orden |
| `orden.estado_cambiado` | Cambia cualquiera de los 3 estados |
| `orden.anulada` | Se anula una orden |
| `orden.clonada` | Se copia una orden |
| `pieza.avance_actualizado` | Operario actualiza porcentaje |
| `pieza.avance_disminuido` | Operario BAJA el porcentaje |
| `pieza.transferida` | Operario transfiere pieza a otro |
| `pieza.liberada_a_pool` | Operario deja pieza en pool general |
| `pieza.tomada_de_pool` | Operario toma pieza del pool |
| `pieza.reasignada` | Recepción reasigna pieza |
| `pieza.completada` | Pieza llega a 100% |
| `pieza.entregada` | Pieza entregada al cliente |
| `pago.registrado` | Se registra un abono |
| `pago.aprobado` | Contabilidad aprueba un abono |
| `foto.subida` | Se sube una foto |
| `cliente.creado` | Se crea un cliente |
| `cliente.actualizado` | Se edita un cliente |
| `usuario.inicio_sesion` | Un usuario inicia sesión |
| `garantia.registrada` | Se registra una devolución por garantía |
| `sistema.borradores_eliminados` | Limpieza automática de borradores |

### 5.11 Tabla: `devoluciones_garantia`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `orden_id` | FK | requerido | Orden original |
| `orden_pieza_id` | FK | requerido | Pieza devuelta |
| `cantidad_devuelta` | integer | requerido, min:1 | Cantidad de piezas devueltas |
| `motivo` | text | requerido | Motivo de la devolución |
| `cobrable` | boolean | default:false | Si se cobra o es garantía gratuita |
| `monto_cobro` | decimal(12,2) | nullable | Monto a cobrar si aplica |
| `estado` | varchar(50) | default:'abierta' | 'abierta', 'en_proceso', 'completada', 'reentregada' |
| `operario_asignado_id` | FK nullable | existe en usuarios | Operario que trabajará la garantía |
| `registrado_por` | FK | requerido | |
| `completada_en` | timestamp | nullable | |
| `reentregada_en` | timestamp | nullable | |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

**Flujo:** Recepción presiona "GARANTÍA" → selecciona pieza → ingresa cantidad devuelta, motivo, si se cobra → asigna operario → operario trabaja y reporta → se re-entrega.

---

## MÓDULO 6: FLUJO DE CREACIÓN DE ORDEN (ROL: RECEPCIÓN)

### 6.1 Pantalla: Crear Orden

**URL:** `/recepcion/ordenes/crear`

**Estructura por secciones:**

#### SECCIÓN 1: Cliente

- **Opción A:** Seleccionar cliente existente — Campo de búsqueda con autocompletado que busca por nombre, celular o correo. Al seleccionar, se auto-llenan los campos.
- **Opción B:** Crear nuevo cliente — Botón "Crear Nuevo Cliente" abre formulario inline o modal: Nombre (requerido), Dirección, Correo, Celular 1, Celular 2. Al guardar se selecciona automáticamente.
- Obligatorio tener cliente para continuar.

#### SECCIÓN 2: Fechas

- **Fecha de Creación** — Automática. Solo lectura. Formato: "01 DE ENERO 2026".
- **Fecha de Entrega** — Selector de fecha.
- **Hora de Entrega** — Selector de hora. Opcional.

#### SECCIÓN 3: Items (Tabla editable)

| Columna | Tipo | Comportamiento |
|---------|------|---------------|
| Código | texto/select | Al seleccionar del catálogo, auto-llena Descripción, P. Unitario y hereda categoría |
| Descripción | texto | Editable |
| Cantidad | número | Min: 0.01. Al cambiar, recalcula Valor |
| P. Unitario | moneda | Editable. Al cambiar, recalcula Valor |
| Valor | moneda (solo lectura) | Cantidad × P. Unitario |
| Acción | botón | Eliminar fila (X) |

- Botón "Agregar Item" → agrega fila vacía.
- Totales debajo:
  - **SUB TOTAL** = Suma de Valores
  - **IVA 19%** = SUB TOTAL × 0.19
  - **TOTAL** = SUB TOTAL + IVA

#### SECCIÓN 4: Bosquejos y Piezas

**6 métodos para insertar bosquejos (se pueden insertar múltiples por orden):**

1. **"Cargar Bosquejo Local"** — Selector de archivos. Acepta jpg, png, webp. Pide nombre.
2. **"Cargar Bosquejo Matriz"** — Modal con biblioteca organizada por grupo como tarjetas con miniatura. Al seleccionar, se agrega. Si tiene piezas predefinidas, se crean automáticamente.
3. **"Cargar Grupo Bosquejos"** — Modal de grupos. Al seleccionar un grupo, se agregan TODOS sus bosquejos.
4. **"Tomar Foto"** — Abre cámara del dispositivo. Preview para confirmar. Pide nombre.
5. **"Realizar Bosquejo en Tablet"** — Canvas de dibujo libre con stylus/dedo. Guarda como imagen. Pide nombre.
6. **"Descargar Bosquejo"** — Para descargar uno ya existente en la orden.

**Tabla de piezas:**
| Campo | Tipo | Comportamiento |
|-------|------|---------------|
| Nombre | texto | "Pieza A", "Pieza B", auto-incrementa letra |
| Cantidad | número | Cuántas unidades. Ej: 100 |
| Material | select | "HR", "CR", "INOX", etc. (configurables desde admin) |
| Calibre | select/texto | "#22", "#20", "1/8\"", etc. (configurables) |
| Bosquejo | select | A cuál bosquejo pertenece |

- Botón "Agregar Pieza" → agrega fila.
- `especificacion` se genera automáticamente.

#### SECCIÓN 5: Firma del Cliente

- Canvas de firma digital con botón "Limpiar". Se guarda como PNG.

#### SECCIÓN 6: Asignación de Operario

- Select que lista todos los operarios activos.
- Este será el **primer operario asignado** que recibe TODAS las piezas inicialmente.
- Obligatorio para GENERAR. NO obligatorio para GUARDAR.
- **Si la orden NO tiene piezas/bosquejos, esta sección NO aparece** (no aplica — venta directa).

#### SECCIÓN 7: Abonos y Método de Pago

- Campo monto del abono + select método de pago.
- Botón "Agregar otro abono" → agrega fila.
- SALDO = TOTAL - Suma de abonos. Solo lectura.
- Los abonos son opcionales.

#### BOTONES PRINCIPALES:

1. **"GUARDAR ORDEN"** — Borrador. NO asigna número. NO crea asignaciones. Mensaje: "La orden ha sido guardada exitosamente." Validación mínima: al menos un dato ingresado.

2. **"GENERAR ORDEN"** — Validaciones:
   - Debe tener cliente.
   - Debe tener al menos un item.
   - Si tiene piezas → debe tener operario asignado.
   - Si falta algo → "Falta diligenciar información para poder GENERAR ORDEN".
   - Confirmación: "¿Está seguro de generar orden?" con botón "Aceptar" que se habilita después de 1 segundo.
   - Al confirmar:
     - Se asigna número consecutivo.
     - Si tiene piezas → `estado_trabajo = 'generada'`, crea asignaciones y historial.
     - Si NO tiene piezas → `estado_trabajo = 'ejecutada'`, `estado_entrega = 'entregada'` (venta directa).
     - Crea pagos si hay abonos.
     - Registra actividad.
     - Mensaje: "La orden ha sido generada con número #XXXX".

### 6.2 Auto-guardado por Inactividad (Recepción)

Si el recepcionista deja de interactuar por el tiempo configurado (default: 5 minutos):
- Se guarda automáticamente como borrador con los datos que tenga.
- Mensaje: "La orden se guardó automáticamente como borrador."
- Para auto-guardado NO se requieren todos los campos obligatorios.

---

## MÓDULO 7: BUSCAR Y GESTIONAR ÓRDENES (ROL: RECEPCIÓN)

### 7.1 Pantalla: Buscar Orden

**URL:** `/recepcion/ordenes`

**Filtros:**
- **Buscar** — Por número de orden, nombre del cliente, celular o correo.
- **Fecha desde** y **Fecha hasta** — Rango de fechas.
- **Estado de trabajo** — Multiselect.
- **Estado de entrega** — Select.
- **Estado de pago** — Select.

**Tabla de resultados:**
| Columna | Contenido |
|---------|-----------|
| Orden # | Clickeable → abre detalle |
| Cliente | Nombre |
| Fecha Creación | |
| Fecha Entrega | |
| Estados | Los 3 badges según combinación |
| Total | Monto total |
| Saldo | Balance pendiente |
| Acciones | Ver, Editar, Imprimir, Entrega Rápida |

**Resumen al pie (sumatoria por categoría de las órdenes filtradas):**
```
RESUMEN DE ÓRDENES FILTRADAS (23 órdenes):
├── Total SERVICIOS:          $4.520.000
├── Total MATERIAL:           $12.350.000
├── Total PRODUCTO TERMINADO: $2.180.000
└── TOTAL GENERAL:            $19.050.000
```

**Acciones globales:**
- "EXPORTAR PDF (separados)" — ZIP con un PDF por orden.
- "EXPORTAR PDF (unido)" — Un PDF con todas las órdenes filtradas con salto de página.
- "GENERAR HOJA CALCULO" — Excel del listado.

### 7.2 Pantalla: Ver Detalle de Orden

**URL:** `/recepcion/ordenes/{id}`

**Secciones:**
1. Encabezado: número, los 3 estados como badges, fecha, persona que generó.
2. Datos del cliente.
3. Fechas de entrega.
4. Items con totales.
5. Bosquejos como tarjetas expandibles. Título: "BOSQUEJOS ORDEN #XXXX".
6. **Piezas con trazabilidad completa:** Cada pieza muestra nombre, especificación, barra de progreso visual con segmentos por operario (cada segmento en distinto color), operario actual, historial de quién hizo qué %.
7. Pagos: tabla de abonos con monto, método, fecha, registrado por, estado.
8. Firma del cliente.
9. Galería de fotos.
10. Comentarios.
11. Garantías (si las hay).

**Botones:**
- "EDITAR ORDEN" — Solo si no está entregada ni anulada. Si operario la tiene abierta, fuerza cierre (ver Módulo 9.6).
- "COPIAR ORDEN" — Crea borrador con mismos datos pero sin pagos, firma ni asignaciones.
- "ANULAR ORDEN" — Pide confirmación con motivo. Libera asignaciones.
- "IMPRIMIR ORDEN" — PDF de 3 hojas.
- "AGREGAR ABONO" — Modal de pago.
- "GARANTÍA" — Solo si hay piezas entregadas. Abre flujo de garantía.
- "CERRAR VISTA"

---

## MÓDULO 8: ENTREGAS (ROL: RECEPCIÓN Y OPERARIO)

### 8.1 Pantalla: Órdenes Pendientes de Entregar

**URL:** `/recepcion/entregas-pendientes`

Tabla con órdenes que tienen piezas completadas (100%) y no entregadas.

| Columna | Contenido |
|---------|-----------|
| Orden # | |
| Cliente | |
| Fecha Entrega | |
| Piezas Listas | Ej: "3 de 5" |
| Estados | Badges |
| Acciones | "ENTREGAR" (flujo completo) y "ENTREGA RÁPIDA" |

### 8.2 Flujo Normal de Entrega

**Paso 1:** Muestra piezas completadas no entregadas con checkbox para seleccionar.
**Paso 2:** Pide foto de entrega con aprobación visual.
**Paso 3:** Confirma. Marca piezas como entregadas. Recalcula estados.

### 8.3 Entrega Rápida

Un solo click → confirmación: "¿Entregar todas las piezas completadas de la Orden #XXXX?" → Sí/No. Sin fotos, sin selección individual. Marca todo lo completado como entregado.

---

## MÓDULO 9: FLUJO DEL OPERARIO

### 9.1 Dashboard del Operario

**URL:** `/operario/panel`

**Indicadores:**
| Widget | Cálculo |
|--------|---------|
| "Ordenes Asignadas" | Órdenes distintas donde tiene piezas asignadas activas |
| "Piezas en Proceso" | Piezas asignadas con avance > 0% y < 100% |
| "Ordenes Pendientes Para Complementar" | Piezas sin operario asignado disponibles |
| "Usuario Actual" | Nombre |
| "Fecha Actual" | Fecha formateada |

**Menú:**
- VER ORDENES ASIGNADAS
- BUSCAR ORDEN
- COMPLEMENTAR OTRAS ORDENES
- VER REGISTRO DE ACTIVIDADES
- VOLVER AL INICIO

### 9.2 Pantalla: Órdenes Asignadas

**URL:** `/operario/ordenes-asignadas`

Solo muestra órdenes donde el operario tiene asignaciones activas.

### 9.3 Pantalla: Vista de Trabajo de Orden

**URL:** `/operario/ordenes/{id}`

Por CADA pieza asignada a este operario:

```
┌──────────────────────────────────────────────────────┐
│ Pieza A - C3mm - HR (100 unidades)                    │
│                                                        │
│ Recibida de: Juan (Cortador) al 30%                   │
│                                                        │
│ Historial:                                             │
│   • Juan: 0% → 30% ✓ (14/Ene 9:00am)                │
│   • Tú: 30% → ? (trabajando ahora)                   │
│                                                        │
│ [████████░░░░░░░░░░░░░░░░░░░░░░░░] 30%               │
│                                                        │
│ ACTUALIZAR PORCENTAJE:                                 │
│ [Campo numérico o slider: puede subir O bajar]        │
│                                                        │
│ [📷 ADJUNTAR FOTO]                                     │
│                                                        │
│ AL TERMINAR TU PARTE:                                  │
│ [🔄 TRANSFERIR A OPERARIO ▼]  [📤 DEJAR EN COLA]     │
└──────────────────────────────────────────────────────┘
```

**IMPORTANTE — Porcentaje puede subir Y bajar:**
- Cualquier operario asignado puede cambiar el porcentaje libremente.
- NO requiere autorización.
- Si BAJA → se genera notificación al usuario configurado y registro de actividad especial (`pieza.avance_disminuido`).

**Botón "ACTUALIZAR ORDEN":**
1. Si no modificó ningún % → "No se modificó porcentaje de alguna pieza, ¿está seguro que no hizo algún avance?"
2. Si alguna pieza llega a 100% → "¿Está seguro de colocar terminado ésta Pieza [nombre]?"
3. Si TODAS las piezas al 100% → "¿Está seguro de colocar la Orden #XXXX como EJECUTADA?"
4. Al confirmar: actualiza avances, cierra/crea historial, recalcula estados de la orden.

**Transferencia:**
- "TRANSFERIR A OPERARIO" → dropdown de operarios activos + notas opcionales → confirmar → se cierra historial actual, se crea nueva asignación, la pieza pasa al otro operario.

**Dejar en cola:**
- "DEJAR EN COLA GENERAL" → cierra historial, `operario_actual_id = NULL`, la pieza aparece en "Complementar".

### 9.4 Buscar Orden

**URL:** `/operario/buscar`

Busca por número. Vista solo lectura.

### 9.5 Complementar Otras Órdenes

**URL:** `/operario/complementar`

Tabla de piezas sin operario asignado, no completadas, de órdenes no canceladas ni entregadas.

| Columna | Contenido |
|---------|-----------|
| Orden # | |
| Pieza | Nombre y especificación |
| Progreso | Barra visual |
| Último Operario | Quién la trabajó antes |
| Cliente | |
| Fecha Entrega | |
| Acción | "TOMAR PIEZA" |

Al tomar: se auto-asigna, la pieza aparece en sus órdenes asignadas.

### 9.6 Bloqueo y Forzar Cierre

**Cuando operario abre orden:** Se registra `ordenes.bloqueada_por` y `ordenes.bloqueada_en`.

**Cierre automático por inactividad:** Si el operario no interactúa por X minutos (configurable, default 10):
- Libera lock.
- NO guarda cambios no confirmados.
- Mensaje: "La sesión de esta orden se cerró por inactividad."

**Forzar cierre por rango mayor:**
1. Recepción intenta editar orden bloqueada por operario.
2. Se muestra al operario: "La orden necesita ser cerrada para actualizar. Un usuario de rango mayor necesita editarla."
3. Temporizador (configurable, default 60 segundos).
4. Si no cierra voluntariamente → el sistema fuerza cierre: guarda progreso actual automáticamente, libera lock.
5. Mensaje al operario: "La orden fue cerrada por el sistema. Su progreso se guardó automáticamente."

**Jerarquía:** Administrador > Recepción > Contabilidad > Operario. Mayor siempre puede forzar sobre menor.

---

## MÓDULO 10: CONTABILIDAD

### 10.1 Dashboard

**URL:** `/contabilidad/panel`

| Widget | Cálculo |
|--------|---------|
| "Ordenes con Saldo Pendiente" | Órdenes con estado_pago = 'saldo_pendiente' |
| "Abonos por Aprobar" | Pagos con aprobado = false |
| "Total Pendiente por Cobrar" | Suma de saldos de todas las órdenes |

### 10.2 Órdenes Pendientes de Pagar

**URL:** `/contabilidad/ordenes-pendientes`

Tabla con filtros. Cada fila tiene "VER", "APROBAR", "AGREGAR ABONO".

**Aprobar pagos:** Muestra pagos no aprobados. Botón "Aprobar" por cada uno. Al aprobar → recalcula totales.

**Agregar abonos inline:** Mini-formulario expandible en cada fila (monto + método + referencia) → "Registrar Abono" → se auto-aprueba porque es Contabilidad.

Resumen por categoría al pie del filtro (igual que en Recepción).

### 10.3 Ver/Editar Items

Misma funcionalidad que Módulo 3.

### 10.4 Registro de Actividades

Tabla de registro_actividades filtrada por usuario actual.

---

## MÓDULO 11: VERSIÓN IMPRIMIBLE (PDF 3 hojas)

**Hoja 1:** Logo, número de orden, fechas, datos del cliente, tabla de items con totales, tabla de abonos, saldo, persona que generó.

**Hoja 2:** Bosquejos (imágenes), lista de piezas con especificación, estado de cada pieza.

**Hoja 3:** Firma del cliente, espacio para firma de entrega, observaciones, espacio para firma de recibido.

---

## MÓDULO 12: TABLA DE PRECIOS PARAMÉTRICA

### 12.1 Tabla: `tabla_precios_servicios`

| Campo | Tipo | Validación | Notas |
|-------|------|-----------|-------|
| `id` | bigserial PK | auto | |
| `tipo_servicio` | varchar(100) | requerido | Clave. Ej: "corte_doblez_hr_cr_galv" |
| `etiqueta_servicio` | varchar(255) | requerido | Label. Ej: "CORTE DOBLEZ HR CR GALVANIZADO" |
| `clave_calibre` | varchar(20) | requerido | "#22", "#20", "1/8", "1/4", "1/2", etc. |
| `calibre_mm` | decimal(5,2) | requerido | Espesor en mm |
| `largo_rango_min` | integer | requerido | Inicio rango largo cm |
| `largo_rango_max` | integer | nullable | Fin rango. NULL para sin límite (">320") |
| `cantidad_rango_min` | integer | requerido | Inicio rango cantidad servicios |
| `cantidad_rango_max` | integer | nullable | Fin rango. NULL para ">200" |
| `precio` | decimal(12,2) | requerido | Precio unitario SIN IVA en COP |
| `precio_minimo` | decimal(12,2) | nullable | Precio mínimo de la tabla |
| `creado_en` | timestamp | auto | |
| `actualizado_en` | timestamp | auto | |

### 12.2 Tablas a precargar (6)

1. **CORTE DOBLEZ HR CR GALVANIZADO** — Mínima: $6,839
2. **DOBLEZ INOX** — Mínima: $7,816
3. **CORTE INOX** — Mínima: $6,839
4. **CORTE DOBLEZ ALUMINIO LISO Y ALFAJOR** — Mínima: $7,816
5. **CORTE DOBLEZ ALFAJOR HR** — Mínima: $7,295
6. **CORTE DOBLEZ ACERO 430** — Mínima: $6,437

13 calibres × 4 rangos largo × 6 rangos cantidad × 6 tablas = **1,872 registros**.

### 12.3 Administración

- CRUD de tipos de servicio.
- Vista tipo spreadsheet para editar precios masivamente.
- Importar/Exportar Excel.
- Cada cambio se registra en registro_actividades.

### 12.4 Consulta de precios (Recepción)

Pantalla de consulta: seleccionar tipo servicio, material, calibre, largo, cantidad → muestra precio. Opcionalmente: al agregar item de tipo 'servicio' a una orden, campos adicionales para auto-completar precio.

---

## MÓDULO 13: NOTIFICACIONES

### 13.1 Tabla: `notificaciones`

Implementar sistema de notificaciones internas con almacenamiento en base de datos. Ícono de campana en barra superior con badge de no leídas.

**Eventos que generan notificación:**
| Evento | Destinatario |
|--------|-------------|
| Operario baja porcentaje | Usuario configurado (supervisor) |
| Recepción quiere editar orden bloqueada | Operario que la tiene |
| Garantía registrada | Operario asignado |
| Borrador próximo a expirar | Recepcionista que lo creó |
| Abono registrado pendiente de aprobación | Contabilidad |

---

## MÓDULO 14: MANEJO DE PÉRDIDA DE CONEXIÓN

1. Indicador de conexión visible (verde=online, rojo=offline).
2. Sin conexión → banner: "⚠️ Sin conexión. Los cambios se guardarán al reconectar." Datos se respaldan en almacenamiento local del navegador.
3. Botones de guardar/generar se deshabilitan sin conexión.
4. Al reconectar → envío automático de datos pendientes. Si hay conflicto → dejar elegir al usuario.
5. Para porcentajes del operario: se guardan localmente y sincronizan al reconectar.

---

## MÓDULO 15: LIMPIEZA AUTOMÁTICA DE BORRADORES

Tarea programada diaria (ejecutar automáticamente cada noche):
- Elimina órdenes con `estado_trabajo = 'borrador'` y `actualizado_en < ahora - dias_expiracion_borradores`.
- Elimina en cascada sus items, piezas, bosquejos.
- Registra en registro_actividades.

En la UI de borradores: por defecto muestra solo los recientes (últimos X días). Toggle "Ver todos". Badge "Expira en X días" para los próximos a expirar.

---

## MÓDULO 16: ADMINISTRACIÓN

### 16.1 Gestión de Usuarios

**URL:** `/admin/usuarios`

Tabla: Nombre, Email, Rol, Terminal, Activo, Acciones.

Formulario: Nombre, Email, Contraseña, Rol (select: administrador, recepcion, operario, contabilidad), Terminal, Activo (toggle).

### 16.2 Tabla: `configuracion_sistema` (clave-valor)

| Clave | Tipo | Default | Descripción |
|-------|------|---------|-------------|
| `nombre_empresa` | texto | "SINDEN S.A.S." | |
| `logo_empresa` | ruta | null | Logo para PDFs |
| `direccion_empresa` | texto | "" | |
| `telefono_empresa` | texto | "" | |
| `nit_empresa` | texto | "" | |
| `numeros_nequi` | json | ["3132292789","3177138139"] | |
| `porcentaje_iva_defecto` | decimal | 19.00 | |
| `timeout_inactividad_operario` | entero (min) | 10 | Cierre automático operario |
| `timeout_autoguardado_recepcion` | entero (min) | 5 | Auto-guardado recepción |
| `timeout_forzar_cierre` | entero (seg) | 60 | Segundos para forzar cierre |
| `dias_expiracion_borradores` | entero | 30 | Días para eliminar borradores |
| `dias_borradores_recientes` | entero | 7 | Mostrar solo borradores recientes |
| `usuario_notificar_baja_porcentaje` | FK nullable | null | A quién notificar |
| `materiales_disponibles` | json | ["HR","CR","INOX","Galvanizado","Aluminio Liso","Alfajor","Alfajor HR","Acero 430"] | |
| `calibres_disponibles` | json | (ver tabla abajo) | |

**Calibres disponibles:**

| Calibre | Espesor (mm) |
|---------|-------------|
| #22 | 0.76 |
| #20 | 0.91 |
| #18 | 1.21 |
| #16 | 1.52 |
| #14 | 1.90 |
| #12 | 2.66 |
| 1/8" | 3.18 |
| - | 4.00 |
| 3/16" | 4.76 |
| 1/4" | 6.35 |
| 5/16" | 7.94 |
| 3/8" | 9.53 |
| 1/2" | 12.70 |

---

## MÓDULO 17: DASHBOARD DE RECEPCIÓN

**URL:** `/recepcion/panel`

| Widget | Cálculo | Color | Click lleva a |
|--------|---------|-------|--------------|
| "Entregas Pendientes HOY" | Órdenes con fecha_entrega = hoy y no entregadas ni anuladas | Amarillo | `/recepcion/entregas-pendientes?fecha=hoy` |
| "Entregas Pendientes HOY/MAÑANA" | Igual pero hoy + mañana | Naranja | `/recepcion/entregas-pendientes?fecha=hoy-manana` |
| "Entregas Vencidas" | fecha_entrega < hoy y no entregadas ni anuladas | Rojo | `/recepcion/entregas-pendientes?vencidas=1` |
| "Ordenes Abiertas" | No entregadas, no anuladas, no borrador | Azul | `/recepcion/ordenes?estado=abiertas` |
| "Ordenes con Saldo Pendiente" | estado_pago = 'saldo_pendiente' | Rojo | `/recepcion/ordenes?pago=pendiente` |
| "Ordenes Para Complementar" | Piezas sin operario asignado | Info | `/recepcion/ordenes?complementar=1` |

---

## MÓDULO 18: REGISTRO DE ACTIVIDADES (TRANSVERSAL)

### 18.1 Vista Particular

**URL:** `/[rol]/actividades`

Tabla filtrada por usuario actual: Fecha/Hora, Acción, Orden (clickeable), Detalle. Filtros por fecha y tipo.

### 18.2 Vista Global

**Acceso:** Solo Recepción y Administrador.

**URL:** `/recepcion/actividades-globales`

Misma tabla sin filtro de usuario. Columnas adicionales: Usuario, Rol.

---

## RELACIONES ENTRE MODELOS

```
clientes
  └── tiene_muchas ordenes

ordenes
  ├── pertenece_a clientes
  ├── pertenece_a usuarios (creado_por)
  ├── tiene_muchos orden_items
  ├── tiene_muchos orden_bosquejos
  ├── tiene_muchos orden_piezas
  ├── tiene_muchos pagos
  ├── tiene_muchos orden_fotos
  ├── tiene_muchos orden_comentarios
  ├── tiene_muchos asignaciones_piezas
  ├── tiene_muchos registro_actividades
  └── tiene_muchos devoluciones_garantia

orden_piezas
  ├── pertenece_a ordenes
  ├── pertenece_a orden_bosquejos (nullable)
  ├── pertenece_a usuarios (operario_actual, nullable)
  ├── tiene_muchos asignaciones_piezas
  ├── tiene_muchos historial_avances
  ├── tiene_muchos orden_fotos
  └── tiene_muchos devoluciones_garantia

asignaciones_piezas
  ├── pertenece_a orden_piezas
  ├── pertenece_a ordenes
  ├── pertenece_a usuarios (asignado_desde, nullable)
  ├── pertenece_a usuarios (asignado_a)
  └── pertenece_a usuarios (asignado_por)

historial_avances
  ├── pertenece_a orden_piezas
  └── pertenece_a usuarios (operario)

pagos
  ├── pertenece_a ordenes
  ├── pertenece_a usuarios (registrado_por)
  └── pertenece_a usuarios (aprobado_por, nullable)

orden_bosquejos
  ├── pertenece_a ordenes
  ├── pertenece_a plantillas_bosquejos (nullable)
  └── tiene_muchos orden_piezas

plantillas_bosquejos
  └── pertenece_a grupos_bosquejos (nullable)

grupos_bosquejos
  └── tiene_muchos plantillas_bosquejos

orden_fotos
  ├── pertenece_a ordenes
  ├── pertenece_a orden_piezas (nullable)
  └── pertenece_a usuarios (subido_por)

orden_comentarios
  ├── pertenece_a ordenes
  └── pertenece_a usuarios

registro_actividades
  ├── pertenece_a usuarios
  └── pertenece_a ordenes (nullable)

devoluciones_garantia
  ├── pertenece_a ordenes
  ├── pertenece_a orden_piezas
  ├── pertenece_a usuarios (operario_asignado, nullable)
  └── pertenece_a usuarios (registrado_por)

tabla_precios_servicios
  └── (tabla independiente, sin FK a otras tablas)

configuracion_sistema
  └── (tabla independiente, clave-valor)
```

---

## RUTAS POR ROL

### Recepción
```
/recepcion/panel
/recepcion/ordenes/crear
/recepcion/ordenes                    (buscar/listar)
/recepcion/ordenes/{id}               (ver detalle)
/recepcion/ordenes/{id}/editar
/recepcion/entregas-pendientes
/recepcion/clientes
/recepcion/items
/recepcion/bosquejos-matriz
/recepcion/consulta-precios
/recepcion/actividades
/recepcion/actividades-globales
```

### Operario
```
/operario/panel
/operario/ordenes-asignadas
/operario/ordenes/{id}                (vista de trabajo)
/operario/buscar
/operario/complementar
/operario/actividades
```

### Contabilidad
```
/contabilidad/panel
/contabilidad/ordenes-pendientes
/contabilidad/items
/contabilidad/actividades
```

### Administrador
```
/admin/configuracion
/admin/usuarios
/admin/tabla-precios
```

---

## MENSAJES EXACTOS DEL SISTEMA

| Trigger | Mensaje | Tipo |
|---------|---------|------|
| Generar sin info completa | "Falta diligenciar información para poder GENERAR ORDEN" | Error |
| Confirmar generación | "¿Está seguro de generar orden?" | Confirmación |
| Orden generada | "La orden ha sido generada con número #XXXX" | Éxito |
| Orden guardada | "La orden ha sido guardada exitosamente." | Éxito |
| Auto-guardado | "La orden se guardó automáticamente como borrador." | Info |
| Pieza al 100% | "¿Está seguro de colocar terminado ésta Pieza [nombre]?" | Confirmación |
| Toda la orden al 100% | "¿Está seguro de colocar la Orden #XXXX como EJECUTADA?" | Confirmación |
| Sin cambio de % | "No se modificó porcentaje de alguna pieza, ¿está seguro que no hizo algún avance?" | Advertencia |
| Forzar cierre al operario | "La orden necesita ser cerrada para actualizar. Un usuario de rango mayor necesita editarla." | Notificación |
| Cierre forzado | "La orden fue cerrada por el sistema. Su progreso se guardó automáticamente." | Info |
| Cierre por inactividad | "La sesión de esta orden se cerró por inactividad." | Info |
| Foto adjuntada | "¿Está bien la foto?" → "Aceptar" / "Repetir" | Confirmación |
| Sin conexión | "⚠️ Sin conexión a internet. Los cambios se guardarán cuando se restablezca la conexión." | Advertencia |
| Reconexión | "Se encontraron datos no guardados. ¿Desea recuperarlos?" | Confirmación |
| Borrador próximo a expirar | "Este borrador expira en X días." | Info |

---

## NOTAS PARA CLAUDE CODE

1. **CRUDs completos** para: Clientes, Items, Usuarios, Bosquejos Matriz, Tabla de Precios.
2. **Páginas personalizadas** para dashboards, vista de trabajo del operario, wizard de creación de orden, flujo de entrega.
3. **Interacciones en tiempo real** para: porcentajes, transferencias, bloqueo/desbloqueo de órdenes.
4. **Cálculos de totales** reactivos — cuando cambia un campo, los demás se recalculan al instante.
5. **Cambios de estado automáticos** — NUNCA el usuario cambia estado manualmente. Usar eventos del modelo o lógica en servicios.
6. **Gestión de imágenes** para bosquejos, fotos y firmas (subida, almacenamiento, miniaturas).
7. **Firma digital** — Componente canvas donde el cliente firma con dedo o stylus.
8. **Dibujo en tablet** — Canvas de dibujo libre para hacer bosquejos a mano.
9. **Generación de PDFs** para la versión imprimible de 3 hojas y exportación en bloque.
10. **Exportación a Excel** para listados y reportes.
11. **Zona horaria:** America/Bogota.
12. **Moneda:** Pesos colombianos (COP). Formato: $1.500.000 (punto como separador de miles).
13. **Borrado lógico** en: clientes, catalogo_items, usuarios. Las órdenes NO se eliminan, solo se anulan. Los borradores sí se eliminan por la tarea programada.
14. Todos los nombres de tablas, campos, enums, valores van en **ESPAÑOL**.
