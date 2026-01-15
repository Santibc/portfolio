# 📦 Sistema de Productos Complementarios y Cross-Selling

## 🎯 Descripción General

Sistema completo de productos complementarios implementado para separar los productos adicionales en dos tipos:

### 1. **Cross-Selling (Productos Generales)**
Productos que se muestran a **TODOS los clientes** al finalizar la compra, agrupados por categorías para mejor organización visual.

**Categorías disponibles:**
- 🍫 Bombones y Chocolates
- 🧸 Peluches
- 🎈 Globos
- 🍷 Vinos y Espumantes
- 🥃 Licores

### 2. **Complementarios Específicos**
Productos que solo se muestran cuando están **asignados a un producto específico** en el carrito.

**Ejemplo de uso:**
- Un cliente agrega un "Ramo de Rosas" al carrito
- El sistema automáticamente sugiere: **Florero** y **Preservante para flores**
- Estos productos NO se muestran si el cliente compra chocolates u otros productos

---

## 🗄️ Base de Datos

### Nuevas Migraciones

#### 1. `2026_01_14_191719_add_tipo_complementario_and_rename_categories_in_productos_adicionales.php`

**Cambios:**
- ✅ Agregado campo `tipo_complementario` (ENUM: 'cross_selling', 'especifico')
- ✅ Renombrada categoría 'chocolate' → 'bombones'
- ✅ Índice para mejor performance: `(tipo_complementario, disponible)`

```sql
ALTER TABLE productos_adicionales
ADD COLUMN tipo_complementario ENUM('cross_selling', 'especifico')
DEFAULT 'cross_selling'
COMMENT 'cross_selling: general para todos, especifico: por tipo de producto';

ALTER TABLE productos_adicionales
ADD INDEX idx_tipo_disponible (tipo_complementario, disponible);

UPDATE productos_adicionales SET categoria = 'bombones' WHERE categoria = 'chocolate';
```

#### 2. `2026_01_14_192053_create_productos_complementarios_sugeridos_table.php`

**Tabla pivot para asignar complementarios específicos a productos:**

```sql
CREATE TABLE productos_complementarios_sugeridos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id BIGINT UNSIGNED NOT NULL COMMENT 'Producto principal',
    producto_adicional_id BIGINT UNSIGNED NOT NULL COMMENT 'Complementario sugerido',
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_producto_activo (producto_id, activo),
    UNIQUE KEY unique_producto_complementario (producto_id, producto_adicional_id),

    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_adicional_id) REFERENCES productos_adicionales(id) ON DELETE CASCADE
);
```

---

## 📊 Modelos Eloquent

### ProductoAdicional

**Ubicación:** `app/Models/ProductoAdicional.php`

**Nuevas constantes:**
```php
const CATEGORIAS = [
    'bombones' => 'Bombones y Chocolates',
    'peluche' => 'Peluches',
    'globo' => 'Globos',
    'vino' => 'Vinos y Espumantes',
    'licor' => 'Licores',
    'florero' => 'Floreros',
    'preservante' => 'Preservantes',
    'otro' => 'Otros',
];

const TIPO_CROSS_SELLING = 'cross_selling';
const TIPO_ESPECIFICO = 'especifico';
```

**Nuevos scopes:**
```php
->crossSelling()    // Filtra solo productos de cross-selling
->especificos()     // Filtra solo productos específicos
```

**Nueva relación:**
```php
public function productos()
{
    return $this->belongsToMany(Producto::class, 'productos_complementarios_sugeridos')
        ->withPivot('orden', 'activo')
        ->withTimestamps();
}
```

### Producto

**Ubicación:** `app/Models/Producto.php`

**Nueva relación:**
```php
public function complementariosSugeridos()
{
    return $this->belongsToMany(
        ProductoAdicional::class,
        'productos_complementarios_sugeridos'
    )
    ->where('tipo_complementario', ProductoAdicional::TIPO_ESPECIFICO)
    ->where('disponible', true)
    ->withPivot('orden', 'activo')
    ->wherePivot('activo', true)
    ->orderBy('orden');
}
```

**Ejemplo de uso:**
```php
$producto = Producto::find(1);
$complementarios = $producto->complementariosSugeridos; // Collection de ProductoAdicional
```

---

## 🎮 Controladores

### TiendaController

**Ubicación:** `app/Http/Controllers/TiendaController.php`

#### Método `verCarrito()`

**Variables enviadas a la vista:**
```php
$productosCrossSelling       // Todos los cross-selling disponibles
$crossSellingPorCategoria    // Cross-selling agrupados (bombones, peluches, etc.)
$complementariosEspecificos  // Complementarios específicos según productos en carrito
```

