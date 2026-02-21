# MANUAL DEL SISTEMA - MIRACLE B2B

## Descripcion General

Miracle es un sistema de gestion empresarial B2B (Business to Business) que integra catalogo de productos, cotizaciones, inventario, facturacion, pagos y envios en una sola plataforma. Esta construido con Laravel 9 y se accede desde el navegador web.

---

## ROLES DEL SISTEMA

El sistema cuenta con los siguientes roles, cada uno con permisos especificos:

| Rol | Descripcion |
|-----|-------------|
| **Admin** | Acceso total a todas las funcionalidades del sistema |
| **Auxiliar Administrativo** | Similar al admin, gestiona usuarios, clientes, productos y stock |
| **Vendedor** | Crea cotizaciones, gestiona sus clientes, accede al catalogo |
| **Facturacion** | Gestiona cotizaciones, pagos, facturas y listas de precios |
| **Inventarios** | Control de stock, traslados, ubicaciones, novedades |
| **Auxiliar Inventario** | Operaciones de stock (entrada, salida, ajuste) y lectura de cotizaciones |
| **Centro de Experiencia** | Recibe y gestiona traslados entre ubicaciones |
| **Cliente** | Accede al portal del cliente para ver sus pedidos y seguimiento |
| **Punto de Venta** | Accede al modulo de punto de venta (ventas directas) |
| **Tecnico** | Accede al modulo de servicio tecnico |

---

## MODULOS Y FUNCIONALIDADES

---

### 1. DASHBOARD (Inicio)

**Acceso:** Todos los usuarios autenticados

Al iniciar sesion, el usuario ve un panel resumen con informacion relevante segun su rol. Los administradores tienen acceso a un **Dashboard de Metricas** adicional con:

- Reportes de ventas
- Exportacion de ventas a Excel
- Exportacion de metricas a PDF

---

### 2. GESTION DE USUARIOS

**Acceso:** Admin, Auxiliar Administrativo, Inventarios, Facturacion

Permite crear, editar y listar usuarios del sistema.

**Funcionalidades:**
- Listado de usuarios con busqueda (DataTable)
- Crear nuevo usuario con nombre, email, contrasena y rol
- Editar usuario existente
- Asignar roles a cada usuario

---

### 3. GESTION DE CLIENTES

**Acceso:** Admin, Auxiliar Administrativo, Vendedor (solo ver), Facturacion, Inventarios

Administra toda la informacion de los clientes.

**Funcionalidades:**
- **Listado de clientes** con busqueda y filtros
- **Crear/Editar cliente** con dos tipos:
  - **Persona Natural:** nombre, identificacion, email, telefono, direccion
  - **Persona Juridica:** razon social, NIT, representante legal
- **Asignar vendedor** a cada cliente
- **Asignar lista de precios** (cada cliente tiene su nivel de precios)
- **Configurar flete:** valor del flete y si aplica o no
- **Sucursales:** agregar multiples direcciones de envio al cliente
- **Documentos:** subir y gestionar documentos del cliente (contratos, RUT, etc.)
- **Ubicacion geografica:** Pais > Departamento > Ciudad

**Nota:** Los vendedores solo pueden ver los clientes que tienen asignados.

---

### 4. CATEGORIAS

**Acceso:** Admin, Auxiliar Administrativo, Inventarios

Organiza los productos en categorias.

**Funcionalidades:**
- Listado de categorias activas
- Crear/editar categoria (nombre, descripcion)
- Eliminar categoria
- Activar/desactivar

---

### 5. PRODUCTOS

**Acceso:** Admin, Auxiliar Administrativo, Inventarios, Facturacion

Modulo central del sistema para gestionar el catalogo de productos.

**Funcionalidades:**

