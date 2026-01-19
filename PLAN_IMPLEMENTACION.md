# Plan de Implementación - Miracle Platform

## Documento de Referencia
**Requerimientos**: `Miracle - Requerimientos y alcance.pdf` (22/12/2025)
**Excluido**:
- Integración con SIIGO (pendiente)
- Soporte para lector de código de barras (pendiente)

---

## REGLAS DE ORO

### Principios SOLID
1. **S - Single Responsibility**: Cada clase/componente tiene una única responsabilidad
2. **O - Open/Closed**: Abierto para extensión, cerrado para modificación
3. **L - Liskov Substitution**: Las clases hijas deben poder sustituir a las padres
4. **I - Interface Segregation**: Interfaces específicas mejor que una general
5. **D - Dependency Inversion**: Depender de abstracciones, no de implementaciones

### Buenas Prácticas Laravel
- **Form Requests** para validación (no validar en controladores)
- **Services** para lógica de negocio compleja
- **Repositories** solo si hay múltiples fuentes de datos
- **Events/Listeners** para acciones desacopladas (emails, notificaciones)
- **Observers** para hooks de modelos (creating, updating, deleting)
- **Policies** para autorización granular
- **Resources/Collections** para transformación de datos API

### Componentes Blade Reutilizables
- Crear componentes en `resources/views/components/`
- Usar `x-component` syntax
- Documentar props y slots
- Ejemplos a crear:
  - `x-modal` - Modal genérico con slots
  - `x-datatable` - Wrapper DataTables
  - `x-form-group` - Input con label y errores
  - `x-status-badge` - Badge de estado con colores
  - `x-card-metric` - Tarjeta de métrica dashboard
  - `x-alert` - Alertas consistentes
  - `x-confirm-button` - Botón con confirmación SweetAlert

### Patrones de Arquitectura
- **Service Layer**: Lógica de negocio en `app/Services/`
- **Repository Pattern**: Solo si se justifica (múltiples DBs)
- **Strategy Pattern**: Para exportaciones (PDF, Excel, CSV)
- **Observer Pattern**: Para eventos de modelos
- **State Pattern**: Para estados de cotización/pedido

### Convenciones de Código
- Nombres en español para entidades de negocio
- Comentarios en español
- Variables y métodos en camelCase
- Clases en PascalCase
- Tablas en snake_case plural
- Validaciones con Form Requests
- Respuestas JSON consistentes: `{success, message, data}`

---

## CHECKLIST DE PROGRESO

### Fase 1: Fundamentos y Correcciones
- [x] 1.1 Corregir responsive del botón Salir
- [x] 1.2 Arreglar importación de Excel
- [x] 1.3 Implementar restablecimiento de contraseña
- [x] 1.4 Crear componentes Blade reutilizables
- [x] 1.5 Configurar sistema de roles completo

### Fase 2: Módulo de Clientes (Mejoras)
- [x] 2.1 CRUD completo de clientes
- [x] 2.2 Campos según tipo (persona natural/jurídica)
- [x] 2.3 Gestión de sucursales por cliente
- [x] 2.4 Carga de documentos PDF
- [x] 2.5 Corregir columna de orden duplicada
- [x] 2.6 Restricción: solo Admin e Inventarios pueden crear/editar

### Fase 3: Módulo de Categorías y Productos
- [x] 3.1 CRUD completo de categorías con referencia única
- [x] 3.2 Cambiar "talla" por "referencia" en variantes
- [x] 3.3 CRUD completo de listas de precios
- [x] 3.4 Activar/desactivar listas de precios
- [x] 3.5 Reportes con imágenes de productos
- [x] 3.6 Corregir importación de archivos

### Fase 4: Sistema de Stock Multi-Ubicación
- [x] 4.1 Crear modelo Ubicacion (Bodega, Tienda)
- [x] 4.2 Refactorizar stock por ubicación
- [x] 4.3 Implementar traslados entre ubicaciones
- [x] 4.4 Indicar tipo operación (crédito/general)
- [x] 4.5 Notas de entrada/salida como documentos
- [x] 4.6 Manejo de garantías, saldos y pérdidas

