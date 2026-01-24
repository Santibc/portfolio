# 📋 PLAN MAESTRO - ERP MANZER AGROFORESTAL

## 📖 INSTRUCCIONES DE USO DE ESTE DOCUMENTO

> **⚠️ IMPORTANTE - LEE ESTO PRIMERO**

Este documento es la **FUENTE DE VERDAD** del proyecto ERP MANZER AGROFORESTAL. Contiene toda la información sobre la base de datos, modelos, arquitectura y módulos a desarrollar.

### Cómo usar este documento:

1. **Antes de iniciar CUALQUIER módulo:**
   - Claude DEBE leer este documento completo
   - Revisar especialmente la sección "DISEÑO DE BASE DE DATOS"
   - Entender qué tablas y relaciones están disponibles

2. **Durante el desarrollo:**
   - Seguir la arquitectura definida (patrones, principios SOLID)
   - Respetar las relaciones entre modelos
   - No crear tablas nuevas sin antes consultar

3. **Después de completar un módulo:**
   - Si se agregaron campos nuevos a tablas → **ACTUALIZAR** este documento
   - Si se crearon migraciones adicionales → **DOCUMENTAR** aquí
   - Si se modificó alguna relación → **REFLEJAR** el cambio
   - Marcar el módulo como completado en la sección correspondiente
   - ⚠️ **ACTUALIZAR NAVEGACIÓN:** Agregar el nuevo módulo en el menú lateral con directivas `@role()` según corresponda

4. **Reglas de oro:**
   - Este .md se mantiene actualizado en TODO momento
   - Cualquier desviación del plan SE DOCUMENTA aquí
   - Es la única verdad - no confiar en memoria ni conversaciones anteriores
   - Al inicio de cada sesión, leer las secciones relevantes
   - ⚠️ **NUNCA HACER COMMITS** - Solo el usuario hace commits en Git

---

## ⚠️ REGLAS CRÍTICAS DEL PROYECTO

### 🗂️ ALMACENAMIENTO DE ARCHIVOS
```
⛔ NUNCA usar `storage/` ni `php artisan storage:link`
✅ TODOS los archivos van en `public/`
```

**Estructura de carpetas:**
```
public/
└── uploads/
    ├── obras/{obra_id}/
    │   ├── documentos/
    │   ├── imagenes/
    │   └── partes_diarios/
    ├── trabajadores/{trabajador_id}/
    │   ├── nominas/
    │   ├── contratos/
    │   ├── formaciones/
    │   └── epis/
    ├── maquinaria/{maquinaria_id}/
    │   ├── documentos/
    │   ├── checklists/
    │   └── mantenimientos/
    ├── vehiculos/{vehiculo_id}/
    │   └── documentos/
    ├── clientes/{cliente_id}/
    │   └── documentos/
    ├── subcontratas/{subcontrata_id}/
    │   ├── cae/
    │   └── documentos_obra/
    └── facturas/
```

**Razón:** El servidor de producción NO soporta enlaces simbólicos (symlinks)
- Usar `public_path()` en lugar de `storage_path()`
- URLs directas: `/uploads/obras/...` (sin `/storage/`)

### 🔔 ALERTAS Y NOTIFICACIONES
```
⛔ NUNCA usar `alert()`, `confirm()` o `prompt()` nativos de JavaScript
✅ SIEMPRE usar SweetAlert2
```

**Ejemplos de uso:**
```javascript
// Confirmación de eliminación
Swal.fire({
    title: '¿Eliminar registro?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        // Ejecutar eliminación
    }
});

// Toast de éxito
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Guardado correctamente',
    showConfirmButton: false,
    timer: 3000
});

// Error
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se pudo completar la operación'
});
```

### 📅 FORMATO DE FECHAS
- **Base de datos:** `YYYY-MM-DD` o `YYYY-MM-DD HH:MM:SS`
- **Visualización:** `DD/MM/YYYY` (formato español)
- Usar Carbon para manipulación de fechas

### 💰 FORMATO DE MONEDA
- **Base de datos:** `DECIMAL(12,2)`
- **Visualización:** `1.234,56 €` (formato español)
- Usar `number_format($valor, 2, ',', '.')` para mostrar

### 🎨 COMPONENTES UI REUTILIZABLES
```
✅ SIEMPRE usar componentes de `resources/views/components/manzer/`
✅ Si un componente no existe, CREARLO antes de usarlo
⛔ NUNCA crear HTML repetitivo sin usar componentes
```

**Componentes Disponibles:**

| Componente | Uso | Ejemplo |
|------------|-----|---------|
| `<x-manzer.page-header>` | Encabezados de página | `<x-manzer.page-header title="Título" description="Desc">` |
| `<x-manzer.stat-card>` | Cards de resumen (iconos 64px) | `<x-manzer.stat-card icon="bi bi-icon" value="10" title="Label" color="primary">` |
| `<x-manzer.badge>` | Badges de estado | `<x-manzer.badge variant="success">Activo</x-manzer.badge>` |
| `<x-manzer.button>` | Botones estilizados | `<x-manzer.button variant="primary" icon="bi bi-plus">Texto</x-manzer.button>` |
| `<x-manzer.modal>` | Modales Bootstrap | `<x-manzer.modal id="miModal" title="Título">Contenido</x-manzer.modal>` |
| `<x-manzer.progress-bar>` | Barras de progreso | `<x-manzer.progress-bar :percentage="75" color="success">` |
| `<x-manzer.alert>` | Alertas | `<x-manzer.alert type="success" message="Mensaje">` |
| `<x-manzer.form-group>` | Campos de formulario | `<x-manzer.form-group label="Nombre" name="nombre" type="text">` |
| `<x-manzer.crew-card>` | Tarjeta de cuadrilla | `<x-manzer.crew-card :cuadrilla="$cuadrilla">` |

**Colores disponibles:** `primary` (verde), `success`, `warning`, `danger`, `info`, `secondary`

**Estructura de Vista Estándar:**
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- 1. Page Header --}}
    <x-manzer.page-header title="Título" description="Descripción">
        <x-slot name="actions">
            <x-manzer.button variant="primary">Acción</x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- 2. Summary Cards --}}
    <div class="summary-cards">
        <x-manzer.stat-card icon="bi bi-icon" :value="$count" title="Label" color="primary" />
    </div>

    {{-- 3. Filtros --}}
    <div class="filters-row">...</div>

    {{-- 4. Contenido Principal --}}
    ...
</div>
@endsection
```

---

## 🏢 INFORMACIÓN DEL CLIENTE

**Empresa:** MANZER AGROFORESTAL, S.R.L.U.
**Ubicación:** España (Cataluña principalmente)
**Sector:** Servicios forestales y agroforestales
**Actividades:** Desbroce, tala, poda, control de vegetación, trabajos para ADIF (líneas ferroviarias)

---

## 👥 ROLES Y PERMISOS

### Roles del Sistema

| Rol | Descripción | Permisos Principales |
|-----|-------------|---------------------|
| **Admin (root)** | Control total | Todo sin límites, incluyendo eliminar registros |
| **Contabilidad** | Gestión financiera | Nóminas, archivos, contabilidad, facturas. NO puede eliminar registros (solo crear/editar) |
| **Encargado** | Gestión operativa | Producción, obras, horarios, calendario. VE costes parciales (horas, maquinaria). NO VE márgenes ni rentabilidad final |
| **RRHH/Prevención** | Gestión de personal | Reconocimientos médicos, aptos, caducidades, formaciones |
| **Auditor** | Solo lectura | Acceso de solo lectura a todo (útil para licitaciones y revisiones) |
| **Trabajador** | Portal individual | Sus horas, vacaciones, documentos, nóminas, alertas personales |

### Matriz de Permisos Detallada

```
Módulo                    | Admin | Contab | Encarg | RRHH | Audit | Trabaj
--------------------------|-------|--------|--------|------|-------|--------
Usuarios                  |  CRUD |   R    |   -    |  R   |   R   |   -
Obras (ver todas)         |  CRUD |   R    |  CR*   |  R   |   R   |   -
Obras (rentabilidad)      |   ✓   |   ✓    |   ✗    |  ✗   |   ✓   |   ✗
Trabajadores              |  CRUD |   R    |   R    | CRUD |   R   |   -
Fichajes                  |  CRUD |   R    |  CRU   |  R   |   R   |  CR**
Partes diarios            |  CRUD |   R    |  CRUD  |  R   |   R   |   -
Maquinaria                |  CRUD |   R    |   R    |  R   |   R   |   -
Vehículos                 |  CRUD |   R    |   R    |  R   |   R   |   -
Clientes/CRM              |  CRUD |  CRU   |   R    |  -   |   R   |   -
Facturación               |  CRUD |  CRUD  |   -    |  -   |   R   |   -
Ingresos/Gastos           |  CRUD |  CRUD  |   R    |  -   |   R   |   -
EPIs                      |  CRUD |   R    |   R    | CRUD |   R   |   R***
Formaciones               |  CRUD |   R    |   R    | CRUD |   R   |   R***
Contratos/Garantías       |  CRUD |  CRU   |   R    |  R   |   R   |   -
Subcontratas              |  CRUD |  CRU   |   R    |  R   |   R   |   -
Dashboards                |   ✓   |  Fin   |  Ops   | RRHH |   ✓   |  Pers
Configuración             |  CRUD |   -    |   -    |  -   |   -   |   -

* Encargado: solo sus obras asignadas
** Trabajador: solo sus propios fichajes
*** Trabajador: solo lectura de sus propios registros
```

---

## 🗄️ DISEÑO DE BASE DE DATOS

### Base Existente (ya migrada)
El proyecto ya tiene las siguientes tablas de Laravel con Spatie Permission:
- `users`
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`
- `password_resets`
- `failed_jobs`
- `personal_access_tokens`
- `migrations`

### Nuevas Tablas a Crear

#### 1. MÓDULO DE TRABAJADORES