#### 5.1 Crear/Editar Producto
- **Datos basicos:** referencia (codigo unico), nombre, descripcion, marca, categoria
- **Unidades:** unidad de venta, unidad de empaque, extension
- **Imagenes:** subir multiples imagenes, marcar una como principal
- **Variantes:** si el producto tiene variantes (tallas, colores), se crean con SKU automatico
- **Precios:** un precio por cada lista de precios activa
- **Stock:** configurar si controla stock, stock inicial, minimo y maximo

#### 5.2 Listado de Productos
Tabla con busqueda y filtros que muestra:
- Imagen miniatura
- Referencia, nombre, marca, categoria
- Ubicacion, unidades, variantes, estado activo

**Botones de accion por producto:**
- Ver variantes (modal)
- Ver imagenes (modal)
- Ver precios (modal)
- Ver stock (modal con enlace a gestion de stock)
- Editar producto
- Eliminar producto

#### 5.3 Exportar a Excel
- **Boton "Excel":** descarga listado de productos con todas las listas de precios (sin imagenes)
- **Boton "Excel + Imagenes":** descarga listado con imagenes incrustadas en cada fila

#### 5.4 Importar Productos
- Descargar plantilla CSV o Excel
- Subir archivo con productos nuevos
- Ver historial de importaciones y detalle de errores

#### 5.5 Actualizar Precios Masivamente
- Descargar plantilla CSV/Excel (delimitador punto y coma `;`)
- Subir archivo con columna `referencia` y columnas de precios
- El sistema actualiza los precios y guarda un historial con errores si los hubo
- Ver historial de actualizaciones de precios

---

### 6. LISTAS DE PRECIOS

**Acceso:** Admin, Auxiliar Administrativo, Facturacion

Define los diferentes niveles de precios del sistema.

**Funcionalidades:**
- Listado de listas de precios
- Crear/editar lista (nombre, codigo, descripcion, orden)
- Activar/desactivar lista
- Cada cliente se asigna a una lista de precios
- Los precios de productos se definen por cada lista activa

---

### 7. CATALOGO

**Acceso:** Admin, Auxiliar Administrativo, Vendedor

El catalogo es la interfaz donde se navegan los productos y se crean cotizaciones. Tiene dos formas de acceso:

#### 7.1 Catalogo Publico (Links Temporales)
- Se genera un enlace unico con token para un cliente especifico
- El cliente accede sin necesidad de cuenta en el sistema
- El enlace tiene fecha de vencimiento configurable
- Se puede configurar si muestra precios y/o stock
- El cliente navega el catalogo y envia una solicitud de cotizacion

#### 7.2 Catalogo Autenticado (Vendedor)
- El vendedor inicia sesion y selecciona un cliente de su cartera
- Navega el catalogo con los precios de la lista asignada al cliente
- Crea la cotizacion en nombre del cliente

**En ambos flujos:**
- Se puede buscar y filtrar productos por categoria
- Se ven imagenes, precios y disponibilidad de stock
- Se agregan productos al carrito de cotizacion
- Al enviar, se crea una solicitud de cotizacion y se **reserva** el stock

---

### 8. ENLACES DE ACCESO (Links)

**Acceso:** Admin, Auxiliar Administrativo, Vendedor

Permite crear enlaces temporales para que los clientes accedan al catalogo sin registrarse.

**Funcionalidades:**
- Listado de enlaces creados
- Crear enlace: seleccionar cliente, definir dias de validez, opciones de precios/stock
- Ver detalle del enlace (estadisticas de uso, visitas, ultimo acceso)
- Activar/desactivar enlace
- El enlace se comparte con el cliente (por WhatsApp, email, etc.)

---

### 9. COTIZACIONES (Solicitudes de Cotizacion)

**Acceso:**
- **Lectura:** Admin, Auxiliar Administrativo, Vendedor, Facturacion, Inventarios, Auxiliar Inventario
- **Acciones:** Admin, Auxiliar Administrativo, Vendedor, Facturacion, Inventarios

Modulo principal para la gestion comercial. Controla todo el flujo desde la solicitud hasta el envio.