### Fase 5: Módulo de Cotizaciones (CRUD Completo)
- [x] 5.1 CRUD completo de cotizaciones
- [x] 5.2 Editar/retomar cotizaciones antiguas
- [x] 5.3 Edición completa al aprobar (lista desplegable)
- [x] 5.4 Reserva de productos por 24h
- [x] 5.5 Liberación automática de reservas expiradas
- [x] 5.6 Observaciones obligatorias del vendedor

### Fase 6: Flujo Post-Cotización
- [x] 6.1 Envío de correo al aceptar cotización
- [x] 6.2 Creación automática de cuenta cliente
- [x] 6.3 Confirmación de pago con comprobante
- [x] 6.4 Botón "Generar factura" (sin SIIGO)
- [x] 6.5 Correos de alerta por cotización aceptada

### Fase 7: Módulo Cliente (Portal)
- [x] 7.1 Rol cliente con acceso limitado
- [x] 7.2 Dashboard de historial de compras
- [x] 7.3 Seguimiento de cotización y envío
- [x] 7.4 Descarga de guía cuando estado="Despachado"
- [x] 7.5 Descarga de factura cuando disponible
- [x] 7.6 Notificaciones por cambio de estado

### Fase 8: Módulo Punto de Venta (NUEVO)
- [x] 8.1 Dashboard PdV con métricas
- [x] 8.2 Gestión de inventario PdV (integrado con stock multi-ubicación)
- [x] 8.3 Movimientos Bodega ↔ PdV (usa traslados existentes)
- [x] 8.4 Registro de garantías/saldos/pérdidas (usa novedades existentes)
- [x] 8.5 Métricas exclusivas de ventas PdV
- [x] 8.6 Exportación PDF (ticket de venta) y reportes

### Fase 9: Catálogo y Métricas
- [x] 9.1 Reorganizar campos del catálogo (verificado: código correcto, no requirió cambios)
- [x] 9.2 Reportes Excel/PDF con colores y filtros
- [x] 9.3 Métricas de ventas, cotizaciones aceptadas/pendientes

### Fase 10: Roles y Permisos
- [x] 10.1 Crear rol "inventarios"
- [x] 10.2 Crear rol "facturación"
- [x] 10.3 Crear rol "punto_venta"
- [x] 10.4 Crear rol "cliente"
- [x] 10.5 Middleware de protección por rol
- [x] 10.6 Vendedor sin permiso de crear clientes
- [x] 10.7 Vendedor sin permiso de cambiar lista de precios

### Fase 11: Validación Final
- [ ] 11.1 Pruebas de cada módulo con Playwright
- [ ] 11.2 Validación de flujos completos
- [ ] 11.3 Pruebas de responsive
- [ ] 11.4 Verificación de permisos por rol

---

## DETALLE POR FASE

### FASE 1: Fundamentos y Correcciones

#### 1.1 Corregir Responsive del Botón Salir
**Archivos a modificar:**
- `resources/views/layouts/navigation-vertical.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/css/app.css` (o Tailwind config)

**Acciones:**
1. Revisar CSS del botón logout en diferentes breakpoints
2. Ajustar clases responsive (sm, md, lg)
3. Corregir solapamiento de botones
4. Testear en móvil, tablet, desktop

#### 1.2 Arreglar Importación de Excel
**Archivos a modificar:**
- `app/Imports/ProductosImport.php`
- `app/Imports/PreciosImport.php`
- `app/Http/Controllers/ImportacionProductosController.php`

**Acciones:**
1. Revisar formato esperado vs formato recibido
2. Validar headers del archivo
3. Mejorar manejo de errores y feedback
4. Crear validaciones más robustas
5. Implementar log de errores por fila

#### 1.3 Restablecimiento de Contraseña
**Archivos a crear/modificar:**
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `app/Http/Controllers/Auth/PasswordResetController.php`

**Acciones:**
1. Verificar configuración de mail en `.env`
2. Implementar vistas de forgot/reset password
3. Configurar notificación de reset
4. Testear flujo completo

