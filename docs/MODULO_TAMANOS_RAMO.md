# Módulo de Administración: Tamaños de Ramo

## Descripción

Este módulo permite gestionar los tamaños de ramo disponibles en la funcionalidad **"Arma tu Ramo"**. Los tamaños definen las opciones que los clientes pueden seleccionar al crear un ramo personalizado, estableciendo:

- Nombre del tamaño (Ej: Pequeño, Mediano, Grande)
- Rango de flores permitidas (mínimo y máximo)
- Precio base de envoltura y preparación
- Imagen representativa
- Estado activo/inactivo

## Rutas Disponibles

| Método | URL | Nombre de Ruta | Descripción |
|--------|-----|----------------|-------------|
| GET | `/admin/tamanos-ramo` | `admin.tamanos-ramo.index` | Listado de tamaños |
| GET | `/admin/tamanos-ramo/create` | `admin.tamanos-ramo.create` | Formulario de creación |
| POST | `/admin/tamanos-ramo` | `admin.tamanos-ramo.store` | Guardar nuevo tamaño |
| GET | `/admin/tamanos-ramo/{id}/edit` | `admin.tamanos-ramo.edit` | Formulario de edición |
| PUT | `/admin/tamanos-ramo/{id}` | `admin.tamanos-ramo.update` | Actualizar tamaño |
| DELETE | `/admin/tamanos-ramo/{id}` | `admin.tamanos-ramo.destroy` | Eliminar tamaño |
| POST | `/admin/tamanos-ramo/{id}/toggle-activo` | `admin.tamanos-ramo.toggle-activo` | Activar/desactivar (AJAX) |

## Acceso al Módulo

### Navegación:
1. Iniciar sesión como administrador o vendedor
2. En el sidebar izquierdo, sección **"Catálogo"**
3. Click en **"Tamaños de Ramo"** (icono: flor)

### URL Directa:
```
http://localhost/admin/tamanos-ramo
```

## Características

### Listado de Tamaños
- Vista en tabla con paginación
- Columnas: Orden, Imagen, Nombre, Rango de Flores, Precio Base, Estado, Acciones
- Toggle activo/inactivo con AJAX (sin recargar página)
- Búsqueda y ordenamiento con DataTables (si hay +10 registros)
- Confirmación antes de eliminar (SweetAlert2)

### Formulario de Creación/Edición
- **Nombre**: Texto descriptivo del tamaño
- **Cantidad Mínima de Flores**: Número entero (ej: 6)
- **Cantidad Máxima de Flores**: Número entero (ej: 12)
  - Validación: Máximo debe ser ≥ Mínimo
- **Precio Base**: Precio de envoltura/preparación (sin incluir flores)
- **Descripción**: Texto opcional (máx 500 caracteres)
- **Imagen**: JPG, PNG o WEBP (máx 2MB)
  - Vista previa en tiempo real al seleccionar archivo
  - Recomendado: 800x600px
- **Orden**: Número para ordenar visualización (menor a mayor)
- **Estado Activo**: Switch para activar/desactivar

### Validaciones
- Nombre requerido
- Cantidades mínimas y máximas requeridas y mayores a 0
- Cantidad máxima debe ser mayor o igual a la mínima
- Precio base requerido y mayor o igual a 0
- Imagen opcional pero con formato y tamaño validados

### Almacenamiento de Imágenes
- Ruta: `storage/app/public/ramos/tamanos/`
- Acceso público: `storage/ramos/tamanos/`
- Las imágenes anteriores se eliminan al actualizar

### Eliminación
- No se puede eliminar si hay ramos personalizados asociados
- Se elimina la imagen del disco al eliminar el tamaño
- Confirmación con SweetAlert2

## Estructura de Archivos

```
app/
├── Http/
│   └── Controllers/
│       └── Admin/
│           └── TamanosRamoController.php          [Controller principal]
├── Models/
│   └── TamanoRamo.php                              [Modelo]

resources/
└── views/
    └── admin/
        └── tamanos-ramo/
            ├── index.blade.php                     [Listado]
            └── form.blade.php                      [Formulario crear/editar]

database/
├── migrations/
│   └── 2025_12_30_130000_create_ramos_personalizados_tables.php  [Tabla tamanos_ramo]
└── seeders/
    └── TamanoRamoSeeder.php                        [Datos de ejemplo]

routes/
└── web.php                                          [Rutas admin]
```

## Base de Datos

### Tabla: `tamanos_ramo`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `nombre` | string | Nombre del tamaño (Ej: Pequeño) |
| `cantidad_flores_min` | int | Cantidad mínima de flores |
| `cantidad_flores_max` | int | Cantidad máxima de flores |
| `precio_base` | decimal(10,2) | Precio base de envoltura |
| `imagen` | string | Ruta de la imagen |
| `descripcion` | text | Descripción opcional |
| `activo` | boolean | Estado activo/inactivo |
| `orden` | int | Orden de visualización |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de actualización |

## Modelo: TamanoRamo

