# PLAN DE TRABAJO AGROMARKET v2.0
**Plan Modular para Desarrollo Completo de la Plataforma**
**Fecha de actualización:** 2025-12-15

---

## ACTUALIZACIÓN v2.0 - RESUMEN DE CAMBIOS

> **IMPORTANTE - NUEVOS REQUERIMIENTOS DEL CLIENTE**
>
> Esta versión incluye cambios significativos basados en nuevos documentos de requerimientos:
> 1. Admin puede CREAR proyectos + agricultor automáticamente
> 2. Formulario de proyecto expandido a 3 FASES con toda la info del agricultor
> 3. Correo automático al agricultor con credenciales (password = documento)
> 4. Módulos Admin separados: "Registro de Proyecto" vs "Aprobar Proyecto"
> 5. Base de datos expandida con nuevas tablas y campos
> 6. **NUEVA CATEGORÍA: FARMING** - Para asociaciones y cooperativas agrícolas

### Decisiones de Diseño v2.0:
- **Familia agricultor**: Tabla separada `familia_agricultor` (permite N familiares)
- **Desglose financiero**: JSON en campo `datos_financieros` (más flexible)
- **Formulario Admin**: Idéntico al agricultor (3 fases completas)
- **Documentos**: Nuevos tipos agregados al ENUM + opción "otro"
- **Vendedor Supervisor**: Fase posterior (no implementar ahora)

### Módulos Afectados:
| Módulo | Estado Anterior | Nuevo Estado |
|--------|-----------------|--------------|
| Módulo 3: Gestión de Proyectos | ✅ Completado | 🔄 REQUIERE ACTUALIZACIÓN |
| Módulo 4: Documentos e Imágenes | ✅ Completado | 🔄 REQUIERE ACTUALIZACIÓN |

---

## CATEGORÍAS DE INVERSIÓN (6 CATEGORÍAS)

> **IMPORTANTE:** Ahora son 6 categorías de inversión (antes eran 5). La categoría FARMING es nueva en v2.0.

### 1. STAKING
**Descripción:** Proyectos de cosecha recurrente cíclica (limón, naranja, etc.) donde cada semana se puede sacar producto para la venta.

| Parámetro | Valor |
|-----------|-------|
| Duración | 12-18 meses (depende del producto) |
| ROI | 21-35% E.A. (Efectivo Anual) |
| Retiro anticipado | NO - Si sale antes de 12 meses pierde rentabilidad |
| Trading | NO directamente, pero puede vender su posición |
| Recaudación | 1 mes (30 días) para alcanzar la meta |

**Opciones si necesita dinero antes:**
1. Retira capital pero pierde rentabilidad
2. Vende su posición en TRADING (conserva algo de ganancia)

---

### 2. TRADING
**Descripción:** Marketplace para compra/venta de posiciones de inversionistas que quieren salir antes de que termine su periodo de STAKING.

| Parámetro | Valor |
|-----------|-------|
| Comisión plataforma | Por definir |
| Beneficio vendedor | Recupera inversión + % proporcional al tiempo |
| Beneficio comprador | Obtiene rentabilidad en menos tiempo |

---

### 3. EAR (Early Anticipated Return)
**Descripción:** Proyectos con productos ya en fase de transformación (café empacado, limón embotellado, aceite de aguacate). Inversionista hace pre-compra de producción.

| Parámetro | Valor |
|-----------|-------|
| Duración capital bloqueado | 18+ meses |
| ROI | 10-12% E.A. |
| Rentabilidades retirables desde | 3-6 meses |
| Tipo de inversión | Pre-compra de producción |
| Visualización | Tipo BINANCE (lista de proyectos) |

**Modelo de negocio:** AGROMARKET ayuda a comercializar productos transformados en grandes superficies o exportación. Dividendos se reparten entre agricultor e inversionista.

---

### 4. FUTUROS
**Descripción:** Proyectos de alto impacto a largo plazo. Cultivos propios de limón de AGROMARKET.

| Parámetro | Valor |
|-----------|-------|
| Duración | 2+ años |
| Recaudación | Abierta por 2+ años |
| Monto mínimo recaudo | $20,000 USD+ |
| ROI | Variable según proyecto |
| Tipo de cultivo | Propios (limón principalmente) |

---

### 5. CROSS FUND
**Descripción:** Paquetes diversificados que combinan proyectos de diferentes categorías.

| Parámetro | Valor |
|-----------|-------|
| Composición | Múltiples proyectos STAKING + EAR + FUTUROS |
| ROI | Ponderado según composición |
| Beneficio | Rentabilidades desde 3-6 meses si incluye EAR |
| Gestión | AGROMARKET crea los paquetes, inversionista elige |

---

### 6. FARMING (NUEVO v2.0)
**Descripción:** Similar a FUTUROS pero para asociaciones y cooperativas agrícolas externas. Exportación de commodities (grano/fruta) NO transformados.

| Parámetro | Valor |
|-----------|-------|
| Duración | 2+ años |
| Recaudación | Abierta por 2+ años |
| Monto mínimo recaudo | $20,000 USD+ |
| ROI | +35% E.A. después de 24 meses |
| Frecuencia dividendos | Trimestrales después de 2 años |
| Duración dividendos | 15-20 años (vida útil planta café) |
| Tipo de cultivo | Café, cacao, etc. (commodities) |
| Tipo de agricultor | Asociaciones/cooperativas con cultivos 2-4 años |

**Diferencias con FUTUROS:**
| Aspecto | FUTUROS | FARMING |
|---------|---------|---------|
| Propietario cultivo | AGROMARKET (propio) | Asociaciones externas |
| Tipo producto | Transformado | Commodities (grano/fruta) |
| Cultivos típicos | Limón | Café, cacao |
| Exportación | Producto terminado | Grano/fruta |

**Perfil de agricultores para FARMING:**
- Asociaciones y cooperativas agrícolas
- Cultivos con 2-4 años de maduración
- Plantas prontas a producir o ya produciendo
- Buscan liquidez para organizarse y exportar
- Necesitan certificaciones y centro de acopio

---

## INSTRUCCIONES DE USO DE ESTE DOCUMENTO

> **IMPORTANTE - LEE ESTO PRIMERO**

Este documento es la **FUENTE DE VERDAD** del proyecto AGROMARKET. Contiene toda la información sobre la base de datos, modelos, arquitectura y módulos a desarrollar.

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
   - **ACTUALIZAR NAVEGACIÓN VERTICAL:** Agregar el nuevo módulo en `resources/views/layouts/navigation-vertical.blade.php` con directivas `@role()` según corresponda

4. **Reglas de oro:**
   - Este .md se mantiene actualizado en TODO momento
   - Cualquier desviación del plan SE DOCUMENTA aquí
   - Es la única verdad - no confiar en memoria ni conversaciones anteriores
   - Al inicio de cada sesión, leer las secciones relevantes
   - **NUNCA HACER COMMITS** - Solo el usuario hace commits en Git
   - Rama de trabajo: **agromarket** (siempre trabajar en esta rama)
   - **ALMACENAMIENTO DE ARCHIVOS - REGLA CRÍTICA:**
     - **NUNCA usar `storage/` ni `php artisan storage:link`**
     - **TODOS los archivos (documentos, imágenes, uploads) van en `public/`**
     - Estructura: `public/uploads/proyectos/{proyecto_id}/documentos/` y `public/uploads/proyectos/{proyecto_id}/imagenes/`
     - Razón: El servidor de producción NO soporta enlaces simbólicos (symlinks)
     - Usar `public_path()` en lugar de `storage_path()`
     - URLs directas: `/uploads/proyectos/...` (sin `/storage/`)
   - **ALERTAS Y NOTIFICACIONES - REGLA CRÍTICA:**
     - **NUNCA usar `alert()`, `confirm()` o `prompt()` nativos de JavaScript**
     - **SIEMPRE usar SweetAlert2** (ya incluido en el layout principal)
     - Para confirmaciones: `Swal.fire({ title, text, icon, showCancelButton: true })`
     - Para notificaciones toast: `Swal.fire({ toast: true, position: 'top-end', timer: 3000 })`
     - Para errores: `Swal.fire({ icon: 'error', title: 'Error', text: mensaje })`
     - SweetAlert2 está disponible globalmente como `Swal`

### Estructura del documento:
- **ACTUALIZACIÓN v2.0:** Nuevos requerimientos y cambios
- **ESTADO ACTUAL:** Qué está hecho, qué falta
- **DISEÑO DE BASE DE DATOS:** 35+ tablas completamente documentadas
- **ARQUITECTURA:** Patrones y principios a seguir
- **MÓDULOS 0-17:** Plan de implementación detallado
- **FASES DE DESARROLLO:** Organización temporal
- **FLUJOS E2E:** Casos de prueba completos

---

## ESTADO ACTUAL DEL PROYECTO

### ✅ Completado
- Laravel 9 con autenticación Breeze
- Sistema de roles y permisos (Spatie Laravel-Permission)
- **Base de datos completa** (28 migraciones, 27 modelos, 5 seeders ejecutados)
- **MÓDULO 0: Template y Layout Base** ✅
  - Layout principal con diseño 100% custom del cliente (sin Bootstrap)
  - Sidebar vertical colapsable con menú del template
  - 15 componentes Blade reutilizables en `resources/views/components/agromarket/`
  - 5 archivos CSS del template organizados por responsabilidad
  - 4 archivos JavaScript del template integrados
  - Sistema de diseño documentado (colores, tipografía, componentes)
  - Integraciones: Font Awesome, Google Fonts, Chart.js, Leaflet