#### 1.4 Crear Componentes Blade Reutilizables
**Archivos a crear:**
```
resources/views/components/
├── modal.blade.php
├── datatable.blade.php
├── form-group.blade.php
├── status-badge.blade.php
├── card-metric.blade.php
├── alert.blade.php
├── confirm-button.blade.php
├── file-upload.blade.php
└── select-ajax.blade.php
```

**Ejemplo componente modal:**
```php
// resources/views/components/modal.blade.php
@props(['id', 'title', 'size' => 'md'])
<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-{{ $size }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($footer))
            <div class="modal-footer">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
```

#### 1.5 Configurar Sistema de Roles Completo
**Archivos a modificar:**
- `database/seeders/RolesAndPermissionsSeeder.php`
- `app/Http/Kernel.php`
- `app/Http/Middleware/CheckRole.php` (crear)

**Acciones:**
1. Crear seeder funcional con todos los roles
2. Definir permisos granulares por módulo
3. Crear middleware `CheckRole`
4. Registrar middleware en Kernel
5. Aplicar middleware a rutas

---

### FASE 2: Módulo de Clientes (Mejoras)

#### 2.1-2.6 Mejoras al CRUD de Clientes
**Archivos a modificar:**
- `app/Models/Cliente.php`
- `app/Http/Controllers/ClientesController.php`
- `resources/views/clientes/form.blade.php`
- `resources/views/clientes/index.blade.php`

**Migraciones a crear:**
```php
// add_tipo_cliente_to_clientes_table.php
$table->enum('tipo_cliente', ['natural', 'juridica'])->default('natural');
$table->string('razon_social')->nullable();
$table->string('nit')->nullable();
$table->string('representante_legal')->nullable();
$table->decimal('valor_flete', 10, 2)->nullable();
$table->boolean('aplica_flete')->default(false);

// create_sucursales_table.php
Schema::create('sucursales', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
    $table->string('nombre');
    $table->string('direccion');
    $table->foreignId('ciudad_id')->nullable()->constrained();
    $table->string('telefono')->nullable();
    $table->string('contacto')->nullable();
    $table->boolean('es_principal')->default(false);
    $table->timestamps();
});

// create_documentos_cliente_table.php
Schema::create('documentos_cliente', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
    $table->string('nombre');
    $table->string('archivo');
    $table->string('tipo')->nullable();
    $table->timestamps();
});
```

**Modelos a crear:**
- `app/Models/Sucursal.php`
- `app/Models/DocumentoCliente.php`

---

### FASE 3: Módulo de Categorías y Productos

#### 3.1 CRUD Categorías con Referencia Única
**Archivos a modificar:**
- `app/Models/Categoria.php`
- `app/Http/Controllers/CategoriasController.php`
- `resources/views/categorias/form.blade.php`

**Migración:**
```php
// add_referencia_to_categorias_table.php
$table->string('referencia')->unique()->after('nombre');
```

#### 3.2 Cambiar "talla" por "referencia" en Variantes
**Archivos a modificar:**
- `app/Models/VarianteProducto.php`
- `database/migrations/xxxx_variantes_producto.php`
- Vistas de productos

**Migración:**
```php
// rename_talla_to_referencia_in_variantes.php
$table->renameColumn('talla', 'referencia_variante');
```

#### 3.3-3.5 Gestión de Listas de Precios
**Archivos a crear:**
- `app/Http/Controllers/ListaPreciosController.php`
- `resources/views/listas-precios/index.blade.php`
- `resources/views/listas-precios/form.blade.php`

**Migración:**
```php
// add_activo_to_lista_precios.php
$table->boolean('activo')->default(true)->after('nombre');
```

---

### FASE 4: Sistema de Stock Multi-Ubicación

#### 4.1 Crear Modelo Ubicación
**Archivos a crear:**
- `app/Models/Ubicacion.php`
- `database/migrations/create_ubicaciones_table.php`