```sql
-- Trabajadores (empleados propios y datos extendidos)
CREATE TABLE trabajadores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL, -- Relación opcional con users (para acceso al portal)
    tipo_relacion ENUM('propio', 'subcontrata') DEFAULT 'propio',
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    dni VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) NULL,
    telefono VARCHAR(20) NULL,
    direccion TEXT NULL,
    fecha_nacimiento DATE NULL,
    fecha_alta DATE NOT NULL,
    fecha_baja DATE NULL,
    categoria_convenio VARCHAR(100) NULL,
    salario_bruto_mensual DECIMAL(10,2) NULL,
    coste_empresa_dia DECIMAL(10,2) NULL, -- Calculado: salario + SS + indirectos
    coste_hora DECIMAL(8,2) NULL,
    vacaciones_anuales INT DEFAULT 22, -- Días de vacaciones al año
    vacaciones_acumuladas DECIMAL(5,2) DEFAULT 0, -- Días acumulados pendientes
    antiguedad DATE NULL, -- Fecha inicio antigüedad
    subcontrata_id BIGINT UNSIGNED NULL, -- Si es de subcontrata
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL, -- Soft delete
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (subcontrata_id) REFERENCES subcontratas(id) ON DELETE SET NULL
);

-- Documentos de trabajadores
CREATE TABLE trabajador_documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('contrato', 'nomina', 'dni', 'ss', 'certificado_formacion', 'apto_medico', 'otro') NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    archivo_path VARCHAR(500) NOT NULL,
    fecha_documento DATE NULL,
    fecha_caducidad DATE NULL,
    visible_trabajador BOOLEAN DEFAULT FALSE, -- Si el trabajador puede verlo
    requiere_lectura BOOLEAN DEFAULT FALSE, -- Si requiere confirmación de lectura
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE
);

-- Lectura certificada de documentos (log inalterable)
CREATE TABLE documento_lecturas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_id BIGINT UNSIGNED NOT NULL,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    fecha_lectura TIMESTAMP NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    aceptado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Sin updated_at ni deleted_at: registro inmutable
    FOREIGN KEY (documento_id) REFERENCES trabajador_documentos(id) ON DELETE CASCADE,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE
);

-- Historial disciplinario
CREATE TABLE trabajador_historial_disciplinario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    tipo ENUM('amonestacion_verbal', 'amonestacion_escrita', 'sancion_leve', 'sancion_grave', 'sancion_muy_grave') NOT NULL,
    descripcion TEXT NOT NULL,
    documento_path VARCHAR(500) NULL,
    registrado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES users(id)
);
```

#### 2. MÓDULO DE FORMACIONES

```sql
-- Tipos de formación
CREATE TABLE formacion_tipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    duracion_horas INT NULL,
    periodicidad_meses INT NULL, -- Cada cuántos meses caduca (NULL = no caduca)
    obligatoria BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Formaciones de trabajadores
CREATE TABLE trabajador_formaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    formacion_tipo_id BIGINT UNSIGNED NOT NULL,
    fecha_realizacion DATE NOT NULL,
    fecha_caducidad DATE NULL,
    centro_formacion VARCHAR(255) NULL,
    certificado_path VARCHAR(500) NULL, -- Solo visible para admin
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    FOREIGN KEY (formacion_tipo_id) REFERENCES formacion_tipos(id)
);
```

#### 3. MÓDULO DE EPIs

```sql
-- Catálogo de EPIs
CREATE TABLE epi_catalogo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NULL, -- Ej: Protección cabeza, Protección altura, etc.
    tiene_caducidad BOOLEAN DEFAULT FALSE,
    requiere_revision BOOLEAN DEFAULT FALSE,
    periodicidad_revision_meses INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inventario de EPIs
CREATE TABLE epi_inventario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    epi_catalogo_id BIGINT UNSIGNED NOT NULL,
    numero_serie VARCHAR(100) NULL,
    fecha_compra DATE NULL,
    fecha_caducidad DATE NULL,
    coste DECIMAL(10,2) NULL,
    estado ENUM('disponible', 'asignado', 'en_revision', 'baja') DEFAULT 'disponible',
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (epi_catalogo_id) REFERENCES epi_catalogo(id)
);

-- Entregas de EPIs a trabajadores
CREATE TABLE epi_entregas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    epi_inventario_id BIGINT UNSIGNED NOT NULL,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    fecha_entrega DATE NOT NULL,
    fecha_devolucion DATE NULL,
    motivo_devolucion VARCHAR(255) NULL,
    firma_trabajador_path VARCHAR(500) NULL,
    entregado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (epi_inventario_id) REFERENCES epi_inventario(id),
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id),
    FOREIGN KEY (entregado_por) REFERENCES users(id)
);

-- Revisiones de EPIs
CREATE TABLE epi_revisiones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    epi_inventario_id BIGINT UNSIGNED NOT NULL,
    fecha_revision DATE NOT NULL,
    proxima_revision DATE NULL,
    resultado ENUM('apto', 'no_apto', 'requiere_reparacion') NOT NULL,
    observaciones TEXT NULL,
    realizado_por BIGINT UNSIGNED NOT NULL,
    documento_path VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (epi_inventario_id) REFERENCES epi_inventario(id),
    FOREIGN KEY (realizado_por) REFERENCES users(id)
);
```

#### 4. MÓDULO DE CUADRILLAS

```sql
-- Cuadrillas
CREATE TABLE cuadrillas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capataz_id BIGINT UNSIGNED NULL, -- Trabajador que lidera
    descripcion TEXT NULL,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (capataz_id) REFERENCES trabajadores(id) ON DELETE SET NULL
);

-- Miembros de cuadrilla
CREATE TABLE cuadrilla_trabajadores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuadrilla_id BIGINT UNSIGNED NOT NULL,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    fecha_incorporacion DATE NOT NULL,
    fecha_salida DATE NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cuadrilla_id) REFERENCES cuadrillas(id) ON DELETE CASCADE,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cuadrilla_trabajador_activo (cuadrilla_id, trabajador_id, activo)
);
```

#### 5. MÓDULO DE CLIENTES Y CRM

```sql
-- Clientes
CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('publico', 'privado') NOT NULL,
    nombre_comercial VARCHAR(255) NOT NULL,
    razon_social VARCHAR(255) NULL,
    cif VARCHAR(20) NULL,
    direccion TEXT NULL,
    codigo_postal VARCHAR(10) NULL,
    ciudad VARCHAR(100) NULL,
    provincia VARCHAR(100) NULL,
    pais VARCHAR(100) DEFAULT 'España',
    telefono VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    persona_contacto VARCHAR(150) NULL,
    telefono_contacto VARCHAR(20) NULL,
    email_contacto VARCHAR(255) NULL,
    condiciones_pago VARCHAR(100) NULL, -- Ej: "30 días", "60 días"
    retencion_porcentaje DECIMAL(5,2) DEFAULT 0, -- % de retención en obras
    notas TEXT NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Leads / Oportunidades comerciales
CREATE TABLE leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NULL, -- Puede ser cliente existente o nuevo
    nombre_empresa VARCHAR(255) NOT NULL,
    persona_contacto VARCHAR(150) NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    origen ENUM('contacto_directo', 'recomendacion', 'licitacion', 'web', 'otro') NOT NULL,
    descripcion TEXT NULL,
    importe_estimado DECIMAL(12,2) NULL,
    probabilidad INT DEFAULT 50, -- Porcentaje 0-100
    temperatura ENUM('frio', 'tibio', 'caliente') DEFAULT 'tibio',
    capacidad_economica_percibida ENUM('baja', 'media', 'alta') NULL,
    fecha_estimada_cierre DATE NULL,
    estado ENUM('nuevo', 'contactado', 'propuesta_enviada', 'negociacion', 'ganado', 'perdido') DEFAULT 'nuevo',
    motivo_perdida TEXT NULL,
    asignado_a BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (asignado_a) REFERENCES users(id) ON DELETE SET NULL
);

-- Historial de interacciones con leads/clientes
CREATE TABLE lead_interacciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id BIGINT UNSIGNED NULL,
    cliente_id BIGINT UNSIGNED NULL,
    tipo ENUM('llamada', 'email', 'reunion', 'visita', 'otro') NOT NULL,
    fecha DATETIME NOT NULL,
    descripcion TEXT NOT NULL,
    proximo_paso TEXT NULL,
    fecha_proximo_contacto DATE NULL,
    registrado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES users(id)
);
```

#### 6. MÓDULO DE OBRAS/PROYECTOS

