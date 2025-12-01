# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a clean Laravel 9 application skeleton with authentication and role-based permissions. It serves as a base template for building new applications with user management and authorization already configured.

**Tech Stack:**
- Backend: Laravel 9 (PHP 8.0+)
- Frontend: Vite + Alpine.js + Tailwind CSS
- Database: MySQL
- Key Packages: Laravel Breeze (authentication), Spatie Laravel-Permission (roles & permissions)
- Development Environment: XAMPP (Windows)

## Common Commands

### Development Server
```bash
# Start development environment (XAMPP + Laravel + Vite)
# Use the custom slash command
/dev-start

# Stop all services
/dev-stop

# Manual alternative:
php artisan serve --host=127.0.0.1 --port=8000
npm run dev
```

### Build
```bash
npm run build
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Fresh migration with seed
php artisan migrate:fresh --seed
```

### Testing
```bash
# Run all tests
php artisan test

# Or using PHPUnit directly
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Unit
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint

# With specific paths
./vendor/bin/pint app/Http/Controllers
```

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize
```

## Architecture

### Authentication & Authorization

**Laravel Breeze**: Provides authentication scaffolding
- Login, registration, password reset, email verification
- Profile management
- Routes in routes/auth.php

**Spatie Laravel-Permission**: Role-based access control
- Models: Role, Permission (managed by Spatie package)
- Assign roles to users: `$user->assignRole('admin')`
- Check permissions: `$user->hasPermissionTo('edit articles')`
- Middleware: `->middleware('role:admin')` or `->middleware('permission:edit articles')`

### Key Models

**User** (app/Models/User.php)
- Default Laravel user model
- Uses Spatie's `HasRoles` trait
- Fields: name, email, password, email_verified_at
- Relationships: roles, permissions

### Controllers

**HomeController** (app/Http/Controllers/HomeController.php)
- `index()`: Shows dashboard for authenticated users
- `welcome()`: Shows "under construction" page for root route

**ProfileController** (app/Http/Controllers/ProfileController.php)
- Handles user profile editing
- From Laravel Breeze

**Auth Controllers** (app/Http/Controllers/Auth/*)
- Authentication logic from Laravel Breeze
- Login, register, password reset, email verification

### Routes Structure

**routes/web.php**: Main application routes
- `/`: Under construction page
- `/dashboard`: Authenticated dashboard
- `/profile`: User profile management (auth required)

**routes/auth.php**: Authentication routes (from Breeze)
- `/login`, `/register`, `/logout`, etc.

### Frontend Stack

**Views**: resources/views (Blade templates)
- `welcome.blade.php`: Under construction landing page
- `dashboard.blade.php`: Dashboard view
- `auth/`: Authentication views (from Breeze)
- `profile/`: Profile management views
- `layouts/`: Layout templates (app.blade.php, guest.blade.php, navigation.blade.php)
- `components/`: Reusable Blade components

**Assets**: Compiled via Vite (vite.config.js)
- resources/js/app.js: Alpine.js, jQuery, DataTables, SweetAlert2
- resources/css/app.css: Tailwind CSS

**Key Frontend Libraries:**
- Alpine.js for interactive components
- jQuery for legacy support
- DataTables for table management
- SweetAlert2 for alerts (available as window.Swal)

### Database

**Core Tables:**
- `users`: User accounts
- `password_resets`: Password reset tokens
- `failed_jobs`: Failed queue jobs
- `personal_access_tokens`: API tokens (Laravel Sanctum)
- `roles`: User roles (Spatie)
- `permissions`: User permissions (Spatie)
- `model_has_roles`: User-role pivot
- `model_has_permissions`: User-permission pivot
- `role_has_permissions`: Role-permission pivot

**Migration**: Use the cleanup migration to remove old unused tables:
```bash
php artisan migrate
```

### Middleware

Standard Laravel middleware configured in app/Http/Kernel.php:
- `auth`: Requires authentication
- `guest`: Only for non-authenticated users
- `verified`: Requires email verification
- `role:admin`: Requires specific role (from Spatie)
- `permission:edit articles`: Requires specific permission (from Spatie)

## Development Notes

### Local Environment
- Runs on XAMPP (Windows)
- Database: MySQL on 127.0.0.1:3306
- Laravel dev server: 127.0.0.1:8000
- Vite dev server: localhost:5173
- Database name: "agro" (configured in .env)

### Adding New Features

When building new modules:
1. Create models in app/Models
2. Create migrations in database/migrations
3. Create controllers in app/Http/Controllers
4. Add routes in routes/web.php
5. Create views in resources/views
6. Update navigation in resources/views/layouts/navigation.blade.php

### Using Roles & Permissions

**Assign roles to users:**
```php
$user->assignRole('admin');
$user->assignRole(['writer', 'editor']);
```

**Check roles:**
```php
if ($user->hasRole('admin')) {
    // User is admin
}
```

**Assign permissions:**
```php
$user->givePermissionTo('edit articles');
$role->givePermissionTo('edit articles');
```

**Check permissions:**
```php
if ($user->can('edit articles')) {
    // User can edit articles
}
```

**Middleware in routes:**
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin only routes
});

Route::middleware(['auth', 'permission:edit articles'])->group(function () {
    // Routes requiring specific permission
});
```

## Database
- Primary database: MySQL (agro)
- Connection settings in .env
- No password for local XAMPP setup
- Run migrations to set up clean database structure