#### 9.1 Listado de Cotizaciones
Tabla con busqueda, filtros y botones:
- **Filtro por vendedor** (solo admin)
- **Botones de filtro rapido:** Pendientes, Aplicadas, Rechazadas
- **Exportar Todo:** descarga Excel con resumen, detalle e items
- **Columnas:** numero, cliente, vendedor, fecha, items, monto, estado, pago, descargue, envio, reserva

**Alerta visual:** las cotizaciones pendientes con mas de 3 dias se resaltan en rojo.

**Permisos por rol del vendedor:**
- Ve unicamente las cotizaciones que el mismo creo
- En cotizaciones propias: puede ver detalle, editar, clonar, descargar PDF, registrar pago
- En cotizaciones ajenas (si tiene acceso): solo puede ver detalle y descargar PDF

#### 9.2 Estados de una Cotizacion

```
PENDIENTE  ──→  APLICADA (aprobada)
    │
    └──────→  RECHAZADA
```

- **Pendiente:** recien creada, esperando revision
- **Aplicada:** aprobada por admin/vendedor, se procede con el pedido
- **Rechazada:** no se procesa, se requiere motivo de rechazo

#### 9.3 Detalle de Cotizacion (Modal)
Al hacer clic en el ojito se muestra:
- Informacion del cliente (nombre, email, telefono, lista de precios)
- Informacion de la cotizacion (numero, fecha, estado, quien creo, quien aprobo/rechazo)
- Tabla de items (producto, variante, cantidad, precio unitario, subtotal)
- Valores totales (subtotal, IVA, flete, total)
- Historial de pagos
- Observaciones del vendedor y admin
- Botones de accion segun el estado

#### 9.4 Acciones sobre Cotizaciones

| Accion | Descripcion | Roles |
|--------|-------------|-------|
| **Ver Detalle** | Ver toda la informacion de la cotizacion | Todos |
| **Editar** | Modificar items, cantidades, precios, observaciones | Admin, Aux Admin, Facturacion, Vendedor (propias), Inventarios |
| **Clonar** | Crear una copia con precios y stock actualizados | Admin, Aux Admin, Vendedor (propias), Facturacion, Inventarios |
| **Descargar PDF** | Generar y descargar cotizacion en PDF | Todos |
| **Aplicar** | Aprobar la cotizacion (cambia estado a aplicada) | Admin, Aux Admin, Facturacion, Inventarios |
| **Rechazar** | Rechazar con motivo obligatorio | Admin, Aux Admin, Facturacion, Inventarios |
| **Descontar Stock** | Descontar fisicamente el stock del inventario | Admin, Aux Admin, Inventarios |
| **Registrar Pago** | Registrar un pago con monto y comprobante | Todos los roles con acceso |
| **Eliminar** | Eliminar la cotizacion (libera reservas de stock) | Admin, Aux Admin, Facturacion, Inventarios |
| **Renovar Reserva** | Extender la reserva de stock por 24 horas mas | Admin, Aux Admin, Inventarios |
| **Liberar Reserva** | Liberar manualmente el stock reservado | Admin, Aux Admin, Inventarios |

#### 9.5 Reserva de Stock
Cuando se crea una cotizacion:
1. El sistema **reserva** la cantidad solicitada de cada producto
2. La reserva dura **24 horas** (configurable)
3. El stock reservado no esta disponible para otras cotizaciones
4. Se puede renovar o liberar manualmente
5. Al aplicar la cotizacion, la reserva se marca como "aplicada"
6. Si la reserva expira, el stock queda disponible nuevamente

#### 9.6 Edicion de Cotizacion
Permite modificar una cotizacion existente:
- Agregar/quitar productos
- Cambiar cantidades y precios
- Editar precios manualmente (se guarda el precio original como referencia)
- Agregar observaciones del vendedor y admin
- Configurar forma de pago, IVA, flete

---

### 10. PAGOS