- **MÓDULO 1: Autenticación y Gestión de Roles** ✅
  - Middleware: CheckRole, CheckKycStatus
  - Controllers: RoleRedirectController, AuthenticatedSessionController (actualizado)
  - Services: RoleService (gestión centralizada de roles)
  - Rutas protegidas por rol (/admin/*, /supervisor/*, /agricultor/*, /inversionista/*, /vendedor/*)
  - TestUsersSeeder: 6 usuarios de prueba (password: 12345678)
  - Tests: RoleAuthorizationTest (14 tests pasando)
  - Vista KYC placeholder
- **MÓDULO 2: Dashboards por Rol** ✅
  - Dashboards personalizados para cada rol
  - Métricas específicas según el tipo de usuario
  - Visualizaciones con Chart.js integrado
  - Componentes reutilizables implementados

### 🔄 Requiere Actualización (v2.0)
- **MÓDULO 3: Gestión de Proyectos** - Necesita formulario de 3 fases + Admin crea proyectos
- **MÓDULO 4: Documentos e Imágenes** - Necesita nuevos tipos de documentos

### 📋 Por Implementar
- **Módulos 5-17:** Funcionalidades completas del negocio
- Arquitectura SOLID con patrones de diseño
- Testing completo de cada flujo E2E

### NOTA IMPORTANTE: INTEGRACIÓN MERCADO PAGO
**MÓDULO 10 (Depósitos)** requiere integración con Mercado Pago:
- Se utilizará **Checkout API** con **Webhooks**
- Solo para **depósitos de inversionistas** (no retiros)
- Los retiros son gestionados manualmente por el administrador
- **PREREQUISITO:** Antes de implementar Módulo 10, el cliente debe suministrar:
  - Link a documentación oficial de Mercado Pago
  - Credenciales de prueba (Public Key, Access Token)
  - Configuración de webhooks

---

## DISEÑO DE BASE DE DATOS

> **Esta sección documenta TODAS las tablas de la base de datos de AGROMARKET**
>
> **Total: 35 tablas** (28 originales + 2 nuevas v2.0 + 5 de Spatie Laravel-Permission)
>
> Todas las migraciones han sido ejecutadas exitosamente.

### Resumen por Categoría

1. **Usuarios y Autenticación** (10 tablas)
   - users, permissions, roles, model_has_roles, model_has_permissions, role_has_permissions, documentos_kyc, cuentas_bancarias
   - **NUEVAS v2.0:** perfiles_agricultor, familia_agricultor

2. **Proyectos Agrícolas** (6 tablas)
   - categorias_proyecto, reglas_penalizacion, proyectos, documentos_proyecto, imagenes_proyecto, actualizaciones_proyecto

3. **Inversiones** (4 tablas)
   - inversiones, transacciones_inversion, dividendos, aceptaciones_contrato

4. **Cross-Fund (Paquetes)** (3 tablas)
   - paquetes_cross_fund, proyectos_cross_fund, compras_cross_fund

5. **Operaciones Financieras** (4 tablas)
   - billeteras, transacciones_billetera, retiros, depositos

6. **Contratos** (1 tabla)
   - plantillas_contrato

7. **Comunicaciones** (2 tablas)
   - notificaciones, mensajes

8. **CRM/Ventas** (2 tablas)
   - prospectos, actividades_prospecto

9. **Sistema/Admin** (3 tablas)
   - logs_auditoria, configuraciones_sistema, reportes

---

## NUEVAS TABLAS v2.0

### TABLA: perfiles_agricultor (NUEVA)
**Propósito:** Información extendida del agricultor (Fase 2 del formulario)

**Modelo:** `App\Models\PerfilAgricultor`

#### Campos:
```sql
CREATE TABLE perfiles_agricultor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,

    -- Tipo de persona
    tipo_persona ENUM('natural', 'juridica') DEFAULT 'natural',

    -- Datos empresa (solo si tipo_persona = 'juridica')
    nombre_empresa VARCHAR(255) NULL,
    nit VARCHAR(50) NULL,
    representante_legal VARCHAR(255) NULL,
    direccion_finca TEXT NULL,

    -- Seguros
    cultivo_asegurado BOOLEAN DEFAULT FALSE,

    -- Experiencia (FASE 2)
    anos_experiencia INT NULL,
    formacion_capacitaciones TEXT NULL,
    cantidad_cosechas INT NULL,
    produccion_promedio TEXT NULL,

    -- Equipo de trabajo (FASE 2)
    num_personas_trabajando INT NULL,
    familia_trabaja_cultivo BOOLEAN DEFAULT FALSE,
    roles_principales TEXT NULL,
    nivel_tecnificacion ENUM('manual', 'semi_tecnificado', 'tecnificado') NULL,

    -- Estado del predio (FASE 2)
    tiene_riego BOOLEAN DEFAULT FALSE,
    tiene_bodega BOOLEAN DEFAULT FALSE,
    tiene_transformacion BOOLEAN DEFAULT FALSE,
    tiene_transporte BOOLEAN DEFAULT FALSE,
    accesibilidad TEXT NULL,
    riesgos_naturales TEXT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Relaciones:
```php
public function usuario(): BelongsTo // → User
```

---

### TABLA: familia_agricultor (NUEVA)
**Propósito:** Miembros de la familia del agricultor (permite N familiares)

**Modelo:** `App\Models\FamiliaAgricultor`

#### Campos:
```sql
CREATE TABLE familia_agricultor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agricultor_id BIGINT UNSIGNED NOT NULL,

    parentesco ENUM('esposa', 'esposo', 'hijo', 'hija', 'otro') NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    edad INT NULL,
    nivel_educativo ENUM('ninguno', 'primaria', 'secundaria', 'tecnico', 'profesional', 'posgrado') NULL,
    estudia_actualmente ENUM('si', 'no', 'estudio_aplazado') NULL,
    trabaja_en_cultivo BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (agricultor_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Relaciones:
```php
public function agricultor(): BelongsTo // → User
```

---

## MODIFICACIONES A TABLAS EXISTENTES (v2.0)

### TABLA: users (MODIFICAR)
```sql
-- Nuevos campos v2.0
ALTER TABLE users ADD COLUMN foto_perfil VARCHAR(500) NULL AFTER direccion;
ALTER TABLE users ADD COLUMN creado_por_admin BOOLEAN DEFAULT FALSE AFTER deleted_at;
ALTER TABLE users ADD COLUMN admin_creador_id BIGINT UNSIGNED NULL AFTER creado_por_admin;
```

#### Nuevas Relaciones:
```php
public function perfilAgricultor(): HasOne // → PerfilAgricultor
public function familia(): HasMany // → FamiliaAgricultor
public function creadoPor(): BelongsTo // → User (admin)
```

---

### TABLA: proyectos (MODIFICAR)
```sql
-- Nuevos campos v2.0
ALTER TABLE proyectos ADD COLUMN tipo_cultivo VARCHAR(100) NULL AFTER descripcion;
ALTER TABLE proyectos ADD COLUMN area_hectareas DECIMAL(10,2) NULL AFTER tipo_cultivo;
ALTER TABLE proyectos ADD COLUMN etapa_cultivo ENUM('siembra', 'crecimiento', 'cosecha', 'transformacion', 'otro') NULL AFTER area_hectareas;
ALTER TABLE proyectos ADD COLUMN ano_inicio_cultivo INT NULL AFTER etapa_cultivo;
ALTER TABLE proyectos ADD COLUMN objetivo_proyecto TEXT NULL AFTER datos_adicionales;
ALTER TABLE proyectos ADD COLUMN detalle_proceso_productivo TEXT NULL AFTER objetivo_proyecto;
ALTER TABLE proyectos ADD COLUMN cronograma_estimado TEXT NULL AFTER detalle_proceso_productivo;

-- Datos financieros como JSON (decisión: más flexible)
ALTER TABLE proyectos ADD COLUMN datos_financieros JSON NULL AFTER cronograma_estimado;
-- Estructura del JSON:
-- {
--   "inversion_solicitada": {
--     "insumos": 0,
--     "mano_obra": 0,
--     "equipos": 0,
--     "transporte": 0,
--     "certificaciones": 0,
--     "empaques": 0,
--     "marketing": 0
--   },
--   "proyecciones": {
--     "produccion_estimada": "",
--     "precio_venta_estimado": 0,
--     "canales_venta_actuales": "",
--     "canales_venta_deseados": "",
--     "proyeccion_ingresos": "",
--     "punto_equilibrio": "",
--     "margen_ganancia": 0
--   },
--   "riesgos": {
--     "plagas": "",
--     "clima": "",
--     "competencia": "",
--     "acceso_mercados": "",
--     "regulaciones": ""
--   }
-- }

-- Datos específicos EARN
ALTER TABLE proyectos ADD COLUMN datos_earn JSON NULL AFTER datos_financieros;
-- {
--   "estado_empaque": "",
--   "certificaciones_pendientes": [],
--   "capacidad_produccion": "",
--   "laboratorio_procesamiento": "",
--   "costos_por_unidad": 0,
--   "inventario_disponible": "",
--   "necesidades_escalar": ""
-- }

-- Datos específicos FUTUROS
ALTER TABLE proyectos ADD COLUMN datos_futuros JSON NULL AFTER datos_earn;
-- {
--   "plan_expansion": "",
--   "infraestructura_requerida": "",
--   "proyeccion_3_anos": "",
--   "proyeccion_5_anos": "",
--   "amenazas_largo_plazo": "",
--   "financiacion_por_fases": ""
-- }

-- Datos específicos FARMING (NUEVO v2.0)
ALTER TABLE proyectos ADD COLUMN datos_farming JSON NULL AFTER datos_futuros;
-- {
--   "tipo_asociacion": "",              -- cooperativa, asociación, grupo
--   "nombre_asociacion": "",
--   "num_asociados": 0,
--   "productos_principales": [],        -- café, cacao, etc.
--   "tipo_exportacion": "commodities",  -- commodities (grano/fruta), NO transformado
--   "certificaciones_exportacion": [],  -- fair trade, orgánico, etc.
--   "centro_acopio": false,
--   "capacidad_acopio_toneladas": 0,
--   "edad_cultivos_anos": 0,            -- 2-4 años típico
--   "produccion_actual": false,         -- ya están produciendo o prontos
--   "mercados_destino": [],             -- países destino exportación
--   "proyeccion_15_anos": "",
--   "proyeccion_20_anos": ""
-- }

-- Metadata de creación por admin
ALTER TABLE proyectos ADD COLUMN creado_por_admin BOOLEAN DEFAULT FALSE AFTER activo;
ALTER TABLE proyectos ADD COLUMN admin_creador_id BIGINT UNSIGNED NULL AFTER creado_por_admin;
```

---

### TABLA: documentos_proyecto (MODIFICAR ENUM)
```sql
-- Tipos actuales + nuevos tipos v2.0
ALTER TABLE documentos_proyecto MODIFY COLUMN tipo_documento ENUM(
    -- Existentes
    'escritura',
    'certificado_camara',
    'cedula_catastral',
    'plan_cultivo',
    'estudio_suelos',
    'licencia_ambiental',
    'poliza_seguro',
    'contrato_compra',
    'foto_terreno',
    -- Nuevos v2.0
    'documento_identidad',
    'nit',
    'certificado_bpa',
    'certificado_ica',
    'certificado_invima',
    'documento_tenencia_tierra',
    'contrato_arriendo',
    'permiso_uso_tierra',
    'certificaciones_asociacion',
    'seguro_cultivo',
    'cotizaciones_gastos',
    'video_presentacion',
    'foto_empaque',
    'foto_producto_terminado',
    'otro'
);
```

---

## TABLAS ORIGINALES (Sin cambios)

### TABLA: users
**Propósito:** Usuarios del sistema (inversionistas, agricultores, administradores, supervisores, vendedores)

**Modelo:** `App\Models\User`

#### Campos Base (Laravel):
- `id`: bigint unsigned (PK, auto-increment)
- `name`: varchar(255) - Nombre completo
- `email`: varchar(255) (unique) - Correo electrónico
- `password`: varchar(255) - Contraseña encriptada
- `email_verified_at`: timestamp (nullable) - Verificación de email
- `remember_token`: varchar(100) (nullable) - Token de sesión
- `created_at`: timestamp
- `updated_at`: timestamp

#### Campos AGROMARKET:
- `telefono`: varchar(20) (nullable) - Número de contacto
- `activo`: boolean (default: true) - Usuario activo/inactivo
- `ultimo_login`: timestamp (nullable) - Última sesión
- `documento_identidad`: varchar(50) (unique, nullable) - Cédula/DNI/NIT
- `tipo_documento`: enum['CC','CE','NIT','PASSPORT','DNI'] (nullable)
- `fecha_nacimiento`: date (nullable)
- `pais`: varchar(2) (nullable) - Código ISO país
- `ciudad`: varchar(100) (nullable)
- `direccion`: text (nullable)
- `foto_perfil`: varchar(500) (nullable) - **NUEVO v2.0**
- `kyc_status`: enum['pendiente','en_revision','aprobado','rechazado'] (default: 'pendiente')
- `kyc_aprobado_at`: timestamp (nullable)
- `kyc_aprobado_por`: bigint unsigned (nullable) - FK → users
- `kyc_notas`: text (nullable) - Observaciones KYC
- `codigo_referido`: varchar(20) (unique, nullable) - Código para referir
- `referido_por`: bigint unsigned (nullable) - FK → users
- `deleted_at`: timestamp (nullable) - Soft delete
- `creado_por_admin`: boolean (default: false) - **NUEVO v2.0**
- `admin_creador_id`: bigint unsigned (nullable) - **NUEVO v2.0** FK → users

#### Relaciones (Eloquent):
```php
// Roles y permisos (Spatie)
public function roles(): BelongsToMany
public function permissions(): BelongsToMany

// KYC
public function kycAprobadoPor(): BelongsTo // → User
public function documentosKyc(): HasMany // → DocumentoKyc

// Referidos
public function referidoPor(): BelongsTo // → User
public function referidos(): HasMany // → User

// Finanzas
public function billetera(): HasOne // → Billetera
public function retiros(): HasMany // → Retiro
public function depositos(): HasMany // → Deposito
public function transacciones(): HasMany // → TransaccionBilletera

// Inversiones
public function inversiones(): HasMany // → Inversion
public function comprasCrossFund(): HasMany // → CompraCrossFund
public function dividendos(): HasMany // → Dividendo

// Proyectos (como agricultor)
public function proyectos(): HasMany // → Proyecto

// Cuentas bancarias
public function cuentasBancarias(): HasMany // → CuentaBancaria

// Comunicaciones
public function notificaciones(): HasMany // → Notificacion
public function mensajesEnviados(): HasMany // → Mensaje (remitente)
public function mensajesRecibidos(): HasMany // → Mensaje (destinatario)

// CRM (como vendedor)
public function prospectosAsignados(): HasMany // → Prospecto

// NUEVAS v2.0
public function perfilAgricultor(): HasOne // → PerfilAgricultor
public function familia(): HasMany // → FamiliaAgricultor
public function creadoPor(): BelongsTo // → User (admin)
```

---

### TABLA: categorias_proyecto
**Propósito:** Tipos de proyectos agrícolas con parámetros de inversión

**Modelo:** `App\Models\CategoriaProyecto`

#### Campos:
- `id`: bigint unsigned (PK)
- `codigo`: varchar(20) (unique) - Ej: 'STAKING', 'TRADING', 'EAR', 'FUTUROS', 'CROSS_FUND', 'FARMING'
- `nombre`: varchar(100) - Nombre descriptivo
- `descripcion`: text (nullable)
- `duracion_minima_meses`: int - Duración mínima proyecto
- `duracion_maxima_meses`: int - Duración máxima proyecto
- `roi_minimo`: decimal(5,2) - ROI mínimo esperado %
- `roi_maximo`: decimal(5,2) - ROI máximo esperado %
- `inversion_minima`: decimal(15,2) - Inversión mínima por usuario
- `inversion_maxima`: decimal(15,2) - Inversión máxima por usuario
- `permite_retiro_anticipado`: boolean (default: false)
- `permite_trading`: boolean (default: false)
- `activo`: boolean (default: true)
- `orden`: int (default: 0) - Orden visualización
- `created_at`: timestamp
- `updated_at`: timestamp

#### Relaciones:
```php
public function proyectos(): HasMany // → Proyecto
public function reglasPenalizacion(): HasMany // → ReglaPenalizacion
```

#### Datos iniciales (seeder) - 6 CATEGORÍAS:
- **STAKING:** Cosecha recurrente cíclica (limón, naranja). Duración 12-18 meses, ROI 21-35%, NO retiro anticipado, NO trading. Si retiro antes de 12 meses pierde rentabilidad o puede vender posición.
- **TRADING:** Marketplace para venta de posiciones de STAKING. Inversionistas que quieren salir antes del periodo.
- **EAR (Early Anticipated Return):** Productos ya transformados (café empacado, limón embotellado, aceite aguacate). ROI 10-12%, rentabilidades desde 3-6 meses, capital en staking 18+ meses. Pre-compra de producción.
- **FUTUROS:** Proyectos alto impacto +2 años. Recaudo abierto 2+ años, +20k USD. Cultivos propios de limón.
- **CROSS_FUND:** Paquetes diversificados con múltiples proyectos de diferentes categorías.
- **FARMING (NUEVO v2.0):** Similar a FUTUROS pero para asociaciones y cooperativas agrícolas (café, cacao). Exportación en commodities (grano/fruta, NO transformado). Recaudo 2+ años, +20k USD, ROI +35% EA después de 24 meses. Ganancias trimestrales por 15-20 años. Perfil: asociaciones con cultivos de 2-4 años prontos a producir.

---

### TABLA: proyectos
**Propósito:** Proyectos agrícolas que buscan financiamiento

**Modelo:** `App\Models\Proyecto`

#### Campos:
- `id`: bigint unsigned (PK)
- `codigo`: varchar(50) (unique) - Código único
- `categoria_id`: bigint unsigned - FK → categorias_proyecto
- `agricultor_id`: bigint unsigned - FK → users
- `nombre`: varchar(200)
- `descripcion`: text
- `tipo_cultivo`: varchar(100) (nullable) - **NUEVO v2.0**
- `area_hectareas`: decimal(10,2) (nullable) - **NUEVO v2.0**
- `etapa_cultivo`: enum['siembra','crecimiento','cosecha','transformacion','otro'] (nullable) - **NUEVO v2.0**
- `ano_inicio_cultivo`: int (nullable) - **NUEVO v2.0**
- `ubicacion`: text (nullable) - Descripción ubicación
- `coordenadas`: varchar(100) (nullable) - GPS lat,long
- `monto_objetivo`: decimal(15,2) - Meta de recaudación
- `monto_recaudado`: decimal(15,2) (default: 0)
- `inversion_minima`: decimal(15,2) - Por inversionista
- `inversion_maxima`: decimal(15,2) (nullable) - Por inversionista
- `roi_anual`: decimal(5,2) - ROI proyectado %
- `duracion_meses`: int
- `periodo_cosecha_meses`: int (nullable)
- `periodo_dividendos_dias`: int - Frecuencia de pago
- `fecha_inicio_recaudacion`: date (nullable)
- `fecha_cierre_recaudacion`: date (nullable)
- `fecha_inicio_proyecto`: date (nullable)
- `fecha_fin_proyecto`: date (nullable)
- `fecha_primer_dividendo`: date (nullable)
- `estado`: enum['borrador','en_revision','rechazado','aprobado','en_recaudacion','fondeado','en_ejecucion','en_cosecha','finalizado','cancelado'] (default: 'borrador')
- `aprobado_por`: bigint unsigned (nullable) - FK → users
- `aprobado_at`: timestamp (nullable)
- `notas_aprobacion`: text (nullable)
- `motivo_rechazo`: text (nullable)
- `nivel_riesgo`: enum['bajo','medio','alto'] (default: 'medio')
- `verificado`: boolean (default: false)
- `destacado`: boolean (default: false)
- `orden_destacado`: int (default: 0)
- `datos_adicionales`: json (nullable)
- `objetivo_proyecto`: text (nullable) - **NUEVO v2.0**
- `detalle_proceso_productivo`: text (nullable) - **NUEVO v2.0**
- `cronograma_estimado`: text (nullable) - **NUEVO v2.0**
- `datos_financieros`: json (nullable) - **NUEVO v2.0**
- `datos_earn`: json (nullable) - **NUEVO v2.0**
- `datos_futuros`: json (nullable) - **NUEVO v2.0**
- `datos_farming`: json (nullable) - **NUEVO v2.0** - Para proyectos de asociaciones/cooperativas
- `activo`: boolean (default: true)
- `creado_por_admin`: boolean (default: false) - **NUEVO v2.0**
- `admin_creador_id`: bigint unsigned (nullable) - **NUEVO v2.0**
- `created_at`: timestamp
- `updated_at`: timestamp
- `deleted_at`: timestamp (nullable)

#### Relaciones:
```php
public function categoria(): BelongsTo // → CategoriaProyecto
public function agricultor(): BelongsTo // → User
public function aprobadoPor(): BelongsTo // → User
public function inversiones(): HasMany // → Inversion
public function documentos(): HasMany // → DocumentoProyecto
public function imagenes(): HasMany // → ImagenProyecto
public function actualizaciones(): HasMany // → ActualizacionProyecto
public function dividendos(): HasMany // → Dividendo
public function proyectosCrossFund(): HasMany // → ProyectoCrossFund
public function creadoPorAdmin(): BelongsTo // → User (NUEVO v2.0)
```

---

### TABLA: documentos_proyecto
**Propósito:** Documentación legal/técnica del proyecto

**Modelo:** `App\Models\DocumentoProyecto`

#### Campos:
- `id`: bigint unsigned (PK)
- `proyecto_id`: bigint unsigned - FK → proyectos
- `tipo_documento`: enum (ver lista expandida v2.0 arriba)
- `nombre_archivo`: varchar(255)
- `ruta_archivo`: varchar(500) - Path en public/uploads
- `tipo_mime`: varchar(100)
- `tamano_bytes`: bigint unsigned
- `descripcion`: text (nullable)
- `verificado`: boolean (default: false)
- `verificado_por`: bigint unsigned (nullable) - FK → users
- `verificado_at`: timestamp (nullable)
- `subido_por`: bigint unsigned - FK → users
- `created_at`: timestamp
- `updated_at`: timestamp
- `deleted_at`: timestamp (nullable)

#### Relaciones:
```php
public function proyecto(): BelongsTo // → Proyecto
public function verificadoPor(): BelongsTo // → User
public function subidoPor(): BelongsTo // → User
```

---

### TABLA: billeteras
**Propósito:** Cartera digital de cada usuario

**Modelo:** `App\Models\Billetera`

#### Campos:
- `id`: bigint unsigned (PK)
- `usuario_id`: bigint unsigned (unique) - FK → users
- `saldo_disponible`: decimal(15,2) (default: 0) - Dinero disponible
- `saldo_bloqueado`: decimal(15,2) (default: 0) - En retiros pendientes
- `saldo_invertido`: decimal(15,2) (default: 0) - En proyectos activos
- `retornos_acumulados`: decimal(15,2) (default: 0) - Retornos históricos
- `dividendos_pendientes`: decimal(15,2) (default: 0) - Dividendos por cobrar
- `created_at`: timestamp
- `updated_at`: timestamp

#### Relaciones:
```php
public function usuario(): BelongsTo // → User
public function transacciones(): HasMany // → TransaccionBilletera
```

---

(Las demás tablas originales permanecen igual - ver plan original para detalles completos)

---

## ARQUITECTURA Y PRINCIPIOS

### Patrones de Diseño a Implementar
1. **Repository Pattern** - Abstracción de acceso a datos
2. **Service Layer** - Lógica de negocio centralizada
3. **DTO (Data Transfer Objects)** - Transferencia de datos tipada
4. **Observer Pattern** - Auditoría y eventos del sistema
5. **Strategy Pattern** - Cálculo de dividendos y penalizaciones
6. **Factory Pattern** - Generación de códigos y contratos

### Estructura de Carpetas
```
app/
├── Http/
│   ├── Controllers/        # Controllers delgados
│   ├── Requests/          # Form Requests (validaciones)
│   └── Resources/         # API Resources (transformers)
├── Services/              # Lógica de negocio
├── Repositories/          # Acceso a datos
├── DTOs/                  # Data Transfer Objects
├── Observers/             # Observers para auditoría
├── Policies/              # Políticas de autorización
├── Enums/                 # Enumeraciones (estados, tipos)
└── Helpers/               # Funciones helper
```

---

## MÓDULO 0: TEMPLATE Y LAYOUT BASE ✅ COMPLETADO
(Sin cambios - ver plan original)

---

## MÓDULO 1: AUTENTICACIÓN Y GESTIÓN DE ROLES ✅ COMPLETADO
(Sin cambios - ver plan original)

---

## MÓDULO 2: DASHBOARDS POR ROL ✅ COMPLETADO
(Sin cambios - ver plan original)

---

## MÓDULO 3: GESTIÓN DE PROYECTOS 🔄 REQUIERE ACTUALIZACIÓN v2.0
**Objetivo:** CRUD completo de proyectos agrícolas con workflow de aprobación + NUEVO: Admin puede crear proyectos

### Estado Actual
- ✅ CRUD básico funcionando
- ✅ Workflow de aprobación (Agricultor → Admin)
- ✅ Estados funcionando
- ✅ Notificaciones básicas

### Cambios Requeridos v2.0

#### 1. NUEVO: Admin puede crear proyectos + agricultor
El administrador debe poder:
- Crear un proyecto completo con formulario de 3 fases
- Crear automáticamente el usuario agricultor si no existe
- Enviar correo al agricultor con credenciales (password = documento_identidad)

#### 2. Formulario expandido a 3 FASES

**FASE 1: Datos Básicos (Agricultor + Proyecto)**
- Datos personales del agricultor (nombre, documento, teléfono, email)
- Tipo de persona (natural/jurídica)
- Datos de empresa si aplica (NIT, representante legal)
- Datos básicos del proyecto (nombre, categoría, tipo cultivo)
- Ubicación y área
- Documentos iniciales (cédula, NIT, documentos de tenencia)

**FASE 2: Evaluación Técnica del Agricultor**
- Experiencia agrícola (años, formación, cosechas anteriores)
- Equipo de trabajo (personas, familia, roles)
- Información familiar (N miembros dinámicos)
- Estado del predio (riego, bodega, transporte, accesibilidad)
- Riesgos naturales identificados

**FASE 3: Evaluación Financiera del Proyecto**
- Desglose de inversión solicitada (insumos, mano obra, equipos, etc.)
- Proyecciones financieras (producción, precios, canales)
- Evaluación de riesgos (plagas, clima, competencia)
- Datos específicos según categoría (EARN, FUTUROS)
- Documentos financieros (cotizaciones, certificaciones)

#### 3. Nuevos Controllers

**Archivo:** `app/Http/Controllers/Admin/ProjectRegistrationController.php`
```php
class ProjectRegistrationController extends Controller
{
    public function index() // Lista proyectos creados por admin
    public function create() // Formulario wizard 3 fases
    public function storePhase1(StoreProjectPhase1Request $request) // Guarda Fase 1
    public function storePhase2(StoreProjectPhase2Request $request, Proyecto $proyecto) // Guarda Fase 2
    public function storePhase3(StoreProjectPhase3Request $request, Proyecto $proyecto) // Guarda Fase 3
    public function show(Proyecto $proyecto) // Ver proyecto registrado
    public function edit(Proyecto $proyecto) // Editar proyecto
}
```

#### 4. Nuevos Services

**Archivo:** `app/Services/Farmer/FarmerCreationService.php`
```php
class FarmerCreationService
{
    public function createFarmerWithProject(array $data): User
    public function generateTemporaryPassword(string $documento): string
    public function sendWelcomeEmail(User $farmer, string $password): void
}
```

**Archivo:** `app/Services/Project/ProjectFormService.php`
```php
class ProjectFormService
{
    public function validatePhase1Data(array $data): bool
    public function validatePhase2Data(array $data): bool
    public function validatePhase3Data(array $data): bool
    public function savePhase1(array $data): Proyecto
    public function savePhase2(Proyecto $proyecto, array $data): void
    public function savePhase3(Proyecto $proyecto, array $data): void
}
```

#### 5. Nuevos Form Requests

```php
// app/Http/Requests/StoreProjectPhase1Request.php
// Valida: datos agricultor + datos básicos proyecto + documentos iniciales

// app/Http/Requests/StoreProjectPhase2Request.php
// Valida: experiencia + equipo trabajo + familia + estado predio

// app/Http/Requests/StoreProjectPhase3Request.php
// Valida: datos financieros + proyecciones + riesgos + EARN/FUTUROS

// app/Http/Requests/StoreFamilyMemberRequest.php
// Valida: datos de familiar (nombre, parentesco, edad, educación)
```

#### 6. Nuevas Vistas Admin

```
resources/views/admin/projects/
├── registration/
│   ├── index.blade.php - Lista de proyectos registrados por admin
│   ├── create.blade.php - Formulario wizard 3 fases
│   ├── phase1.blade.php - Partial: Datos agricultor + proyecto básico
│   ├── phase2.blade.php - Partial: Evaluación técnica agricultor
│   ├── phase3.blade.php - Partial: Evaluación financiera
│   ├── show.blade.php - Ver proyecto registrado
│   └── edit.blade.php - Editar proyecto
│
└── approval/ (existente, sin cambios mayores)
    ├── index.blade.php
    └── show.blade.php
```

#### 7. Actualizar Vistas Farmer

```
resources/views/farmer/projects/
├── index.blade.php (actualizar para mostrar fase actual)
├── create.blade.php (REHACER como wizard 3 fases)
├── phase1.blade.php (NUEVO)
├── phase2.blade.php (NUEVO)
├── phase3.blade.php (NUEVO)
├── show.blade.php (actualizar para mostrar toda la info)
├── edit.blade.php (actualizar para editar por fases)
└── files.blade.php (actualizar con nuevos tipos)
```

#### 8. Nuevos Componentes Blade

```
resources/views/components/agromarket/
├── wizard-steps.blade.php - Indicador de pasos del wizard
├── family-member-form.blade.php - Formulario dinámico para familiares
├── financial-breakdown.blade.php - Desglose financiero
└── risk-assessment.blade.php - Evaluación de riesgos
```

#### 9. Nuevas Rutas

```php
// routes/web.php - Sección Admin
Route::prefix('admin')->middleware(['auth', 'role:Administrador'])->group(function () {
    // Registro de proyectos (NUEVO)
    Route::prefix('proyectos/registro')->name('admin.projects.registration.')->group(function () {
        Route::get('/', [ProjectRegistrationController::class, 'index'])->name('index');
        Route::get('/crear', [ProjectRegistrationController::class, 'create'])->name('create');
        Route::post('/fase-1', [ProjectRegistrationController::class, 'storePhase1'])->name('phase1.store');
        Route::post('/fase-2/{proyecto}', [ProjectRegistrationController::class, 'storePhase2'])->name('phase2.store');
        Route::post('/fase-3/{proyecto}', [ProjectRegistrationController::class, 'storePhase3'])->name('phase3.store');
        Route::get('/{proyecto}', [ProjectRegistrationController::class, 'show'])->name('show');
        Route::get('/{proyecto}/editar', [ProjectRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{proyecto}', [ProjectRegistrationController::class, 'update'])->name('update');
    });

    // Aprobación (existente, mantener)
    Route::prefix('proyectos/revision')->name('admin.projects.approval.')->group(function () {
        // ... rutas existentes
    });
});

// Farmer - agregar rutas para fases
Route::post('/projects/{proyecto}/fase-2', [ProjectController::class, 'storePhase2'])->name('phase2.store');
Route::post('/projects/{proyecto}/fase-3', [ProjectController::class, 'storePhase3'])->name('phase3.store');
```

#### 10. Nueva Notificación

**Archivo:** `app/Notifications/FarmerWelcomeNotification.php`
```php
class FarmerWelcomeNotification extends Notification
{
    public function __construct(
        private string $password,
        private Proyecto $proyecto
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenido a AGROMARKET - Tu cuenta ha sido creada')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu cuenta en AGROMARKET ha sido creada exitosamente.')
            ->line('**Credenciales de acceso:**')
            ->line('Email: ' . $notifiable->email)
            ->line('Contraseña: ' . $this->password)
            ->line('Se ha registrado tu proyecto: ' . $this->proyecto->nombre)
            ->action('Acceder a AGROMARKET', url('/login'))
            ->line('Te recomendamos cambiar tu contraseña después del primer acceso.');
    }
}
```

**Template de email:** `resources/views/emails/farmer-welcome.blade.php`

#### 11. Actualizar Navegación Admin

```blade
{{-- resources/views/layouts/navigation-vertical.blade.php --}}

@role('Administrador')
<li class="nav-section">Proyectos</li>
<li class="nav-item">
    <a href="{{ route('admin.projects.registration.index') }}">
        <i class="fas fa-plus-circle"></i>
        <span>Registrar Proyecto</span>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.projects.approval.index') }}">
        <i class="fas fa-check-circle"></i>
        <span>Aprobar Proyectos</span>
    </a>
</li>
@endrole
```

### ✅ Criterios de Aceptación v2.0
- [ ] Admin puede crear proyecto con formulario de 3 fases
- [ ] Admin puede crear agricultor automáticamente si no existe
- [ ] Agricultor recibe email con credenciales (password = documento)
- [ ] Formulario wizard funciona correctamente (guardar y continuar)
- [ ] Se pueden agregar N familiares dinámicamente
- [ ] Datos financieros se guardan correctamente en JSON
- [ ] Navegación admin muestra "Registrar" y "Aprobar" separados
- [ ] Agricultor puede ver/editar su proyecto si el admin lo permite
- [ ] El flujo existente del agricultor sigue funcionando
- [ ] Nuevos tipos de documentos disponibles

---

## MÓDULO 4: GESTIÓN DE DOCUMENTOS E IMÁGENES 🔄 REQUIERE ACTUALIZACIÓN v2.0
**Objetivo:** Upload y gestión de documentos/imágenes de proyectos + nuevos tipos

### Estado Actual
- ✅ Upload de documentos funcionando
- ✅ Upload de imágenes con thumbnail
- ✅ Validación de tipos de archivo
- ✅ Storage organizado en public/uploads

### Cambios Requeridos v2.0

#### 1. Nuevos tipos de documentos

Agregar los siguientes tipos al ENUM y a la vista:
- `documento_identidad` - Cédula de ciudadanía
- `nit` - NIT de empresa
- `certificado_bpa` - Buenas Prácticas Agrícolas
- `certificado_ica` - Certificación ICA
- `certificado_invima` - Registro INVIMA
- `documento_tenencia_tierra` - Documento de propiedad
- `contrato_arriendo` - Contrato de arrendamiento
- `permiso_uso_tierra` - Permiso de uso de tierra
- `certificaciones_asociacion` - Certificaciones de asociaciones
- `seguro_cultivo` - Póliza de seguro agrícola
- `cotizaciones_gastos` - Cotizaciones de gastos
- `video_presentacion` - Video de presentación del proyecto
- `foto_empaque` - Foto del empaque del producto
- `foto_producto_terminado` - Foto del producto terminado

#### 2. Actualizar vista de archivos

Modificar `resources/views/farmer/projects/files.blade.php` para:
- Mostrar todos los nuevos tipos de documentos
- Agrupar documentos por categoría (legales, técnicos, financieros, multimedia)
- Permitir subir videos (validar formato y tamaño)

### ✅ Criterios de Aceptación v2.0
- [ ] Todos los nuevos tipos de documentos disponibles
- [ ] Videos pueden subirse (MP4, máx 50MB)
- [ ] Documentos agrupados por categoría en la vista
- [ ] Opción "otro" sigue funcionando con descripción

---

## MÓDULO 5: PROCESO KYC ⏳ PENDIENTE

### Descripción
Verificación de identidad (Know Your Customer) para inversionistas.

### Niveles de Verificación
| Nivel | Requisitos | Límites |
|-------|------------|---------|
| Nivel 0 | Solo registro | Solo ver catálogo |
| Nivel 1 | Documento + Selfie | Invertir hasta $5M COP |
| Nivel 2 | Nivel 1 + Comprobante dirección | Invertir hasta $50M COP |
| Nivel 3 | Nivel 2 + Verificación manual | Sin límites |

### Flujo de Verificación
```
1. Usuario sube documento de identidad (ambos lados)
2. Usuario sube selfie sosteniendo documento
3. Sistema valida calidad de imágenes
4. Admin/Supervisor revisa documentos
5. Aprobación o rechazo con comentarios
6. Notificación al usuario
```

### Tablas de Base de Datos
```sql
kyc_documentos:
  id, user_id
  tipo ENUM('cedula_frontal', 'cedula_posterior', 'selfie', 'comprobante_direccion', 'extracto_bancario')
  nombre_archivo, ruta
  estado ENUM('pendiente', 'aprobado', 'rechazado')
  comentarios
  revisado_por, revisado_at
  created_at, updated_at

kyc_historial:
  id, user_id
  nivel_anterior, nivel_nuevo
  accion ENUM('solicitud', 'aprobacion', 'rechazo')
  motivo, admin_id
  created_at
```

### Archivos del Módulo
```
app/Models/
├── KycDocumento.php
└── KycHistorial.php

app/Services/
└── KycService.php

app/Http/Controllers/
├── Investor/KycController.php
└── Admin/KycReviewController.php

resources/views/investor/
└── kyc/
    ├── index.blade.php
    ├── upload.blade.php
    └── status.blade.php

resources/views/admin/
└── kyc/
    ├── index.blade.php
    └── review.blade.php
```

---

## MÓDULO 6: CATÁLOGO PÚBLICO ⏳ PENDIENTE

### Descripción
Listado público de proyectos disponibles para inversión.

### Funcionalidades
- Listado con filtros (categoría, monto, plazo, ubicación)
- Búsqueda por texto
- Ordenamiento (más recientes, mayor retorno, menor monto)
- Vista de detalle del proyecto
- Calculadora de inversión
- Comparador de proyectos
- Proyectos destacados/promocionados

### Filtros Disponibles
```
- Categoría: STAKING, TRADING, EARN, FUTUROS, CROSS_FUND, FARMING
- Monto mínimo: $100.000 - $50.000.000
- Plazo: 1-36 meses
- Retorno: 5% - 50%
- Ubicación: Departamento, Municipio
- Estado: En recaudación, Próximamente
- Tipo de cultivo
```

### Archivos del Módulo
```
app/Http/Controllers/
└── CatalogController.php

resources/views/catalog/
├── index.blade.php - Listado con filtros
├── show.blade.php - Detalle del proyecto
├── calculator.blade.php - Calculadora
└── compare.blade.php - Comparador
```

---

## MÓDULO 7: SISTEMA DE WALLET ⏳ PENDIENTE

### Descripción
Billetera virtual para manejo de fondos de inversionistas.

### Funcionalidades
- Balance disponible
- Balance en inversiones
- Balance pendiente (retiros/depósitos en proceso)
- Historial de movimientos
- Tipos de movimiento: depósito, retiro, inversión, dividendo, reembolso

### Tablas de Base de Datos
```sql
wallets:
  id, user_id
  balance_disponible DECIMAL(15,2) DEFAULT 0
  balance_invertido DECIMAL(15,2) DEFAULT 0
  balance_pendiente DECIMAL(15,2) DEFAULT 0
  activo BOOLEAN DEFAULT TRUE
  created_at, updated_at

movimientos_wallet:
  id, wallet_id
  tipo ENUM('deposito', 'retiro', 'inversion', 'dividendo', 'reembolso', 'comision', 'ajuste')
  monto DECIMAL(15,2)
  balance_anterior DECIMAL(15,2)
  balance_posterior DECIMAL(15,2)
  referencia_tipo VARCHAR(50) -- 'deposito', 'inversion', etc.
  referencia_id BIGINT UNSIGNED
  descripcion TEXT
  metadata JSON
  created_at
```

### Archivos del Módulo
```
app/Models/
├── Wallet.php
└── MovimientoWallet.php

app/Services/
└── WalletService.php

app/Http/Controllers/Investor/
└── WalletController.php

resources/views/investor/
└── wallet/
    ├── index.blade.php - Balance y resumen
    └── movements.blade.php - Historial
```

---

## MÓDULO 8: SISTEMA DE INVERSIONES ⏳ PENDIENTE

### Descripción
Proceso de inversión en proyectos.

### Flujo de Inversión
```
1. Usuario selecciona proyecto
2. Ingresa monto a invertir
3. Sistema valida:
   - KYC aprobado
   - Balance suficiente
   - Monto dentro de límites
   - Proyecto en recaudación
4. Se reserva el monto
5. Se confirma la inversión
6. Se actualiza wallet y proyecto
7. Se envía confirmación
```

### Límites de Inversión
```
- Mínimo por proyecto: $100.000 COP
- Máximo por proyecto: 20% del monto solicitado
- Máximo total: según nivel KYC
```

### Tablas de Base de Datos
```sql
inversiones:
  id, codigo
  proyecto_id, inversionista_id
  monto DECIMAL(15,2)
  porcentaje_participacion DECIMAL(5,4)
  fecha_inversion TIMESTAMP
  estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada')
  dividendos_recibidos DECIMAL(15,2) DEFAULT 0
  dividendos_pendientes DECIMAL(15,2) DEFAULT 0
  metadata JSON
  created_at, updated_at

participaciones:
  id, inversion_id
  proyecto_id, inversionista_id
  porcentaje DECIMAL(5,4)
  fecha_inicio DATE
  fecha_fin DATE
  estado ENUM('activa', 'finalizada', 'transferida')
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── Inversion.php
└── Participacion.php

app/Services/
└── InvestmentService.php

app/Http/Controllers/Investor/
├── InvestmentController.php
└── PortfolioController.php

resources/views/investor/
├── investments/
│   ├── index.blade.php - Mis inversiones
│   ├── show.blade.php - Detalle inversión
│   └── create.blade.php - Nueva inversión
└── portfolio/
    └── index.blade.php - Portafolio
```

---

## MÓDULO 9: SISTEMA DE DIVIDENDOS ⏳ PENDIENTE

### Descripción
Cálculo y distribución de ganancias a inversionistas.

### Tipos de Distribución
| Tipo | Descripción | Frecuencia |
|------|-------------|------------|
| Mensual | Pagos mensuales fijos | Cada mes |
| Trimestral | Pagos cada 3 meses | Cada trimestre |
| Al finalizar | Pago único al completar | Al cierre |
| Mixto | Anticipos + cierre | Variable |

### Flujo de Dividendos
```
1. Agricultor reporta cosecha/venta
2. Admin valida el reporte
3. Sistema calcula dividendos por participación
4. Se generan los pagos pendientes
5. Admin aprueba distribución
6. Se acreditan las wallets
7. Se notifica a inversionistas
```

### Tablas de Base de Datos
```sql
reportes_proyecto:
  id, proyecto_id
  tipo ENUM('cosecha', 'venta', 'gasto', 'incidente')
  fecha DATE
  descripcion TEXT
  monto_asociado DECIMAL(15,2)
  documentos JSON -- [{nombre, ruta}]
  verificado BOOLEAN DEFAULT FALSE
  verificado_por, verificado_at
  created_at, updated_at

dividendos:
  id, codigo
  proyecto_id, inversion_id, inversionista_id
  periodo_inicio DATE
  periodo_fin DATE
  monto_base DECIMAL(15,2)
  porcentaje_participacion DECIMAL(5,4)
  monto_bruto DECIMAL(15,2)
  retencion DECIMAL(15,2) DEFAULT 0
  monto_neto DECIMAL(15,2)
  estado ENUM('calculado', 'aprobado', 'pagado', 'cancelado')
  fecha_pago TIMESTAMP
  movimiento_wallet_id BIGINT UNSIGNED
  created_at, updated_at

distribucion_dividendos:
  id, proyecto_id
  periodo VARCHAR(20) -- '2025-01', '2025-Q1'
  monto_total DECIMAL(15,2)
  num_inversionistas INT
  estado ENUM('pendiente', 'en_proceso', 'completado')
  procesado_por, procesado_at
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── ReporteProyecto.php
├── Dividendo.php
└── DistribucionDividendos.php

app/Services/
└── DividendService.php

app/Http/Controllers/
├── Farmer/ProjectReportController.php
├── Admin/DividendController.php
└── Investor/DividendController.php

resources/views/
├── farmer/projects/reports/
│   ├── index.blade.php
│   └── create.blade.php
├── admin/dividends/
│   ├── index.blade.php
│   ├── calculate.blade.php
│   └── distribute.blade.php
└── investor/dividends/
    ├── index.blade.php
    └── show.blade.php
```

---

## MÓDULO 10: DEPÓSITOS (MERCADO PAGO) ⏳ PENDIENTE

### Descripción
Integración con Mercado Pago para recarga de wallet.

### Métodos de Pago
- PSE (transferencia bancaria)
- Tarjeta de crédito/débito
- Efectivo (Efecty, Baloto)
- Nequi/Daviplata

### Flujo de Depósito
```
1. Usuario ingresa monto a depositar
2. Selecciona método de pago
3. Se redirige a Mercado Pago
4. Usuario completa el pago
5. Webhook recibe confirmación
6. Se acredita la wallet
7. Se notifica al usuario
```

### Tablas de Base de Datos
```sql
depositos:
  id, codigo
  user_id, wallet_id
  monto DECIMAL(15,2)
  comision DECIMAL(15,2) DEFAULT 0
  monto_neto DECIMAL(15,2)
  metodo_pago VARCHAR(50)
  estado ENUM('pendiente', 'procesando', 'completado', 'fallido', 'cancelado')
  mercadopago_id VARCHAR(255)
  mercadopago_status VARCHAR(50)
  metadata JSON
  ip_address VARCHAR(45)
  completado_at TIMESTAMP
  created_at, updated_at
```

### Archivos del Módulo
```
app/Services/Payment/
├── MercadoPagoService.php
└── PaymentService.php

app/Http/Controllers/Investor/
└── DepositController.php

app/Http/Controllers/Webhooks/
└── MercadoPagoWebhookController.php

resources/views/investor/
└── deposits/
    ├── index.blade.php
    ├── create.blade.php
    ├── success.blade.php
    └── failure.blade.php
```

### Configuración Mercado Pago
```env
MERCADOPAGO_PUBLIC_KEY=your_public_key
MERCADOPAGO_ACCESS_TOKEN=your_access_token
MERCADOPAGO_WEBHOOK_SECRET=your_webhook_secret
```

---

## MÓDULO 11: RETIROS ⏳ PENDIENTE

### Descripción
Sistema de retiro de fondos de la wallet.

### Métodos de Retiro
- Transferencia bancaria
- Nequi/Daviplata

### Flujo de Retiro
```
1. Usuario solicita retiro
2. Ingresa monto y cuenta destino
3. Sistema valida:
   - Balance suficiente
   - Cuenta verificada
   - Límites diarios/mensuales
4. Se descuenta de wallet (pendiente)
5. Admin revisa y aprueba
6. Se procesa la transferencia
7. Se confirma y notifica
```

### Límites de Retiro
```
- Mínimo: $50.000 COP
- Máximo diario: $10.000.000 COP
- Máximo mensual: $50.000.000 COP
```

### Tablas de Base de Datos
```sql
cuentas_bancarias:
  id, user_id
  banco VARCHAR(100)
  tipo_cuenta ENUM('ahorros', 'corriente')
  numero_cuenta VARCHAR(50)
  titular VARCHAR(255)
  documento_titular VARCHAR(50)
  verificada BOOLEAN DEFAULT FALSE
  verificada_at TIMESTAMP
  es_principal BOOLEAN DEFAULT FALSE
  created_at, updated_at, deleted_at

retiros:
  id, codigo
  user_id, wallet_id
  cuenta_bancaria_id
  monto DECIMAL(15,2)
  comision DECIMAL(15,2) DEFAULT 0
  monto_neto DECIMAL(15,2)
  estado ENUM('pendiente', 'en_revision', 'aprobado', 'procesando', 'completado', 'rechazado')
  motivo_rechazo TEXT
  aprobado_por BIGINT UNSIGNED
  aprobado_at TIMESTAMP
  procesado_at TIMESTAMP
  comprobante VARCHAR(255)
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── CuentaBancaria.php
└── Retiro.php

app/Services/
└── WithdrawalService.php

app/Http/Controllers/
├── Investor/WithdrawalController.php
├── Investor/BankAccountController.php
└── Admin/WithdrawalApprovalController.php

resources/views/
├── investor/
│   ├── withdrawals/
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   └── bank-accounts/
│       ├── index.blade.php
│       └── create.blade.php
└── admin/withdrawals/
    ├── index.blade.php
    └── review.blade.php
```

---

## MÓDULO 12: MARKETPLACE DE TRADING ⏳ PENDIENTE

### Descripción
Mercado secundario para compra-venta de participaciones.

### Funcionalidades
- Listar participación en venta
- Ofertar por participaciones
- Negociación de precio
- Transferencia de participación
- Historial de transacciones

### Flujo de Venta
```
1. Inversionista lista su participación
2. Define precio de venta
3. Otros inversionistas ofertan
4. Vendedor acepta oferta
5. Se verifica balance del comprador
6. Se transfiere la participación
7. Se actualiza wallet de ambos
```

### Tablas de Base de Datos
```sql
ofertas_mercado:
  id, codigo
  participacion_id
  vendedor_id
  precio_unitario DECIMAL(15,2)
  porcentaje_en_venta DECIMAL(5,4)
  monto_total DECIMAL(15,2)
  estado ENUM('activa', 'parcial', 'vendida', 'cancelada', 'expirada')
  fecha_expiracion DATE
  created_at, updated_at

transacciones_mercado:
  id, codigo
  oferta_id
  comprador_id, vendedor_id
  participacion_id
  porcentaje_transado DECIMAL(5,4)
  precio_unitario DECIMAL(15,2)
  monto_total DECIMAL(15,2)
  comision_plataforma DECIMAL(15,2)
  estado ENUM('pendiente', 'completada', 'cancelada')
  completada_at TIMESTAMP
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── OfertaMercado.php
└── TransaccionMercado.php

app/Services/
└── TradingService.php

app/Http/Controllers/Investor/
├── MarketplaceController.php
└── TradingController.php

resources/views/investor/
└── marketplace/
    ├── index.blade.php
    ├── my-offers.blade.php
    ├── create-offer.blade.php
    └── buy.blade.php
```

---

## MÓDULO 13: CROSS FUND ⏳ PENDIENTE

### Descripción
Sistema de fondos diversificados que agrupan múltiples proyectos.

### Tipos de Fondos
| Tipo | Descripción | Composición |
|------|-------------|-------------|
| Conservador | Bajo riesgo | 70% STAKING, 30% EARN |
| Moderado | Riesgo medio | 50% EARN, 30% TRADING, 20% FUTUROS |
| Agresivo | Alto riesgo | 40% FUTUROS, 40% TRADING, 20% FARMING |

### Funcionalidades
- Crear fondos con múltiples proyectos
- Definir porcentajes de distribución
- Inversión automática proporcional
- Rebalanceo periódico
- Reportes consolidados

### Tablas de Base de Datos
```sql
fondos:
  id, codigo, nombre, slug
  descripcion, tipo
  monto_minimo DECIMAL(15,2)
  monto_maximo DECIMAL(15,2)
  monto_actual DECIMAL(15,2) DEFAULT 0
  retorno_objetivo DECIMAL(5,2)
  riesgo_nivel ENUM('bajo', 'medio', 'alto')
  estado ENUM('activo', 'cerrado', 'liquidado')
  fecha_cierre DATE
  created_at, updated_at

fondo_proyectos:
  id, fondo_id, proyecto_id
  porcentaje_asignacion DECIMAL(5,2)
  monto_asignado DECIMAL(15,2) DEFAULT 0
  created_at, updated_at

inversiones_fondo:
  id, codigo
  fondo_id, inversionista_id
  monto DECIMAL(15,2)
  porcentaje_participacion DECIMAL(5,4)
  estado ENUM('activa', 'redimida')
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── Fondo.php
├── FondoProyecto.php
└── InversionFondo.php

app/Services/
└── CrossFundService.php

app/Http/Controllers/
├── Admin/FundController.php
└── Investor/CrossFundController.php

resources/views/
├── admin/funds/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
└── investor/funds/
    ├── index.blade.php
    ├── show.blade.php
    └── invest.blade.php
```

---

## MÓDULO 14: CRM DE VENTAS ⏳ PENDIENTE

### Descripción
Sistema de gestión de relaciones con clientes para equipo de ventas.

### Funcionalidades
- Gestión de leads
- Pipeline de ventas
- Seguimiento de interacciones
- Asignación de vendedores
- Comisiones por conversión
- Reportes de rendimiento

### Estados del Lead
```
nuevo → contactado → interesado → en_negociacion → convertido/perdido
```

### Tablas de Base de Datos
```sql
leads:
  id, codigo
  nombre, email, telefono
  fuente ENUM('web', 'referido', 'redes', 'evento', 'otro')
  estado ENUM('nuevo', 'contactado', 'interesado', 'negociacion', 'convertido', 'perdido')
  interes_monto DECIMAL(15,2)
  interes_categoria VARCHAR(50)
  vendedor_id BIGINT UNSIGNED
  notas TEXT
  convertido_user_id BIGINT UNSIGNED
  fecha_conversion TIMESTAMP
  created_at, updated_at

interacciones_lead:
  id, lead_id
  vendedor_id
  tipo ENUM('llamada', 'email', 'whatsapp', 'reunion', 'nota')
  descripcion TEXT
  fecha_programada TIMESTAMP
  fecha_realizada TIMESTAMP
  resultado TEXT
  created_at

comisiones_vendedor:
  id, vendedor_id
  lead_id, inversion_id
  monto_inversion DECIMAL(15,2)
  porcentaje_comision DECIMAL(5,2)
  monto_comision DECIMAL(15,2)
  estado ENUM('pendiente', 'pagada')
  pagada_at TIMESTAMP
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
├── Lead.php
├── InteraccionLead.php
└── ComisionVendedor.php

app/Services/
└── CrmService.php

app/Http/Controllers/Seller/
├── LeadController.php
├── InteractionController.php
└── CommissionController.php

resources/views/seller/
├── leads/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── kanban.blade.php
├── interactions/
│   └── create.blade.php
└── commissions/
    └── index.blade.php
```

---

## MÓDULO 15: SISTEMA DE NOTIFICACIONES ⏳ PENDIENTE

### Descripción
Sistema centralizado de notificaciones multi-canal.

### Canales de Notificación
- Email
- Base de datos (notificaciones in-app)
- SMS (opcional, fase 2)
- Push notifications (opcional, fase 2)

### Tipos de Notificaciones
| Evento | Destinatario | Canales |
|--------|--------------|---------|
| Nuevo proyecto aprobado | Todos inversionistas | Email, DB |
| Inversión confirmada | Inversionista | Email, DB |
| Dividendo disponible | Inversionista | Email, DB |
| Proyecto en recaudación | Inversionistas interesados | Email |
| Retiro procesado | Inversionista | Email, DB |
| KYC aprobado/rechazado | Inversionista | Email, DB |
| Nuevo lead asignado | Vendedor | Email, DB |
| Proyecto requiere revisión | Admin/Supervisor | Email, DB |
| Depósito confirmado | Inversionista | Email, DB |
| Bienvenida agricultor | Agricultor | Email |

### Tablas de Base de Datos
```sql
-- Usa tabla nativa de Laravel: notifications
-- Adicional para preferencias:

preferencias_notificacion:
  id, user_id
  tipo_notificacion VARCHAR(100)
  canal_email BOOLEAN DEFAULT TRUE
  canal_database BOOLEAN DEFAULT TRUE
  canal_sms BOOLEAN DEFAULT FALSE
  created_at, updated_at
```

### Archivos del Módulo
```
app/Notifications/
├── FarmerWelcomeNotification.php
├── InvestmentConfirmedNotification.php
├── DividendAvailableNotification.php
├── WithdrawalProcessedNotification.php
├── KycStatusNotification.php
├── NewLeadAssignedNotification.php
├── ProjectApprovedNotification.php
└── DepositConfirmedNotification.php

app/Services/
└── NotificationService.php

app/Http/Controllers/
└── NotificationController.php

resources/views/
├── notifications/
│   └── index.blade.php
└── emails/
    ├── farmer-welcome.blade.php
    ├── investment-confirmed.blade.php
    ├── dividend-available.blade.php
    └── ...
```

---

## MÓDULO 16: REPORTES Y ANALÍTICAS ⏳ PENDIENTE

### Descripción
Sistema de generación de reportes y dashboards analíticos.

### Reportes Disponibles

**Para Administrador:**
- Resumen general de plataforma
- Proyectos por estado/categoría
- Inversiones por período
- Usuarios por rol/estado KYC
- Retiros pendientes/procesados
- Comisiones de vendedores

**Para Agricultor:**
- Estado de mi proyecto
- Recaudación vs objetivo
- Historial de dividendos distribuidos
- Documentos pendientes

**Para Inversionista:**
- Mi portafolio
- Rendimiento histórico
- Dividendos recibidos
- Movimientos de wallet

**Para Vendedor:**
- Pipeline de ventas
- Conversiones por período
- Comisiones ganadas
- Rendimiento vs objetivos

### Formatos de Exportación
- PDF
- Excel (XLSX)
- CSV

### Archivos del Módulo
```
app/Services/Reports/
├── ReportService.php
├── AdminReportService.php
├── FarmerReportService.php
├── InvestorReportService.php
└── SellerReportService.php

app/Exports/
├── ProjectsExport.php
├── InvestmentsExport.php
├── DividendsExport.php
└── MovementsExport.php

app/Http/Controllers/
├── Admin/ReportController.php
├── Farmer/ReportController.php
├── Investor/ReportController.php
└── Seller/ReportController.php

resources/views/reports/
├── admin/
├── farmer/
├── investor/
└── seller/
```

---

## MÓDULO 17: CONFIGURACIÓN DEL SISTEMA ⏳ PENDIENTE

### Descripción
Panel de configuración general del sistema.

### Configuraciones Disponibles
```
General:
- Nombre de la plataforma
- Logo y favicon
- Información de contacto
- Redes sociales

Financiero:
- Comisión por inversión (%)
- Comisión por trading (%)
- Comisión por retiro
- Límites de inversión por nivel KYC
- Límites de retiro

Proyectos:
- Monto mínimo de proyecto
- Plazo mínimo/máximo
- Tasa de retorno mínima/máxima
- Documentos requeridos por categoría

Usuarios:
- Requisitos de contraseña
- Tiempo de sesión
- Intentos de login

Notificaciones:
- Plantillas de email
- Configuración de SMS
```

### Tablas de Base de Datos
```sql
configuraciones:
  id
  clave VARCHAR(100) UNIQUE
  valor TEXT
  tipo ENUM('string', 'integer', 'decimal', 'boolean', 'json')
  grupo VARCHAR(50) -- 'general', 'financiero', 'proyectos', etc.
  descripcion TEXT
  editable BOOLEAN DEFAULT TRUE
  created_at, updated_at
```

### Archivos del Módulo
```
app/Models/
└── Configuracion.php

app/Services/
└── ConfigurationService.php

app/Http/Controllers/Admin/
└── ConfigurationController.php

resources/views/admin/
└── configuration/
    ├── index.blade.php
    ├── general.blade.php
    ├── financial.blade.php
    ├── projects.blade.php
    └── notifications.blade.php
```

---

## RESUMEN DE ESTADO DE MÓDULOS

| # | Módulo | Estado | Prioridad |
|---|--------|--------|-----------|
| 0 | Template y Layout Base | ✅ Completado | - |
| 1 | Autenticación y Roles | ✅ Completado | - |
| 2 | Dashboards por Rol | ✅ Completado | - |
| 3 | Gestión de Proyectos | 🔄 En Actualización v2.0 | Alta |
| 4 | Documentos e Imágenes | 🔄 En Actualización v2.0 | Alta |
| 5 | Proceso KYC | ⏳ Pendiente | Alta |
| 6 | Catálogo Público | ⏳ Pendiente | Alta |
| 7 | Sistema de Wallet | ⏳ Pendiente | Alta |
| 8 | Sistema de Inversiones | ⏳ Pendiente | Alta |
| 9 | Sistema de Dividendos | ⏳ Pendiente | Media |
| 10 | Depósitos (Mercado Pago) | ⏳ Pendiente | Alta |
| 11 | Retiros | ⏳ Pendiente | Alta |
| 12 | Marketplace Trading | ⏳ Pendiente | Baja |
| 13 | Cross Fund | ⏳ Pendiente | Baja |
| 14 | CRM de Ventas | ⏳ Pendiente | Media |
| 15 | Sistema de Notificaciones | ⏳ Pendiente | Media |
| 16 | Reportes y Analíticas | ⏳ Pendiente | Baja |
| 17 | Configuración del Sistema | ⏳ Pendiente | Baja |

---

## MIGRACIONES v2.0

### Orden de ejecución:

```bash
# 1. Crear tabla perfiles_agricultor
php artisan make:migration create_perfiles_agricultor_table

# 2. Crear tabla familia_agricultor
php artisan make:migration create_familia_agricultor_table

# 3. Agregar campos a users
php artisan make:migration add_foto_and_admin_fields_to_users_table

# 4. Agregar campos expandidos a proyectos
php artisan make:migration add_expanded_fields_to_proyectos_table

# 5. Actualizar ENUM de documentos
php artisan make:migration update_documentos_proyecto_tipos
```

---

## ORDEN DE IMPLEMENTACIÓN v2.0

### Sprint 1: Base de Datos y Modelos
1. Crear migraciones
2. Ejecutar migraciones
3. Crear modelos nuevos (PerfilAgricultor, FamiliaAgricultor)
4. Actualizar modelos existentes (User, Proyecto, DocumentoProyecto)
5. Crear seeders de prueba

### Sprint 2: Backend - Servicios y Controladores
1. Crear FarmerCreationService
2. Crear ProjectFormService
3. Actualizar ProjectService
4. Crear ProjectRegistrationController (Admin)
5. Actualizar ProjectController (Farmer)
6. Crear Form Requests para cada fase

### Sprint 3: Frontend - Vistas Admin
1. Crear wizard-steps component
2. Crear vistas de registro de proyectos (admin)
3. Crear formulario Fase 1
4. Crear formulario Fase 2 (con familia dinámica)
5. Crear formulario Fase 3 (con financiero y riesgos)
6. Actualizar navegación admin

### Sprint 4: Frontend - Vistas Farmer
1. Actualizar create.blade.php como wizard
2. Crear partials de cada fase
3. Actualizar show.blade.php para mostrar toda la info
4. Actualizar files.blade.php con nuevos tipos de documentos

### Sprint 5: Notificaciones y Pruebas
1. Crear FarmerWelcomeNotification
2. Crear template de email
3. Pruebas manuales del flujo completo
4. Corrección de bugs

---

## CHECKLIST GENERAL ACTUALIZADO

**Fase 1: Fundación**
- [x] Módulo 0: Template y Layout ✅ COMPLETADO
- [x] Módulo 1: Autenticación y Roles ✅ COMPLETADO
- [x] Módulo 2: Dashboards por Rol ✅ COMPLETADO

**Fase 2: Gestión de Proyectos (ACTUALIZAR)**
- [ ] Módulo 3: Gestión de Proyectos 🔄 ACTUALIZAR para v2.0
- [ ] Módulo 4: Documentos e Imágenes 🔄 ACTUALIZAR para v2.0
- [ ] Módulo 5: Proceso KYC
- [ ] Módulo 6: Catálogo Público

**Fase 3: Core del Negocio**
- [ ] Módulo 7: Sistema de Billetera
- [ ] Módulo 8: Sistema de Inversiones
- [ ] Módulo 9: Sistema de Dividendos

**Fase 4: Operaciones Financieras**
- [ ] Módulo 10: Gestión de Depósitos (Mercado Pago)
- [ ] Módulo 11: Gestión de Retiros

**Fase 5: Funcionalidades Avanzadas**
- [ ] Módulo 12: Marketplace Trading
- [ ] Módulo 13: Cross Fund

**Fase 6: CRM y Comunicación**
- [ ] Módulo 14: CRM Vendedores
- [ ] Módulo 15: Sistema de Notificaciones

**Fase 7: Administración**
- [ ] Módulo 16: Reportes y Analytics
- [ ] Módulo 17: Configuración del Sistema

---

## NOTAS IMPORTANTES v2.0

1. **Contraseña temporal**: La contraseña del agricultor = su documento de identidad
2. **El agricultor puede no usar la app**: Todo debe poder ser completado por el admin
3. **Formulario wizard**: Debe permitir guardar y continuar después
4. **JSON para datos financieros**: Más flexible para reportes y modificaciones futuras
5. **Vendedor Supervisor**: Se implementará en fase posterior (no incluir ahora)

---

## CONCLUSIÓN

Este plan de trabajo v2.0 incorpora los nuevos requerimientos del cliente:
- Admin puede crear proyectos y agricultores
- Formulario expandido a 3 fases
- Nuevos campos y tablas en la base de datos
- Separación de módulos Admin (Registrar vs Aprobar)

**Próximos pasos:**
1. Ejecutar migraciones v2.0
2. Actualizar modelos
3. Implementar Sprint 1-5 para completar Módulos 3 y 4 actualizados

---

*Documento actualizado: 2025-12-15*
*Versión: 2.0*
