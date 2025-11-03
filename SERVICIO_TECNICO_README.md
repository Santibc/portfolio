# Módulo de Servicio Técnico

## Descripción General

Módulo completo de gestión de servicio técnico para una empresa de cámaras de seguridad. Este módulo está **completamente aislado** de los demás módulos del sistema en términos de base de datos, con sus propias tablas de clientes, equipos y gestión operativa.

---

## Características Principales

### 1. **Gestión de Clientes (Aislados)**
- Registro de clientes exclusivos para servicio técnico
- Tipos de cliente: Particular y Empresa
- Información completa de contacto
- Historial de equipos y órdenes de servicio

### 2. **Gestión de Equipos/Cámaras**
- Registro detallado de equipos de seguridad
- Tipos: Cámaras IP, Cámaras Analógicas, DVR, NVR, Control de Acceso
- Tracking de garantías
- Estados: Operativo, En Reparación, Fuera de Servicio
- Especificaciones técnicas (MAC, IP, modelo, serial)

### 3. **Órdenes de Servicio**
- Sistema completo de tickets de servicio
- Estados: Recibida, Asignada, En Proceso, Pendiente Repuestos, Completada, Entregada, Cancelada
- Prioridades: Baja, Media, Alta, Urgente
- Tipos de servicio: Reparación, Mantenimiento Preventivo, Instalación, Diagnóstico, Garantía
- Tracking de fechas (recepción, promesa de entrega, inicio, finalización)
- Cálculo automático de costos

### 4. **Gestión de Técnicos**
- Registro de técnicos especializados
- Especialidades: CCTV, DVR/NVR, Control Acceso, Alarmas, Redes
- Certificaciones
- Monitoreo de carga de trabajo

### 5. **Inventario de Repuestos**
- Control de stock de componentes
- Categorías: Lentes, Placas, Fuentes, Cables, Sensores, Almacenamiento
- Alertas de stock bajo
- Precios de costo y venta
- Ubicación en bodega

### 6. **Diagnósticos Técnicos**
- Registro detallado de diagnósticos por orden
- Fallas encontradas
- Reparaciones realizadas
- Recomendaciones al cliente
- Aprobación de trabajos

### 7. **Historial y Auditoría**
- Registro completo de cambios de estado
- Historial de servicios por equipo
- Trazabilidad de movimientos de repuestos
- Imágenes de respaldo (recepción, diagnóstico, reparación, entrega)

### 8. **Dashboard Estadístico**
- Órdenes pendientes y por estado
- Órdenes urgentes y retrasadas
- Carga de trabajo por técnico
- Ingresos del mes
- Equipos en reparación
- Alertas de repuestos

---

## Estructura de Base de Datos

### Tablas Creadas

| Tabla | Descripción |
|-------|-------------|
| `st_clientes` | Clientes exclusivos de servicio técnico |
| `st_tecnicos` | Técnicos especializados |
| `st_equipos` | Equipos/cámaras registrados |
| `st_ordenes_servicio` | Órdenes de trabajo |
| `st_diagnosticos` | Diagnósticos técnicos |
| `st_repuestos` | Inventario de repuestos |
| `st_repuestos_usados` | Repuestos utilizados en órdenes |
| `st_historial_estados` | Auditoría de cambios de estado |
| `st_imagenes_orden` | Imágenes de evidencia |

**Nota:** Todas las tablas tienen el prefijo `st_` (Servicio Técnico) para mantener el aislamiento.

---

## Rutas del Módulo

Todas las rutas están bajo el prefijo `/servicio-tecnico` y requieren autenticación:

### Dashboard
- `GET /servicio-tecnico/dashboard` - Dashboard principal

### Clientes
- `GET /servicio-tecnico/clientes` - Listado de clientes
- `GET /servicio-tecnico/clientes/create` - Formulario nuevo cliente
- `POST /servicio-tecnico/clientes` - Guardar cliente
- `GET /servicio-tecnico/clientes/{id}` - Ver cliente
- `GET /servicio-tecnico/clientes/{id}/edit` - Editar cliente
- `PUT /servicio-tecnico/clientes/{id}` - Actualizar cliente
- `DELETE /servicio-tecnico/clientes/{id}` - Desactivar cliente

### Técnicos
- Rutas resource similares a clientes

### Equipos
- Rutas resource + `GET /servicio-tecnico/equipos/cliente/{cliente}` - Equipos por cliente

### Órdenes de Servicio
- Rutas resource
- `POST /servicio-tecnico/ordenes/{id}/cambiar-estado` - Cambiar estado
- `GET /servicio-tecnico/ordenes/{id}/pdf` - Generar PDF
- `GET /servicio-tecnico/equipos-cliente/{clienteId}` - AJAX para equipos