**Lógica implementada:**
```php
// 1. Obtener solo cross-selling
$productosCrossSelling = ProductoAdicional::disponibles()
    ->paraCheckout()
    ->crossSelling()
    ->orderBy('orden')
    ->get();

// 2. Agrupar por categoría
$crossSellingPorCategoria = $productosCrossSelling->groupBy('categoria');

// 3. Obtener complementarios específicos de productos en carrito
$complementariosEspecificos = collect();
foreach ($carrito->items as $item) {
    $producto = Producto::with('complementariosSugeridos')->find($item['producto_id']);
    if ($producto && $producto->complementariosSugeridos->isNotEmpty()) {
        $complementariosEspecificos = $complementariosEspecificos->merge(
            $producto->complementariosSugeridos
        );
    }
}
$complementariosEspecificos = $complementariosEspecificos->unique('id');
```

#### Método `checkout()`

Implementa la **misma lógica** que `verCarrito()` para mostrar complementarios en el checkout.

### ProductosAdicionalesController

**Ubicación:** `app/Http/Controllers/ProductosAdicionalesController.php`

**Cambios realizados:**

1. **Validación actualizada:**
```php
'tipo_complementario' => 'required|in:cross_selling,especifico'
```

2. **Guardado del campo:**
```php
$adicional->tipo_complementario = $request->tipo_complementario;
```

3. **Nueva columna en DataTable:**
```php
->addColumn('tipo_badge', function ($adicional) {
    if ($adicional->tipo_complementario === ProductoAdicional::TIPO_CROSS_SELLING) {
        return '<span class="badge bg-primary"><i class="bi bi-shop"></i> Cross-selling</span>';
    }
    return '<span class="badge bg-info"><i class="bi bi-tag"></i> Específico</span>';
})
```

4. **Colores actualizados en categorías:**
```php
$colores = [
    'bombones' => 'bg-warning text-dark',
    'peluche' => 'bg-info',
    'globo' => 'bg-primary',
    'vino' => 'bg-danger',
    'licor' => 'bg-dark',
    'florero' => 'bg-success',
    'preservante' => 'bg-success',
    'otro' => 'bg-secondary',
];
```

---

## 🎨 Vistas Administrativas

### Vista Index (Listado)

**Archivo:** `resources/views/adicionales/index.blade.php`

**Cambios:**
- ✅ Título cambiado: "Productos Adicionales" → "Productos Complementarios"
- ✅ Icono actualizado: `bi-gift` → `bi-box-seam`
- ✅ Nueva columna "Tipo" en DataTable
- ✅ Descripción actualizada explicando cross-selling vs específicos

**Estructura de tabla:**
```
Acciones | Imagen | Nombre | Categoría | Tipo | Precio | Stock | Orden | Checkout | Estado
```

### Vista Formulario

**Archivo:** `resources/views/adicionales/form.blade.php`

**Cambios:**
- ✅ Título cambiado: "Adicional" → "Complementario"
- ✅ Nuevo campo: **Tipo de Complementario**
  - Cross-selling (General)
  - Específico (Asignado a productos)
- ✅ Ayuda contextual explicando diferencias

**Campo tipo_complementario:**
```html
<select name="tipo_complementario" required>
    <option value="cross_selling">Cross-selling (General)</option>
    <option value="especifico">Específico (Asignado a productos)</option>
</select>
<small class="text-muted">
    <strong>Cross-selling:</strong> Se muestra a todos |
    <strong>Específico:</strong> Solo si está asignado
</small>
```

---

## 📱 Interfaz de Usuario (Sidebar)

**Archivo:** `resources/views/layouts/navigation-vertical.blade.php`

**Cambios:**
```html
<!-- ANTES -->
<a href="{{ route('adicionales.index') }}">
    <i class="bi bi-gift"></i>
    <span>Adicionales</span>
</a>

<!-- DESPUÉS -->
<a href="{{ route('adicionales.index') }}">
    <i class="bi bi-box-seam"></i>
    <span>Complementarios</span>
</a>
```

---

## 🌱 Seeder de Datos

**Archivo:** `database/seeders/ComplementariosSeeder.php`

**Ejecutar:** `php artisan db:seed --class=ComplementariosSeeder`

**Datos creados:** 14 productos de ejemplo

### Cross-Selling (10 productos)
- 2 Bombones (Ferrero Rocher, Artesanales)
- 2 Peluches (Osito, Corazón)
- 2 Globos (Corazón Rojo, Feliz Cumpleaños)
- 2 Vinos (Tinto Reserva, Espumante)
- 2 Licores (Whisky, Ron)