```php
// create_ubicaciones_table.php
Schema::create('ubicaciones', function (Blueprint $table) {
    $table->id();
    $table->string('nombre'); // Bodega, Tienda, etc.
    $table->string('codigo')->unique();
    $table->enum('tipo', ['bodega', 'tienda', 'otro']);
    $table->string('direccion')->nullable();
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
```

#### 4.2 Refactorizar Stock por Ubicación
**Migración:**
```php
// add_ubicacion_id_to_stock_productos.php
$table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones');
// Crear índice compuesto
$table->unique(['producto_id', 'variante_producto_id', 'ubicacion_id'], 'stock_unique');
```

#### 4.3 Implementar Traslados
**Archivos a crear:**
- `app/Models/TrasladoStock.php`
- `app/Http/Controllers/TrasladoController.php`
- `app/Services/TrasladoService.php`
- `resources/views/stock/traslados.blade.php`

```php
// create_traslados_stock_table.php
Schema::create('traslados_stock', function (Blueprint $table) {
    $table->id();
    $table->string('numero_traslado')->unique();
    $table->foreignId('ubicacion_origen_id')->constrained('ubicaciones');
    $table->foreignId('ubicacion_destino_id')->constrained('ubicaciones');
    $table->foreignId('producto_id')->constrained();
    $table->foreignId('variante_producto_id')->nullable()->constrained();
    $table->integer('cantidad');
    $table->enum('estado', ['pendiente', 'completado', 'cancelado'])->default('pendiente');
    $table->text('notas')->nullable();
    $table->foreignId('usuario_id')->constrained('users');
    $table->timestamp('completado_en')->nullable();
    $table->timestamps();
});
```

#### 4.4 Tipo de Operación (Crédito/General)
**Migración:**
```php
// add_tipo_operacion_to_movimientos_stock.php
$table->enum('tipo_operacion', ['contado', 'credito', 'general'])->default('general');
```

#### 4.5 Notas de Entrada/Salida como Documentos
**Archivos a crear:**
- `app/Services/NotaMovimientoService.php`
- `resources/views/pdf/nota-entrada.blade.php`
- `resources/views/pdf/nota-salida.blade.php`

#### 4.6 Manejo de Garantías, Saldos y Pérdidas
**Migración:**
```php
// create_novedades_stock_table.php
Schema::create('novedades_stock', function (Blueprint $table) {
    $table->id();
    $table->foreignId('producto_id')->constrained();
    $table->foreignId('variante_producto_id')->nullable()->constrained();
    $table->foreignId('ubicacion_id')->constrained('ubicaciones');
    $table->enum('tipo', ['garantia', 'saldo', 'perdida']);
    $table->integer('cantidad');
    $table->decimal('valor_original', 10, 2);
    $table->decimal('valor_saldo', 10, 2)->nullable();
    $table->text('descripcion');
    $table->foreignId('usuario_id')->constrained('users');
    $table->timestamps();
});
```

---

### FASE 5: Módulo de Cotizaciones (CRUD Completo)

#### 5.1-5.3 CRUD Completo
**Archivos a modificar/crear:**
- `app/Http/Controllers/SolicitudController.php`
- `app/Services/CotizacionService.php`
- `resources/views/solicitudes/form.blade.php`
- `resources/views/solicitudes/edit.blade.php`

**Migraciones:**
```php
// add_fields_to_solicitudes_cotizacion.php
$table->decimal('valor_flete', 10, 2)->nullable();
$table->decimal('descuento_total', 10, 2)->nullable();
$table->text('observaciones_vendedor')->nullable();
$table->boolean('observaciones_obligatorias')->default(true);
$table->softDeletes();
```

#### 5.4-5.5 Sistema de Reservas con Expiración
**Archivos a crear:**
- `app/Models/ReservaStock.php`
- `app/Console/Commands/LiberarReservasExpiradas.php`
- `app/Services/ReservaService.php`

```php
// create_reservas_stock_table.php
Schema::create('reservas_stock', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_cotizacion_id')->constrained()->onDelete('cascade');
    $table->foreignId('stock_producto_id')->constrained()->onDelete('cascade');
    $table->integer('cantidad_reservada');
    $table->timestamp('expira_en');
    $table->timestamp('liberada_en')->nullable();
    $table->enum('estado', ['activa', 'aplicada', 'expirada', 'liberada'])->default('activa');
    $table->timestamps();
});
```

