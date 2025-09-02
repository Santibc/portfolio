# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 9 e-commerce application called "Betoge" that provides a multi-vendor marketplace platform with membership management, commission tracking, and payment gateway integration.

### Key Features
- Multi-vendor marketplace with company registration and management
- Membership plans and commission system
- Product catalog with categories, variants, and stock management
- Shopping cart and order processing
- Payment gateway integration (configured via `ConfiguracionPasarela` model)
- Role-based access control using Spatie Laravel Permission
- Excel import/export functionality
- PDF generation using DomPDF
- DataTables integration for data presentation

## Development Commands

### Artisan Commands
```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate application key
php artisan key:generate

# Run queue workers (if using queues)
php artisan queue:work

# Create symbolic link for storage
php artisan storage:link
```

### Composer Commands
```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Run autoload dump
composer dump-autoload
```

### Frontend Build Commands
```bash
# Install NPM dependencies
npm install

# Development build with hot reloading
npm run dev

# Production build
npm run build
```

### Testing Commands
```bash
# Run all tests
php artisan test
# or
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Unit

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage
```

## Architecture & Key Components

### Models & Relationships
- **User**: Extends Authenticatable, uses HasRoles trait for permissions
- **Empresa**: Company/vendor model with one-to-one relationship with User
- **Producto**: Product model with categories, variants, images, and stock
- **Categoria**: Product categories with hierarchical structure
- **Membresia**: Membership plans with associated features
- **Compra**: Order model with items, payments, and shipping
- **StockProducto**: Inventory management per product/variant
- **Cliente**: Customer model linked to vendors (vendedor_id)

### Controllers Structure
- **Admin Controllers**: `DashboardAdminController`, `UsuariosController`
- **Business Logic**: `EmpresasController`, `ProductosController`, `ComprasController`
- **E-commerce**: `TiendaController`, `CatalogoController`, `ClientesController`
- **Integrations**: `WebhookController` for payment gateway callbacks
- **Utilities**: `ActualizacionPreciosController`, `SolicitudController`

### Frontend Architecture
- **Blade Templates**: Located in `resources/views/`
- **Layout System**: Main layout in `layouts/app.blade.php` with sidebar navigation
- **CSS Framework**: Bootstrap 5.3.7 with custom styles (primary framework)
- **JavaScript**: jQuery, DataTables, SweetAlert2 (primary stack)
- **Build Tool**: Vite with Laravel plugin
- **Note**: Vite, TailwindCSS, and Alpine.js are only used for authentication pages (login/register) - the main application uses Bootstrap + jQuery stack

### Authentication & Authorization
- Laravel Breeze for authentication scaffolding
- Spatie Laravel Permission for role-based access control
- Custom middleware for role verification
- User types: 'admin', 'vendedor' (vendor), 'empresa' (company)

### Database Architecture
- Standard Laravel migrations in `database/migrations/`
- Seeders for initial data setup
- Foreign key relationships between models
- Soft deletes where applicable

## Configuration Notes

### Environment Setup
- Copy `.env.example` to `.env` and configure database credentials
- Set `APP_KEY` using `php artisan key:generate`
- Configure mail settings for notifications
- Set up payment gateway credentials

### Database Configuration
- Supports MySQL (primary), PostgreSQL, SQLite
- Configure `DB_*` variables in `.env`
- Run migrations and seeders for initial setup

### Frontend Assets
- CSS/JS assets compiled via Vite (primarily for auth pages)
- Bootstrap and jQuery loaded via CDN (main application stack)
- Custom styles in `resources/css/app.css`
- Images stored in `resources/images/` and `public/images/`
- **Note**: Main application does not use Vite build process - assets are loaded directly via CDN

## Key Dependencies

### Backend (Composer)
- `laravel/framework ^9.19`: Core Laravel framework
- `spatie/laravel-permission ^6.20`: Role and permission management
- `livewire/livewire ^2.12`: Dynamic interfaces
- `barryvdh/laravel-dompdf ^3.1`: PDF generation
- `maatwebsite/excel ^3.1`: Excel import/export
- `yajra/laravel-datatables-oracle 10.0`: DataTables server-side processing

### Frontend (NPM)
- `vite ^4.0.0`: Build tool and dev server (only used for login/register pages)
- `tailwindcss ^3.4.17`: Utility-first CSS framework (only used for login/register pages)
- `alpinejs ^3.4.2`: Minimal JavaScript framework (only used for login/register pages)
- `bootstrap ^5.3.7`: CSS framework (primary frontend framework)
- `datatables.net ^2.3.2`: Interactive tables
- `sweetalert2 ^11.22.2`: Beautiful alerts

## Development Workflow

### Adding New Features
1. Create migration files for database changes
2. Generate/update models with relationships
3. Create controllers following RESTful conventions
4. Define routes in appropriate route files
5. Create Blade views with consistent layout
6. Add frontend interactions using Alpine.js/jQuery
7. Write feature tests for new functionality

### Code Conventions
- Follow PSR-12 coding standards
- Use Laravel's naming conventions (PascalCase for models, snake_case for tables)
- Implement proper error handling and validation
- Use Laravel's built-in features (Eloquent, Blade, etc.)
- Maintain consistent indentation (4 spaces)

### Database Migrations
- Always create reversible migrations
- Use descriptive migration names
- Add proper indexes for foreign keys
- Include default values where appropriate