**Acceso:** Admin, Auxiliar Administrativo, Facturacion, Vendedor, Inventarios

Gestiona los pagos asociados a cada cotizacion.

#### 10.1 Registrar Pago
- Seleccionar forma de pago: Contado o Credito
- Seleccionar metodo de pago: Transferencia, Efectivo, Tarjeta, Cheque, Credito
- Ingresar monto
- Subir comprobante(s) de pago (imagenes o PDF)
- Agregar notas

**Reglas especiales:**
- Si la forma de pago es **Credito**: el metodo de pago se bloquea en "Credito" automaticamente y el pago se **aprueba automaticamente**
- Si la forma de pago es **Contado**: la opcion "Credito" no aparece en el metodo de pago

#### 10.2 Estados de Pago

| Estado | Descripcion |
|--------|-------------|
| **Pendiente** | Pago registrado, esperando aprobacion |
| **Aprobado** | Pago verificado y aprobado |
| **Rechazado** | Pago rechazado (no cuenta hacia el total) |

#### 10.3 Estado de Pago de la Cotizacion

| Estado | Descripcion |
|--------|-------------|
| **Pendiente** | No se ha registrado ningun pago |
| **Parcialmente Pagado** | Se ha pagado una parte del monto total |
| **Pagado** | Se ha cubierto el monto total |

#### 10.4 Aprobar/Rechazar Pagos
- Los pagos pendientes aparecen en el detalle de la cotizacion
- Admin y Facturacion pueden aprobar o rechazar
- Al aprobar, el monto se suma al total pagado
- Al rechazar, el pago no cuenta

---

### 11. FACTURACION

**Acceso:** Admin, Auxiliar Administrativo, Facturacion

Genera facturas a partir de cotizaciones aplicadas.

**Funcionalidades:**
- Generar factura desde una cotizacion aplicada
- Configurar forma de pago y fecha de vencimiento
- Descargar factura en PDF

---

### 12. GESTION DE ENVIOS

**Acceso:** Admin, Auxiliar Administrativo, Facturacion, Inventarios, Auxiliar Inventario

Gestiona el proceso de envio de las cotizaciones aprobadas.

#### 12.1 Estados de Envio

```
PENDIENTE → PREPARANDO → DESPACHADO → EN TRANSITO → ENTREGADO
```

#### 12.2 Funcionalidades
- Actualizar estado de envio desde la lista de cotizaciones
- Ingresar transportadora (Servientrega, Coordinadora, etc.)
- Ingresar numero de guia
- Subir archivo PDF de la guia de envio
- El cliente puede ver el seguimiento desde su portal

---

### 13. GESTION DE STOCK (Inventario)

**Acceso:** Admin, Auxiliar Administrativo, Inventarios, Auxiliar Inventario

Modulo completo para el control de inventario.

#### 13.1 Listado de Stock
Tabla con todos los productos y su stock actual:
- Producto, variante, referencia
- Cantidad disponible, cantidad reservada, stock real
- Stock minimo/maximo configurado
- Ubicacion (bodega/tienda)
- Estado (normal, bajo, agotado)

**Filtros disponibles:**
- Por estado (con stock, sin stock, stock bajo)
- Por ubicacion
- Por producto especifico

#### 13.2 Operaciones de Stock

| Operacion | Descripcion | Ejemplo |
|-----------|-------------|---------|
| **Entrada** | Agregar unidades al inventario | Recepcion de mercancia del proveedor |
| **Salida** | Restar unidades del inventario | Entrega a cliente, perdida |
| **Ajuste** | Corregir la cantidad (puede ser + o -) | Inventario fisico, correccion de error |

Cada operacion registra:
- Producto y variante
- Cantidad
- Stock anterior y stock nuevo
- Motivo/descripcion
- Documento de referencia
- Usuario que realizo la operacion
- Fecha y hora