### Específicos (4 productos)
- 2 Floreros (Cristal, Cerámico)
- 2 Preservantes (Sobre, Fertilizante)

---

## 🚀 Flujo de Usuario (Carrito/Checkout)

### Estructura Visual Recomendada

#### **Sección 1: Productos Principales**
- Productos agregados al carrito
- Cantidad, precio, subtotal

#### **Sección 2: Complementarios Específicos**
```php
@if($complementariosEspecificos->isNotEmpty())
    <h5>Complementarios Sugeridos para tus Productos</h5>
    <p class="text-muted">Productos especiales que complementan tu compra</p>

    @foreach($complementariosEspecificos as $complementario)
        <!-- Card del complementario específico -->
    @endforeach
@endif
```

#### **Sección 3: Cross-Selling Agrupado**
```php
@if($crossSellingPorCategoria->isNotEmpty())
    <h5>¿Quieres agregar algo más a tu pedido?</h5>

    @foreach($crossSellingPorCategoria as $categoria => $productos)
        <h6>{{ ProductoAdicional::CATEGORIAS[$categoria] }}</h6>

        @foreach($productos as $producto)
            <!-- Card del producto cross-selling -->
        @endforeach
    @endforeach
@endif
```

---

## 📋 Variables Disponibles en Vistas

### En `carrito.blade.php` y `checkout.blade.php`

```php
$empresa                      // Empresa actual
$carrito                      // Carrito con items
$listaPrecio                  // Lista de precios activa
$productosCrossSelling        // Collection de todos los cross-selling
$crossSellingPorCategoria     // Collection agrupada por categoría
$complementariosEspecificos   // Collection de complementarios del producto
```

**Ejemplo de uso:**
```blade
{{-- Mostrar cross-selling por categoría --}}
@foreach($crossSellingPorCategoria as $categoria => $productos)
    <div class="categoria-section">
        <h6>{{ \App\Models\ProductoAdicional::CATEGORIAS[$categoria] }}</h6>

        @foreach($productos as $producto)
            <div class="producto-card">
                <h5>{{ $producto->nombre }}</h5>
                <p>{{ $producto->descripcion }}</p>
                <span>${{ number_format($producto->precio, 0, ',', '.') }}</span>
                <button onclick="agregarAdicional({{ $producto->id }})">Agregar</button>
            </div>
        @endforeach
    </div>
@endforeach
```

---

## 🔧 Funciones JavaScript Existentes

### Agregar Complementario al Carrito

**Ruta:** `POST /tienda/carrito/adicional`

```javascript
function agregarAdicional(adicionalId, cantidad = 1) {
    $.ajax({
        url: '/tienda/carrito/adicional',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            adicional_id: adicionalId,
            cantidad: cantidad
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Producto agregado al carrito');
                // Actualizar vista del carrito
                actualizarCarrito();
            }
        }
    });
}
```

---

## 📊 Casos de Uso

### Caso 1: Cliente compra un Ramo de Flores

**Productos en carrito:**
- Ramo de 12 Rosas Rojas

**Complementarios que se muestran:**

**Específicos (aparecen primero):**
- Florero Cristal Clásico
- Florero Cerámico Moderno
- Preservante para Flores
- Fertilizante para Flores Cortadas

**Cross-Selling (aparecen después, agrupados):**
- **Bombones:** Ferrero Rocher, Artesanales
- **Peluches:** Osito, Corazón
- **Globos:** Corazón Rojo, Feliz Cumpleaños
- **Vinos:** Tinto Reserva, Espumante
- **Licores:** Whisky, Ron

### Caso 2: Cliente compra Torta de Cumpleaños

**Productos en carrito:**
- Torta Cumpleaños 2kg

**Complementarios que se muestran:**

**Específicos:** Ninguno (las tortas no tienen complementarios específicos asignados)

**Cross-Selling (todos los disponibles):**
- **Bombones:** Ferrero Rocher, Artesanales
- **Peluches:** Osito, Corazón
- **Globos:** Corazón Rojo, Feliz Cumpleaños
- **Vinos:** Tinto Reserva, Espumante
- **Licores:** Whisky, Ron

---

## 🎯 Asignar Complementarios Específicos a Productos

### Opción 1: Vía Base de Datos

```sql
-- Asignar Florero y Preservante al producto con ID 5 (Ramo de Rosas)
INSERT INTO productos_complementarios_sugeridos
    (producto_id, producto_adicional_id, orden, activo)
VALUES
    (5, 11, 1, 1),  -- Florero Cristal
    (5, 12, 2, 1),  -- Florero Cerámico
    (5, 13, 3, 1),  -- Preservante
    (5, 14, 4, 1);  -- Fertilizante
```