### Repuestos
- Rutas resource
- `GET /servicio-tecnico/repuestos-json` - JSON para selects

### Diagnósticos
- `POST /servicio-tecnico/diagnosticos` - Crear diagnóstico
- `PUT /servicio-tecnico/diagnosticos/{id}` - Actualizar diagnóstico

---

## Acceso al Módulo

### Navegación
El módulo se agregó al menú vertical principal en:
```
resources/views/layouts/navigation-vertical.blade.php
```

Solo visible para usuarios con rol **admin**.

### Icono
- Icono Bootstrap: `bi bi-tools`
- Nombre: "Servicio Técnico"

---

## Modelos y Relaciones

### STCliente
```php
- equipos() -> hasMany(STEquipo)
- ordenesServicio() -> hasMany(STOrdenServicio)
```

### STTecnico
```php
- ordenesServicio() -> hasMany(STOrdenServicio)
- diagnosticos() -> hasMany(STDiagnostico)
```

### STEquipo
```php
- cliente() -> belongsTo(STCliente)
- ordenesServicio() -> hasMany(STOrdenServicio)
```

### STOrdenServicio
```php
- cliente() -> belongsTo(STCliente)
- equipo() -> belongsTo(STEquipo)
- tecnico() -> belongsTo(STTecnico)
- diagnosticos() -> hasMany(STDiagnostico)
- repuestosUsados() -> hasMany(STRepuestoUsado)
- historialEstados() -> hasMany(STHistorialEstado)
- imagenes() -> hasMany(STImagenOrden)
```

---

## Métodos Importantes

### STOrdenServicio

#### `calcularCostoTotal()`
Calcula automáticamente el costo total de la orden sumando mano de obra + repuestos.

```php
$orden->calcularCostoTotal();
```

#### `cambiarEstado($nuevoEstado, $observaciones = null)`
Cambia el estado de la orden y registra en historial.

```php
$orden->cambiarEstado('en_proceso', 'Técnico comenzó reparación');
```

### STRepuesto

#### `ajustarStock($cantidad, $tipo = 'salida')`
Ajusta el stock del repuesto (entrada o salida).

```php
$repuesto->ajustarStock(5, 'entrada'); // Sumar 5
$repuesto->ajustarStock(2, 'salida');  // Restar 2
```

---

## Scopes Útiles

### STCliente
- `activos()` - Solo clientes activos
- `empresas()` - Solo tipo empresa
- `particulares()` - Solo tipo particular

### STOrdenServicio
- `porEstado($estado)` - Filtrar por estado
- `recibidas()` - Estado recibida
- `enProceso()` - Estado en proceso
- `completadas()` - Estado completada
- `urgentes()` - Prioridad urgente
- `porTecnico($tecnicoId)` - Asignadas a un técnico

### STRepuesto
- `activos()` - Repuestos activos
- `conStockBajo()` - Stock actual <= stock mínimo
- `sinStock()` - Stock actual <= 0

---

## Datos de Ejemplo (Seeder)

Ejecutar el seeder crea:
- ✅ 3 Técnicos (Carlos, Ana María, Pedro)
- ✅ 3 Clientes (2 empresas, 1 particular)
- ✅ 4 Equipos (Cámaras IP, NVR, DVR, Cámara Analógica)
- ✅ 4 Repuestos (Fuente, Cable UTP, Lente, Disco duro)
- ✅ 3 Órdenes de Servicio (En proceso, Completada, Recibida)
- ✅ 2 Diagnósticos

```bash
php artisan db:seed --class=ServicioTecnicoSeeder
```

---

## Controladores

### Namespace: `App\Http\Controllers\ServicioTecnico`

| Controlador | Responsabilidad |
|-------------|-----------------|
| `DashboardSTController` | Dashboard y estadísticas |
| `STClienteController` | CRUD de clientes ST |
| `STTecnicoController` | CRUD de técnicos |
| `STEquipoController` | CRUD de equipos |
| `STOrdenServicioController` | CRUD de órdenes + cambios de estado |
| `STRepuestoController` | CRUD de repuestos |
| `STDiagnosticoController` | Crear/editar diagnósticos |

---

## Vistas Principales

### Dashboard
`resources/views/servicio-tecnico/dashboard.blade.php`
- Tarjetas de estadísticas
- Gráfico de órdenes por estado (Chart.js)
- Carga de trabajo de técnicos
- Últimas 10 órdenes
- Accesos rápidos

### Clientes
`resources/views/servicio-tecnico/clientes/index.blade.php`
- DataTable con paginación server-side
- Filtros por tipo y estado
- Botones de acción (ver, editar, desactivar)

### Órdenes de Servicio
`resources/views/servicio-tecnico/ordenes/index.blade.php`
- DataTable con filtros avanzados
- Filtros por estado, prioridad y técnico
- Badges de estado y prioridad
- Indicador de días transcurridos