#### 13.3 Historial de Movimientos
- Tabla con todos los movimientos de un producto/variante
- Muestra: fecha, tipo (entrada/salida/ajuste), cantidad, stock anterior/nuevo, origen, referencia, motivo, usuario
- Boton para descargar nota en PDF de cada movimiento

#### 13.4 Reservas de Stock
- Boton "Ver Reservas" (aparece solo si hay reservas)
- Muestra: cliente, cotizacion, producto, cantidad reservada, estado, expiracion

#### 13.5 Dashboard de Stock
Panel visual con:
- Total productos con stock, sin stock, stock bajo
- Grafico de movimientos del mes
- Productos con mayor rotacion
- Productos criticos (por debajo del minimo)

#### 13.6 Configuracion de Stock
Para cada producto/variante:
- Stock minimo (alerta cuando baja de este nivel)
- Stock maximo
- Ubicacion asignada
- Activar/desactivar alerta de stock bajo

#### 13.7 Notas PDF
Se genera automaticamente un PDF por cada movimiento:
- **NE-XXXXX:** Nota de Entrada
- **NS-XXXXX:** Nota de Salida
- **NA-XXXXX:** Nota de Ajuste

---

### 14. UBICACIONES (Bodegas/Tiendas)

**Acceso:** Admin, Auxiliar Administrativo, Inventarios

Administra las ubicaciones fisicas donde se almacena el stock.

**Funcionalidades:**
- Listado de ubicaciones
- Crear/editar ubicacion (nombre, tipo: bodega/tienda/otro)
- Marcar una como ubicacion principal
- Activar/desactivar ubicaciones
- Cada registro de stock puede asignarse a una ubicacion

---

### 15. TRASLADOS DE STOCK

**Acceso:**
- **Crear/Enviar:** Admin, Auxiliar Administrativo, Inventarios
- **Ver/Recibir/Cancelar:** Admin, Auxiliar Administrativo, Inventarios, Centro de Experiencia

Permite mover stock entre ubicaciones (ej: de bodega principal a tienda).

#### 15.1 Flujo de un Traslado

```
PENDIENTE → EN TRANSITO → COMPLETADO
    │
    └──→ CANCELADO (devuelve stock al origen)
```

1. **Crear traslado:** seleccionar origen, destino, productos y cantidades
2. **Enviar:** el stock se descuenta de la ubicacion de origen
3. **Recibir:** el stock se suma en la ubicacion de destino
4. **Cancelar:** si esta en transito, el stock se devuelve al origen

#### 15.2 Tipos de Operacion
- **General:** traslado normal entre ubicaciones
- **Credito:** traslado de productos en credito/consignacion

#### 15.3 Funcionalidades
- Listado de traslados con filtro por estado
- Crear/editar traslado con seleccion de productos
- Ver stock disponible en tiempo real al seleccionar productos
- Validacion inteligente (solo valida items que cambiaron al editar)
- Generar PDF del traslado
- Ver detalle completo

---

### 16. NOVEDADES DE STOCK

**Acceso:** Admin, Auxiliar Administrativo, Inventarios

Registra novedades o incidencias relacionadas con el inventario.

**Funcionalidades:**
- Dashboard de novedades
- Crear novedad (producto afectado, descripcion, tipo)
- Ver detalle de novedad
- Cerrar novedad resuelta
- Consultar stock disponible desde el formulario

---

### 17. PORTAL DEL CLIENTE

**Acceso:** Usuarios con rol Cliente

Interfaz dedicada para que los clientes consulten sus pedidos.

**Funcionalidades:**
- **Dashboard:** resumen de pedidos recientes y estados
- **Historial de Pedidos:** lista de todas las cotizaciones/pedidos
- **Detalle de Pedido:** ver items, precios, estado de pago
- **Seguimiento de Envio:** ver estado del envio, transportadora, numero de guia
- **Descargar Guia:** descargar PDF de la guia de envio
- **Descargar Factura:** descargar factura en PDF