```sql
-- Tipos de obra
CREATE TABLE obra_tipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- desbroce, tala, poda, emergencia, mixto
    descripcion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Obras/Proyectos
CREATE TABLE obras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- Código interno
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    cliente_id BIGINT UNSIGNED NOT NULL,
    obra_tipo_id BIGINT UNSIGNED NULL,
    
    -- Ubicación
    direccion TEXT NULL,
    localidad VARCHAR(150) NULL,
    provincia VARCHAR(100) NULL,
    codigo_postal VARCHAR(10) NULL,
    coordenadas_lat DECIMAL(10,8) NULL,
    coordenadas_lng DECIMAL(11,8) NULL,
    
    -- Datos ADIF (específicos para obras ferroviarias)
    linea VARCHAR(100) NULL, -- Ej: L220 E1
    trayecto VARCHAR(255) NULL, -- Ej: Calaf - Manresa
    pk_inicio VARCHAR(20) NULL,
    pk_fin VARCHAR(20) NULL,
    gerencia_jefatura VARCHAR(50) NULL, -- BCN, ZGZ, etc.
    distrito VARCHAR(100) NULL,
    
    -- Fechas
    fecha_inicio_prevista DATE NULL,
    fecha_fin_prevista DATE NULL,
    fecha_inicio_real DATE NULL,
    fecha_fin_real DATE NULL,
    
    -- Economía
    presupuesto DECIMAL(14,2) NULL,
    coste_estimado DECIMAL(14,2) NULL,
    margen_previsto DECIMAL(14,2) NULL,
    
    -- Estado y riesgo
    estado ENUM('presentada', 'aprobada', 'en_curso', 'pausada', 'finalizada', 'cancelada') DEFAULT 'presentada',
    riesgo_operativo ENUM('bajo', 'medio', 'alto') DEFAULT 'bajo',
    
    -- Penalizaciones
    tiene_penalizaciones BOOLEAN DEFAULT FALSE,
    importe_penalizacion_prevista DECIMAL(12,2) NULL,
    
    -- Contrato asociado
    contrato_id BIGINT UNSIGNED NULL,
    
    -- Centro de coste
    centro_coste VARCHAR(50) NULL,
    
    -- Responsables
    encargado_id BIGINT UNSIGNED NULL, -- Usuario encargado
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (obra_tipo_id) REFERENCES obra_tipos(id),
    FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE SET NULL,
    FOREIGN KEY (encargado_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Hitos de obra
CREATE TABLE obra_hitos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    porcentaje_obra INT NULL, -- Ej: 30 = 30% completado
    fecha_prevista DATE NULL,
    fecha_completado DATE NULL,
    importe_cobro DECIMAL(12,2) NULL, -- Cobro parcial asociado al hito
    completado BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE
);

-- Asignación de trabajadores a obras
CREATE TABLE obra_trabajadores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    rol VARCHAR(100) NULL, -- Ej: operario, capataz, aplicador
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE
);

-- Asignación de cuadrillas a obras
CREATE TABLE obra_cuadrillas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    cuadrilla_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (cuadrilla_id) REFERENCES cuadrillas(id) ON DELETE CASCADE
);

-- Documentos de obra
CREATE TABLE obra_documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('contrato', 'plano', 'permiso', 'acta', 'foto', 'informe', 'otro') NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    archivo_path VARCHAR(500) NOT NULL,
    descripcion TEXT NULL,
    fecha_documento DATE NULL,
    subido_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (subido_por) REFERENCES users(id)
);

-- Historial de cambios de estado de obra
CREATE TABLE obra_historial (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    estado_anterior VARCHAR(50) NULL,
    estado_nuevo VARCHAR(50) NOT NULL,
    comentario TEXT NULL,
    cambiado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (cambiado_por) REFERENCES users(id)
);
```

#### 7. MÓDULO DE FICHAJES Y CONTROL HORARIO

> **⚠️ ACTUALIZACIÓN (Enero 2026)**
> Se ha eliminado el constraint `UNIQUE KEY unique_fichaje_dia` para permitir **múltiples fichajes por día**.
> Un trabajador puede fichar entrada y salida múltiples veces en el mismo día (ej: jornada partida, emergencias).
> La lógica del controlador busca fichajes "abiertos" (sin hora_salida) en lugar de "cualquier fichaje del día".

```sql
-- Fichajes de trabajadores
CREATE TABLE fichajes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NULL,
    fecha DATE NOT NULL,

    -- Check-in
    hora_entrada TIME NULL,
    latitud_entrada DECIMAL(10,8) NULL,
    longitud_entrada DECIMAL(11,8) NULL,

    -- Check-out
    hora_salida TIME NULL,
    latitud_salida DECIMAL(10,8) NULL,
    longitud_salida DECIMAL(11,8) NULL,

    -- Calculado
    horas_trabajadas DECIMAL(5,2) NULL,
    horas_extra DECIMAL(5,2) DEFAULT 0,

    -- Validación
    validado BOOLEAN DEFAULT FALSE,
    validado_por BIGINT UNSIGNED NULL,
    fecha_validacion DATETIME NULL,

    -- Correcciones
    corregido BOOLEAN DEFAULT FALSE,
    corregido_por BIGINT UNSIGNED NULL,
    motivo_correccion TEXT NULL,

    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE SET NULL,
    FOREIGN KEY (validado_por) REFERENCES users(id),
    FOREIGN KEY (corregido_por) REFERENCES users(id)

    -- ⚠️ ELIMINADO: UNIQUE KEY unique_fichaje_dia (trabajador_id, fecha)
    -- Permite múltiples fichajes por día para jornadas partidas
);
```

#### 8. MÓDULO DE PARTES DIARIOS

> **⚠️ ACTUALIZACIÓN EN CURSO (Enero 2026)**
> Este módulo está siendo actualizado para soportar **conceptos de producción configurables por obra**.
> Los campos fijos (`desbroce_p5_m2`, `desbroce_p6_m2`, etc.) serán reemplazados por un sistema dinámico.
> Ver sección "8b. CONCEPTOS DE PRODUCCIÓN POR OBRA" más abajo.

```sql
-- Partes diarios de obra (VERSIÓN ACTUAL - SERÁ ACTUALIZADA)
CREATE TABLE partes_diarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    jornada ENUM('diurna', 'nocturna') DEFAULT 'diurna',
    
    -- Datos ADIF específicos
    linea VARCHAR(100) NULL,
    trayecto VARCHAR(255) NULL,
    gerencia_jefatura VARCHAR(50) NULL,
    distrito VARCHAR(100) NULL,
    
    -- Brigada
    brigada VARCHAR(100) NULL, -- MANZER, subcontrata, etc.
    
    -- Totales del día
    desbroce_total_m2 DECIMAL(12,2) DEFAULT 0,
    desbroce_p5_m2 DECIMAL(12,2) DEFAULT 0,
    desbroce_p6_m2 DECIMAL(12,2) DEFAULT 0,
    limpieza_p8_m2 DECIMAL(12,2) DEFAULT 0,
    herbicida_p4_m2 DECIMAL(12,2) DEFAULT 0,
    talas_unidades INT DEFAULT 0,
    podas_unidades INT DEFAULT 0,
    
    -- Observaciones
    observaciones TEXT NULL,
    incidencias TEXT NULL,
    
    -- Firmas
    encargado_firma VARCHAR(255) NULL,
    encargado_nombre VARCHAR(150) NULL,
    cliente_firma VARCHAR(255) NULL, -- ADIF u otro
    cliente_nombre VARCHAR(150) NULL,
    
    -- Estado
    estado ENUM('borrador', 'completado', 'validado') DEFAULT 'borrador',
    
    creado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES users(id)

    -- ⚠️ ELIMINADO: UNIQUE KEY unique_parte_obra_fecha (obra_id, fecha)
    -- Permite múltiples partes diarios por obra/fecha (ej: jornada diurna + nocturna)
);

-- Trabajadores en el parte diario
CREATE TABLE parte_diario_trabajadores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parte_diario_id BIGINT UNSIGNED NOT NULL,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    es_aplicador BOOLEAN DEFAULT FALSE, -- Aplicador de herbicida
    dni_aplicador VARCHAR(20) NULL, -- Solo si es aplicador
    horas_trabajadas DECIMAL(5,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parte_diario_id) REFERENCES partes_diarios(id) ON DELETE CASCADE,
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id)
);

-- Líneas de trabajo del parte diario
CREATE TABLE parte_diario_lineas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parte_diario_id BIGINT UNSIGNED NOT NULL,
    
    -- Tipo de trabajo
    herbicida BOOLEAN DEFAULT FALSE,
    desbroce BOOLEAN DEFAULT FALSE,
    poda BOOLEAN DEFAULT FALSE,
    tala BOOLEAN DEFAULT FALSE,
    limpieza BOOLEAN DEFAULT FALSE,
    
    -- Ubicación (puntos kilométricos)
    pk_inicio VARCHAR(20) NULL,
    pk_fin VARCHAR(20) NULL,
    
    -- Márgenes y medidas
    margen_izquierda BOOLEAN DEFAULT FALSE,
    ancho_izquierda DECIMAL(8,2) NULL,
    margen_derecha BOOLEAN DEFAULT FALSE,
    ancho_derecha DECIMAL(8,2) NULL,
    unidades INT NULL, -- Para talas/podas
    
    -- Metros cuadrados calculados
    metros_cuadrados DECIMAL(12,2) NULL,
    
    observaciones TEXT NULL,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parte_diario_id) REFERENCES partes_diarios(id) ON DELETE CASCADE
);

-- Herbicidas utilizados en el parte
CREATE TABLE parte_diario_herbicidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parte_diario_id BIGINT UNSIGNED NOT NULL,
    producto VARCHAR(255) NOT NULL,
    numero_registro VARCHAR(100) NULL,
    dosificacion VARCHAR(100) NULL,
    cantidad DECIMAL(10,2) NULL,
    unidad VARCHAR(20) NULL, -- litros, kg, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parte_diario_id) REFERENCES partes_diarios(id) ON DELETE CASCADE
);
```

#### 8b. CONCEPTOS DE PRODUCCIÓN POR OBRA (NUEVO)

> **🆕 Sistema Flexible de Producción**
> Reemplaza los campos fijos (P5, P6, P8, P4) por conceptos configurables por obra.
> Cada obra puede definir sus propios códigos, nombres y tarifas de producción.

```sql
-- Conceptos de producción configurables por obra
CREATE TABLE obra_conceptos_produccion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(20) NOT NULL,                    -- P5, P6, BOSQUE1, ZONA_A, etc.
    nombre VARCHAR(150) NOT NULL,                   -- Desbroce herbáceo, Limpieza, etc.
    descripcion TEXT NULL,
    categoria ENUM('desbroce', 'limpieza', 'herbicida', 'tala', 'poda', 'otro') NOT NULL,
    unidad ENUM('m2', 'unidades', 'hectareas', 'jornal') NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,         -- €/m², €/unidad, etc.
    activo BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,                             -- Para ordenar en formularios
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    UNIQUE KEY unique_obra_codigo (obra_id, codigo),
    INDEX idx_obra_activo (obra_id, activo)
);

-- Producciones registradas en partes diarios (reemplaza campos fijos)
CREATE TABLE parte_diario_producciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parte_diario_id BIGINT UNSIGNED NOT NULL,
    concepto_produccion_id BIGINT UNSIGNED NOT NULL,  -- FK a obra_conceptos_produccion
    cantidad DECIMAL(12,2) NOT NULL,                   -- Cantidad producida
    precio_unitario DECIMAL(10,2) NOT NULL,            -- Precio al momento (snapshot)
    importe_calculado DECIMAL(14,2) NOT NULL,          -- cantidad × precio_unitario
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (parte_diario_id) REFERENCES partes_diarios(id) ON DELETE CASCADE,
    FOREIGN KEY (concepto_produccion_id) REFERENCES obra_conceptos_produccion(id) ON DELETE CASCADE,
    UNIQUE KEY unique_parte_concepto (parte_diario_id, concepto_produccion_id),
    INDEX idx_parte_importe (parte_diario_id, importe_calculado)
);
```

