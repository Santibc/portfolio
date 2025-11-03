# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 9** B2B e-commerce/catalog application built with:
- **Backend**: Laravel 9.52 (PHP 8.0+)
- **Frontend**: Blade templates with Tailwind CSS, Alpine.js, Bootstrap 5
- **Database**: MySQL (via XAMPP)
- **Build Tools**: Vite 4.0
- **Key Features**: Product catalog with variants, stock management, price lists, quotations, role-based access

## Development Environment Setup

This project runs on **XAMPP** (Windows environment). The development workflow requires three services running simultaneously:

### Starting Development Services

1. **Start XAMPP**: Launch MySQL and Apache from XAMPP Control Panel
2. **Start Laravel development server**:
   ```bash
   php artisan serve
   ```
3. **Start Vite dev server** (for hot module reloading):
   ```bash
   npm run dev
   ```

### Building for Production

```bash
npm run build
```

### Running Tests

```bash
php artisan test
# or
vendor/bin/phpunit
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

## Architecture Overview

### Multi-Flow Product Catalog System

The application has **two distinct catalog access flows**:

1. **Flow A - Public Access via Temporary Links** (`EnlaceAcceso`):
   - Clients receive time-limited tokenized URLs
   - Access catalog without authentication
   - Submit quotation requests
   - Route: `/catalogo/{token}`

2. **Flow B - Authenticated Vendor Access**:
   - Vendors/admins log in
   - Select a client from their portfolio
   - Create quotations on behalf of clients
   - Route: `/catalogo` → `/catalogo/cliente`

### Product Management with Variants

Products (`Producto`) support:
- **Simple products**: Single SKU, single price per price list
- **Products with variants** (`VarianteProducto`): Multiple SKUs based on attributes (size/color)
  - Each variant has independent pricing (`PrecioVariante`)
  - SKUs auto-generated from product reference + attributes

### Stock Management System

The application includes a comprehensive stock control system:
- **Stock tracking per product/variant** (`StockProducto`)
- **Stock movements** (`MovimientoStock`): entrada (in), salida (out), ajuste (adjustment)
- **Min/max levels** with low-stock alerts
- **Ubicaciones** (warehouse locations)
- Products can be configured to:
  - Control stock (`controlar_stock`)
  - Allow sales without stock (`permitir_venta_sin_stock`)

### Price Management

Multi-tier pricing system:
- **Price Lists** (`ListaPrecio`): Multiple price tiers (export1, export2, local1-4)
- **Bulk price updates via Excel/CSV**:
  - Import flow: `ProductosController@actualizarPreciosExcel` → `PreciosImport`
  - CSV delimiter: semicolon (`;`)
  - Supports multiple price list columns in one file
  - Audit trail via `ActualizacionPrecio` model

### Role-Based Access Control

Uses **Spatie Laravel Permission** package:
- **Roles**: admin, vendedor (seller)
- Vendors see only their assigned clients
- Admins have full access

### Key Models and Relationships

```
Producto (products)
├── categoria (belongsTo Categoria)
├── imagenes (hasMany ImagenProducto) - ordered, with principal flag
├── variantes (hasMany VarianteProducto) - optional
├── precios (hasMany PrecioProducto) - per price list
├── stock (hasMany StockProducto) - per product or variant
└── movimientosStock (hasMany MovimientoStock)

Cliente (clients)
├── vendedor (belongsTo User)
├── listaPrecio (belongsTo ListaPrecio)
├── ciudad → departamento → pais (geography chain)
├── enlacesAcceso (hasMany EnlaceAcceso)
└── solicitudesCotizacion (hasMany SolicitudCotizacion)