---

### 18. PUNTO DE VENTA (POS)

**Acceso:** Admin, Auxiliar Administrativo, Inventarios, Punto de Venta

Modulo para ventas directas en tienda/mostrador.

**Funcionalidades:**
- Dashboard con ubicacion seleccionada
- Cambiar ubicacion de venta
- Nueva venta: buscar productos, agregar al carrito, procesar
- Verificacion de stock en tiempo real
- Procesar venta (descuenta stock automaticamente)
- Historial de ventas
- Ver detalle de venta
- Anular venta
- Generar ticket de venta (PDF)
- Reportes de ventas
- Exportar datos

---

### 19. SERVICIO TECNICO

**Acceso:** Admin, Auxiliar Administrativo, Tecnico

Modulo para gestion de ordenes de servicio tecnico.

**Funcionalidades:**
- **Dashboard** con estadisticas
- **Clientes de Servicio Tecnico:** gestionar clientes especificos del area
- **Tecnicos:** administrar el equipo tecnico
- **Equipos:** registrar equipos de clientes (por cliente)
- **Ordenes de Servicio:**
  - Crear/editar ordenes
  - Cambiar estado de la orden
  - Generar PDF de la orden
  - Agregar diagnosticos
  - Agregar/quitar repuestos usados
  - Subir imagenes (antes/despues)
- **Repuestos:** inventario de repuestos disponibles
- **Reportes:** por ordenes y por tecnicos

---

### 20. IMPORTACION MASIVA DE PRODUCTOS

**Acceso:** Admin, Auxiliar Administrativo, Inventarios

Permite cargar productos de forma masiva desde archivos.

**Funcionalidades:**
- Descargar plantilla CSV o Excel
- Subir archivo con datos de productos
- El sistema procesa y crea los productos automaticamente
- Ver historial de importaciones
- Ver detalle de cada importacion (exitosos, fallidos, errores)

---

### 21. EXPORTACIONES Y REPORTES

#### Excel de Productos
- **Excel sin imagenes:** listado con referencia, nombre, categoria, precios de todas las listas activas
- **Excel con imagenes:** mismo listado pero con imagen miniatura incrustada por fila

#### Excel de Cotizaciones
Exporta a Excel con tres hojas:
1. **Resumen:** informacion general de cada cotizacion
2. **Detalle:** todos los items de todas las cotizaciones
3. **Productos:** resumen de productos mas cotizados

Filtros: por estado y rango de fechas.

#### PDF de Cotizacion
Documento con:
- Logo de la empresa
- Datos del cliente
- Tabla de items con imagen, referencia, producto, variante, cantidad, precio, subtotal
- Totales (subtotal, IVA, flete, total)
- Observaciones

#### PDF de Factura
Documento formal de factura con datos fiscales.

#### PDF de Traslado
Documento con origen, destino, items trasladados y cantidades.

#### PDF de Movimiento de Stock
Nota de entrada/salida/ajuste con numero unico, producto, cantidades y motivo.

#### Reporte de Ventas (Excel)
Exporta metricas de ventas desde el dashboard de metricas.

#### Reporte de Metricas (PDF)
PDF con resumen de metricas del sistema.

---

## FLUJOS PRINCIPALES DEL NEGOCIO

### Flujo Completo: De Catalogo a Entrega

```
1. Vendedor crea ENLACE para cliente
         │
2. Cliente accede al CATALOGO via link
         │
3. Cliente selecciona productos y envia COTIZACION
         │ (stock se RESERVA automaticamente)
         │
4. Vendedor/Admin revisa COTIZACION
         │
    ┌─────┴─────┐
    │            │
APROBAR      RECHAZAR
    │         (reserva se libera)
    │
5. Se descuenta STOCK del inventario
         │
6. Se registra PAGO (contado o credito)
         │
7. Se genera FACTURA
         │
8. Se prepara ENVIO
    │
    PREPARANDO → DESPACHADO → EN TRANSITO → ENTREGADO
         │
9. Cliente ve SEGUIMIENTO en su portal
```