**Ejemplo de uso:**
```sql
-- Para obra ADIF:
INSERT INTO obra_conceptos_produccion (obra_id, codigo, nombre, categoria, unidad, precio_unitario, orden) VALUES
(1, 'P5', 'Desbroce herbáceo', 'desbroce', 'm2', 0.08, 1),
(1, 'P6', 'Desbroce arbustivo', 'desbroce', 'm2', 0.14, 2),
(1, 'P8', 'Limpieza con recogida', 'limpieza', 'm2', 0.14, 3),
(1, 'P10', 'Talas <25cm', 'tala', 'unidades', 27.00, 4),
(1, 'P11', 'Talas 25-100cm', 'tala', 'unidades', 40.00, 5);

-- Para obra forestal privada:
INSERT INTO obra_conceptos_produccion (obra_id, codigo, nombre, categoria, unidad, precio_unitario, orden) VALUES
(2, 'BOSQUE1', 'Limpieza forestal', 'limpieza', 'hectareas', 150.00, 1),
(2, 'DESBROCE_A', 'Desbroce zona A', 'desbroce', 'm2', 0.12, 2);
```

#### 8c. DISCREPANCIAS DE VALORACIÓN ✅ COMPLETADO

> **✅ Control de Diferencias con Clientes**
> Registra discrepancias cuando el cliente (ej: ADIF) no acepta el total producido por Manzer.
> Permite tracking de importes pendientes por obra y período.
>
> **⚠️ ACTUALIZACIÓN (Enero 2026)**
> - Se ha eliminado el constraint `UNIQUE KEY unique_obra_periodo` para permitir **múltiples discrepancias por período**.
> - Documentos se almacenan en `public/uploads/obras/{obra_id}/discrepancias/` (NO en storage/).
> - Nombres de archivo incluyen timestamp para evitar colisiones: `valoracion_{obra_id}_{periodo}_{timestamp}.ext`

```sql
-- Discrepancias mensuales entre producido y aceptado por cliente
CREATE TABLE obra_discrepancias_valoracion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    periodo_mes VARCHAR(7) NOT NULL,                   -- Formato: 2025-01, 2025-02

    -- Valores Manzer
    importe_producido_manzer DECIMAL(14,2) NOT NULL,   -- Total que Manzer dice producir
    importe_validado_cuadrilla DECIMAL(14,2) NULL,     -- Confirmación del encargado/cuadrilla

    -- Valores Cliente
    importe_aceptado_cliente DECIMAL(14,2) NULL,       -- Lo que el cliente acepta pagar
    fecha_respuesta_cliente DATE NULL,

    -- Discrepancia
    importe_pendiente DECIMAL(14,2) NOT NULL,          -- Diferencia por aclarar
    estado ENUM('pendiente', 'parcial', 'resuelto') DEFAULT 'pendiente',

    -- Metadatos
    notas TEXT NULL,
    documento_valoracion_path VARCHAR(500) NULL,       -- Ruta en public/uploads/ (sin storage/)
    registrado_por BIGINT UNSIGNED NOT NULL,
    fecha_resolucion DATE NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES users(id),
    INDEX idx_obra_estado (obra_id, estado),
    INDEX idx_obra_periodo (obra_id, periodo_mes)

    -- ⚠️ ELIMINADO: UNIQUE KEY unique_obra_periodo (obra_id, periodo_mes)
    -- Permite múltiples discrepancias por período (ej: parciales, correcciones)
);
```

**Almacenamiento de documentos:**
```
public/uploads/obras/{obra_id}/discrepancias/
├── valoracion_4_2026-01_1737052800.pdf
├── valoracion_4_2026-01_1737139200.pdf  -- Otra discrepancia del mismo período
└── valoracion_4_2026-02_1739731200.jpg
```

**Flujo de uso:**
1. Fin de mes: Sistema calcula `importe_producido_manzer` = Σ(partes diarios valorados)
2. Encargado puede confirmar con `importe_validado_cuadrilla`
3. Admin registra `importe_aceptado_cliente` cuando cliente responde
4. Sistema calcula automáticamente: `importe_pendiente` = producido - aceptado
5. Se marca como `resuelto` cuando se aclara/cobra
6. **Se pueden crear múltiples discrepancias por período** si hay varias diferencias

#### 8d. BONOS Y PRIMAS MANUALES (NUEVO)

> **🆕 Gestión Manual de Bonos**
> Admin/Contabilidad registra primas, bonos y plus manualmente.
> Reemplaza el sistema automático de primas por producción.

```sql
-- Bonos y primas registrados manualmente
CREATE TABLE trabajador_bonos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NULL,                      -- Opcional: asociado a obra específica
    tipo ENUM('prima_produccion', 'bono_especial', 'plus_nocturnidad', 'otro') NOT NULL,
    concepto VARCHAR(255) NOT NULL,                    -- Descripción del bono
    fecha DATE NOT NULL,                                -- Fecha del bono
    importe DECIMAL(10,2) NOT NULL,
    pagado BOOLEAN DEFAULT FALSE,
    fecha_pago DATE NULL,
    notas TEXT NULL,
    registrado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE SET NULL,
    FOREIGN KEY (registrado_por) REFERENCES users(id),
    INDEX idx_trabajador_pagado (trabajador_id, pagado),
    INDEX idx_fecha (fecha)
);
```

**Ejemplo de uso:**
```sql
-- Registrar prima por superar producción
INSERT INTO trabajador_bonos (trabajador_id, obra_id, tipo, concepto, fecha, importe, registrado_por) VALUES
(15, 3, 'prima_produccion', 'Prima por superar 15,000m² en semana 12', '2025-03-25', 85.00, 1);

-- Registrar plus de nocturnidad
INSERT INTO trabajador_bonos (trabajador_id, tipo, concepto, fecha, importe, registrado_por) VALUES
(22, 'plus_nocturnidad', 'Plus trabajo nocturno 15-19 marzo', '2025-03-19', 120.00, 1);
```

#### 8e. ACTUALIZACIÓN DE TABLA PARTES_DIARIOS

> **Cambios a aplicar:**
> - ELIMINAR: `desbroce_total_m2`, `desbroce_p5_m2`, `desbroce_p6_m2`, `limpieza_p8_m2`, `herbicida_p4_m2`, `talas_unidades`, `podas_unidades`
> - AGREGAR: `importe_total_calculado` DECIMAL(14,2) DEFAULT 0

```sql
-- Migración a ejecutar
ALTER TABLE partes_diarios
DROP COLUMN desbroce_total_m2,
DROP COLUMN desbroce_p5_m2,
DROP COLUMN desbroce_p6_m2,
DROP COLUMN limpieza_p8_m2,
DROP COLUMN herbicida_p4_m2,
DROP COLUMN talas_unidades,
DROP COLUMN podas_unidades,
ADD COLUMN importe_total_calculado DECIMAL(14,2) DEFAULT 0 AFTER incidencias;
```

#### 8f. ACTUALIZACIÓN DE TABLA OBRAS

> **Campos adicionales para totales acumulados**

```sql
-- Agregar campos de tracking económico
ALTER TABLE obras
ADD COLUMN importe_producido_acumulado DECIMAL(14,2) DEFAULT 0 AFTER margen_previsto,
ADD COLUMN importe_pendiente_acumulado DECIMAL(14,2) DEFAULT 0 AFTER importe_producido_acumulado;
```

---

#### 9. MÓDULO DE MAQUINARIA