### Opción 2: Vía Eloquent

```php
$producto = Producto::find(5); // Ramo de Rosas

$producto->complementariosSugeridos()->attach([
    11 => ['orden' => 1, 'activo' => true],  // Florero Cristal
    12 => ['orden' => 2, 'activo' => true],  // Florero Cerámico
    13 => ['orden' => 3, 'activo' => true],  // Preservante
    14 => ['orden' => 4, 'activo' => true],  // Fertilizante
]);
```

### Opción 3: Interfaz Administrativa (Futuro)

**Ubicación sugerida:** `/productos/{id}/complementarios`

**Funcionalidad:**
- Listado de complementarios específicos disponibles
- Drag & drop para ordenar
- Toggle para activar/desactivar
- Botón "Agregar complementario"

---

## ✅ Checklist de Implementación

### Base de Datos
- [x] Migración `tipo_complementario` ejecutada
- [x] Migración `productos_complementarios_sugeridos` ejecutada
- [x] Categoría 'chocolate' renombrada a 'bombones'
- [x] Seeder ejecutado con datos de ejemplo

### Modelos
- [x] ProductoAdicional actualizado con scopes
- [x] Producto actualizado con relación complementarios
- [x] Constantes CATEGORIAS actualizadas
- [x] Constantes TIPO_* agregadas

### Controladores
- [x] TiendaController actualizado (carrito y checkout)
- [x] ProductosAdicionalesController actualizado
- [x] Validación de tipo_complementario agregada
- [x] DataTable con columna tipo

### Vistas Administrativas
- [x] Index actualizado con nueva columna
- [x] Formulario actualizado con campo tipo
- [x] Títulos e iconos actualizados
- [x] Sidebar renombrado

### Vistas Frontend
- [ ] Carrito actualizado con 3 secciones ⚠️ **PENDIENTE**
- [ ] Checkout actualizado con 3 secciones ⚠️ **PENDIENTE**

### Interfaz de Asignación
- [ ] Crear vista para asignar complementarios a productos ⚠️ **PENDIENTE**
- [ ] Crear controlador para gestionar asignaciones ⚠️ **PENDIENTE**
- [ ] Agregar ruta en web.php ⚠️ **PENDIENTE**

---

## 🎓 Conceptos Clave

### Cross-Selling
**Definición:** Técnica de ventas que sugiere productos complementarios **a todos los clientes** sin importar qué estén comprando.

**Ventajas:**
- Aumenta el valor promedio del pedido
- Expone todos los productos disponibles
- Mejora la experiencia de compra

**Ejemplo:** Un cliente compra flores, se le sugieren chocolates, vinos, globos, etc.

### Complementarios Específicos
**Definición:** Productos que solo se sugieren cuando están **específicamente asignados** a los productos en el carrito.

**Ventajas:**
- Relevancia directa al producto principal
- Mejor experiencia del usuario
- Conversión más alta

**Ejemplo:** Un cliente compra ramo de flores, solo se le sugieren floreros y preservantes (no chocolates ni vinos).

---

## 🔮 Mejoras Futuras

### Alta Prioridad
1. **Actualizar vistas del carrito y checkout** con las 3 secciones
2. **Crear interfaz para asignar complementarios** a productos
3. **Agregar analytics** de conversión por tipo de complementario

### Media Prioridad
4. Límite de productos cross-selling mostrados (evitar saturación)
5. A/B testing de orden de presentación
6. Descuentos automáticos por combos (producto + complementario)

### Baja Prioridad
7. Machine learning para sugerencias personalizadas
8. Reglas de negocio avanzadas (si compra X, sugerir Y)
9. Variantes de complementarios según monto del carrito

---

## 📞 Soporte

Para dudas o problemas con el sistema:
1. Revisar este documento completo
2. Verificar logs en `storage/logs/laravel.log`
3. Ejecutar `php artisan cache:clear` si hay cambios que no se reflejan

---

## 📝 Notas Importantes

1. **Rendimiento:** El sistema usa eager loading para evitar N+1 queries
2. **Cache:** No implementado aún, considerar para producción
3. **Imágenes:** Asegurarse de tener imágenes en `public/images/adicionales/`
4. **Stock:** Los complementarios respetan el stock disponible
5. **Precios:** Se toman de la tabla `productos_adicionales`

---

**Fecha de implementación:** 14 de Enero, 2026
**Versión:** 1.0
**Estado:** ✅ Backend completado | ⚠️ Frontend pendiente