**Comando Artisan:**
```php
// app/Console/Commands/LiberarReservasExpiradas.php
// Ejecutar cada hora via scheduler
// Libera reservas donde expira_en < now() y estado = 'activa'
```

---

### FASE 6: Flujo Post-Cotización

#### 6.1-6.2 Emails y Creación de Cuenta
**Archivos a crear:**
- `app/Mail/CotizacionAceptada.php`
- `app/Mail/CuentaClienteCreada.php`
- `app/Listeners/CrearCuentaCliente.php`
- `app/Events/CotizacionAprobada.php`
- `resources/views/emails/cotizacion-aceptada.blade.php`
- `resources/views/emails/cuenta-creada.blade.php`

#### 6.3-6.4 Confirmación de Pago
**Migraciones:**
```php
// add_pago_fields_to_solicitudes.php
$table->boolean('pagada')->default(false);
$table->string('comprobante_pago')->nullable();
$table->timestamp('pagada_en')->nullable();
$table->foreignId('verificada_por')->nullable()->constrained('users');
$table->string('numero_factura')->nullable();
$table->timestamp('facturada_en')->nullable();
```

**Archivos a crear:**
- `app/Http/Controllers/PagoController.php`
- `resources/views/solicitudes/confirmar-pago.blade.php`

---

### FASE 7: Portal de Cliente

#### 7.1-7.6 Módulo Cliente
**Archivos a crear:**
- `app/Http/Controllers/Portal/DashboardController.php`
- `app/Http/Controllers/Portal/PedidosController.php`
- `app/Http/Controllers/Portal/SeguimientoController.php`
- `resources/views/portal/dashboard.blade.php`
- `resources/views/portal/historial.blade.php`
- `resources/views/portal/seguimiento.blade.php`
- `app/Notifications/EstadoPedidoCambiado.php`

**Migraciones:**
```php
// add_envio_fields_to_solicitudes.php
$table->enum('estado_envio', ['pendiente', 'preparando', 'despachado', 'en_transito', 'entregado'])->nullable();
$table->string('numero_guia')->nullable();
$table->string('transportadora')->nullable();
$table->string('archivo_guia')->nullable();
$table->timestamp('despachado_en')->nullable();
$table->timestamp('entregado_en')->nullable();
```

---

### FASE 8: Módulo Punto de Venta (NUEVO)

**Archivos a crear:**
- `app/Http/Controllers/PuntoVentaController.php`
- `app/Models/VentaPdv.php`
- `app/Services/PuntoVentaService.php`
- `resources/views/punto-venta/index.blade.php`
- `resources/views/punto-venta/dashboard.blade.php`
- `resources/views/punto-venta/venta.blade.php`

**Migraciones:**
```php
// create_ventas_pdv_table.php
Schema::create('ventas_pdv', function (Blueprint $table) {
    $table->id();
    $table->string('numero_venta')->unique();
    $table->foreignId('ubicacion_id')->constrained('ubicaciones');
    $table->foreignId('cliente_id')->nullable()->constrained();
    $table->decimal('subtotal', 12, 2);
    $table->decimal('descuento', 10, 2)->default(0);
    $table->decimal('total', 12, 2);
    $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto']);
    $table->enum('estado', ['completada', 'anulada'])->default('completada');
    $table->foreignId('usuario_id')->constrained('users');
    $table->timestamps();
});

// create_items_venta_pdv_table.php
Schema::create('items_venta_pdv', function (Blueprint $table) {
    $table->id();
    $table->foreignId('venta_pdv_id')->constrained()->onDelete('cascade');
    $table->foreignId('producto_id')->constrained();
    $table->foreignId('variante_producto_id')->nullable()->constrained();
    $table->integer('cantidad');
    $table->decimal('precio_unitario', 10, 2);
    $table->decimal('descuento', 10, 2)->default(0);
    $table->decimal('total', 12, 2);
    $table->timestamps();
});
```