```sql
-- Tipos de maquinaria
CREATE TABLE maquinaria_tipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Motosierra, Sopladora, Desbrozadora, etc.
    descripcion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Maquinaria
CREATE TABLE maquinaria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquinaria_tipo_id BIGINT UNSIGNED NOT NULL,
    codigo_interno VARCHAR(50) UNIQUE NULL,
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    numero_serie VARCHAR(100) NULL,
    numero_bastidor VARCHAR(100) NULL,
    
    -- Económico
    fecha_compra DATE NULL,
    coste_adquisicion DECIMAL(12,2) NULL,
    vida_util_meses INT NULL,
    amortizacion_dia DECIMAL(8,2) NULL, -- €/día
    coste_hora DECIMAL(8,2) NULL,
    
    -- Estado
    estado ENUM('operativa', 'en_reparacion', 'baja') DEFAULT 'operativa',
    
    -- Asignación actual
    obra_asignada_id BIGINT UNSIGNED NULL,
    trabajador_asignado_id BIGINT UNSIGNED NULL,
    
    -- Documentación
    tiene_marcado_ce BOOLEAN DEFAULT TRUE,
    tiene_manual BOOLEAN DEFAULT TRUE,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (maquinaria_tipo_id) REFERENCES maquinaria_tipos(id),
    FOREIGN KEY (obra_asignada_id) REFERENCES obras(id) ON DELETE SET NULL,
    FOREIGN KEY (trabajador_asignado_id) REFERENCES trabajadores(id) ON DELETE SET NULL
);

-- Checklist de inspección de maquinaria (plantillas)
CREATE TABLE maquinaria_checklist_plantillas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquinaria_tipo_id BIGINT UNSIGNED NULL, -- NULL = genérico para todos
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (maquinaria_tipo_id) REFERENCES maquinaria_tipos(id)
);

-- Items del checklist
CREATE TABLE maquinaria_checklist_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plantilla_id BIGINT UNSIGNED NOT NULL,
    categoria VARCHAR(100) NULL, -- Documentación, Seguridad, Pictogramas, etc.
    descripcion TEXT NOT NULL,
    orden INT DEFAULT 0,
    obligatorio BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plantilla_id) REFERENCES maquinaria_checklist_plantillas(id) ON DELETE CASCADE
);

-- Inspecciones realizadas
CREATE TABLE maquinaria_inspecciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquinaria_id BIGINT UNSIGNED NOT NULL,
    plantilla_id BIGINT UNSIGNED NOT NULL,
    fecha_inspeccion DATE NOT NULL,
    fecha_proxima_inspeccion DATE NULL,
    resultado ENUM('apto', 'no_apto') NOT NULL,
    observaciones TEXT NULL,
    realizado_por BIGINT UNSIGNED NOT NULL,
    firma_path VARCHAR(500) NULL,
    documento_path VARCHAR(500) NULL, -- PDF generado
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maquinaria_id) REFERENCES maquinaria(id) ON DELETE CASCADE,
    FOREIGN KEY (plantilla_id) REFERENCES maquinaria_checklist_plantillas(id),
    FOREIGN KEY (realizado_por) REFERENCES users(id)
);

-- Resultados de items de inspección
CREATE TABLE maquinaria_inspeccion_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inspeccion_id BIGINT UNSIGNED NOT NULL,
    checklist_item_id BIGINT UNSIGNED NOT NULL,
    cumple BOOLEAN NULL,
    observacion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inspeccion_id) REFERENCES maquinaria_inspecciones(id) ON DELETE CASCADE,
    FOREIGN KEY (checklist_item_id) REFERENCES maquinaria_checklist_items(id)
);

-- Mantenimientos de maquinaria
CREATE TABLE maquinaria_mantenimientos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquinaria_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('preventivo', 'correctivo') NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT NOT NULL,
    coste DECIMAL(10,2) NULL,
    proveedor VARCHAR(255) NULL,
    realizado_por VARCHAR(255) NULL,
    proxima_revision DATE NULL,
    documento_path VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (maquinaria_id) REFERENCES maquinaria(id) ON DELETE CASCADE
);

-- Asignación de maquinaria a obras (historial)
CREATE TABLE maquinaria_asignaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maquinaria_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (maquinaria_id) REFERENCES maquinaria(id) ON DELETE CASCADE,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE
);
```

#### 10. MÓDULO DE VEHÍCULOS

```sql
-- Tipos de vehículo
CREATE TABLE vehiculo_tipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Furgoneta, Camión, Tractor, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vehículos
CREATE TABLE vehiculos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehiculo_tipo_id BIGINT UNSIGNED NOT NULL,
    matricula VARCHAR(20) UNIQUE NOT NULL,
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    numero_bastidor VARCHAR(100) NULL,
    
    -- Fechas
    fecha_matriculacion DATE NULL,
    fecha_compra DATE NULL,
    
    -- ITV
    fecha_ultima_itv DATE NULL,
    fecha_proxima_itv DATE NULL,
    
    -- Seguro
    compania_seguro VARCHAR(150) NULL,
    numero_poliza VARCHAR(100) NULL,
    fecha_vencimiento_seguro DATE NULL,
    
    -- Económico
    coste_adquisicion DECIMAL(12,2) NULL,
    coste_dia DECIMAL(8,2) NULL,
    
    -- Estado
    estado ENUM('operativo', 'en_taller', 'baja') DEFAULT 'operativo',
    kilometraje_actual INT NULL,
    
    -- Asignación
    conductor_habitual_id BIGINT UNSIGNED NULL,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (vehiculo_tipo_id) REFERENCES vehiculo_tipos(id),
    FOREIGN KEY (conductor_habitual_id) REFERENCES trabajadores(id) ON DELETE SET NULL
);

-- Documentos de vehículo
CREATE TABLE vehiculo_documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehiculo_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('ficha_tecnica', 'permiso_circulacion', 'seguro', 'itv', 'otro') NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    archivo_path VARCHAR(500) NOT NULL,
    fecha_documento DATE NULL,
    fecha_caducidad DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE CASCADE
);
```

#### 11. MÓDULO DE SUBCONTRATAS

```sql
-- Empresas subcontratadas
CREATE TABLE subcontratas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    razon_social VARCHAR(255) NULL,
    cif VARCHAR(20) NULL,
    direccion TEXT NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    persona_contacto VARCHAR(150) NULL,
    
    -- Tarifas
    tarifa_hora DECIMAL(8,2) NULL,
    tarifa_dia DECIMAL(10,2) NULL,
    
    -- Estado
    activa BOOLEAN DEFAULT TRUE,
    homologada BOOLEAN DEFAULT FALSE,
    fecha_homologacion DATE NULL,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Documentación CAE de subcontratas
CREATE TABLE subcontrata_documentos_cae (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subcontrata_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(100) NOT NULL, -- TC1, TC2, Seguro RC, etc.
    nombre VARCHAR(255) NOT NULL,
    archivo_path VARCHAR(500) NOT NULL,
    fecha_documento DATE NULL,
    fecha_caducidad DATE NULL,
    verificado BOOLEAN DEFAULT FALSE,
    verificado_por BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subcontrata_id) REFERENCES subcontratas(id) ON DELETE CASCADE,
    FOREIGN KEY (verificado_por) REFERENCES users(id)
);

-- Documentación específica de subcontrata por obra
CREATE TABLE subcontrata_documentos_obra (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subcontrata_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    archivo_path VARCHAR(500) NOT NULL,
    fecha_documento DATE NULL,
    fecha_caducidad DATE NULL,
    obligatorio BOOLEAN DEFAULT TRUE,
    verificado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subcontrata_id) REFERENCES subcontratas(id) ON DELETE CASCADE,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE
);

-- Asignación de subcontratas a obras
CREATE TABLE obra_subcontratas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NOT NULL,
    subcontrata_id BIGINT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    importe_contratado DECIMAL(12,2) NULL,
    notas TEXT NULL,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (subcontrata_id) REFERENCES subcontratas(id) ON DELETE CASCADE
);
```

#### 12. MÓDULO DE CONTRATOS Y GARANTÍAS

```sql
-- Tipos de contrato
CREATE TABLE contrato_tipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Fijo, Esporádico, Servicios, Salud
    descripcion TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contratos
CREATE TABLE contratos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contrato_tipo_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(50) UNIQUE NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    
    -- Partes
    cliente_id BIGINT UNSIGNED NULL,
    proveedor_id BIGINT UNSIGNED NULL, -- Si es contrato con proveedor
    subcontrata_id BIGINT UNSIGNED NULL,
    
    -- Fechas
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    fecha_firma DATE NULL,
    
    -- Económico
    importe DECIMAL(14,2) NULL,
    iva_porcentaje DECIMAL(5,2) DEFAULT 21,
    
    -- Retenciones de garantía
    tiene_retencion BOOLEAN DEFAULT FALSE,
    retencion_porcentaje DECIMAL(5,2) NULL,
    importe_retenido DECIMAL(12,2) NULL,
    fecha_liberacion_garantia DATE NULL,
    
    -- Estado
    estado ENUM('borrador', 'activo', 'vencido', 'cancelado') DEFAULT 'borrador',
    
    -- Documento
    documento_path VARCHAR(500) NULL,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (contrato_tipo_id) REFERENCES contrato_tipos(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (subcontrata_id) REFERENCES subcontratas(id) ON DELETE SET NULL
);
```

#### 13. MÓDULO FINANCIERO

```sql
-- Categorías de gastos
CREATE TABLE gasto_categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) NULL,
    tipo ENUM('directo', 'indirecto') DEFAULT 'directo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar categorías predefinidas
INSERT INTO gasto_categorias (nombre, codigo, tipo) VALUES
('Personal propio', 'PERS', 'directo'),
('Subcontratas', 'SUBC', 'directo'),
('Maquinaria', 'MAQ', 'directo'),
('Combustible', 'COMB', 'directo'),
('Mantenimiento', 'MANT', 'directo'),
('EPIs', 'EPI', 'directo'),
('Gestoría / Seguros', 'GEST', 'indirecto'),
('Penalizaciones', 'PEN', 'directo'),
('Otros', 'OTRO', 'indirecto');

-- Ingresos
CREATE TABLE ingresos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    obra_id BIGINT UNSIGNED NULL, -- Obligatorio según requisitos
    cliente_id BIGINT UNSIGNED NOT NULL,
    factura_id BIGINT UNSIGNED NULL,
    
    concepto VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    importe DECIMAL(14,2) NOT NULL,
    iva_porcentaje DECIMAL(5,2) DEFAULT 21,
    iva_importe DECIMAL(12,2) NULL,
    retencion_porcentaje DECIMAL(5,2) DEFAULT 0,
    retencion_importe DECIMAL(12,2) NULL,
    importe_total DECIMAL(14,2) NOT NULL, -- Importe + IVA - Retención
    
    fecha DATE NOT NULL,
    fecha_prevista_cobro DATE NULL,
    fecha_cobro DATE NULL,
    
    estado ENUM('pendiente', 'parcial', 'cobrado') DEFAULT 'pendiente',
    forma_pago VARCHAR(100) NULL,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE SET NULL
);

-- Gastos
CREATE TABLE gastos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gasto_categoria_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NULL,
    proveedor VARCHAR(255) NULL,
    
    concepto VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    importe DECIMAL(14,2) NOT NULL,
    iva_porcentaje DECIMAL(5,2) DEFAULT 21,
    iva_importe DECIMAL(12,2) NULL,
    importe_total DECIMAL(14,2) NOT NULL,
    
    fecha DATE NOT NULL,
    fecha_vencimiento DATE NULL,
    fecha_pago DATE NULL,
    
    estado ENUM('pendiente', 'pagado') DEFAULT 'pendiente',
    forma_pago VARCHAR(100) NULL,
    
    documento_path VARCHAR(500) NULL,
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (gasto_categoria_id) REFERENCES gasto_categorias(id),
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE SET NULL
);

-- Facturas emitidas
CREATE TABLE facturas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL, -- Formato: F-2025-00001
    serie VARCHAR(10) DEFAULT 'F',
    
    cliente_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NULL,
    
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NULL,
    
    -- Totales
    base_imponible DECIMAL(14,2) NOT NULL,
    iva_porcentaje DECIMAL(5,2) DEFAULT 21,
    iva_importe DECIMAL(12,2) NOT NULL,
    retencion_porcentaje DECIMAL(5,2) DEFAULT 0,
    retencion_importe DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(14,2) NOT NULL,
    
    -- Estado
    estado ENUM('borrador', 'emitida', 'enviada', 'cobrada', 'anulada') DEFAULT 'borrador',
    fecha_cobro DATE NULL,
    
    -- PDF
    pdf_path VARCHAR(500) NULL,
    
    notas TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE SET NULL
);

-- Líneas de factura
CREATE TABLE factura_lineas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    factura_id BIGINT UNSIGNED NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    cantidad DECIMAL(10,2) DEFAULT 1,
    precio_unitario DECIMAL(12,2) NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    importe DECIMAL(14,2) NOT NULL,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE
);
```