---

## Funcionalidades Pendientes (Extensiones Futuras)

Puedes agregar:
- ✅ **Formularios de clientes**: Crear/editar clientes
- ✅ **Formularios de equipos**: Crear/editar equipos
- ✅ **Formularios de técnicos**: Crear/editar técnicos
- ✅ **Formulario de orden de servicio**: Crear/editar órdenes completas
- ✅ **Vista detallada de orden**: Ver orden con diagnósticos, repuestos, imágenes
- ✅ **Subida de imágenes**: Para evidencias de recepción/reparación/entrega
- ✅ **Generación de PDF**: Orden de servicio imprimible para el cliente
- ✅ **Reportes**: Ventas por técnico, equipos más reparados, etc.
- ✅ **Notificaciones**: Email/SMS cuando cambia el estado de la orden
- ✅ **Portal del cliente**: Link temporal para que el cliente vea el estado de su orden

---

## Integración con el Sistema Existente

### Aislamiento Total
- **NO comparte** la tabla `clientes` principal
- **NO comparte** productos o catálogo
- **SÍ comparte** la tabla `users` para asignar quién creó la orden

### Usuario Creador
Las órdenes tienen un campo `user_id` que referencia al usuario autenticado que creó la orden (vendedor/admin), pero los clientes son independientes.

---

## Permisos

Actualmente el módulo está disponible solo para:
- ✅ **Rol: admin**

Si deseas agregarlo para vendedores u otros roles, modifica la condición en `navigation-vertical.blade.php`:

```php
@if(auth()->user()->hasRole(['admin', 'tecnico']))
    <a href="{{ route('st.dashboard') }}" ...>
```

---

## Tecnologías Utilizadas

- **Backend**: Laravel 9
- **Base de datos**: MySQL (tablas con prefijo `st_`)
- **Frontend**: Blade + Bootstrap 5 + Bootstrap Icons
- **DataTables**: Yajra DataTables para listados
- **JavaScript**: jQuery, SweetAlert2 para confirmaciones
- **Gráficos**: Chart.js para dashboard

---

## Comandos Útiles

### Ejecutar migraciones
```bash
php artisan migrate
```

### Revertir migraciones del módulo
```bash
php artisan migrate:rollback --step=9
```

### Ejecutar seeder
```bash
php artisan db:seed --class=ServicioTecnicoSeeder
```

### Ver rutas del módulo
```bash
php artisan route:list --path=servicio-tecnico
```

---

## Estructura de Archivos

```
app/
├── Http/Controllers/ServicioTecnico/
│   ├── DashboardSTController.php
│   ├── STClienteController.php
│   ├── STTecnicoController.php
│   ├── STEquipoController.php
│   ├── STOrdenServicioController.php
│   ├── STRepuestoController.php
│   └── STDiagnosticoController.php
├── Models/
│   ├── STCliente.php
│   ├── STTecnico.php
│   ├── STEquipo.php
│   ├── STOrdenServicio.php
│   ├── STDiagnostico.php
│   ├── STRepuesto.php
│   ├── STRepuestoUsado.php
│   ├── STHistorialEstado.php
│   └── STImagenOrden.php

database/
├── migrations/
│   ├── 2025_11_02_120001_create_st_clientes_table.php
│   ├── 2025_11_02_120002_create_st_tecnicos_table.php
│   ├── 2025_11_02_120003_create_st_equipos_table.php
│   ├── 2025_11_02_120004_create_st_ordenes_servicio_table.php
│   ├── 2025_11_02_120005_create_st_diagnosticos_table.php
│   ├── 2025_11_02_120006_create_st_repuestos_table.php
│   ├── 2025_11_02_120007_create_st_repuestos_usados_table.php
│   ├── 2025_11_02_120008_create_st_historial_estados_table.php
│   └── 2025_11_02_120009_create_st_imagenes_orden_table.php
└── seeders/
    └── ServicioTecnicoSeeder.php

resources/views/servicio-tecnico/
├── dashboard.blade.php
├── clientes/
│   └── index.blade.php
└── ordenes/
    └── index.blade.php

routes/
└── web.php (líneas 172-214: rutas del módulo)
```

---

## Soporte y Mantenimiento

### Logging
Los errores se registran automáticamente en `storage/logs/laravel.log`.

### Respaldos
Asegúrate de incluir las tablas `st_*` en tus respaldos de base de datos.

---

## Contacto

Para preguntas o mejoras del módulo, contacta al equipo de desarrollo.

---

## Licencia

Este módulo es parte del sistema Laravel interno de la empresa.

---

**Creado por:** Claude Code Assistant
**Fecha:** 02/11/2025
**Versión:** 1.0.0