### Relaciones
```php
// Un tamaño tiene muchos ramos personalizados
public function ramosPersonalizados()
{
    return $this->hasMany(RamoPersonalizado::class, 'tamano_ramo_id');
}
```

### Scopes
```php
// Solo tamaños activos
TamanoRamo::activos()->get()

// Ordenados por campo 'orden' y luego por cantidad mínima
TamanoRamo::ordenado()->get()
```

### Accessors
```php
// URL de imagen o imagen por defecto
$tamano->imagen_url
```

## Seeder de Datos de Ejemplo

Para crear los 3 tamaños básicos (Pequeño, Mediano, Grande):

```bash
php artisan db:seed --class=TamanoRamoSeeder
```

Esto creará:
- **Pequeño**: 6-12 flores, $15.000
- **Mediano**: 12-24 flores, $25.000
- **Grande**: 24-50 flores, $40.000

## Uso desde el Frontend (Arma tu Ramo)

El `ArmaTuRamoController` consulta los tamaños activos:

```php
$tamanos = TamanoRamo::activos()->ordenado()->get();
```

El cliente selecciona un tamaño y el sistema:
1. Establece el precio base del ramo
2. Valida que no supere el máximo de flores permitidas
3. Valida que cumpla con el mínimo de flores antes de agregar al carrito

## Permisos y Middleware

### Middleware Aplicado:
- `auth`: Usuario autenticado
- `role:admin`: Solo usuarios con rol de administrador

### Grupos de Rutas:
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('tamanos-ramo')->name('tamanos-ramo.')->group(function () {
        // ... rutas del módulo
    });
});
```

## Ejemplo de Uso

### Crear un Nuevo Tamaño

1. Ir a `/admin/tamanos-ramo`
2. Click en "Nuevo Tamaño"
3. Completar el formulario:
   ```
   Nombre: Extra Grande
   Cantidad Mínima: 50
   Cantidad Máxima: 100
   Precio Base: $60.000
   Descripción: Para eventos y celebraciones especiales
   Imagen: [Seleccionar archivo]
   Orden: 4
   Estado: ✓ Activo
   ```
4. Click en "Crear Tamaño"

### Editar un Tamaño Existente

1. Ir a `/admin/tamanos-ramo`
2. Click en el icono de editar (lápiz) del tamaño deseado
3. Modificar los campos necesarios
4. Click en "Actualizar Tamaño"

### Activar/Desactivar un Tamaño

Opción 1: Desde el listado
- Toggle del switch en la columna "Estado"
- El cambio se guarda automáticamente (AJAX)
- Se muestra notificación de éxito

Opción 2: Desde el formulario de edición
- Marcar/desmarcar el checkbox "Tamaño Activo"
- Click en "Actualizar Tamaño"

### Eliminar un Tamaño

1. Ir a `/admin/tamanos-ramo`
2. Click en el icono de eliminar (papelera)
3. Confirmar en el modal de SweetAlert2
4. Si hay ramos asociados, aparecerá un error y no se eliminará

## Tecnologías Utilizadas

### Backend:
- Laravel 9
- PHP 8.x
- Eloquent ORM
- Blade Templates
- Laravel File Storage

### Frontend:
- Bootstrap 5.3.7
- jQuery
- SweetAlert2 (alertas)
- Bootstrap Icons
- Font Awesome

### Librerías JavaScript:
- DataTables (tablas interactivas)
- SweetAlert2 (confirmaciones)

## Troubleshooting

### Error: "Route [admin.tamanos-ramo.index] not defined"

**Solución:**
```bash
php artisan route:clear
php artisan cache:clear
```

### Error: "Class TamanosRamoController not found"

**Solución:**
```bash
composer dump-autoload
```

### Error: "Storage symlink not found"

**Solución:**
```bash
php artisan storage:link
```

### Las imágenes no se muestran

**Solución:**
1. Verificar que existe el symlink: `public/storage -> storage/app/public`
2. Ejecutar: `php artisan storage:link`
3. Verificar permisos de carpeta `storage/`

## Mejoras Futuras

- [ ] Drag & drop para reordenar tamaños
- [ ] Importación masiva desde Excel
- [ ] Clonación de tamaños
- [ ] Previsualización 3D del ramo según tamaño
- [ ] Estadísticas de uso por tamaño
- [ ] Multiidioma en descripciones
- [ ] Galería de imágenes múltiples por tamaño

## Notas Importantes

1. **Solo usuarios admin** pueden gestionar los tamaños
2. **No eliminar tamaños** si tienen ramos personalizados asociados
3. **Recomendado**: Mantener rangos de flores sin solape (ej: 6-12, 13-24, 25-50)
4. **Precio base** no incluye el costo de las flores, solo envoltura
5. **Imágenes** se almacenan en `storage/app/public/ramos/tamanos/`
6. **Cache**: El módulo no usa cache, los cambios son inmediatos

## Contacto y Soporte

Para más información sobre este módulo, consultar el código fuente o contactar al equipo de desarrollo.

---

**Última actualización**: 2026-01-08
**Versión del módulo**: 1.0.0
**Compatible con**: Laravel 9.x
