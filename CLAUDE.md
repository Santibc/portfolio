# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture Overview

This is a Laravel 9 multi-tenant e-commerce platform called "Montano" that enables companies to create online stores and manage quotation catalogs. The application supports:

- Multi-tenant architecture where multiple companies (`empresas`) operate their own stores
- Product catalog management with categories, variants, and pricing
- Quote request system (`solicitudes_cotizacion`) for B2B interactions  
- E-commerce functionality with shopping cart and payment processing via Wompi
- Stock management and price tracking
- Customer management per company
- User authentication and role-based access control using Spatie Permissions

Key entities:
- `Empresa` (Company): Core tenant entity
- `Producto` (Product): Multi-variant products with images and pricing
- `Cliente` (Customer): Company-specific customers  
- `SolicitudCotizacion` (Quote Request): B2B quote system
- `Compra` (Purchase): E-commerce orders

## Development Commands

**Backend (Laravel/PHP):**
```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run tests
php artisan test
# OR
./vendor/bin/phpunit

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate application key
php artisan key:generate

# Link storage
php artisan storage:link
```

**Frontend (Vite + TailwindCSS):**
```bash
# Install dependencies
npm install

# Development server (with hot reload)
npm run dev

# Production build
npm run build
```

## Database Structure

The application uses MySQL with migrations located in `database/migrations/`. Key migration patterns:
- Companies are created first (`empresas` table)
- Products belong to companies (`empresa_id` foreign key)
- Multi-tenant isolation via `empresa_id` scoping
- Soft deletes are used throughout

## Key Directories

- `app/Models/`: Eloquent models with multi-tenant relationships
- `app/Http/Controllers/`: Controllers organized by feature (Productos, Clientes, etc.)
- `app/Services/`: Business logic including `WompiService` for payments
- `resources/views/`: Blade templates with component-based structure
- `resources/js/app.js`: Frontend entry point using Alpine.js and vanilla JS
- `database/migrations/`: Database schema evolution
- `routes/web.php`: All web routes with middleware protection

## Multi-Tenancy Implementation

The application uses a single database with `empresa_id` scoping:
- Middleware `VerificarEmpresa` ensures proper tenant isolation
- Models include `empresa_id` in fillable arrays and relationships
- Controllers filter by authenticated user's company

## Frontend Stack

- **CSS Framework**: TailwindCSS 3.4
- **JavaScript**: Alpine.js 3.4 for reactivity
- **Components**: Bootstrap 5.3 for some components
- **Tables**: DataTables.net for advanced table functionality  
- **Alerts**: SweetAlert2 for user notifications
- **Build Tool**: Vite 4.0

## Payment Integration

Uses Wompi payment gateway (`WompiService`) for Colombian payments. Configuration stored in `configuracion_pasarela` table per company.

## File Storage

Images and files are stored using Laravel's storage system. Run `php artisan storage:link` to create the public symlink for file access.

## Testing Approach

Uses PHPUnit with Laravel's testing utilities. Test files should be in `tests/` directory following Laravel conventions.