#### 14. MÓDULO DE PRIMAS POR PRODUCCIÓN

```sql
-- Configuración de primas por tipo de trabajo
CREATE TABLE prima_configuraciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    obra_tipo_id BIGINT UNSIGNED NULL, -- NULL = aplica a todos los tipos
    
    -- Mínimo requerido
    unidad_medida ENUM('m2', 'unidades', 'hectareas') NOT NULL,
    minimo_por_trabajador DECIMAL(10,2) NOT NULL, -- Ej: 2500 m²/trabajador
    
    -- Prima
    tramo_prima DECIMAL(10,2) NOT NULL, -- Cada X unidades extra = prima (ej: cada 1000 m² extra)
    importe_prima_por_trabajador DECIMAL(8,2) NOT NULL, -- € por trabajador por tramo
    
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_tipo_id) REFERENCES obra_tipos(id) ON DELETE SET NULL
);

-- Primas calculadas por trabajador
CREATE TABLE primas_trabajador (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trabajador_id BIGINT UNSIGNED NOT NULL,
    obra_id BIGINT UNSIGNED NOT NULL,
    parte_diario_id BIGINT UNSIGNED NULL,
    prima_configuracion_id BIGINT UNSIGNED NOT NULL,
    
    fecha DATE NOT NULL,
    
    -- Cálculo
    produccion_equipo DECIMAL(12,2) NOT NULL, -- Total producido por el equipo
    trabajadores_equipo INT NOT NULL,
    minimo_requerido DECIMAL(12,2) NOT NULL, -- minimo_por_trabajador * trabajadores_equipo
    excedente DECIMAL(12,2) NOT NULL, -- produccion_equipo - minimo_requerido
    tramos_conseguidos INT NOT NULL, -- excedente / tramo_prima
    importe_prima DECIMAL(10,2) NOT NULL, -- tramos * importe_por_trabajador
    
    pagada BOOLEAN DEFAULT FALSE,
    fecha_pago DATE NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (parte_diario_id) REFERENCES partes_diarios(id) ON DELETE SET NULL,
    FOREIGN KEY (prima_configuracion_id) REFERENCES prima_configuraciones(id)
);
```

#### 15. MÓDULO DE ALERTAS Y CADUCIDADES

```sql
-- Configuración de alertas
CREATE TABLE alerta_configuraciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL, -- formacion, epi, itv, seguro, contrato, etc.
    dias_antelacion INT NOT NULL, -- Días antes de caducidad para alertar
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Alertas generadas
CREATE TABLE alertas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    prioridad ENUM('baja', 'media', 'alta', 'critica') DEFAULT 'media',
    
    -- Referencia al registro que genera la alerta
    alertable_type VARCHAR(255) NOT NULL, -- Modelo: Trabajador, Vehiculo, etc.
    alertable_id BIGINT UNSIGNED NOT NULL,
    
    -- Destinatarios
    para_roles JSON NULL, -- ['admin', 'rrhh']
    para_usuario_id BIGINT UNSIGNED NULL,
    
    fecha_vencimiento DATE NULL, -- Fecha del vencimiento que genera la alerta
    
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura DATETIME NULL,
    resuelta BOOLEAN DEFAULT FALSE,
    fecha_resolucion DATETIME NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (para_usuario_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Línea de tiempo de caducidades generales
CREATE TABLE caducidades_generales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL, -- seguro_rc, iso, certificacion, etc.
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    fecha_emision DATE NULL,
    fecha_caducidad DATE NOT NULL,
    documento_path VARCHAR(500) NULL,
    alerta_activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 16. AUDITORÍA

```sql
-- Log de auditoría
CREATE TABLE auditoria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    accion ENUM('crear', 'editar', 'eliminar', 'ver', 'login', 'logout', 'otro') NOT NULL,
    tabla VARCHAR(100) NOT NULL,
    registro_id BIGINT UNSIGNED NULL,
    datos_anteriores JSON NULL,
    datos_nuevos JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 📊 MÓDULOS Y FUNCIONALIDADES

### Estado de Desarrollo

| # | Módulo | Estado | Prioridad | Notas |
|---|--------|--------|-----------|-------|
| 1 | Base, Usuarios, Roles y Permisos | ✅ Completado | - | |
| 2 | Trabajadores | ✅ Completado | Alta | |
| 3 | Cuadrillas | ✅ Completado | Alta | |
| 4 | Clientes y CRM | ✅ Completado | Alta | |
| 5 | Obras/Proyectos | ✅ Completado | Alta | |
| 6 | Fichajes/Control Horario | ✅ Completado | Alta | Múltiples fichajes/día permitidos |
| 7 | Partes Diarios (Base) | ✅ Completado | Alta | Múltiples partes/obra/día permitidos |
| 7b | Conceptos de Producción por Obra | ✅ Completado | Alta | Sistema flexible P5, P6, etc. por obra |
| 7c | Discrepancias de Valoración | ✅ Completado | Alta | Múltiples por período, docs en public/ |
| 8 | Maquinaria | ✅ Completado | Media | CRUD, asignaciones, inspecciones con checklist, mantenimientos |
| 9 | Vehículos | ✅ Completado | Media | CRUD, documentos, alertas ITV/Seguro, asignación conductor |
| 10 | Subcontratas | ✅ Completado | Media | CRUD, docs CAE, asignación obras, homologación |
| 11 | Contratos y Garantías | ✅ Completado | Media | CRUD, estados, garantías, documentos |
| 12 | Ingresos y Gastos | ✅ Completado | Alta | CRUD completo con estados, KPIs |
| 13 | Facturación | ✅ Completado | Alta | CRUD, líneas dinámicas, estados, PDF con DomPDF |
| 14 | EPIs | ✅ Completado | Media | Catálogo, inventario, entregas con firma, revisiones, devoluciones, bajas |
| 15 | Formaciones | ✅ Completado | Media | Catálogo tipos (seeder incluido), registro desde trabajador, estadísticas caducidades |
| 16 | Bonos/Primas Manuales | ✅ Completado | Media | Registro manual de primas y plus |
| 17 | Alertas y Caducidades | ✅ Completado | Alta | Dashboard, configuración, caducidades empresa, command artisan |
| 18 | Dashboard Admin | ✅ Completado | Alta | KPIs, gráficos Chart.js, widgets AJAX, filtros |
| 19 | Dashboard Encargado | ✅ Completado | Alta | Vista operativa para encargados, filtrado por obras asignadas |
| 20 | Dashboard Trabajador (Portal) | ✅ Completado | Alta | Portal personal: KPIs, fichajes, vacaciones, EPIs, formaciones, documentos, primas, alertas |
| 21 | Auditoría | ✅ Completado | Media | Registro de acciones CRUD, filtros por usuario/tabla/acción/fecha, vista detalle, exportación CSV |
| 22 | Integración Email | ✅ Completado | Alta | Facturas con PDF adjunto, notificaciones trabajadores, logs de envíos |

---

## 🔄 FLUJOS DETALLADOS POR MÓDULO

### MÓDULO 2: TRABAJADORES

**Descripción:** Gestión completa de trabajadores propios y de subcontrata.

**Vistas:**
1. `trabajadores/index.blade.php` - Listado con filtros (activos, tipo, cuadrilla)
2. `trabajadores/create.blade.php` - Formulario de alta
3. `trabajadores/edit.blade.php` - Formulario de edición
4. `trabajadores/show.blade.php` - Ficha completa del trabajador con tabs:
   - Datos personales
   - Documentos
   - Formaciones
   - EPIs asignados
   - Historial de obras
   - Historial disciplinario
   - Fichajes

**Flujo de Alta:**
1. Admin/RRHH accede a Trabajadores > Nuevo
2. Completa formulario con datos obligatorios (nombre, apellidos, DNI, fecha alta)
3. Opcionalmente crea usuario para acceso al portal
4. Si es de subcontrata, selecciona la empresa
5. Guarda → Se crea registro → Redirección a ficha

**Campos del Formulario:**
- Tipo relación (propio/subcontrata) *
- Nombre *
- Apellidos *
- DNI *
- Email
- Teléfono
- Dirección
- Fecha nacimiento
- Fecha alta *
- Categoría convenio
- Salario bruto mensual
- Coste empresa/día (calculable)
- Vacaciones anuales (default 22)
- Subcontrata (si aplica)
- Crear usuario portal (checkbox)