SolicitudCotizacion (quotation requests)
├── cliente (belongsTo Cliente)
├── enlaceAcceso (belongsTo EnlaceAcceso) - nullable
└── items (hasMany ItemSolicitudCotizacion)
```

## Important File Locations

### Controllers
- `ProductosController.php`: Product CRUD + Excel price updates + AJAX endpoints for variants/images/prices/stock modals
- `CatalogoController.php`: Both catalog flows (public token + authenticated)
- `StockController.php`: Stock operations (entrada/salida/ajuste), reports, import/export
- `ActualizacionPreciosController.php`: Price update history and file downloads
- `SolicitudController.php`: Quotation management, PDF generation, Excel export

### Models
All models in `app/Models/` with Eloquent relationships and scopes.
Notable scopes:
- `Producto::activos()`, `conStock()`, `sinStock()`, `conStockBajo()`
- `Cliente::activos()`, `porVendedor($id)`
- `Categoria::activas()`

### Views
- `resources/views/productos/` - Product management UI
- `resources/views/catalogo/` - Dual catalog interfaces (public + authenticated)
- `resources/views/stock/` - Stock management dashboard
- `resources/views/solicitudes/` - Quotation views
- Layouts: `resources/views/layouts/`

### Migrations
All in `database/migrations/`, chronological. Key tables:
- Core: productos, categorias, clientes, users
- Variants: variantes_producto, precios_variantes
- Stock: stock_productos, movimientos_stock
- Catalog: enlaces_acceso, solicitud_cotizaciones, items_solicitud_cotizacion
- Audit: actualizaciones_precios, logs

## Working with Features

### Adding/Modifying Products

Products form supports:
1. Basic fields: referencia, nombre, descripcion, categoria_id, unidad_venta, unidad_empaque
2. Stock configuration: controlar_stock, permitir_venta_sin_stock, stock_inicial/minimo/maximo
3. Variants: Dynamic rows for talla/color/sku with stock per variant
4. Images: Multiple upload with principal flag, stored in `public/imagenes/productos/{product_id}/`
5. Prices: One input per active price list

### Stock Operations

Stock changes are recorded via `MovimientoStock`:
- **tipo_movimiento**: 'entrada', 'salida', 'ajuste'
- **origen**: 'venta', 'compra', 'ajuste_inventario', 'devolucion'
- All movements track: stock_anterior → stock_nuevo

### Excel Price Updates

The CSV/Excel import expects:
- **Delimiter**: `;` (semicolon)
- **Required column**: `referencia` (product reference code)
- **Price columns**: `export1`, `export2`, `local1`, `local2`, `local3`, `local4` (case-insensitive)
- Download templates from: `ActualizacionPreciosController@descargarPlantillaCsv` or `@descargarPlantillaExcel`

## Database Conventions

- **Boolean flags**: `activo` (active status) on most entities
- **Soft deletes**: Not used; instead, `activo = false`
- **Timestamps**: All models have `created_at`, `updated_at`
- **Foreign keys**: Follow pattern `{table_singular}_id` (e.g., `categoria_id`, `producto_id`)

## Frontend Stack

- **CSS Framework**: Tailwind CSS + Bootstrap 5 (hybrid - Bootstrap for DataTables/components, Tailwind for utilities)
- **JavaScript Libraries**:
  - **Alpine.js**: Reactive components
  - **jQuery**: Required for DataTables
  - **DataTables**: For all index pages with server-side processing (Yajra DataTables)
  - **SweetAlert2**: Confirmations and alerts
  - **Livewire**: Used for some reactive components
- **Build**: Vite with hot reloading

### DataTables Pattern

All index pages use server-side DataTables:
```php
// Controller
if ($request->ajax()) {
    return DataTables::of($query)
        ->addColumn('action', function($row) { /* buttons */ })
        ->rawColumns(['action', 'imagen'])
        ->make(true);
}
```

### AJAX Modal Pattern

Product index shows modals for variants/images/prices/stock:
```javascript
function verVariantes(productoId) {
    fetch(`/productos/${productoId}/variantes-ajax`)
        .then(res => res.text())
        .then(html => { /* show in modal */ });
}
```

## Authentication & Authorization

- **Auth**: Laravel Breeze (Blade)
- **Middleware**: Routes wrapped in `auth` middleware
- **Permissions**: Check role via `$user->hasRole('admin')` or `$user->hasRole('vendedor')`
- **Guest catalog access**: Token-based, validated via `EnlaceAcceso::esValido()` (checks `activo` and `expira_en`)

## Key Business Logic

### Quotation Workflow

1. **Client browses catalog** (Flow A or B)
2. **Adds products to quotation** (stored in session or form data)
3. **Submits `SolicitudCotizacion`** with `ItemSolicitudCotizacion` records
4. **Vendor reviews** in `/solicitudes`
5. **Can export to PDF** or **Excel**
6. **Applying quotation** (future feature): converts to actual sale/order

### Stock Initialization

When creating products with `controlar_stock = true`:
- If no variants: creates one `StockProducto` record with `variante_producto_id = null`
- If variants: creates one `StockProducto` per variant
- Initial stock creates `MovimientoStock` with `origen = 'ajuste_inventario'`

## Common Tasks

### Create a new product
1. Navigate to `/productos/form`
2. Fill basic fields
3. Toggle "Tiene Variantes" if needed
4. Upload images (mark one as principal)
5. Enter prices for each active price list
6. Configure stock if `controlar_stock = true`

### Update prices via Excel
1. Download template from `/productos/historial-precios`
2. Fill with references + new prices
3. Upload at `/productos` (price update section)
4. Review `ActualizacionPrecio` record for errors

### Generate quotation link for client
1. Go to `/enlaces/crear`
2. Select client
3. Set expiration date
4. Generate link
5. Send to client (they access via `/catalogo/{token}`)

### Check stock movements
1. Navigate to `/stock/historial`
2. Filter by product/variant/date range
3. See full audit trail of all movements

## File Upload Paths

- **Product images**: `public/imagenes/productos/{product_id}/`
- **Price update files**: `public/uploads/actualizaciones_precios/`
- Ensure `public/` directories exist and are writable

## Important Notes

- **XAMPP paths**: Project assumes `c:\xampp\htdocs\portfolio` on Windows
- **Git status**: The repository tracks changes to `public/images/logo.png` (recent logo update)
- **Excel library**: Uses `maatwebsite/excel` v3.1 with `phpoffice/phpspreadsheet` pinned to 1.29.7
- **PDF generation**: Uses `barryvdh/laravel-dompdf` for quotation PDFs
- **Permissions**: Spatie Laravel Permission manages roles (database-driven, not config)