### Flujo de Traslado de Stock

```
1. Crear traslado (origen → destino)
         │
2. Agregar productos y cantidades
         │
3. ENVIAR traslado
    │ (stock se descuenta del origen)
    │
    ┌─────┴─────┐
    │            │
RECIBIR      CANCELAR
(stock se     (stock se
suma al       devuelve al
destino)      origen)
```

### Flujo de Pago

```
1. Registrar pago en cotizacion
    │
    ┌─────┴─────┐
    │            │
 CONTADO      CREDITO
    │         (auto-aprobado)
    │
2. Pago queda PENDIENTE
    │
    ┌─────┴─────┐
    │            │
APROBAR      RECHAZAR
(suma al      (no cuenta)
total pagado)
```

---

## CALCULO DE STOCK

```
Stock Disponible = Cantidad en sistema
Stock Reservado  = Cantidad apartada por cotizaciones activas
Stock Real       = Stock Disponible - Stock Reservado
Stock Efectivo   = Stock Real - Items en Transito (traslados)
```

**Estados de Stock:**
- **Normal (verde):** stock por encima del minimo
- **Bajo (amarillo):** stock igual o menor al minimo configurado
- **Agotado (rojo):** stock en cero

---

## ESTRUCTURA DE PERMISOS POR MODULO

| Modulo | Admin | Aux Admin | Vendedor | Facturacion | Inventarios | Aux Inventario | Centro Exp | Cliente |
|--------|:-----:|:---------:|:--------:|:-----------:|:-----------:|:--------------:|:----------:|:-------:|
| Dashboard | Si | Si | Si | Si | Si | Si | Si | Si |
| Metricas | Si | - | - | - | - | - | - | - |
| Usuarios | Si | Si | - | Si | Si | - | - | - |
| Clientes | Si | Si | Ver | Si | Si | - | - | - |
| Categorias | Si | Si | - | - | Si | - | - | - |
| Productos | Si | Si | - | Si | Si | - | - | - |
| Listas Precios | Si | Si | - | Si | - | - | - | - |
| Catalogo | Si | Si | Si | - | - | - | - | - |
| Enlaces | Si | Si | Si | - | - | - | - | - |
| Cotizaciones | Si | Si | Propias | Si | Si | Ver | - | - |
| Pagos | Si | Si | Si | Si | Si | - | - | - |
| Stock | Si | Si | - | - | Si | Si | - | - |
| Traslados | Si | Si | - | - | Si | - | Recibir | - |
| Ubicaciones | Si | Si | - | - | Si | - | - | - |
| Novedades | Si | Si | - | - | Si | - | - | - |
| Importar | Si | Si | - | - | Si | - | - | - |
| Portal | - | - | - | - | - | - | - | Si |
| POS | Si | Si | - | - | Si | - | - | - |
| Serv. Tecnico | Si | Si | - | - | - | - | - | - |

---

## DATOS TECNICOS

| Aspecto | Detalle |
|---------|---------|
| Framework | Laravel 9.52 (PHP 8.0+) |
| Base de datos | MySQL |
| Servidor local | XAMPP (Apache + MySQL) |
| Frontend | Blade + Tailwind CSS + Bootstrap 5 + Alpine.js |
| Tablas | Yajra DataTables (server-side) |
| Excel | Maatwebsite Excel v3.1 |
| PDF | Barryvdh Laravel DomPDF |
| Permisos | Spatie Laravel Permission |
| Build | Vite 4.0 |
| Imagenes | /public/imagenes/productos/{id}/ |
| Documentos | /public/documentos/clientes/{id}/ |
| Guias envio | /public/uploads/guias/ |

---

*Documento generado el 20 de febrero de 2026*
*Sistema Miracle B2B - Todos los derechos reservados*