### MÓDULO 5: OBRAS/PROYECTOS

**Descripción:** Gestión de obras con estados, hitos, asignaciones y control económico.

**Vistas:**
1. `obras/index.blade.php` - Listado con filtros y estados visuales
2. `obras/create.blade.php` - Formulario de nueva obra
3. `obras/edit.blade.php` - Edición
4. `obras/show.blade.php` - Vista detallada con tabs:
   - Información general
   - Hitos y progreso
   - Personal asignado (trabajadores y cuadrillas)
   - Maquinaria asignada
   - Partes diarios
   - Documentos
   - Económico (ingresos/gastos/rentabilidad)
   - Historial de cambios

**Estados de Obra:**
1. **Presentada** - Propuesta enviada al cliente
2. **Aprobada** - Cliente acepta, pendiente inicio
3. **En curso** - Trabajo activo
4. **Pausada** - Detenida temporalmente
5. **Finalizada** - Trabajo completado
6. **Cancelada** - Obra cancelada

**Flujo de Cambio de Estado:**
- Cada cambio se registra en `obra_historial`
- Se puede añadir comentario al cambio
- Algunos cambios disparan acciones:
  - A "Finalizada" → Revisar cobros pendientes
  - A "Cancelada" → Marcar razón

### MÓDULO 7: PARTES DIARIOS

**Descripción:** Registro diario de trabajos realizados en obra, específico para ADIF.

**Vistas:**
1. `partes_diarios/index.blade.php` - Listado por obra/fecha
2. `partes_diarios/create.blade.php` - Formulario de nuevo parte
3. `partes_diarios/edit.blade.php` - Edición
4. `partes_diarios/show.blade.php` - Vista detalle
5. `partes_diarios/pdf.blade.php` - Versión imprimible/PDF

**Campos del Parte Diario:**
- Obra *
- Fecha *
- Jornada (diurna/nocturna) *
- Línea (L220, L222, etc.)
- Trayecto
- Gerencia/Jefatura
- Distrito
- Brigada

**Trabajadores del día:**
- Lista de trabajadores seleccionables de los asignados a la obra
- Checkbox "Es aplicador" para cada uno
- DNI si es aplicador

**Líneas de trabajo:**
- Tipo: Herbicida (H), Desbroce (D), Poda (P), Tala (T), Limpieza (L)
- PK Inicio
- PK Fin
- Margen izquierda (checkbox) + Ancho
- Margen derecha (checkbox) + Ancho
- Unidades (para talas/podas)
- Metros cuadrados (calculado automático)

**Herbicidas utilizados:**
- Producto
- Nº Registro
- Dosificación
- Cantidad

**Totales automáticos:**
- Desbroce total m²
- Por códigos (P5, P6, P8, P4, P10, P23)
- Talas total
- Podas total

**Observaciones e Incidencias**

**Firmas:**
- Encargado (nombre + firma digital)
- Cliente/ADIF (nombre + firma)

**Flujo:**
1. Encargado crea parte al final del día
2. Selecciona trabajadores presentes
3. Añade líneas de trabajo con PKs y medidas
4. Sistema calcula totales automáticamente
5. Si hubo herbicida, registra productos usados
6. Añade observaciones/incidencias si las hay
7. Guarda como borrador o completado
8. Opcionalmente genera PDF para firma

### MÓDULO 12: INGRESOS Y GASTOS

**Descripción:** Gestión completa de ingresos y gastos por obra. Permite seguimiento de flujo de caja, estados de cobro/pago y categorización de gastos.

**Vistas Ingresos:**
1. `ingresos/index.blade.php` - Listado con KPIs (total, pendiente, cobrado, mes actual)
2. `ingresos/create.blade.php` - Formulario con cálculo automático IVA/retención
3. `ingresos/edit.blade.php` - Edición de ingreso
4. `ingresos/show.blade.php` - Detalle con acciones (marcar cobrado/pendiente)

**Vistas Gastos:**
1. `gastos/index.blade.php` - Listado con KPIs y filtros por categoría
2. `gastos/create.blade.php` - Formulario con cálculo automático IVA
3. `gastos/edit.blade.php` - Edición con reemplazo de documentos
4. `gastos/show.blade.php` - Detalle con acciones (marcar pagado/pendiente)

**Vistas Categorías:**
1. `gasto-categorias/index.blade.php` - Gestión CRUD con modales (solo Admin)

**Flujo de Ingreso:**
1. Contabilidad/Admin crea ingreso asociado a obra
2. Al seleccionar obra, se auto-selecciona cliente
3. Cliente tiene % de retención configurado que se aplica automáticamente
4. Cálculo automático: Total = Base + IVA - Retención
5. Guarda como pendiente
6. Cuando cobran → Marca como "Cobrado" con fecha

**Flujo de Gasto:**
1. Contabilidad/Admin crea gasto
2. Selecciona categoría (directo/indirecto)
3. Si es directo, selecciona obra
4. Puede adjuntar documento (PDF, JPG, PNG) en `public/uploads/gastos/{año}/{mes}/`
5. Guarda como pendiente
6. Cuando pagan → Marca como "Pagado" con fecha

**Categorías de Gastos:**
- **Directos:** Asociados a obra específica (personal, materiales, maquinaria, combustible)
- **Indirectos:** Gastos generales de empresa (gestoría, seguros, alquileres, administración)

**Acceso:**
- Ingresos y Gastos: Administrador + Contabilidad
- Categorías de Gastos: Solo Administrador

### MÓDULO 13: FACTURACIÓN

**Descripción:** Generación de facturas PDF en formato del cliente.

**Vistas:**
1. `facturas/index.blade.php` - Listado con filtros por estado
2. `facturas/create.blade.php` - Nueva factura
3. `facturas/edit.blade.php` - Edición (solo borrador)
4. `facturas/show.blade.php` - Vista previa
5. `facturas/pdf.blade.php` - Template PDF

**Flujo de Facturación:**
1. Contabilidad/Admin crea nueva factura
2. Selecciona cliente y opcionalmente obra
3. Añade líneas (concepto, cantidad, precio)
4. Sistema calcula totales (base, IVA, retención, total)
5. Guarda como borrador
6. Revisa → Marca como "Emitida"
7. Genera PDF en formato del cliente
8. Envía al cliente → Marca como "Enviada"
9. Cuando pagan → Marca como "Cobrada" con fecha

**Numeración:**
- Formato: {SERIE}-{AÑO}-{NUMERO}
- Ejemplo: F-2025-00001
- Auto-incremental por año

### MÓDULO 18: DASHBOARD ADMIN

**Descripción:** Panel principal con KPIs y métricas clave.

**Widgets:**
1. **Rentabilidad Global** - Gráfico de barras mes/año
2. **Rentabilidad por Obra** - Top 5 y Bottom 5
3. **Rentabilidad por Cuadrilla** - Ranking
4. **Flujo de Caja** - Ingresos vs Gastos últimos 12 meses
5. **Cobros Pendientes** - Listado con aging
6. **Obras en Riesgo** - Obras con desviación negativa
7. **Alertas Críticas** - Caducidades próximas
8. **Producción** - m² / talas del mes

**Filtros globales:**
- Rango de fechas
- Obra específica
- Cuadrilla
- Cliente

### MÓDULO 19: DASHBOARD ENCARGADO

**Descripción:** Panel operativo para encargados de obra.

**Widgets:**
1. **Mis Obras** - Obras asignadas y su estado
2. **Producción Diaria** - Resumen del día
3. **Horas por Trabajador** - Hoy/semana
4. **Maquinaria Asignada** - Estado
5. **Calendario** - Vista semanal de planificación
6. **Partes Pendientes** - Por validar/completar

**NO muestra:** Márgenes, rentabilidad final, costes globales

### MÓDULO 20: PORTAL DEL TRABAJADOR

**Descripción:** Acceso individual para trabajadores.

**Secciones:**
1. **Mis Horas** - Fichajes del mes, totales
2. **Mis Vacaciones** - Días acumulados, solicitar
3. **Mis Documentos** - Nóminas, contratos (requiere lectura certificada)
4. **Mis EPIs** - Asignados, próximas revisiones
5. **Mis Formaciones** - Caducidades
6. **Mis Primas** - Primas generadas, pagadas/pendientes
7. **Alertas** - Notificaciones personales

---

## 🎨 TECNOLOGÍAS Y LIBRERÍAS

### Backend
- Laravel 10.x
- PHP 8.2+
- MySQL/MariaDB
- Spatie Laravel Permission (ya instalado)

### Frontend
- Bootstrap 5
- jQuery
- SweetAlert2
- DataTables
- Chart.js (para dashboards)
- Select2 (selectores con búsqueda)
- Flatpickr (selectores de fecha)

### Generación de PDFs
- DomPDF o Snappy (para facturas y partes)

### Otros
- Carbon (fechas)
- Laravel Excel (exportaciones)

---

## 📁 ESTRUCTURA DE CARPETAS

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TrabajadorController.php
│   │   ├── CuadrillaController.php
│   │   ├── ClienteController.php
│   │   ├── LeadController.php
│   │   ├── ObraController.php
│   │   ├── FichajeController.php
│   │   ├── ParteDiarioController.php
│   │   ├── MaquinariaController.php
│   │   ├── VehiculoController.php
│   │   ├── SubcontrataController.php
│   │   ├── ContratoController.php
│   │   ├── IngresoController.php
│   │   ├── GastoController.php
│   │   ├── FacturaController.php
│   │   ├── EpiController.php
│   │   ├── FormacionController.php
│   │   ├── PrimaController.php
│   │   ├── AlertaController.php
│   │   └── DashboardController.php
│   └── Middleware/
├── Models/
│   ├── Trabajador.php
│   ├── Cuadrilla.php
│   ├── Cliente.php
│   ├── Lead.php
│   ├── Obra.php
│   ├── ObraHito.php
│   ├── Fichaje.php
│   ├── ParteDiario.php
│   ├── Maquinaria.php
│   ├── Vehiculo.php
│   ├── Subcontrata.php
│   ├── Contrato.php
│   ├── Ingreso.php
│   ├── Gasto.php
│   ├── Factura.php
│   ├── Epi.php
│   ├── Formacion.php
│   ├── Prima.php
│   └── Alerta.php
├── Services/
│   ├── RentabilidadService.php
│   ├── PrimaCalculatorService.php
│   ├── AlertaService.php
│   └── FacturaPdfService.php
└── Traits/
    └── Auditable.php