---

### FASE 9: Catálogo y Métricas

#### 9.1 Reorganizar Campos del Catálogo
**Archivos a modificar:**
- `resources/views/catalogo/index.blade.php`
- `app/Http/Controllers/CatalogoController.php`

#### 9.2-9.3 Reportes Mejorados
**Archivos a crear/modificar:**
- `app/Exports/CotizacionesExport.php` (mejorar con estilos)
- `app/Services/ReporteService.php`
- `resources/views/pdf/reporte-cotizaciones.blade.php`

---

### FASE 10: Roles y Permisos

**Seeder actualizado:**
```php
// RolesAndPermissionsSeeder.php
$roles = [
    'admin' => ['*'], // todos los permisos
    'vendedor' => ['cotizaciones.*', 'clientes.ver', 'catalogo.*', 'enlaces.*'],
    'inventarios' => ['stock.*', 'productos.ver', 'traslados.*'],
    'facturacion' => ['cotizaciones.ver', 'pagos.*', 'facturas.*'],
    'punto_venta' => ['pdv.*', 'stock.ver'],
    'cliente' => ['portal.*'],
];
```

---

### FASE 11: Validación Final

#### 11.1 Pruebas con Playwright (MCP)
**Flujos a validar:**
1. Login/Logout con cada rol
2. CRUD completo de clientes
3. CRUD completo de productos
4. Flujo de cotización completo
5. Gestión de stock y traslados
6. Portal de cliente
7. Punto de venta
8. Exportación de reportes
9. Responsive en diferentes tamaños

---

## ARCHIVOS CRÍTICOS A MODIFICAR

### Controladores
| Archivo | Cambios |
|---------|---------|
| `ClientesController.php` | CRUD completo, sucursales, documentos |
| `CategoriasController.php` | Agregar referencia única |
| `ProductosController.php` | Variantes con referencia |
| `SolicitudController.php` | CRUD completo, reservas |
| `StockController.php` | Multi-ubicación, traslados |
| `CatalogoController.php` | Reorganizar campos |

### Modelos a Crear
| Modelo | Propósito |
|--------|-----------|
| `Ubicacion` | Bodega, Tienda, etc. |
| `Sucursal` | Sucursales de clientes |
| `DocumentoCliente` | PDFs adjuntos |
| `TrasladoStock` | Movimientos entre ubicaciones |
| `ReservaStock` | Reservas con expiración |
| `NovedadStock` | Garantías, saldos, pérdidas |
| `VentaPdv` | Ventas punto de venta |

### Vistas a Crear
| Vista | Propósito |
|-------|-----------|
| `portal/*` | Portal de cliente |
| `punto-venta/*` | Módulo PdV |
| `stock/traslados.blade.php` | Gestión traslados |
| `components/*` | Componentes reutilizables |

---

## VERIFICACIÓN

### Pruebas Manuales (Playwright MCP)
1. Navegar a `http://localhost:8000`
2. Login como admin
3. Verificar navegación lateral
4. Probar cada módulo según rol
5. Verificar responsive
6. Probar flujos completos

### Pruebas Automatizadas
```bash
php artisan test
```

### Comandos de Verificación
```bash
# Verificar migraciones
php artisan migrate:status

# Verificar rutas
php artisan route:list

# Limpiar caché
php artisan optimize:clear

# Verificar roles
php artisan permission:show
```

---

## ORDEN DE IMPLEMENTACIÓN RECOMENDADO

1. **Fase 1** - Fundamentos (crítico para todo lo demás)
2. **Fase 10** - Roles (necesario para permisos)
3. **Fase 2** - Clientes (base para cotizaciones)
4. **Fase 3** - Categorías/Productos (base para stock)
5. **Fase 4** - Stock Multi-Ubicación (base para PdV)
6. **Fase 5** - Cotizaciones CRUD
7. **Fase 6** - Post-Cotización
8. **Fase 7** - Portal Cliente
9. **Fase 8** - Punto de Venta
10. **Fase 9** - Métricas
11. **Fase 11** - Validación Final
