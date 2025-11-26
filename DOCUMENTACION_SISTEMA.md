# DOCUMENTACIÓN COMPLETA DEL SISTEMA
## Sistema de E-Commerce B2B y Gestión de Servicio Técnico

**Versión:** 1.0
**Fecha:** Noviembre 2025
**Framework:** Laravel 9.52

---

# TABLA DE CONTENIDOS

## PARTE 1: DOCUMENTACIÓN TÉCNICA
1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Esquema de Base de Datos](#3-esquema-de-base-de-datos)
4. [Módulos del Sistema](#4-módulos-del-sistema)
5. [API y Endpoints](#5-api-y-endpoints)
6. [Seguridad y Autenticación](#6-seguridad-y-autenticación)
7. [Workflows Técnicos](#7-workflows-técnicos)
8. [Comandos de Desarrollo](#8-comandos-de-desarrollo)

## PARTE 2: MANUAL DE USUARIO
9. [Introducción para Usuarios](#9-introducción-para-usuarios)
10. [Guía de Configuración Inicial](#10-guía-de-configuración-inicial)
11. [Módulos de Servicio Técnico](#11-módulos-de-servicio-técnico)
12. [Operaciones Diarias](#12-operaciones-diarias)
13. [Flujos de Trabajo Completos](#13-flujos-de-trabajo-completos)
14. [Preguntas Frecuentes](#14-preguntas-frecuentes)
15. [Glosario](#15-glosario)

---

# PARTE 1: DOCUMENTACIÓN TÉCNICA

---

## 1. RESUMEN EJECUTIVO

### 1.1 Propósito del Sistema

Este sistema Laravel 9 integra dos líneas de negocio principales:

1. **E-Commerce B2B**: Catálogo de productos con sistema de cotizaciones, gestión de inventario y acceso temporal por enlaces tokenizados
2. **Gestión de Servicio Técnico**: Administración completa de órdenes de servicio para equipos de seguridad (CCTV, control de acceso)

### 1.2 Usuarios Objetivo

- **Administradores**: Acceso total al sistema
- **Vendedores**: Gestión de catálogo, clientes y cotizaciones B2B
- **Técnicos**: Gestión de órdenes de servicio y reparaciones

### 1.3 Características Principales

- Catálogo de productos con variantes (talla/color)
- Sistema de precios multi-nivel (6 listas de precios)
- Control de inventario con trazabilidad completa
- Generación de enlaces temporales para acceso de clientes
- Gestión de órdenes de servicio técnico
- Registro de equipos y repuestos
- Generación de PDFs y exportación a Excel
- Dashboard con métricas y KPIs

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Stack Tecnológico

#### Backend
- **Framework**: Laravel 9.52
- **PHP**: 8.0.2+
- **Base de Datos**: MySQL (via XAMPP)
- **Servidor Web**: Apache (XAMPP)

#### Frontend
- **Motor de Plantillas**: Blade
- **CSS**: Tailwind CSS 3.4.17 + Bootstrap 5.3.7 (híbrido)
- **JavaScript**:
  - Alpine.js 3.4.2 (componentes reactivos)
  - jQuery 3.7.1 (requerido por DataTables)
  - Livewire 2.12 (componentes dinámicos)

#### Librerías Clave
- **Autenticación**: Laravel Breeze
- **Permisos**: Spatie Laravel Permission
- **DataTables**: Yajra DataTables 10.0 (paginación server-side)
- **Excel**: Maatwebsite Excel 3.1 + PHPSpreadsheet 1.29.7
- **PDF**: Barryvdh DomPDF 3.1
- **Notificaciones**: SweetAlert2 11.22

#### Build Tools
- **Bundler**: Vite 4.0
- **CSS Processors**: PostCSS, Autoprefixer

### 2.2 Patrón de Arquitectura

**MVC (Model-View-Controller)** con las siguientes capas:

```
app/
├── Http/
│   └── Controllers/          # Controladores
│       ├── ProductosController.php
│       ├── StockController.php
│       ├── CatalogoController.php
│       ├── SolicitudController.php
│       └── ServicioTecnico/  # Namespace para módulo ST
├── Models/                   # Modelos Eloquent (30+)
├── Imports/                  # Clases de importación Excel
├── Exports/                  # Clases de exportación Excel
└── Mail/                     # Plantillas de email

resources/
└── views/                    # Vistas Blade
    ├── productos/
    ├── catalogo/
    ├── stock/
    ├── solicitudes/
    ├── servicio-tecnico/
    ├── pdf/
    └── layouts/

routes/
└── web.php                   # Definición de rutas

database/
└── migrations/               # 40+ archivos de migración
```

### 2.3 Entorno de Desarrollo

**Servicios Requeridos:**
1. XAMPP (MySQL + Apache)
2. Laravel Dev Server: `php artisan serve` (puerto 8000)
3. Vite Dev Server: `npm run dev` (puerto 5173, hot reload)

**Comandos de Build:**
```bash
# Desarrollo
npm run dev          # Inicia Vite con hot reload
php artisan serve    # Inicia servidor Laravel

# Producción
npm run build        # Compila assets para producción

# Base de datos
php artisan migrate              # Ejecuta migraciones
php artisan migrate:fresh --seed # Base de datos limpia con datos

# Testing
php artisan test
vendor/bin/phpunit
```

---

## 3. ESQUEMA DE BASE DE DATOS

### 3.1 Resumen

- **Total de Tablas**: 40+
- **Relaciones**: Predominantemente 1:N con algunas N:M
- **Convenciones**:
  - Foreign keys: `{tabla_singular}_id`
  - Timestamps: `created_at`, `updated_at`
  - Soft delete: Flag `activo` (no usa Laravel soft deletes)

### 3.2 Tablas Principales - E-Commerce B2B

#### 3.2.1 Productos y Catálogo

**`productos`**
```sql
id                          BIGINT PRIMARY KEY AUTO_INCREMENT
referencia                  VARCHAR(255) UNIQUE NOT NULL
nombre                      VARCHAR(255) NOT NULL
descripcion                 TEXT NULL
marca                       VARCHAR(255) NULL
categoria_id                BIGINT → categorias.id
unidad_venta                VARCHAR(255) NOT NULL
unidad_empaque              VARCHAR(255) NOT NULL
extension                   VARCHAR(255) NULL
activo                      BOOLEAN DEFAULT TRUE
tiene_variantes             BOOLEAN DEFAULT FALSE
controlar_stock             BOOLEAN DEFAULT TRUE
permitir_venta_sin_stock    BOOLEAN DEFAULT FALSE
eliminado                   BOOLEAN DEFAULT FALSE
created_at, updated_at      TIMESTAMP
```

**`categorias`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
nombre              VARCHAR(255) NOT NULL
slug                VARCHAR(255) UNIQUE NOT NULL
descripcion         TEXT NULL
orden               INTEGER NOT NULL
activo              BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`variantes_productos`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
producto_id         BIGINT → productos.id
talla               VARCHAR(255) NULL
color               VARCHAR(255) NULL
sku                 VARCHAR(255) UNIQUE NOT NULL
activo              BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`imagenes_producto`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
producto_id         BIGINT → productos.id
ruta_imagen         VARCHAR(255) NOT NULL
texto_alternativo   VARCHAR(255) NULL
es_principal        BOOLEAN DEFAULT FALSE
orden               INTEGER DEFAULT 0
created_at, updated_at TIMESTAMP
```

#### 3.2.2 Sistema de Precios

**`listas_precios`**
```sql
id          BIGINT PRIMARY KEY AUTO_INCREMENT
codigo      VARCHAR(50) UNIQUE NOT NULL  -- export1, export2, local1-4
nombre      VARCHAR(255) NOT NULL
descripcion TEXT NULL
activo      BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`precios_productos`**
```sql
id              BIGINT PRIMARY KEY AUTO_INCREMENT
producto_id     BIGINT → productos.id
lista_precio_id BIGINT → listas_precios.id
precio          DECIMAL(10,2) NOT NULL
activo          BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
UNIQUE(producto_id, lista_precio_id)
```

**`precios_variantes`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
variante_producto_id    BIGINT → variantes_productos.id
lista_precio_id         BIGINT → listas_precios.id
precio                  DECIMAL(10,2) NOT NULL
activo                  BOOLEAN DEFAULT TRUE
created_at, updated_at  TIMESTAMP
UNIQUE(variante_producto_id, lista_precio_id)
```

**`actualizaciones_precios`** (auditoría)
```sql
id                          BIGINT PRIMARY KEY AUTO_INCREMENT
usuario_id                  BIGINT → users.id
estado                      ENUM('procesando','completado','error')
nombre_archivo              VARCHAR(255) NOT NULL
ruta_archivo                VARCHAR(255) NOT NULL
total_filas                 INTEGER NOT NULL
actualizaciones_exitosas    INTEGER DEFAULT 0
actualizaciones_fallidas    INTEGER DEFAULT 0
errores                     JSON NULL
detalles_procesados         JSON NULL
created_at, updated_at      TIMESTAMP
```

#### 3.2.3 Control de Stock

**`stock_productos`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
producto_id             BIGINT → productos.id
variante_producto_id    BIGINT → variantes_productos.id (nullable)
cantidad_disponible     INTEGER DEFAULT 0
cantidad_reservada      INTEGER DEFAULT 0
stock_minimo            INTEGER DEFAULT 0
stock_maximo            INTEGER NULL
ubicacion               VARCHAR(255) NULL
alerta_stock_bajo       BOOLEAN DEFAULT TRUE
notas                   TEXT NULL
created_at, updated_at  TIMESTAMP
UNIQUE(producto_id, variante_producto_id)
```

**`movimientos_stock`**
```sql
id                          BIGINT PRIMARY KEY AUTO_INCREMENT
producto_id                 BIGINT → productos.id
variante_producto_id        BIGINT → variantes_productos.id (nullable)
tipo_movimiento             ENUM('entrada','salida','ajuste','reserva','liberacion')
cantidad                    INTEGER NOT NULL
stock_anterior              INTEGER NOT NULL
stock_nuevo                 INTEGER NOT NULL
referencia_documento        VARCHAR(255) NULL
origen                      ENUM('compra','venta','devolucion','ajuste_inventario','cotizacion')
motivo                      TEXT NULL
usuario_id                  BIGINT → users.id
solicitud_cotizacion_id     BIGINT → solicitudes_cotizacion.id (nullable)
created_at, updated_at      TIMESTAMP
```

#### 3.2.4 Clientes y Geografía

**`clientes`** (clientes B2B)
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
numero_identificacion   VARCHAR(255) UNIQUE NOT NULL
nombre_contacto         VARCHAR(255) NOT NULL
email                   VARCHAR(255) UNIQUE NOT NULL
telefono                VARCHAR(100) NULL
pais_id                 BIGINT → paises.id
ciudad_id               BIGINT → ciudades.id
vendedor_id             BIGINT → users.id
lista_precio_id         BIGINT → listas_precios.id
activo                  BOOLEAN DEFAULT TRUE
created_at, updated_at  TIMESTAMP
```

**`paises`**
```sql
id          BIGINT PRIMARY KEY AUTO_INCREMENT
nombre      VARCHAR(255) NOT NULL
codigo      VARCHAR(10) UNIQUE NOT NULL
created_at, updated_at TIMESTAMP
```

**`departamentos`**
```sql
id          BIGINT PRIMARY KEY AUTO_INCREMENT
pais_id     BIGINT → paises.id
nombre      VARCHAR(255) NOT NULL
created_at, updated_at TIMESTAMP
```

**`ciudades`**
```sql
id              BIGINT PRIMARY KEY AUTO_INCREMENT
departamento_id BIGINT → departamentos.id
nombre          VARCHAR(255) NOT NULL
created_at, updated_at TIMESTAMP
```

#### 3.2.5 Cotizaciones y Enlaces

**`enlaces_acceso`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
cliente_id          BIGINT → clientes.id
creado_por          BIGINT → users.id
token               VARCHAR(255) UNIQUE NOT NULL
dias_validos        INTEGER NOT NULL
mostrar_precios     BOOLEAN DEFAULT TRUE
mostrar_stock       BOOLEAN DEFAULT TRUE
expira_en           TIMESTAMP NOT NULL
activo              BOOLEAN DEFAULT TRUE
visitas             INTEGER DEFAULT 0
ultimo_acceso       TIMESTAMP NULL
notas               TEXT NULL
created_at, updated_at TIMESTAMP
```

**`solicitudes_cotizacion`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
numero_solicitud        VARCHAR(255) UNIQUE NOT NULL  -- SC-YYYYMMDDHHMMSS-XXXX
cliente_id              BIGINT → clientes.id
enlace_acceso_id        BIGINT → enlaces_acceso.id (nullable)
created_by              BIGINT → users.id (nullable)
estado                  ENUM('pendiente','aplicada','rechazada') DEFAULT 'pendiente'
monto_total             DECIMAL(10,2) NOT NULL
notas_cliente           TEXT NULL
observaciones_admin     TEXT NULL
motivo_rechazo          TEXT NULL
aplicada_en             TIMESTAMP NULL
aplicada_por            BIGINT → users.id (nullable)
rechazada_en            TIMESTAMP NULL
rechazada_por           BIGINT → users.id (nullable)
created_at, updated_at  TIMESTAMP
```

**`items_solicitud_cotizacion`**
```sql
id                          BIGINT PRIMARY KEY AUTO_INCREMENT
solicitud_cotizacion_id     BIGINT → solicitudes_cotizacion.id
producto_id                 BIGINT → productos.id
variante_producto_id        BIGINT → variantes_productos.id (nullable)
cantidad                    INTEGER NOT NULL
precio_unitario             DECIMAL(10,2) NOT NULL
precio_total                DECIMAL(10,2) NOT NULL
precio_editado_manualmente  BOOLEAN DEFAULT FALSE
precio_original             DECIMAL(10,2) NULL
referencia_producto         VARCHAR(255) NOT NULL
nombre_producto             VARCHAR(255) NOT NULL
marca_producto              VARCHAR(255) NULL
info_variante               VARCHAR(255) NULL
created_at, updated_at      TIMESTAMP
```

### 3.3 Tablas Principales - Servicio Técnico

#### 3.3.1 Clientes y Equipos ST

**`st_clientes`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
tipo_cliente        ENUM('particular','empresa') NOT NULL
tipo_documento      VARCHAR(20) NOT NULL
numero_documento    VARCHAR(50) UNIQUE NOT NULL
nombre_completo     VARCHAR(255) NOT NULL
razon_social        VARCHAR(255) NULL
email               VARCHAR(255) NULL
telefono            VARCHAR(20) NULL
celular             VARCHAR(20) NOT NULL
direccion           TEXT NULL
ciudad              VARCHAR(255) NULL
departamento        VARCHAR(255) NULL
observaciones       TEXT NULL
activo              BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`st_equipos`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
st_cliente_id           BIGINT → st_clientes.id
tipo_equipo             VARCHAR(100) NOT NULL
marca                   VARCHAR(100) NULL
modelo                  VARCHAR(100) NULL
numero_serie            VARCHAR(100) UNIQUE NOT NULL
mac_address             VARCHAR(50) NULL
ip_address              VARCHAR(50) NULL
especificaciones        TEXT NULL
fecha_compra            DATE NULL
fecha_instalacion       DATE NULL
en_garantia             BOOLEAN DEFAULT FALSE
vencimiento_garantia    DATE NULL
ubicacion_instalacion   VARCHAR(255) NULL
estado                  ENUM('operativo','en_reparacion','fuera_servicio','en_bodega')
activo                  BOOLEAN DEFAULT TRUE
created_at, updated_at  TIMESTAMP
```

#### 3.3.2 Técnicos y Órdenes

**`st_tecnicos`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
user_id             BIGINT → users.id (nullable)
codigo              VARCHAR(20) UNIQUE NOT NULL
nombre_completo     VARCHAR(255) NOT NULL
documento           VARCHAR(50) UNIQUE NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
telefono            VARCHAR(20) NOT NULL
celular             VARCHAR(20) NOT NULL
especialidad        VARCHAR(255) NULL
fecha_ingreso       DATE NULL
certificaciones     TEXT NULL
activo              BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`st_ordenes_servicio`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
numero_orden            VARCHAR(255) UNIQUE NOT NULL  -- ST-YYYY-XXXXXX
st_cliente_id           BIGINT → st_clientes.id
st_equipo_id            BIGINT → st_equipos.id (nullable)
st_tecnico_id           BIGINT → st_tecnicos.id (nullable)
tipo_servicio           VARCHAR(255) NOT NULL
prioridad               ENUM('baja','media','alta','urgente') DEFAULT 'media'
estado                  ENUM('recibida','asignada','en_proceso','pendiente_repuestos',
                             'completada','entregada','cancelada') DEFAULT 'recibida'
descripcion_problema    TEXT NOT NULL
accesorios_entregados   TEXT NULL
fecha_recepcion         DATE NOT NULL
fecha_asignacion        TIMESTAMP NULL
fecha_inicio_trabajo    TIMESTAMP NULL
fecha_finalizacion      TIMESTAMP NULL
fecha_promesa_entrega   DATE NULL
fecha_entrega           TIMESTAMP NULL
costo_mano_obra         DECIMAL(10,2) DEFAULT 0
costo_repuestos         DECIMAL(10,2) DEFAULT 0
costo_total             DECIMAL(10,2) DEFAULT 0
cliente_notificado      BOOLEAN DEFAULT FALSE
observaciones           TEXT NULL
user_id                 BIGINT → users.id  -- usuario creador
created_at, updated_at  TIMESTAMP
```

**`st_diagnosticos`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
st_orden_servicio_id    BIGINT → st_ordenes_servicio.id
st_tecnico_id           BIGINT → st_tecnicos.id
fecha_diagnostico       TIMESTAMP NOT NULL
fallas_encontradas      TEXT NOT NULL
diagnostico_tecnico     TEXT NOT NULL
reparaciones_realizadas TEXT NULL
recomendaciones         TEXT NULL
requiere_repuestos      BOOLEAN DEFAULT FALSE
repuestos_necesarios    TEXT NULL
tiempo_estimado_horas   DECIMAL(5,2) NULL
costo_estimado          DECIMAL(10,2) NULL
created_at, updated_at  TIMESTAMP
```

#### 3.3.3 Repuestos e Historial

**`st_repuestos`**
```sql
id                  BIGINT PRIMARY KEY AUTO_INCREMENT
codigo              VARCHAR(50) UNIQUE NOT NULL
nombre              VARCHAR(255) NOT NULL
descripcion         TEXT NULL
categoria           VARCHAR(100) NOT NULL
marca               VARCHAR(100) NULL
modelo_compatible   VARCHAR(100) NULL
stock_actual        INTEGER DEFAULT 0
stock_minimo        INTEGER DEFAULT 0
precio_costo        DECIMAL(10,2) NULL
precio_venta        DECIMAL(10,2) NULL
proveedor           VARCHAR(255) NULL
activo              BOOLEAN DEFAULT TRUE
created_at, updated_at TIMESTAMP
```

**`st_repuestos_usados`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
st_orden_servicio_id    BIGINT → st_ordenes_servicio.id
st_repuesto_id          BIGINT → st_repuestos.id
cantidad                INTEGER NOT NULL
precio_unitario         DECIMAL(10,2) NOT NULL
created_at, updated_at  TIMESTAMP
```

**`st_historial_estados`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
st_orden_servicio_id    BIGINT → st_ordenes_servicio.id
estado_anterior         VARCHAR(50) NOT NULL
estado_nuevo            VARCHAR(50) NOT NULL
observaciones           TEXT NULL
usuario_id              BIGINT → users.id
created_at, updated_at  TIMESTAMP
```

**`st_imagenes_orden`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
st_orden_servicio_id    BIGINT → st_ordenes_servicio.id
nombre_archivo          VARCHAR(255) NOT NULL
ruta_archivo            VARCHAR(255) NOT NULL
tipo_imagen             ENUM('recepcion','diagnostico','reparacion','entrega')
orden                   INTEGER DEFAULT 0
created_at, updated_at  TIMESTAMP
```

### 3.4 Tablas de Sistema

**`users`**
```sql
id                      BIGINT PRIMARY KEY AUTO_INCREMENT
name                    VARCHAR(255) NOT NULL
email                   VARCHAR(255) UNIQUE NOT NULL
email_verified_at       TIMESTAMP NULL
password                VARCHAR(255) NOT NULL
remember_token          VARCHAR(100) NULL
created_at, updated_at  TIMESTAMP
```

**`roles`** (Spatie)
```sql
id          BIGINT PRIMARY KEY AUTO_INCREMENT
name        VARCHAR(255) NOT NULL  -- admin, vendedor, tecnico
guard_name  VARCHAR(255) NOT NULL
created_at, updated_at TIMESTAMP
```

**`model_has_roles`** (Spatie)
```sql
role_id     BIGINT → roles.id
model_type  VARCHAR(255) NOT NULL  -- App\Models\User
model_id    BIGINT NOT NULL
PRIMARY KEY(role_id, model_id, model_type)
```

**`logs`**
```sql
id              BIGINT PRIMARY KEY AUTO_INCREMENT
user_id         BIGINT → users.id (nullable)
accion          VARCHAR(255) NOT NULL
modulo          VARCHAR(255) NOT NULL
descripcion     TEXT NULL
ip_address      VARCHAR(45) NULL
created_at, updated_at TIMESTAMP
```

### 3.5 Diagramas de Relaciones

#### Diagrama E-Commerce B2B
```
categorias (1) ───┬──→ (N) productos
                  │
productos (1) ────┼──→ (N) variantes_productos
                  ├──→ (N) imagenes_producto
                  ├──→ (N) precios_productos
                  └──→ (N) stock_productos

listas_precios (1) ─┬──→ (N) precios_productos
                     ├──→ (N) precios_variantes
                     └──→ (N) clientes

paises (1) ──→ (N) departamentos (1) ──→ (N) ciudades
                                            │
clientes (1) ───────────────────────────────┼──→ (N) enlaces_acceso
                                            └──→ (N) solicitudes_cotizacion

solicitudes_cotizacion (1) ──→ (N) items_solicitud_cotizacion
```

#### Diagrama Servicio Técnico
```
st_clientes (1) ──→ (N) st_equipos

st_ordenes_servicio (1) ─┬──→ (N) st_diagnosticos
                          ├──→ (N) st_repuestos_usados
                          ├──→ (N) st_historial_estados
                          └──→ (N) st_imagenes_orden

st_tecnicos (1) ──→ (N) st_ordenes_servicio
st_equipos (1) ──→ (N) st_ordenes_servicio
st_repuestos (1) ──→ (N) st_repuestos_usados
```

---

## 4. MÓDULOS DEL SISTEMA

### 4.1 Inicio (Dashboard)

**Ruta:** `/dashboard`
**Controlador:** `HomeController`
**Acceso:** Todos los usuarios autenticados

**Funcionalidad:**
- Panel principal personalizado por rol
- Acceso rápido a módulos disponibles
- Notificaciones y alertas
- Últimas actividades

### 4.2 Servicio Técnico

#### 4.2.1 Dashboard ST

**Ruta:** `/servicio-tecnico/dashboard`
**Controlador:** `ServicioTecnico\DashboardSTController`
**Acceso:** Admin, Técnico

**KPIs Mostrados:**
- Total de órdenes activas
- Órdenes por estado (recibida, asignada, en_proceso, completada)
- Órdenes urgentes pendientes
- Técnicos disponibles
- Equipos en reparación
- Stock bajo de repuestos
- Gráficos de tendencias mensuales

#### 4.2.2 Órdenes de Servicio

**Ruta:** `/servicio-tecnico/ordenes`
**Controlador:** `ServicioTecnico\STOrdenServicioController`
**Acceso:** Admin, Técnico

**Funcionalidades:**
- Crear nueva orden de servicio
- Ver listado con filtros (estado, prioridad, técnico, fecha)
- Ver detalle de orden
- Cambiar estado (workflow)
- Asignar/reasignar técnico
- Agregar diagnósticos
- Agregar repuestos usados
- Subir imágenes (recepción, diagnóstico, reparación, entrega)
- Generar PDF de orden
- Notificar cliente por email

**Estados del Workflow:**
1. recibida → 2. asignada → 3. en_proceso → 4. pendiente_repuestos (opcional) →
5. completada → 6. entregada | cancelada

#### 4.2.3 Técnicos

**Ruta:** `/servicio-tecnico/tecnicos`
**Controlador:** `ServicioTecnico\STTecnicoController`
**Acceso:** Admin, Técnico

**Funcionalidades:**
- CRUD de técnicos
- Auto-creación de cuenta de usuario (rol: tecnico)
- Asignación de especialidades
- Vista de órdenes asignadas
- Historial de trabajo

#### 4.2.4 Clientes ST

**Ruta:** `/servicio-tecnico/clientes`
**Controlador:** `ServicioTecnico\STClienteController`
**Acceso:** Admin, Técnico

**Funcionalidades:**
- CRUD de clientes de servicio técnico
- Diferenciación entre particular/empresa
- Vista de equipos del cliente
- Historial de órdenes del cliente

#### 4.2.5 Equipos

**Ruta:** `/servicio-tecnico/equipos`
**Controlador:** `ServicioTecnico\STEquipoController`
**Acceso:** Admin, Técnico

**Funcionalidades:**
- CRUD de equipos de seguridad
- Registro de número de serie único
- Control de garantía
- Estados operacionales
- Historial de servicio por equipo
- Especificaciones técnicas

#### 4.2.6 Repuestos

**Ruta:** `/servicio-tecnico/repuestos`
**Controlador:** `ServicioTecnico\STRepuestoController`
**Acceso:** Admin, Técnico

**Funcionalidades:**
- CRUD de repuestos
- Control de inventario
- Alertas de stock bajo
- Historial de uso
- Precios de compra/venta
- Proveedores

### 4.3 Métricas

**Ruta:** `/dashboard-metricas`
**Controlador:** `DashboardMetricasController`
**Acceso:** Admin únicamente

**Métricas Mostradas:**
- Valor total cotizado
- Cotizaciones aplicadas vs rechazadas vs pendientes
- Tasa de conversión
- Valor cotizado por vendedor
- Gráficos de tendencias
- Filtros por fecha y vendedor

### 4.4 Usuarios

**Ruta:** `/usuarios`
**Controlador:** `UsuariosController`
**Acceso:** Admin únicamente

**Funcionalidades:**
- CRUD de usuarios
- Asignación de roles (admin, vendedor, tecnico)
- Gestión de contraseñas
- Activar/desactivar usuarios
- Importación masiva de usuarios

### 4.5 Categorías

**Ruta:** `/categorias`
**Controlador:** `CategoriasController`
**Acceso:** Admin únicamente

**Funcionalidades:**
- CRUD de categorías de productos
- Orden de visualización
- Slugs para SEO
- Activar/desactivar categorías

### 4.6 Productos

**Ruta:** `/productos`
**Controlador:** `ProductosController`
**Acceso:** Admin únicamente

**Funcionalidades:**
- CRUD de productos
- Gestión de variantes (talla/color)
- Múltiples imágenes por producto
- Precios por lista (6 listas)
- Configuración de control de stock
- Importación masiva de productos (Excel/CSV)
- Actualización masiva de precios (Excel/CSV)
- Modales AJAX para:
  - Ver variantes
  - Ver imágenes
  - Ver precios
  - Ver stock actual
- Historial de actualizaciones de precios
- Soft delete (flag `eliminado`)

### 4.7 Cotizaciones

**Ruta:** `/solicitudes`
**Controlador:** `SolicitudController`
**Acceso:** Admin, Vendedor

**Funcionalidades:**
- Listado de cotizaciones con filtros
- Ver detalle de cotización
- Aprobar cotización (marca como "aplicada")
  - Opción de descontar stock
  - Registra usuario y fecha
- Rechazar cotización (marca como "rechazada")
  - Requiere motivo
  - Registra usuario y fecha
- Generar PDF (formato Excel)
- Exportar listado a Excel
- Enviar notificación por email al cliente

**Estados:**
- pendiente: Nueva cotización sin revisar
- aplicada: Cotización aprobada/aceptada
- rechazada: Cotización rechazada

### 4.8 Clientes

**Ruta:** `/clientes`
**Controlador:** `ClientesController`
**Acceso:** Admin, Vendedor

**Funcionalidades:**
- CRUD de clientes B2B
- Asignación a vendedor
- Asignación de lista de precios
- Ubicación geográfica (país, departamento, ciudad)
- Vista de enlaces generados
- Vista de cotizaciones del cliente
- Filtro por vendedor (vendedores solo ven sus clientes)

### 4.9 Catálogo

**Ruta:** `/catalogo`
**Controlador:** `CatalogoController`
**Acceso:** Admin, Vendedor (autenticado) | Clientes (vía token)

**Dos Flujos de Acceso:**

#### Flujo A: Acceso Público (Token)
**Ruta:** `/catalogo/{token}`
**Usuarios:** Clientes sin autenticación

**Proceso:**
1. Cliente recibe email con enlace tokenizado
2. Accede al catálogo con visibilidad configurada (precios/stock)
3. Navega categorías y productos
4. Agrega productos al carrito
5. Envía solicitud de cotización
6. Sistema registra visita y genera número de solicitud

#### Flujo B: Acceso Autenticado (Tienda a Tienda)
**Ruta:** `/catalogo` → selección de cliente → `/catalogo/cliente`
**Usuarios:** Vendedores y administradores

**Proceso:**
1. Vendedor selecciona cliente de su cartera
2. Sistema carga catálogo con precios del cliente
3. Vendedor crea cotización en nombre del cliente
4. Puede editar precios manualmente
5. Envía cotización (queda registrado vendedor creador)

### 4.10 Links (Enlaces de Acceso)

**Ruta:** `/enlaces`
**Controlador:** `EnlacesController`
**Acceso:** Admin, Vendedor

**Funcionalidades:**
- Crear enlace temporal para cliente
- Configuración:
  - Días de validez (1-365)
  - Mostrar/ocultar precios
  - Mostrar/ocultar stock
  - Notas internas
- Ver listado de enlaces (activos/expirados)
- Ver detalle de enlace:
  - URL completa
  - Fecha de expiración
  - Número de visitas
  - Último acceso
  - Cotizaciones generadas
- Activar/desactivar enlace
- Regenerar token
- Enviar por email al cliente

**Token:** Cadena aleatoria de 32 caracteres, único en la base de datos

### 4.11 Gestión de Stock

**Ruta:** `/stock`
**Controlador:** `StockController`
**Acceso:** Admin, Vendedor

**Funcionalidades:**

#### Dashboard de Stock
- Productos con stock bajo
- Productos sin stock
- Movimientos del mes (entradas/salidas)
- Top productos con más rotación
- Productos críticos

#### Operaciones
- **Entrada de Stock**:
  - Tipo: compra, devolucion
  - Campos: producto/variante, cantidad, ubicación, referencia documento, motivo
  - Registra movimiento con stock_anterior → stock_nuevo

- **Salida de Stock**:
  - Tipo: venta, transferencia
  - Campos: producto/variante, cantidad, referencia documento, motivo
  - Valida stock disponible

- **Ajuste de Stock**:
  - Tipo: ajuste_inventario
  - Corrige discrepancias
  - Puede aumentar o disminuir

#### Historial
- Listado completo de movimientos
- Filtros: producto, variante, tipo, fecha, usuario
- Exportar a Excel
- Búsqueda de productos por referencia/nombre (AJAX)

#### Configuración
- Configurar parámetros de stock por producto:
  - stock_minimo
  - stock_maximo
  - ubicacion
  - alerta_stock_bajo (activar/desactivar)

---

## 5. API Y ENDPOINTS

### 5.1 Endpoints AJAX - Productos

```
GET  /productos/{producto}/variantes-ajax
     Retorna: HTML con tabla de variantes del producto
     Uso: Modal en index de productos

GET  /productos/{producto}/imagenes-ajax
     Retorna: HTML con galería de imágenes del producto
     Uso: Modal en index de productos

GET  /productos/{producto}/precios-ajax
     Retorna: HTML con tabla de precios por lista
     Uso: Modal en index de productos

GET  /productos/{producto}/stock-ajax
     Retorna: HTML con información de stock actual
     Uso: Modal en index de productos
```

### 5.2 Endpoints AJAX - Catálogo

```
POST /catalogo/productos
     Body: {categoria_id?, search?, page}
     Retorna: JSON con productos paginados
     Uso: Carga de productos en catálogo con filtros

GET  /catalogo/producto/{producto}
     Retorna: HTML con detalle completo del producto
     Uso: Modal de producto en catálogo

POST /catalogo/solicitud
     Body: {cliente_id, enlace_id?, items[], notas}
     Retorna: JSON {success, numero_solicitud, mensaje}
     Uso: Envío de cotización desde catálogo
```

### 5.3 Endpoints AJAX - Stock

```
GET  /stock/productos-json?term={busqueda}
     Retorna: JSON [{id, text, referencia, nombre, tiene_variantes}, ...]
     Uso: Autocompletado en operaciones de stock

GET  /stock/{id}/obtener
     Retorna: JSON con stock actual del producto
     Uso: Validación antes de operaciones

GET  /stock/historial?producto_id={id}&fecha_desde={date}&fecha_hasta={date}
     Retorna: HTML con tabla de movimientos
     Uso: Ver historial filtrado
```

### 5.4 Endpoints AJAX - Geografía

```
GET  /ajax/ciudades?departamento_id={id}
     Retorna: JSON [{id, nombre}, ...]
     Uso: Carga dinámica de ciudades al seleccionar departamento
```

### 5.5 Endpoints AJAX - Servicio Técnico

```
GET  /servicio-tecnico/equipos-cliente/{clienteId}
     Retorna: JSON [{id, descripcion}, ...]
     Uso: Carga de equipos al seleccionar cliente en formulario de orden

POST /servicio-tecnico/ordenes/{orden}/cambiar-estado
     Body: {estado, observaciones}
     Retorna: JSON {success, mensaje}
     Uso: Actualización de estado de orden

POST /servicio-tecnico/ordenes/{orden}/diagnosticos
     Body: {datos del diagnóstico}
     Retorna: JSON {success, mensaje}
     Uso: Agregar diagnóstico a orden

POST /servicio-tecnico/ordenes/{orden}/repuestos
     Body: {repuesto_id, cantidad}
     Retorna: JSON {success, mensaje}
     Uso: Agregar repuesto a orden (descuenta stock)
```

### 5.6 Endpoints de Exportación

```
GET  /solicitudes/{solicitud}/pdf
     Retorna: PDF (inline o download)
     Uso: Generar PDF de cotización

GET  /solicitudes/exportar-excel?fecha_desde={date}&fecha_hasta={date}
     Retorna: Archivo Excel
     Uso: Exportar listado de cotizaciones

GET  /servicio-tecnico/ordenes/{orden}/pdf
     Retorna: PDF de orden de servicio
     Uso: Generar PDF de orden

GET  /stock/exportar-movimientos?fecha_desde={date}&fecha_hasta={date}
     Retorna: Archivo Excel
     Uso: Exportar historial de movimientos
```

### 5.7 Endpoints de Importación

```
POST /productos/actualizar-precios-excel
     Body: FormData {archivo (CSV/Excel)}
     Retorna: JSON {success, mensaje, estadisticas}
     Formato CSV: Delimitador ';'
     Columnas: referencia;export1;export2;local1;local2;local3;local4
     Uso: Actualización masiva de precios

POST /productos/importar
     Body: FormData {archivo (CSV/Excel)}
     Retorna: JSON {success, mensaje, estadisticas}
     Uso: Importación masiva de productos
```

### 5.8 DataTables Server-Side

Todos los índices usan DataTables con procesamiento server-side:

```
GET  /productos?draw={n}&start={n}&length={n}&search[value]={text}&order[0][column]={n}
GET  /clientes?...
GET  /solicitudes?...
GET  /stock?...
GET  /servicio-tecnico/ordenes?...
GET  /servicio-tecnico/clientes?...
GET  /servicio-tecnico/equipos?...
GET  /servicio-tecnico/repuestos?...

Retorna: JSON DataTables format
{
  draw: int,
  recordsTotal: int,
  recordsFiltered: int,
  data: [...]
}
```

---

## 6. SEGURIDAD Y AUTENTICACIÓN

### 6.1 Autenticación

**Sistema:** Laravel Breeze (sesiones)

**Rutas de autenticación:**
```
GET|POST  /login          - Inicio de sesión
POST      /logout         - Cierre de sesión
GET|POST  /register       - Registro (deshabilitado en producción)
GET|POST  /forgot-password - Recuperación de contraseña
GET|POST  /reset-password  - Reseteo de contraseña
```

**Características:**
- Hash de contraseñas con bcrypt
- Protección CSRF en todos los formularios
- Throttling de intentos de login
- Remember me functionality
- Email verification (opcional)

### 6.2 Autorización

**Sistema:** Spatie Laravel Permission

**Roles del Sistema:**
1. **admin**: Acceso total
2. **vendedor**: Acceso a módulos de catálogo y ventas
3. **tecnico**: Acceso a módulo de servicio técnico

**Matriz de Permisos:**

| Módulo | Admin | Vendedor | Técnico |
|--------|-------|----------|---------|
| Dashboard | ✓ | ✓ | ✓ |
| Métricas | ✓ | ✗ | ✗ |
| Usuarios | ✓ | ✗ | ✗ |
| Categorías | ✓ | ✗ | ✗ |
| Productos | ✓ | ✗ | ✗ |
| Clientes (B2B) | ✓ | ✓* | ✗ |
| Enlaces | ✓ | ✓* | ✗ |
| Catálogo | ✓ | ✓ | ✗ |
| Cotizaciones | ✓ | ✓* | ✗ |
| Stock | ✓ | ✓ | ✗ |
| Servicio Técnico | ✓ | ✗ | ✓ |

*Vendedores solo ven sus clientes asignados

**Implementación en Rutas:**
```php
Route::middleware(['auth'])->group(function () {
    // Rutas protegidas
});

// Verificación en controladores
if (auth()->user()->hasRole('admin')) {
    // Acción de admin
}

// Filtrado por vendedor
if (auth()->user()->hasRole('vendedor')) {
    $clientes = Cliente::where('vendedor_id', auth()->id())->get();
}
```

### 6.3 Acceso de Clientes (Token)

**Sistema:** Enlaces temporales con tokens únicos

**Características:**
- Token aleatorio de 32 caracteres
- Fecha de expiración configurable
- Control de activación manual
- Registro de visitas y último acceso
- Sin necesidad de crear cuenta de usuario

**Validación:**
```php
public function esValido(): bool
{
    return $this->activo
        && $this->expira_en->isFuture();
}
```

### 6.4 Protección CSRF

Todos los formularios incluyen token CSRF:
```blade
<form method="POST" action="...">
    @csrf
    <!-- campos -->
</form>
```

### 6.5 Validación de Inputs

**Server-side validation** en todos los formularios:
```php
$request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6',
    // ...
]);
```

**Client-side validation** con HTML5:
```html
<input type="email" required>
<input type="number" min="0" max="100">
```

### 6.6 Protección XSS

- Blade escapa automáticamente: `{{ $variable }}`
- Para HTML confiable: `{!! $html !!}` (usar con precaución)
- Sanitización en inputs con `strip_tags()` donde sea necesario

### 6.7 Protección SQL Injection

- Uso exclusivo de Eloquent ORM y Query Builder
- Prepared statements automáticos
- No se ejecuta SQL raw sin bindings

### 6.8 Auditoría

**Tabla `logs`** registra acciones críticas:
- Creación/edición/eliminación de registros
- Cambios de estado
- Accesos a módulos sensibles
- IP del usuario

**Tabla `movimientos_stock`** auditoría completa:
- Todos los movimientos de stock
- Usuario responsable
- Stock antes/después
- Documento de referencia

**Tabla `actualizaciones_precios`** auditoría de precios:
- Archivo subido
- Resultados del procesamiento
- Errores detallados
- Usuario responsable

---

## 7. WORKFLOWS TÉCNICOS

### 7.1 Workflow: Creación de Producto

```
1. Admin accede a /productos/form
2. Completa información básica
   ├─ referencia (único)
   ├─ nombre, descripción, marca
   ├─ categoria_id (select)
   └─ unidades (venta, empaque)
3. Decide si tiene variantes
   ├─ NO: Configura stock inicial
   └─ SÍ: Agrega filas de variantes (talla, color, sku)
4. Sube imágenes (marca una como principal)
5. Ingresa precios por lista (opcional)
6. Submit → ProductosController@guardar
7. Backend validation
8. Guarda producto en DB
9. Si controlar_stock = true:
   ├─ Crea registro(s) en stock_productos
   └─ Si stock_inicial > 0: crea MovimientoStock (tipo: ajuste, origen: ajuste_inventario)
10. Guarda variantes (si aplica)
11. Procesa imágenes:
    ├─ Mueve a storage/app/public/imagenes/productos/{producto_id}/
    └─ Guarda registros en imagenes_producto
12. Guarda precios (si aplica)
13. Redirect a /productos con mensaje éxito
```

### 7.2 Workflow: Actualización Masiva de Precios

```
1. Admin descarga plantilla CSV/Excel
   └─ /productos/descargar-plantilla-csv o -excel
2. Llena archivo con referencias y nuevos precios
   Formato: referencia;export1;export2;local1;local2;local3;local4
3. Sube archivo en /productos (sección actualización precios)
4. ProductosController@actualizarPreciosExcel
5. Valida archivo (formato, extensión, tamaño)
6. Crea registro en actualizaciones_precios (estado: procesando)
7. Guarda archivo en storage/app/public/uploads/actualizaciones_precios/
8. Ejecuta PreciosImport (Maatwebsite Excel)
9. Por cada fila:
   ├─ Busca producto por referencia
   ├─ Si no existe: registra error
   └─ Si existe:
       ├─ Por cada lista de precio con valor:
       │   ├─ Busca o crea registro en precios_productos
       │   └─ Actualiza precio
       └─ Registra éxito
10. Actualiza registro actualizaciones_precios:
    ├─ estado = completado/error
    ├─ total_filas, exitosas, fallidas
    ├─ errores (JSON)
    └─ detalles_procesados (JSON)
11. Retorna JSON con estadísticas
12. Frontend muestra resumen con SweetAlert2
```

### 7.3 Workflow: Cliente Solicita Cotización (Flujo A)

```
1. Vendedor crea Cliente en /clientes
2. Vendedor crea Enlace en /enlaces
   ├─ Selecciona cliente
   ├─ Configura días_validos, mostrar_precios, mostrar_stock
   └─ Sistema genera token único
3. Sistema envía email a cliente con URL:
   /catalogo/{token}
4. Cliente hace clic en enlace
5. CatalogoController@show valida:
   ├─ Token existe
   ├─ Enlace activo
   ├─ No expirado
   └─ Si válido: incrementa visitas, actualiza ultimo_acceso
6. Cliente navega catálogo:
   ├─ Ve productos según categoria
   ├─ Ve precios si mostrar_precios = true
   └─ Ve stock si mostrar_stock = true
7. Cliente agrega productos a carrito (session)
8. Cliente envía cotización
9. POST /catalogo/solicitud
10. Backend crea SolicitudCotizacion:
    ├─ Genera numero_solicitud (SC-YYYYMMDDHHMMSS-XXXX)
    ├─ estado = pendiente
    ├─ cliente_id, enlace_acceso_id
    └─ monto_total calculado
11. Backend crea ItemSolicitudCotizacion por cada producto
12. Sistema envía email a vendedor asignado
13. Retorna JSON con número de solicitud
14. Frontend muestra confirmación
```

### 7.4 Workflow: Revisión y Aprobación de Cotización

```
1. Vendedor/Admin accede a /solicitudes
2. Ve listado de cotizaciones (filtradas por vendedor si aplica)
3. Hace clic en "Ver detalle"
4. SolicitudController@detalle muestra:
   ├─ Información del cliente
   ├─ Items cotizados (producto, cantidad, precio)
   ├─ Monto total
   ├─ Notas del cliente
   ├─ Disponibilidad de stock (en tiempo real)
   └─ Acciones disponibles
5. Vendedor decide:

   OPCIÓN A: Aprobar
   ├─ Hace clic en "Aplicar Cotización"
   ├─ Modal pregunta si descontar stock
   ├─ POST /solicitudes/{id}/aplicar
   ├─ Backend:
   │   ├─ Actualiza solicitud: estado = aplicada, aplicada_en = now(), aplicada_por = auth()->id()
   │   └─ Si descontar_stock = true:
   │       ├─ Por cada item:
   │       │   ├─ Valida stock disponible
   │       │   ├─ Crea MovimientoStock (tipo: salida, origen: venta)
   │       │   └─ Actualiza stock_productos (descuenta cantidad)
   │       └─ Vincula movimientos con solicitud_cotizacion_id
   ├─ Envía email al cliente (cotización aprobada)
   └─ Retorna JSON success

   OPCIÓN B: Rechazar
   ├─ Hace clic en "Rechazar Cotización"
   ├─ Modal solicita motivo
   ├─ POST /solicitudes/{id}/rechazar
   ├─ Backend:
   │   ├─ Actualiza solicitud: estado = rechazada, rechazada_en = now(),
   │   │   rechazada_por = auth()->id(), motivo_rechazo = motivo
   │   └─ Envía email al cliente (cotización rechazada con motivo)
   └─ Retorna JSON success

6. Frontend actualiza vista con nuevo estado
```

### 7.5 Workflow: Orden de Servicio Técnico

```
1. Cliente trae equipo al taller
2. Técnico/Admin accede a /servicio-tecnico/ordenes/form
3. Busca cliente:
   ├─ Si existe: selecciona
   └─ Si no existe: crea nuevo en modal (/servicio-tecnico/clientes/create)
4. Selecciona cliente → AJAX carga equipos del cliente
5. Busca equipo:
   ├─ Si está registrado: selecciona
   └─ Si no: puede crear nuevo en modal (/servicio-tecnico/equipos/create)
6. Completa información de orden:
   ├─ tipo_servicio (Reparación, Mantenimiento, etc.)
   ├─ prioridad (baja, media, alta, urgente)
   ├─ descripcion_problema (detallado)
   ├─ accesorios_entregados
   ├─ fecha_recepcion (default: hoy)
   ├─ fecha_promesa_entrega (opcional)
   └─ tecnico asignado (opcional)
7. Submit → STOrdenServicioController@store
8. Backend:
   ├─ Genera numero_orden (ST-YYYY-XXXXXX)
   ├─ estado = 'recibida' (si no hay técnico) o 'asignada' (si hay técnico)
   ├─ user_id = auth()->id()
   ├─ Guarda orden
   └─ Si tecnico_id presente:
       ├─ Crea registro en st_historial_estados (recibida → asignada)
       └─ Envía email al técnico
9. Redirect a detalle de orden
10. Técnico accede a orden:
    ├─ Sube imágenes de recepción
    ├─ Inicia trabajo (cambia estado a 'en_proceso')
    ├─ Agrega diagnóstico:
    │   ├─ Modal /servicio-tecnico/ordenes/{id}/diagnosticos
    │   ├─ Completa diagnóstico técnico
    │   └─ Marca si requiere repuestos
    ├─ Si requiere repuestos:
    │   ├─ Verifica stock en /servicio-tecnico/repuestos
    │   ├─ Si hay stock: agrega repuestos a orden
    │   │   └─ POST /servicio-tecnico/ordenes/{id}/repuestos
    │   │       ├─ Descuenta de st_repuestos.stock_actual
    │   │       ├─ Crea registro en st_repuestos_usados
    │   │       └─ Actualiza costo_repuestos y costo_total de orden
    │   └─ Si no hay stock: cambia estado a 'pendiente_repuestos'
    ├─ Realiza reparación
    ├─ Sube imágenes de reparación
    ├─ Actualiza costo_mano_obra
    └─ Marca como 'completada'
11. Notifica a cliente (email/SMS)
12. Cliente recoge equipo
13. Técnico marca como 'entregada'
14. Sistema registra fecha_entrega
```

### 7.6 Workflow: Movimiento de Stock

```
ENTRADA DE STOCK:
1. Usuario accede a /stock
2. Clic en "Registrar Entrada"
3. Modal muestra formulario
4. Busca producto (autocomplete AJAX)
5. Si tiene variantes: selecciona variante
6. Ingresa:
   ├─ cantidad (positiva)
   ├─ tipo: compra o devolucion
   ├─ referencia_documento (ej: Factura #12345)
   ├─ motivo (opcional)
   └─ ubicacion (opcional)
7. Submit → StockController@entrada
8. Backend:
   ├─ Busca registro en stock_productos
   ├─ stock_anterior = cantidad_disponible actual
   ├─ stock_nuevo = stock_anterior + cantidad
   ├─ Actualiza stock_productos.cantidad_disponible
   ├─ Crea MovimientoStock:
   │   ├─ tipo_movimiento = 'entrada'
   │   ├─ origen = 'compra' o 'devolucion'
   │   ├─ cantidad, stock_anterior, stock_nuevo
   │   ├─ usuario_id = auth()->id()
   │   └─ referencia_documento, motivo
   └─ Retorna JSON success
9. Frontend actualiza tabla y cierra modal

SALIDA DE STOCK:
Similar a entrada, pero:
- Valida que stock_disponible >= cantidad
- stock_nuevo = stock_anterior - cantidad
- tipo_movimiento = 'salida'
- origen = 'venta' o 'transferencia'

AJUSTE DE STOCK:
- Permite stock positivo o negativo
- tipo_movimiento = 'ajuste'
- origen = 'ajuste_inventario'
- Usado para correcciones de inventario físico
```

---

## 8. COMANDOS DE DESARROLLO

### 8.1 Instalación Inicial

```bash
# Clonar repositorio
git clone <repository-url>
cd portfolio

# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Copiar archivo de configuración
copy .env.example .env

# Generar app key
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nombre_bd
# DB_USERNAME=root
# DB_PASSWORD=

# Iniciar XAMPP (MySQL + Apache)

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (opcional)
php artisan db:seed

# Crear symlink para storage
php artisan storage:link

# Compilar assets
npm run build
```

### 8.2 Desarrollo Diario

```bash
# Terminal 1: Iniciar XAMPP
# (Interfaz gráfica de XAMPP)

# Terminal 2: Laravel dev server
php artisan serve
# Acceso: http://localhost:8000

# Terminal 3: Vite dev server (hot reload)
npm run dev
# Vite corre en http://localhost:5173
```

### 8.3 Base de Datos

```bash
# Ejecutar migraciones pendientes
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Rollback todas las migraciones
php artisan migrate:reset

# Rollback + migrate (refresh)
php artisan migrate:refresh

# Fresh: Drop todas las tablas + migrate
php artisan migrate:fresh

# Fresh + seeders
php artisan migrate:fresh --seed

# Ejecutar seeder específico
php artisan db:seed --class=RolesSeeder

# Crear nueva migración
php artisan make:migration create_tabla_nombre

# Crear modelo + migración
php artisan make:model NombreModelo -m

# Crear modelo + migración + controller + factory + seeder
php artisan make:model NombreModelo -a
```

### 8.4 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Con coverage
php artisan test --coverage

# Test específico
php artisan test --filter NombreTest

# PHPUnit directo
vendor/bin/phpunit

# Con configuración específica
vendor/bin/phpunit --configuration phpunit.xml
```

### 8.5 Cache y Optimización

```bash
# Limpiar todos los caches
php artisan optimize:clear

# Limpiar cache de configuración
php artisan config:clear

# Limpiar cache de rutas
php artisan route:clear

# Limpiar cache de vistas
php artisan view:clear

# Cachear configuración (producción)
php artisan config:cache

# Cachear rutas (producción)
php artisan route:cache

# Cachear vistas (producción)
php artisan view:cache
```

### 8.6 Assets y Build

```bash
# Desarrollo (watch mode)
npm run dev

# Build producción
npm run build

# Limpiar node_modules y reinstalar
rm -rf node_modules
npm install
```

### 8.7 Comandos Personalizados del Proyecto

```bash
# Inicializar stock de productos sin stock
# (Crea registros en stock_productos para productos con controlar_stock=true)
php artisan stock:inicializar

# Limpiar enlaces expirados
php artisan enlaces:limpiar-expirados

# Generar reporte de stock bajo (email)
php artisan stock:reporte-bajo
```

### 8.8 Mantenimiento

```bash
# Ver rutas registradas
php artisan route:list

# Buscar ruta específica
php artisan route:list --path=productos

# Ver rutas de un método
php artisan route:list --method=GET

# Información de la aplicación
php artisan about

# Modo mantenimiento ON
php artisan down

# Modo mantenimiento OFF
php artisan up

# Ver logs en tiempo real (requiere tail)
tail -f storage/logs/laravel.log
```

---

# PARTE 2: MANUAL DE USUARIO

---

## 9. INTRODUCCIÓN PARA USUARIOS

### 9.1 ¿Qué es este sistema?

Este sistema es una plataforma web que integra dos áreas de negocio:

1. **Tienda en Línea B2B**: Permite gestionar un catálogo de productos, enviar cotizaciones a clientes mediante enlaces temporales y controlar el inventario.

2. **Servicio Técnico**: Administra órdenes de reparación y mantenimiento de equipos de seguridad (cámaras, DVR, NVR, controles de acceso, etc.).

### 9.2 ¿Quién puede usar el sistema?

El sistema tiene tres tipos de usuarios:

#### Administrador
- **¿Qué puede hacer?**
  - Acceso completo a todos los módulos
  - Crear y gestionar usuarios
  - Configurar productos, categorías y precios
  - Ver todas las cotizaciones y órdenes de servicio
  - Generar reportes y métricas

#### Vendedor
- **¿Qué puede hacer?**
  - Gestionar sus clientes asignados
  - Crear enlaces de catálogo para sus clientes
  - Ver y aprobar cotizaciones de sus clientes
  - Controlar stock de productos
  - Crear cotizaciones en nombre de clientes

#### Técnico
- **¿Qué puede hacer?**
  - Ver y gestionar órdenes de servicio
  - Registrar diagnósticos y reparaciones
  - Agregar repuestos utilizados
  - Actualizar estado de órdenes
  - Gestionar equipos y clientes de servicio técnico

### 9.3 ¿Cómo ingreso al sistema?

1. Abra su navegador web (Chrome, Firefox, Edge)
2. Ingrese la dirección del sistema (proporcionada por su administrador)
3. Verá una pantalla de inicio de sesión
4. Ingrese su **email** y **contraseña**
5. Haga clic en **"Iniciar Sesión"**

**Nota:** Si olvidó su contraseña, haga clic en "¿Olvidaste tu contraseña?" y siga las instrucciones.

### 9.4 Navegación del Sistema

Una vez dentro, verá:

- **Barra lateral izquierda**: Menú principal con todos los módulos disponibles según su rol
- **Barra superior**: Su nombre de usuario y botón para salir
- **Área central**: Contenido del módulo seleccionado

**Módulos visibles según su rol:**

| Módulo | Administrador | Vendedor | Técnico |
|--------|---------------|----------|---------|
| Inicio | ✓ | ✓ | ✓ |
| Servicio Técnico | ✓ | ✗ | ✓ |
| Métricas | ✓ | ✗ | ✗ |
| Usuarios | ✓ | ✗ | ✗ |
| Categorías | ✓ | ✗ | ✗ |
| Productos | ✓ | ✗ | ✗ |
| Cotizaciones | ✓ | ✓ | ✗ |
| Clientes | ✓ | ✓ | ✗ |
| Catálogo | ✓ | ✓ | ✗ |
| Links | ✓ | ✓ | ✗ |
| Gestión de Stock | ✓ | ✓ | ✗ |

---

## 10. GUÍA DE CONFIGURACIÓN INICIAL

Esta sección explica el orden en que debe configurar el sistema la primera vez que lo usa.

⚠️ **IMPORTANTE**: Siga este orden exacto para evitar errores.

---

### PASO 1: Crear Usuarios

**¿Quién lo hace?** Solo Administradores

**¿Por qué es importante?** Los usuarios son necesarios para asignar vendedores a clientes y técnicos a órdenes de servicio.

#### Cómo crear un usuario:

1. En el menú lateral, haga clic en **"Usuarios"**
2. Haga clic en el botón **"+ Nuevo Usuario"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Nombre*** | SÍ | Nombre completo del usuario | Juan Pérez |
| **Email*** | SÍ | Correo electrónico (debe ser único) | juan@empresa.com |
| **Contraseña*** | SÍ | Contraseña de acceso (mínimo 6 caracteres) | 123456 |
| **Rol*** | SÍ | Tipo de usuario | Vendedor |

*\*Campo obligatorio*

4. Haga clic en **"Guardar"**

#### Roles disponibles:

- **admin**: Acceso total al sistema
- **vendedor**: Gestión de catálogo y ventas
- **tecnico**: Gestión de servicio técnico

**Consejo:** Cree al menos un usuario con rol "vendedor" antes de crear clientes.

---

### PASO 2: Crear Categorías

**¿Quién lo hace?** Solo Administradores

**¿Por qué es importante?** Los productos deben pertenecer a una categoría. Sin categorías, no puede crear productos.

#### Cómo crear una categoría:

1. En el menú lateral, haga clic en **"Categorías"**
2. Haga clic en el botón **"+ Nueva Categoría"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Nombre*** | SÍ | Nombre de la categoría | Cámaras IP |
| **Slug** | NO | Identificador en URL (se genera automáticamente) | camaras-ip |
| **Descripción** | NO | Descripción de la categoría | Cámaras de vigilancia con conexión IP |
| **Orden*** | SÍ | Orden de aparición en el catálogo (número) | 1 |

*\*Campo obligatorio*

4. Haga clic en **"Guardar"**

**Consejos:**
- Use números consecutivos para el orden (1, 2, 3...)
- Las categorías con menor número aparecen primero
- Puede cambiar el orden después editando la categoría

---

### PASO 3: Crear Productos

**¿Quién lo hace?** Solo Administradores

**¿Por qué es importante?** Los productos son el corazón del catálogo. Sin productos, los clientes no pueden solicitar cotizaciones.

⚠️ **REQUISITO**: Debe tener al menos una categoría creada.

#### Cómo crear un producto:

1. En el menú lateral, haga clic en **"Productos"**
2. Haga clic en el botón **"+ Nuevo Producto"**
3. Complete el formulario (tiene varias secciones):

#### SECCIÓN A: Información Básica

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Referencia*** | SÍ | Código único del producto (SKU) | CAM-IP-001 |
| **Nombre del Producto*** | SÍ | Nombre descriptivo | Cámara IP 2MP Domo |
| **Descripción** | NO | Descripción detallada del producto | Cámara domo con resolución 1080p, visión nocturna 30m |
| **Marca** | NO | Marca del producto | Hikvision |
| **Unidad de Venta*** | SÍ | Cómo se vende el producto | Unidad |
| **Unidad de Empaque*** | SÍ | Cómo se empaca | Caja |
| **Extensión (Color/Motivo)** | NO | Color, diseño o variante | Blanco |
| **Categoría*** | SÍ | Seleccione una categoría | Cámaras IP |
| **Tiene Variantes** | NO | ¿El producto viene en diferentes tallas/colores? | ☐ (desmarcado) |

*\*Campo obligatorio*

#### SECCIÓN B: Control de Stock

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Controlar Stock** | NO (marcado por defecto) | ¿Quiere llevar control de inventario? | ☑ (marcado) |
| **Permitir Venta Sin Stock** | NO | ¿Permitir vender aunque no haya inventario? | ☐ (desmarcado) |

**Si marcó "Controlar Stock" y NO marcó "Tiene Variantes":**

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Stock Inicial*** | SÍ | Cantidad inicial en inventario | 50 |
| **Stock Mínimo** | NO | Cantidad mínima (alerta de stock bajo) | 10 |
| **Stock Máximo** | NO | Cantidad máxima (opcional) | 200 |
| **Ubicación en Bodega** | NO | Dónde está almacenado | Bodega A, Estante 3 |

*\*Campo obligatorio solo al crear producto nuevo*

**Nota:** Si marca "Tiene Variantes", el stock se gestiona por cada variante, no aquí.

#### SECCIÓN C: Variantes (solo si marcó "Tiene Variantes")

Si su producto viene en diferentes tamaños o colores, marque "Tiene Variantes" y agregue filas:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Talla** | NO | Tamaño o medida | 4mm, 6mm, 8mm |
| **Color** | NO | Color del producto | Blanco, Negro |
| **SKU** | NO | Se genera automáticamente | CAM-IP-001-4MM-BLANCO |

**Ejemplo de variantes:**
- Cámara IP 2MP - Lente 4mm - Blanco
- Cámara IP 2MP - Lente 4mm - Negro
- Cámara IP 2MP - Lente 6mm - Blanco
- Cámara IP 2MP - Lente 6mm - Negro

Haga clic en "Agregar Variante" para cada combinación.

#### SECCIÓN D: Imágenes del Producto

| Campo | ¿Es obligatorio? | ¿Qué significa? | Restricciones |
|-------|------------------|-----------------|---------------|
| **Agregar Imágenes** | NO | Fotos del producto | JPG, PNG, WebP - Máx 2MB cada una |
| **Imagen Principal** | NO (pero recomendado) | La imagen destacada | Marque una con el radio button |

**Pasos:**
1. Haga clic en "Seleccionar archivos"
2. Elija una o varias imágenes
3. Marque una como "Imagen Principal"
4. Puede agregar más imágenes después editando el producto

#### SECCIÓN E: Precios por Lista

Verá una tabla con las listas de precios del sistema:

| Lista de Precio | ¿Es obligatorio? | ¿Qué significa? |
|-----------------|------------------|-----------------|
| Export 1 | NO | Precio para exportación tier 1 |
| Export 2 | NO | Precio para exportación tier 2 |
| Local 1 | NO | Precio local tier 1 |
| Local 2 | NO | Precio local tier 2 |
| Local 3 | NO | Precio local tier 3 |
| Local 4 | NO | Precio local tier 4 |

**Ingrese el precio para cada lista que use.** Puede dejarlo vacío si no aplica.

4. Revise toda la información
5. Haga clic en **"Guardar Producto"**

**Consejos:**
- La referencia debe ser única (no puede haber dos productos con la misma referencia)
- Si no sube imagen, el producto aparecerá con imagen por defecto
- Puede editar el producto después para agregar más imágenes o cambiar precios
- Si tiene muchos productos, puede importarlos desde Excel (opción avanzada)

---

### PASO 4: Crear Clientes B2B

**¿Quién lo hace?** Administradores y Vendedores

**¿Por qué es importante?** Necesita clientes registrados para generarles enlaces de catálogo y recibir cotizaciones.

⚠️ **REQUISITO**: Debe tener al menos un usuario con rol "vendedor" creado.

#### Cómo crear un cliente:

1. En el menú lateral, haga clic en **"Clientes"**
2. Haga clic en el botón **"+ Nuevo Cliente"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Identificación*** | SÍ | NIT o cédula del cliente | 900123456-7 |
| **Contacto*** | SÍ | Nombre de la persona de contacto | María García |
| **Email*** | SÍ | Correo electrónico (único) | maria@clienteempresa.com |
| **Teléfono** | NO | Teléfono de contacto | 601 234 5678 |
| **Departamento*** | SÍ | Departamento (carga ciudades) | Cundinamarca |
| **Ciudad*** | SÍ | Ciudad del cliente | Bogotá |
| **Vendedor*** | SÍ | Vendedor asignado | Juan Pérez (vendedor) |
| **Lista de Precio*** | SÍ | Lista de precios que verá el cliente | Local 1 |

*\*Campo obligatorio*

4. Haga clic en **"Guardar"**

**Notas importantes:**
- Si es **Vendedor**, automáticamente usted será el vendedor asignado (no puede cambiarlo)
- Si es **Administrador**, puede asignar cualquier vendedor
- Al seleccionar Departamento, las ciudades se cargan automáticamente
- La lista de precio determina qué precios verá este cliente en el catálogo

---

### PASO 5: Generar Enlaces de Acceso

**¿Quién lo hace?** Administradores y Vendedores

**¿Por qué es importante?** Los enlaces permiten que los clientes accedan al catálogo sin necesidad de crear una cuenta de usuario. Son temporales y personalizados.

⚠️ **REQUISITO**: Debe tener al menos un cliente creado.

#### Cómo crear un enlace:

1. En el menú lateral, haga clic en **"Links"**
2. Haga clic en el botón **"+ Nuevo Enlace"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Cliente*** | SÍ | Cliente que recibirá el enlace | María García |
| **Días de Validez*** | SÍ | Cuántos días estará activo el enlace | 7 |
| **Mostrar Precios*** | SÍ | ¿El cliente verá los precios? | Sí ●  No ○ |
| **Mostrar Stock*** | SÍ | ¿El cliente verá las cantidades disponibles? | Sí ●  No ○ |
| **Notas Internas** | NO | Notas que solo ve el vendedor | Contactar antes del 15 de noviembre |

*\*Campo obligatorio*

4. Haga clic en **"Generar Enlace"**

**¿Qué sucede después?**
- El sistema genera un enlace único
- Se calcula la fecha de expiración (hoy + días de validez)
- Se envía un email automático al cliente con el enlace
- El cliente puede hacer clic en el enlace y acceder al catálogo

**Ejemplo de enlace generado:**
```
https://misistema.com/catalogo/a3f5d8c9b2e1f4a6d7c8e9f0b1c2d3e4
```

#### Ver detalles del enlace:

En la lista de enlaces, haga clic en "Ver Detalle" para ver:
- URL completa (puede copiarla y enviarla por WhatsApp)
- Fecha de creación
- Fecha de expiración
- Número de visitas
- Último acceso del cliente
- Cotizaciones generadas con este enlace

#### Activar/Desactivar enlace:

Puede activar o desactivar un enlace manualmente:
- **Activo**: El cliente puede acceder
- **Inactivo**: El cliente verá mensaje "Enlace no disponible"

Esto es útil si quiere revocar el acceso antes de que expire.

---

## 11. MÓDULOS DE SERVICIO TÉCNICO

⚠️ **NOTA**: Esta sección es para Administradores y Técnicos únicamente.

---

### PASO 6: Crear Clientes de Servicio Técnico

**¿Quién lo hace?** Administradores y Técnicos

**¿Por qué es importante?** Necesita registrar a los clientes que traen equipos a reparar. Este módulo es independiente del módulo de Clientes B2B.

#### Cómo crear un cliente ST:

1. En el menú lateral, haga clic en **"Servicio"** → **"Clientes"**
2. Haga clic en el botón **"+ Nuevo Cliente"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Tipo de Cliente*** | SÍ | ¿Es persona natural o empresa? | Particular / Empresa |
| **Tipo Documento*** | SÍ | Tipo de identificación | CC, NIT, CE, Pasaporte |
| **Número de Documento*** | SÍ | Número de identificación (único) | 1234567890 |
| **Nombre Completo*** | SÍ | Nombre del cliente | Carlos Rodríguez |
| **Razón Social** | Solo si es Empresa | Nombre de la empresa | Seguridad Total SAS |
| **Celular*** | SÍ | Número de celular (para notificaciones) | 321 456 7890 |
| **Teléfono** | NO | Teléfono fijo | 601 234 5678 |
| **Email** | NO (pero recomendado) | Correo electrónico | carlos@mail.com |
| **Dirección** | NO | Dirección completa | Calle 123 #45-67 |
| **Ciudad** | NO | Ciudad | Bogotá |
| **Departamento** | NO | Departamento | Cundinamarca |
| **Observaciones** | NO | Notas adicionales | Cliente frecuente, pago de contado |
| **Cliente Activo** | NO (marcado por defecto) | ¿Está activo? | ☑ |

*\*Campo obligatorio*

4. Haga clic en **"Guardar"**

**Notas:**
- Si selecciona "Empresa" en Tipo de Cliente, aparecerá el campo "Razón Social"
- El número de documento debe ser único (no puede repetirse)
- El celular es obligatorio porque se usa para notificar al cliente sobre su orden

---

### PASO 7: Registrar Técnicos

**¿Quién lo hace?** Administradores y Técnicos

**¿Por qué es importante?** Los técnicos son quienes reparan los equipos. Al crear un técnico, el sistema automáticamente crea una cuenta de usuario con rol "tecnico".

#### Cómo crear un técnico:

1. En el menú lateral, haga clic en **"Servicio"** → **"Técnicos"**
2. Haga clic en el botón **"+ Nuevo Técnico"**
3. Complete el formulario:

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Código*** | SÍ | Código único del técnico | TEC001 |
| **Nombre Completo*** | SÍ | Nombre del técnico | Pedro Martínez |
| **Documento de Identidad*** | SÍ | Cédula (único) | 987654321 |
| **Email*** | SÍ | Correo electrónico (único) | pedro@empresa.com |
| **Teléfono*** | SÍ | Teléfono fijo | 601 234 5678 |
| **Celular*** | SÍ | Celular | 321 987 6543 |
| **Especialidad** | NO | Áreas de especialización | CCTV, Control de acceso, Alarmas |
| **Fecha de Ingreso** | NO | Fecha en que empezó a trabajar | 15/01/2024 |
| **Certificaciones** | NO | Certificados y cursos | Certificado Hikvision, Curso Dahua |

*\*Campo obligatorio*

4. Haga clic en **"Guardar"**

**¿Qué sucede automáticamente?**
- Se crea una cuenta de usuario con:
  - Email: el mismo del técnico
  - Contraseña: **12345678** (contraseña temporal)
  - Rol: **tecnico**

⚠️ **IMPORTANTE**: El técnico debe cambiar su contraseña la primera vez que inicie sesión.

---

### PASO 8: Registrar Equipos

**¿Quién lo hace?** Administradores y Técnicos

**¿Por qué es importante?** Registra los equipos de seguridad que los clientes tienen instalados. Esto crea un historial y facilita futuras órdenes de servicio.

⚠️ **REQUISITO**: Debe tener al menos un cliente ST creado.

#### Cómo crear un equipo:

1. En el menú lateral, haga clic en **"Servicio"** → **"Equipos"**
2. Haga clic en el botón **"+ Nuevo Equipo"**
3. Complete el formulario:

#### SECCIÓN A: Cliente Propietario

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Cliente*** | SÍ | Dueño del equipo | Carlos Rodríguez |

*\*Campo obligatorio*

#### SECCIÓN B: Información del Equipo

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Tipo de Equipo*** | SÍ | Categoría del equipo | Cámara IP / DVR / NVR / Monitor |
| **Marca** | NO | Fabricante | Hikvision |
| **Modelo** | NO | Modelo específico | DS-2CD2143G0-I |
| **Número de Serie*** | SÍ | Serial único del equipo | HK123456789 |
| **Dirección MAC** | NO | MAC address del equipo | 00:1A:2B:3C:4D:5E |
| **Dirección IP** | NO | IP del equipo en la red | 192.168.1.100 |
| **Especificaciones Técnicas** | NO | Detalles técnicos | 4MP, lente 2.8mm, IR 30m |

*\*Campo obligatorio*

#### SECCIÓN C: Ubicación e Instalación

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Ubicación de Instalación** | NO | Dónde está instalado | Entrada principal |
| **Fecha de Compra** | NO | Cuándo lo compró | 15/03/2024 |
| **Fecha de Instalación** | NO | Cuándo se instaló | 20/03/2024 |

#### SECCIÓN D: Garantía

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Equipo en Garantía** | NO | ¿Tiene garantía vigente? | ☑ Sí |
| **Fecha de Vencimiento** | Solo si marcó "En Garantía" | Cuándo vence la garantía | 15/03/2025 |

#### SECCIÓN E: Estado del Equipo

| Campo | ¿Es obligatorio? | ¿Qué significa? | Opciones |
|-------|------------------|-----------------|----------|
| **Estado Operacional*** | SÍ | Estado actual del equipo | Operativo / En reparación / Fuera de servicio / En bodega |

*\*Campo obligatorio*

4. Haga clic en **"Guardar Equipo"**

**Consejos:**
- El número de serie debe ser único (no puede registrar el mismo equipo dos veces)
- Si marca "Equipo en Garantía", debe ingresar la fecha de vencimiento
- El estado "Operativo" significa que funciona correctamente
- El estado cambia automáticamente a "En reparación" cuando se crea una orden de servicio

---

### PASO 9: Registrar Repuestos

**¿Quién lo hace?** Administradores y Técnicos

**¿Por qué es importante?** Controla el inventario de repuestos y permite agregar partes utilizadas a las órdenes de servicio.

#### Cómo crear un repuesto:

1. En el menú lateral, haga clic en **"Servicio"** → **"Repuestos"**
2. Haga clic en el botón **"+ Nuevo Repuesto"**
3. Complete el formulario:

#### SECCIÓN A: Información del Repuesto

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Código*** | SÍ | Código único del repuesto | REP-001 |
| **Nombre del Repuesto*** | SÍ | Descripción del repuesto | Lente 4mm |
| **Categoría*** | SÍ | Tipo de repuesto | Lente / Sensor / Fuente / Cable / Conector |
| **Marca Compatible** | NO | Marcas con las que funciona | Hikvision, Dahua |
| **Modelo Compatible** | NO | Modelos compatibles | DS-2CD21xx |
| **Descripción** | NO | Descripción detallada | Lente varifocal 4mm para cámaras IP |

*\*Campo obligatorio*

#### SECCIÓN B: Control de Inventario

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Stock Actual*** | SÍ | Cantidad disponible en bodega | 25 |
| **Stock Mínimo*** | SÍ | Alerta cuando llegue a esta cantidad | 5 |

*\*Campo obligatorio*

⚠️ **ALERTA**: Si el Stock Actual es menor o igual al Stock Mínimo, aparecerá una advertencia de "Stock Bajo".

#### SECCIÓN C: Precios

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Precio de Compra** | NO | Cuánto costó comprarlo | $15,000 |
| **Precio de Venta** | NO | Precio al cliente | $25,000 |

Si ingresa ambos precios, el sistema calcula automáticamente el margen de ganancia.

#### SECCIÓN D: Proveedor

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **Nombre del Proveedor** | NO | Quién suministra el repuesto | Distribuidora XYZ |

4. Haga clic en **"Guardar Repuesto"**

**Notas:**
- El código debe ser único
- Cuando use un repuesto en una orden, el stock se descuenta automáticamente
- Los repuestos con stock bajo aparecen resaltados en la lista

---

### PASO 10: Crear Órdenes de Servicio

**¿Quién lo hace?** Administradores y Técnicos

**¿Por qué es importante?** Las órdenes de servicio documentan todo el proceso de reparación, desde la recepción del equipo hasta la entrega.

⚠️ **REQUISITOS**:
- Cliente ST creado
- Equipo registrado (opcional, pero recomendado)
- Técnico registrado (opcional, se puede asignar después)

#### Cómo crear una orden de servicio:

1. En el menú lateral, haga clic en **"Servicio"** → **"Órdenes"**
2. Haga clic en el botón **"+ Nueva Orden"**
3. Complete el formulario:

#### SECCIÓN A: Información de la Orden

| Campo | ¿Es obligatorio? | ¿Qué significa? | Ejemplo |
|-------|------------------|-----------------|---------|
| **N° Orden** | SÍ (auto) | Número único generado automáticamente | ST-2024-000123 |
| **Fecha Recepción*** | SÍ | Cuándo se recibió el equipo (default: hoy) | 25/11/2024 |
| **Cliente*** | SÍ | Cliente que trae el equipo | Carlos Rodríguez |
| **Equipo** | NO | Equipo a reparar (se cargan los del cliente) | Cámara IP HK123456789 |
| **Tipo de Servicio*** | SÍ | Qué tipo de trabajo es | Reparación / Mantenimiento / Instalación |
| **Prioridad*** | SÍ | Urgencia del trabajo | Baja / Media / Alta / Urgente |
| **Descripción del Problema*** | SÍ | Qué reporta el cliente | No prende, no se ve imagen en el monitor |
| **Accesorios Entregados** | NO | Qué dejó el cliente | Fuente de poder, cable de red, manual |
| **Técnico Asignado** | NO | Quién trabajará en la orden | Pedro Martínez (TEC001) |
| **Fecha Promesa Entrega** | NO | Cuándo se entregará | 30/11/2024 |
| **Observaciones** | NO | Notas adicionales | Cliente solicita presupuesto antes de reparar |

*\*Campo obligatorio*

4. Haga clic en **"Guardar Orden"**

**¿Qué sucede después?**
- Se genera un número de orden único (ST-YYYY-XXXXXX)
- Si asignó técnico, el estado es "asignada" y se envía email al técnico
- Si NO asignó técnico, el estado es "recibida"
- Se puede imprimir una copia para entregar al cliente

#### Estados de una orden:

| Estado | ¿Qué significa? |
|--------|-----------------|
| **Recibida** | Orden creada, sin técnico asignado |
| **Asignada** | Se asignó técnico, pero aún no inicia trabajo |
| **En Proceso** | Técnico está trabajando en la reparación |
| **Pendiente Repuestos** | Esperando que lleguen repuestos |
| **Completada** | Reparación terminada, lista para entrega |
| **Entregada** | Cliente recogió el equipo |
| **Cancelada** | Orden cancelada (cliente no autorizó, no tiene reparación, etc.) |

---

## 12. OPERACIONES DIARIAS

### 12.1 Gestión de Stock

**¿Quién lo hace?** Administradores y Vendedores

#### Ver Estado del Stock

1. Haga clic en **"Gestión de Stock"** en el menú
2. Verá el dashboard con:
   - Productos con stock bajo (en rojo)
   - Productos sin stock
   - Movimientos del mes
   - Top productos más vendidos

#### Registrar Entrada de Stock (Compra)

1. En el dashboard de stock, haga clic en **"Registrar Entrada"**
2. Complete el formulario:

| Campo | ¿Qué significa? | Ejemplo |
|-------|-----------------|---------|
| **Producto** | Busque el producto por referencia o nombre | CAM-IP-001 |
| **Variante** | Si tiene variantes, seleccione cuál | Lente 4mm - Blanco |
| **Cantidad** | Cuántas unidades ingresan | 20 |
| **Tipo** | Compra o Devolución | Compra |
| **Documento** | Número de factura o remisión | FAC-12345 |
| **Motivo** | Razón del ingreso (opcional) | Compra a proveedor XYZ |
| **Ubicación** | Dónde se almacena (opcional) | Bodega A, Estante 3 |

3. Haga clic en **"Registrar Entrada"**

**¿Qué pasa?**
- El stock disponible aumenta
- Se crea un registro en el historial de movimientos
- Si el producto estaba en stock bajo, se quita la alerta

#### Registrar Salida de Stock (Venta/Transferencia)

Similar a la entrada, pero:
1. Haga clic en **"Registrar Salida"**
2. El sistema valida que haya stock suficiente
3. El stock disponible disminuye

#### Ajustar Stock (Corrección de Inventario)

Si encuentra que el stock en el sistema no coincide con el físico:

1. Haga clic en **"Ajustar Stock"**
2. Ingrese la cantidad real
3. El sistema calcula la diferencia y ajusta

**Ejemplo:**
- Stock en sistema: 50
- Stock físico (conteo real): 48
- Diferencia: -2
- Se registra salida de 2 unidades con origen "ajuste_inventario"

#### Ver Historial de Movimientos

1. Haga clic en **"Ver Historial"**
2. Puede filtrar por:
   - Producto específico
   - Rango de fechas
   - Tipo de movimiento
   - Usuario que lo hizo

### 12.2 Gestión de Cotizaciones

**¿Quién lo hace?** Administradores y Vendedores

#### Ver Cotizaciones

1. Haga clic en **"Cotizaciones"** en el menú
2. Verá listado de todas las cotizaciones
3. Puede filtrar por:
   - Estado (pendiente, aplicada, rechazada)
   - Cliente
   - Fecha
   - Vendedor (administrador ve todas, vendedor solo las suyas)

#### Ver Detalle de Cotización

1. Haga clic en el ícono de "Ver" (ojo) en una cotización
2. Verá:
   - Información del cliente
   - Lista de productos cotizados (referencia, nombre, cantidad, precio unitario, total)
   - Monto total de la cotización
   - Stock disponible actual (en tiempo real)
   - Notas del cliente

#### Aprobar Cotización

1. En el detalle de la cotización, haga clic en **"Aplicar Cotización"**
2. El sistema pregunta: **"¿Desea descontar el stock?"**
   - **Sí**: Se descuenta el stock de los productos (recomendado si es una venta confirmada)
   - **No**: No se descuenta stock (úselo si es solo una cotización sin venta)
3. Confirme la acción
4. La cotización cambia a estado "Aplicada"
5. Se envía email al cliente notificando la aprobación

#### Rechazar Cotización

1. En el detalle de la cotización, haga clic en **"Rechazar Cotización"**
2. Ingrese el **motivo del rechazo** (obligatorio)
   - Ejemplo: "Producto descontinuado", "Precio ha cambiado", "Cliente no respondió"
3. Confirme la acción
4. La cotización cambia a estado "Rechazada"
5. Se envía email al cliente con el motivo

#### Generar PDF de Cotización

1. En el detalle de la cotización, haga clic en **"Descargar PDF"**
2. Se genera un PDF con formato profesional
3. Puede enviarlo al cliente por email o WhatsApp

### 12.3 Gestión del Catálogo (Tienda a Tienda)

**¿Quién lo hace?** Administradores y Vendedores

Este flujo permite crear cotizaciones en nombre de un cliente (sin que el cliente acceda al sistema).

#### Crear Cotización para un Cliente

1. Haga clic en **"Catálogo"** en el menú
2. Verá una lista de sus clientes (o todos si es admin)
3. Haga clic en **"Ver Catálogo"** del cliente que desea
4. Navegue por el catálogo (verá los precios de la lista asignada al cliente)
5. Agregue productos al carrito:
   - Haga clic en un producto para ver detalles
   - Seleccione cantidad
   - Haga clic en "Agregar a Cotización"
6. Cuando termine, haga clic en **"Ver Carrito"**
7. Revise los productos:
   - Puede **editar el precio** manualmente si necesita hacer un descuento
   - Puede eliminar productos
8. Agregue notas si es necesario
9. Haga clic en **"Enviar Cotización"**
10. La cotización queda registrada con su nombre como creador

**Diferencias con el enlace público:**
- Usted crea la cotización en nombre del cliente
- Puede editar precios manualmente
- Puede ver todo el stock disponible
- No hay límite de tiempo

### 12.4 Trabajo con Órdenes de Servicio

**¿Quién lo hace?** Administradores y Técnicos

#### Ver Órdenes Asignadas

1. Haga clic en **"Servicio"** → **"Órdenes"**
2. Técnicos ven solo las órdenes asignadas a ellos
3. Administradores ven todas

#### Trabajar en una Orden

1. Haga clic en el número de orden para ver detalle
2. Verá las secciones:

**A. Información General**
- Datos del cliente, equipo, tipo de servicio
- Estado actual
- Fechas (recepción, inicio, finalización)

**B. Diagnóstico**
- Haga clic en **"Agregar Diagnóstico"**
- Complete:
  - Fallas encontradas
  - Diagnóstico técnico detallado
  - Reparaciones realizadas
  - Recomendaciones para el cliente
  - ¿Requiere repuestos? (Sí/No)
  - Si requiere repuestos, liste cuáles
  - Tiempo estimado (horas)
  - Costo estimado
- Guardar

**C. Repuestos Utilizados**
- Haga clic en **"Agregar Repuesto"**
- Busque el repuesto
- Ingrese cantidad
- El sistema:
  - Descuenta del stock de repuestos
  - Suma al costo total de la orden
  - Registra el uso

**D. Imágenes**
- Suba imágenes en cada etapa:
  - Recepción: Estado inicial del equipo
  - Diagnóstico: Fallas encontradas
  - Reparación: Proceso de reparación
  - Entrega: Estado final

**E. Cambiar Estado**
- Haga clic en **"Cambiar Estado"**
- Seleccione el nuevo estado
- Agregue observaciones si es necesario
- El cliente recibe notificación automática por email

#### Workflow Típico:

```
1. Recibida (creación)
   ↓ (asignar técnico)
2. Asignada
   ↓ (técnico inicia trabajo)
3. En Proceso
   ↓ (si necesita repuestos que no hay)
4. Pendiente Repuestos (opcional)
   ↓ (llegan repuestos, termina reparación)
5. Completada
   ↓ (cliente recoge equipo)
6. Entregada
```

#### Generar PDF de Orden

1. En el detalle de la orden, haga clic en **"Descargar PDF"**
2. El PDF incluye:
   - Información del cliente y equipo
   - Diagnóstico
   - Repuestos utilizados
   - Costos desglosados
   - Estado actual
3. Puede entregarlo al cliente o guardarlo en archivo

### 12.5 Dashboard y Métricas

**¿Quién lo hace?** Administradores

#### Dashboard Principal

Al iniciar sesión, verá:
- Resumen de cotizaciones pendientes
- Alertas de stock bajo
- Órdenes de servicio urgentes
- Accesos rápidos a módulos

#### Métricas (Solo Admin)

1. Haga clic en **"Métricas"** en el menú
2. Verá:
   - **Valor Total Cotizado**: Suma de todas las cotizaciones del período
   - **Cotizaciones Aplicadas**: Cantidad y valor
   - **Cotizaciones Rechazadas**: Cantidad y valor
   - **Cotizaciones Pendientes**: Cantidad y valor
   - **Tasa de Conversión**: % de cotizaciones aplicadas
   - **Por Vendedor**: Desempeño individual
3. Puede filtrar por:
   - Rango de fechas
   - Vendedor específico

#### Dashboard de Servicio Técnico

1. Haga clic en **"Servicio"** → **"Dashboard"**
2. Verá:
   - Órdenes activas por estado
   - Órdenes urgentes pendientes
   - Técnicos disponibles
   - Repuestos con stock bajo
   - Gráficos de tendencias

---

## 13. FLUJOS DE TRABAJO COMPLETOS

### 13.1 Flujo: Cliente B2B Solicita Cotización

**Actores:** Vendedor, Cliente, Admin

```
DÍA 1:
1. VENDEDOR crea un nuevo Cliente
   - Ingresa datos del cliente
   - Asigna lista de precio "Local 1"

2. VENDEDOR genera Enlace de acceso
   - Selecciona el cliente
   - Configura: 7 días de validez, mostrar precios SÍ, mostrar stock SÍ
   - El sistema envía email automático al cliente

DÍA 2:
3. CLIENTE recibe email
   - Hace clic en el enlace
   - Accede al catálogo sin necesidad de registrarse

4. CLIENTE navega catálogo
   - Ve productos organizados por categorías
   - Ve precios según su lista asignada (Local 1)
   - Ve stock disponible

5. CLIENTE agrega productos
   - Cámara IP 2MP x 10 unidades
   - DVR 16 canales x 1 unidad
   - Cable UTP x 5 rollos

6. CLIENTE envía cotización
   - Agrega nota: "Necesito instalación incluida"
   - Confirma envío
   - Sistema genera número: SC-20241125143022-AB12

DÍA 3:
7. VENDEDOR recibe notificación por email
   - Revisa cotización en el módulo "Cotizaciones"

8. VENDEDOR revisa stock
   - Verifica que hay stock de todos los productos
   - Si faltara algo, puede contactar al cliente antes de aprobar

9. VENDEDOR decide:

   OPCIÓN A - Aprobar:
   - Hace clic en "Aplicar Cotización"
   - Marca "Sí" en descontar stock
   - El sistema descuenta el stock automáticamente
   - Cliente recibe email: "Tu cotización ha sido aprobada"

   OPCIÓN B - Rechazar:
   - Hace clic en "Rechazar"
   - Motivo: "Producto DVR descontinuado, ofrecemos modelo nuevo"
   - Cliente recibe email con el motivo

10. VENDEDOR genera PDF
    - Descarga PDF profesional de la cotización
    - Lo envía al cliente por WhatsApp
```

### 13.2 Flujo: Reparación de Equipo de Seguridad

**Actores:** Cliente, Técnico, Admin

```
DÍA 1 - RECEPCIÓN:
1. CLIENTE llega al taller
   - Trae cámara IP que no funciona
   - Reporta: "No se ve imagen, está desconectada"

2. TÉCNICO verifica si el cliente está registrado
   - Busca por nombre o documento
   - Si NO existe: crea nuevo Cliente ST
     * Nombre: Carlos Rodríguez
     * Celular: 321 456 7890
     * Email: carlos@mail.com

3. TÉCNICO verifica si el equipo está registrado
   - Busca por número de serie
   - Si NO existe: crea nuevo Equipo
     * Tipo: Cámara IP
     * Marca: Hikvision
     * Modelo: DS-2CD2143G0-I
     * Serie: HK123456789
     * Cliente: Carlos Rodríguez

4. TÉCNICO crea Orden de Servicio
   - Cliente: Carlos Rodríguez
   - Equipo: Cámara IP HK123456789
   - Tipo de servicio: Reparación
   - Prioridad: Media
   - Descripción: "No se ve imagen, equipo no responde al ping"
   - Accesorios: "Fuente de poder original"
   - Técnico asignado: Pedro Martínez
   - Fecha promesa: 28/11/2024
   - Sistema genera: ST-2024-000156

5. TÉCNICO sube fotos de recepción
   - Foto del equipo
   - Foto de la etiqueta con el serial
   - Estado físico del equipo

6. TÉCNICO imprime recibo
   - Entrega copia al cliente con número de orden
   - Cliente se retira

DÍA 2 - DIAGNÓSTICO:
7. TÉCNICO inicia trabajo
   - Cambia estado a "En Proceso"
   - Sistema envía notificación al cliente

8. TÉCNICO realiza pruebas
   - Conecta equipo a banco de pruebas
   - Identifica falla: fuente de poder defectuosa

9. TÉCNICO agrega diagnóstico
   - Fallas encontradas: "Fuente de poder con corto circuito, sensor dañado"
   - Diagnóstico: "Requiere reemplazo de fuente y sensor de imagen"
   - Reparaciones: "Pendiente autorización del cliente"
   - Requiere repuestos: SÍ
   - Repuestos necesarios: "Fuente 12V 2A, Sensor CMOS 1/3"
   - Costo estimado: $150,000

10. TÉCNICO verifica stock de repuestos
    - Entra a "Servicio" → "Repuestos"
    - Fuente 12V 2A: Stock 3 ✓
    - Sensor CMOS: Stock 0 ✗

11. TÉCNICO cambia estado
    - Estado: "Pendiente Repuestos"
    - Observaciones: "Esperando llegada de sensor"
    - Cliente recibe notificación automática

DÍA 4 - REPUESTOS LLEGAN:
12. ADMIN registra entrada de repuestos
    - Recibe pedido del proveedor
    - Entra a "Repuestos" → Editar "Sensor CMOS"
    - Actualiza stock: 0 → 5

13. TÉCNICO es notificado
    - Ve que ya hay stock del repuesto faltante
    - Cambia estado a "En Proceso"

DÍA 5 - REPARACIÓN:
14. TÉCNICO realiza reparación
    - Reemplaza fuente de poder
    - Instala nuevo sensor
    - Prueba equipo: funciona correctamente

15. TÉCNICO agrega repuestos usados
    - "Agregar Repuesto" en la orden
    - Fuente 12V 2A - Cantidad: 1 - Precio: $25,000
    - Sensor CMOS - Cantidad: 1 - Precio: $80,000
    - Sistema descuenta automáticamente del stock
    - Suma al costo de repuestos: $105,000

16. TÉCNICO actualiza costos
    - Mano de obra: $45,000
    - Repuestos: $105,000 (auto-calculado)
    - Total: $150,000

17. TÉCNICO sube fotos de reparación
    - Equipo reparado
    - Pruebas realizadas

18. TÉCNICO cambia estado
    - Estado: "Completada"
    - Cliente recibe notificación: "Su equipo está listo para recoger"

DÍA 6 - ENTREGA:
19. CLIENTE llega a recoger equipo
    - TÉCNICO muestra equipo funcionando
    - Cliente aprueba la reparación

20. TÉCNICO genera PDF
    - Incluye diagnóstico, repuestos, costos
    - Cliente firma conformidad

21. TÉCNICO cambia estado final
    - Estado: "Entregada"
    - Fecha entrega: 30/11/2024
    - Cliente paga y se retira con equipo

22. SISTEMA registra todo
    - Historial completo en la orden
    - Equipo vuelve a estado "Operativo"
    - Costos y repuestos trazables
```

---

## 14. PREGUNTAS FRECUENTES

### 14.1 Acceso y Seguridad

**P: ¿Olvidé mi contraseña, qué hago?**

R:
1. En la pantalla de inicio de sesión, haga clic en "¿Olvidaste tu contraseña?"
2. Ingrese su email
3. Recibirá un correo con un enlace para restablecer
4. Haga clic en el enlace
5. Ingrese su nueva contraseña (mínimo 6 caracteres)
6. Confirme la nueva contraseña

**P: ¿Cómo cambio mi contraseña?**

R:
1. Inicie sesión
2. Haga clic en su nombre en la esquina superior derecha
3. Seleccione "Perfil"
4. Haga clic en "Cambiar Contraseña"
5. Ingrese contraseña actual
6. Ingrese nueva contraseña
7. Confirme nueva contraseña
8. Guardar

**P: ¿Por qué no veo el módulo de "Productos"?**

R: Los módulos visibles dependen de su rol:
- Si es **Vendedor o Técnico**: No tiene acceso a Productos (solo admin)
- Si es **Admin**: Verifique que haya iniciado sesión correctamente
- Contacte a su administrador si cree que debería tener acceso

### 14.2 Productos y Catálogo

**P: ¿Puedo crear un producto sin imagen?**

R: Sí, las imágenes son opcionales. El producto aparecerá con una imagen por defecto (placeholder).

**P: ¿Qué es una variante de producto?**

R: Una variante es una versión del mismo producto con diferentes atributos. Por ejemplo:
- Producto: Cámara IP 2MP
- Variantes:
  - Cámara IP 2MP - Lente 4mm - Blanco
  - Cámara IP 2MP - Lente 4mm - Negro
  - Cámara IP 2MP - Lente 6mm - Blanco

**P: ¿Cómo actualizo los precios de muchos productos a la vez?**

R:
1. Vaya a "Productos"
2. En la sección "Actualización Masiva de Precios", haga clic en "Descargar Plantilla CSV"
3. Abra el archivo en Excel
4. Llene las columnas de precios (mantenga la referencia de producto)
5. Guarde el archivo
6. En "Productos", suba el archivo
7. El sistema procesará y mostrará el resultado

**P: ¿Puedo vender un producto aunque no tenga stock?**

R: Sí, si en la configuración del producto marcó "Permitir Venta Sin Stock". Si no, el sistema no permitirá agregar a cotizaciones productos sin stock.

### 14.3 Cotizaciones

**P: ¿Por cuánto tiempo es válido un enlace de catálogo?**

R: El tiempo lo define el vendedor al crear el enlace (de 1 a 365 días). Después de ese tiempo, el enlace expira automáticamente.

**P: ¿Puedo desactivar un enlace antes de que expire?**

R: Sí:
1. Vaya a "Links"
2. Busque el enlace
3. Haga clic en "Ver Detalle"
4. Haga clic en "Desactivar"
El cliente ya no podrá acceder, incluso si la fecha de expiración no ha llegado.

**P: ¿Qué pasa si apruebo una cotización y marco "descontar stock"?**

R: El sistema automáticamente:
- Descuenta las cantidades de cada producto cotizado
- Crea registros en el historial de movimientos
- Si algún producto queda con stock bajo, activa la alerta

**P: ¿Puedo editar el precio en una cotización?**

R:
- Si usa el **Catálogo Tienda a Tienda** (vendedor crea cotización): SÍ, puede editar precios antes de enviar
- Si el **cliente envía cotización vía enlace**: NO, los precios se toman de la lista asignada al cliente

### 14.4 Stock

**P: ¿Qué significa "Stock Bajo"?**

R: Un producto tiene "stock bajo" cuando:
- Tiene activado "Controlar Stock"
- Tiene activada la alerta "Alerta Stock Bajo"
- La cantidad disponible es menor o igual al stock mínimo configurado

**P: ¿Cómo corrijo el stock si conté mal?**

R:
1. Vaya a "Gestión de Stock"
2. Haga clic en "Ajustar Stock"
3. Busque el producto
4. Ingrese la cantidad real (lo que contó físicamente)
5. El sistema ajusta la diferencia automáticamente

**P: ¿Puedo ver quién hizo un movimiento de stock?**

R: Sí:
1. Vaya a "Gestión de Stock" → "Ver Historial"
2. Busque el movimiento
3. Verá la columna "Usuario" con el nombre de quien lo hizo

### 14.5 Servicio Técnico

**P: ¿Cuál es la diferencia entre un Cliente y un Cliente ST?**

R:
- **Cliente** (B2B): Empresas o personas que compran productos del catálogo
- **Cliente ST**: Personas o empresas que traen equipos a reparar

Son registros independientes. Un mismo cliente puede estar en ambos módulos.

**P: ¿Qué contraseña se genera automáticamente para los técnicos?**

R: Al crear un técnico, el sistema crea una cuenta de usuario con contraseña: **12345678**

⚠️ El técnico debe cambiarla en su primer inicio de sesión.

**P: ¿Puedo registrar una orden sin asignar técnico?**

R: Sí, el técnico es opcional al crear la orden. El estado inicial será "Recibida". Puede asignar técnico después, y el estado cambiará a "Asignada".

**P: ¿Qué pasa si uso un repuesto que no tengo en stock?**

R: El sistema no lo permite. Si intenta agregar un repuesto con stock 0, aparecerá un error. Debe:
1. Registrar entrada de ese repuesto primero
2. Luego agregarlo a la orden

**P: ¿Puedo cancelar una orden de servicio?**

R: Sí:
1. Abra la orden
2. Haga clic en "Cambiar Estado"
3. Seleccione "Cancelada"
4. Ingrese observaciones (motivo de cancelación)
5. Guardar

### 14.6 Errores Comunes

**P: Error: "El campo email ya ha sido tomado"**

R: Ya existe un usuario o cliente con ese email. Los emails deben ser únicos. Use otro email o busque el registro existente.

**P: Error: "La referencia ya existe"**

R: Ya hay un producto con esa referencia. Las referencias deben ser únicas. Use otra referencia o edite el producto existente.

**P: Error: "Stock insuficiente"**

R: Está intentando:
- Sacar más stock del que hay disponible, o
- Aprobar una cotización con productos sin stock

Verifique el stock disponible antes de la operación.

**P: Error: "Enlace expirado o inactivo"**

R: El cliente está intentando acceder a un enlace que:
- Ya expiró (pasó la fecha de vencimiento), o
- Fue desactivado manualmente

Genere un nuevo enlace para el cliente.

**P: La página se ve sin estilos (solo texto)**

R:
1. Presione Ctrl + F5 para recargar la página
2. Si persiste, verifique que el servidor Vite esté corriendo: `npm run dev`
3. Si está en producción, ejecute: `npm run build`

---

## 15. GLOSARIO

**Admin / Administrador**: Usuario con acceso total al sistema.

**Ajuste de Stock**: Corrección de inventario para que el sistema refleje la cantidad física real.

**B2B**: Business to Business (negocio a negocio). Ventas entre empresas, no al consumidor final.

**Catálogo**: Listado de productos disponibles para cotización.

**Cliente B2B**: Empresa o persona que compra productos del catálogo.

**Cliente ST**: Persona o empresa que trae equipos a reparar en servicio técnico.

**Cotización**: Solicitud de precios enviada por un cliente para un conjunto de productos.

**DVR**: Digital Video Recorder. Grabador de video digital para cámaras análogas.

**Enlace de Acceso**: URL temporal y única que permite a un cliente acceder al catálogo sin registrarse.

**Estado de Orden**: Etapa actual de una orden de servicio (recibida, en proceso, completada, etc.).

**Inventario**: Cantidad de productos disponibles en bodega.

**IP**: Internet Protocol. En este contexto, se refiere a cámaras con conexión de red.

**Lista de Precio**: Conjunto de precios para productos. Hay 6 listas: Export 1-2, Local 1-4.

**Movimiento de Stock**: Registro de entrada o salida de productos del inventario.

**NVR**: Network Video Recorder. Grabador de video para cámaras IP.

**Orden de Servicio**: Documento que registra la recepción, diagnóstico y reparación de un equipo.

**Producto**: Artículo disponible en el catálogo para venta.

**Referencia**: Código único que identifica un producto (también llamado SKU).

**Repuesto**: Parte o componente usado para reparar equipos en servicio técnico.

**ROL**: Tipo de usuario (admin, vendedor, técnico) que determina qué módulos puede ver.

**SKU**: Stock Keeping Unit. Código único de producto.

**Stock**: Cantidad de un producto disponible en inventario.

**Stock Bajo**: Alerta que indica que un producto llegó al nivel mínimo de inventario.

**Stock Disponible**: Cantidad real disponible para venta (stock total - stock reservado).

**Stock Reservado**: Cantidad apartada para cotizaciones pendientes.

**Técnico**: Usuario que trabaja en reparaciones de equipos en el módulo de servicio técnico.

**Token**: Código único y aleatorio usado en los enlaces de acceso.

**Variante**: Versión de un producto con atributos diferentes (ej: diferente tamaño o color).

**Vendedor**: Usuario que gestiona clientes y cotizaciones del catálogo B2B.

---

# FIN DE LA DOCUMENTACIÓN

**Versión:** 1.0
**Fecha de actualización:** Noviembre 2025
**Sistema:** Portfolio B2B + Servicio Técnico
**Framework:** Laravel 9.52

Para soporte técnico o preguntas adicionales, contacte a su administrador del sistema.

---