resources/views/
├── layouts/
│   ├── app.blade.php
│   └── navigation.blade.php
├── trabajadores/
├── cuadrillas/
├── clientes/
├── leads/
├── obras/
├── fichajes/
├── partes_diarios/
├── maquinaria/
├── vehiculos/
├── subcontratas/
├── contratos/
├── ingresos/
├── gastos/
├── facturas/
├── epis/
├── formaciones/
├── primas/
├── alertas/
├── dashboards/
│   ├── admin.blade.php
│   ├── encargado.blade.php
│   └── trabajador.blade.php
└── components/

public/uploads/
├── obras/
├── trabajadores/
├── maquinaria/
├── vehiculos/
├── clientes/
├── subcontratas/
└── facturas/
```

---

## 📝 NOTAS ADICIONALES

### Cálculo de Rentabilidad por Obra
```
Rentabilidad = Ingresos - Gastos Directos - (Gastos Indirectos * % asignación)

Donde Gastos Directos incluye:
- Coste de personal (horas * coste/hora)
- Subcontratas
- Maquinaria (días * amortización/día)
- Combustible
- Materiales
```

### Cálculo de Primas
```
Ejemplo: Desbroce con 4 operarios

Configuración:
- Mínimo por trabajador: 2500 m²
- Tramo prima: 1000 m² (entre todos)
- Importe prima: 5€/trabajador

Cálculo:
1. Mínimo requerido = 4 * 2500 = 10000 m²
2. Producción del día = 14000 m²
3. Excedente = 14000 - 10000 = 4000 m²
4. Tramos conseguidos = 4000 / 1000 = 4 tramos
5. Prima por trabajador = 4 * 5€ = 20€
```

### Formato de PK (Punto Kilométrico)
```
Formato: XXX+YYY
Donde:
- XXX = Kilómetros (3 dígitos)
- YYY = Metros (3 dígitos)

Ejemplo: 262+450 = Kilómetro 262, metro 450
```

---

## ✅ CHECKLIST PRE-DESARROLLO

Antes de empezar cada módulo:

- [ ] Leer sección de base de datos del módulo
- [ ] Crear migración
- [ ] Crear modelo con relaciones
- [ ] Crear controlador con métodos CRUD
- [ ] Crear vistas (index, create, edit, show)
- [ ] Configurar rutas con middleware de permisos
- [ ] Añadir al menú de navegación con @role()
- [ ] Probar todos los flujos
- [ ] Actualizar este documento si hay cambios

---

**Última actualización:** 2026-01-19
**Versión del documento:** 1.6

### Cambios en versión 1.6 (2026-01-19):
- ✅ Marcado módulo 22 (Integración Email) como **completado**
  - Migraciones: `email_logs`, campos `email_enviado` en facturas
  - Modelo: `EmailLog.php` - Tracking de emails enviados/fallidos
  - Mailable: `FacturaEnviadaMail.php` - Email con PDF adjunto
  - Notificaciones:
    - `DocumentoTrabajadorNotification.php` - Aviso de nuevo documento
    - `FichajeCorregidoNotification.php` - Aviso de corrección de fichaje
    - `BienvenidaTrabajadorNotification.php` - Bienvenida al portal
  - Vistas email: `resources/views/emails/factura-enviada.blade.php`
  - Controladores modificados:
    - `FacturaController::enviar()` - Envía email con PDF al cliente
    - `TrabajadorController::storeDocumento()` - Notifica al trabajador
    - `FichajeController::update()` - Notifica correcciones
  - Configuración SMTP: Gmail (configurable en .env)
  - Tabla `email_logs` para auditoría de envíos

### Cambios en versión 1.5 (2026-01-19):
- ✅ Marcado módulo 18 (Dashboard Admin) como **completado** (ya existía implementado)
- ✅ Marcado módulo 19 (Dashboard Encargado) como **completado**
  - Servicio: `EncargadoDashboardService.php` - Datos filtrados por obras del encargado
  - Controlador: `Encargado\DashboardController.php` - Vista principal + 8 endpoints API
  - Vista principal: `encargado/dashboard/index.blade.php` - Dashboard operativo completo
  - Widgets parciales (7):
    - `_widget-mis-obras.blade.php` - Obras asignadas con estado
    - `_widget-produccion-diaria.blade.php` - Producción de hoy con variaciones
    - `_widget-horas-trabajadores.blade.php` - Horas por trabajador hoy/semana
    - `_widget-maquinaria-asignada.blade.php` - Maquinaria en sus obras
    - `_widget-calendario-semanal.blade.php` - Vista 7 días con eventos
    - `_widget-partes-pendientes.blade.php` - Partes borrador/completados
    - `_widget-alertas-encargado.blade.php` - Alertas de sus obras
  - Rutas: `/encargado/dashboard/*` con middleware `role:Encargado`
  - Navegación: Enlace "Mi Panel" para rol Encargado
  - Restricciones de seguridad: No muestra márgenes, rentabilidad ni costes globales
  - Solo datos de obras donde `encargado_id = auth()->id()`

### Cambios en versión 1.4 (2026-01-19):
- ✅ Marcado módulo 17 (Alertas y Caducidades) como **completado**
  - Servicio: `AlertaService.php` - Generación automática y consultas de alertas
  - Controladores: `AlertaController`, `AlertaConfiguracionController`, `CaducidadGeneralController`
  - Command: `php artisan alertas:generar` - Genera alertas de caducidades próximas
  - Vistas:
    - `alertas/index.blade.php` - Dashboard con KPIs, filtros, acciones masivas
    - `alertas/show.blade.php` - Detalle con enlace al registro relacionado
    - `alertas/configuracion/index.blade.php` - Config días de antelación por tipo
    - `caducidades-generales/*.blade.php` - CRUD completo para caducidades empresa
  - View Composer: `AlertaComposer.php` - Badge con contador en navegación
  - Seeder: `AlertaConfiguracionSeeder.php` - 12 tipos de alerta configurados
  - Rutas con middleware según rol (Administrador, RRHH, Contabilidad, etc.)
  - Tipos de alerta monitoreados:
    - Formaciones, documentos trabajador, aptos médicos
    - EPIs (caducidad y revisiones)
    - Vehículos (ITV, seguro, documentos)
    - Contratos (vencimiento y garantías)
    - Documentos CAE de subcontratas
    - Caducidades generales de empresa (ISO, RC, etc.)

### Cambios en versión 1.3 (2026-01-16):
- ✅ Marcado módulo 7b (Conceptos de Producción por Obra) como **completado**
  - Migraciones: `obra_conceptos_produccion`, `parte_diario_producciones`
  - Modelos: `ObraConceptoProduccion`, `ParteDiarioProduccion`
  - Controlador: `ObraConceptoProduccionController` (CRUD + duplicar)
  - Vistas integradas en `obras/show.blade.php` y `partes-diarios/*`
  - Rutas: `/obras/{obra}/conceptos/*` con middleware
- ✅ Marcado módulo 16 (Bonos/Primas Manuales) como **completado**
  - Migración: `trabajador_bonos`
  - Modelo: `TrabajadorBono` con scopes y helpers
  - Controlador: `TrabajadorBonoController` (CRUD + marcarPagado/Pendiente)
  - Vistas: `trabajadores/bonos/index|create|edit.blade.php`
  - Rutas con middleware `role:Administrador|Contabilidad`
- 📝 Agregada relación `bonos()` en modelo `Trabajador.php`

### Cambios en versión 1.2 (2026-01-16):
- ✅ Marcado módulo 7c (Discrepancias de Valoración) como **completado**
- ⚠️ **ELIMINADO** constraint `UNIQUE KEY unique_fichaje_dia` de tabla `fichajes`
  - Permite múltiples fichajes por día para jornadas partidas
  - Lógica actualizada para buscar fichajes "abiertos" (sin hora_salida)
- ⚠️ **ELIMINADO** constraint `UNIQUE KEY unique_parte_obra_fecha` de tabla `partes_diarios`
  - Permite múltiples partes por obra/fecha (ej: jornada diurna + nocturna)
- ⚠️ **ELIMINADO** constraint `UNIQUE KEY unique_obra_periodo` de tabla `obra_discrepancias_valoracion`
  - Permite múltiples discrepancias por período
- 📝 Documentos de discrepancias se almacenan en `public/uploads/obras/{id}/discrepancias/`
  - Nombres incluyen timestamp para evitar colisiones
- 📝 Migraciones creadas: `2026_01_16_000001_drop_unique_parte_obra_fecha.php`, `2026_01_16_000002_drop_unique_fichaje_dia.php`

### Cambios en versión 1.1 (2026-01-10):
- ✅ Marcado módulo 5 (Obras/Proyectos) como completado
- ✅ Marcado módulo 7 (Partes Diarios - Base) como completado
- 🆕 Agregado módulo 7b: Conceptos de Producción por Obra (en desarrollo)
- 🆕 Agregado módulo 7c: Discrepancias de Valoración (en desarrollo)
- 🔄 Modificado módulo 16: Bonos/Primas ahora son manuales (en desarrollo)
- 📝 Documentadas 4 nuevas tablas: `obra_conceptos_produccion`, `parte_diario_producciones`, `obra_discrepancias_valoracion`, `trabajador_bonos`
- 📝 Documentadas modificaciones a tablas `partes_diarios` y `obras`
