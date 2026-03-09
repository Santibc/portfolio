# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 9 application skeleton with authentication and role-based permissions. Serves as a base template for building applications with user management already configured.

**Tech Stack:**
- Laravel 9 (PHP 8.0+), Vite, Alpine.js, Tailwind CSS
- MySQL database, XAMPP (Windows)
- Laravel Breeze (auth), Spatie Laravel-Permission (roles), Livewire
- Yajra DataTables, DomPDF, Maatwebsite Excel

## Commands

```bash
# Development (use slash commands)
/dev-start                    # Start XAMPP + Laravel + Vite + Chrome
/dev-stop                     # Stop all services

# Manual alternative
php artisan serve --host=127.0.0.1 --port=8000
npm run dev

# Database
php artisan migrate:fresh --seed   # Fresh database with seed data

# Testing
php artisan test                   # Run all tests
./vendor/bin/phpunit --filter=TestName  # Run single test

# Code formatting
./vendor/bin/pint                  # Laravel Pint

# Cache (run after config changes)
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## Architecture

### Service Layer Pattern
Role management is centralized in `app/Services/Auth/RoleService.php`:
- `assignDefaultRole()`, `changeUserRole()`, `addRoleToUser()`
- `getDashboardRoute()`, `canAccessSection()`

Dashboard services in `app/Services/Dashboard/` provide role-specific dashboard data.

### Roles & Permissions (Spatie)
Default roles defined in `database/seeders/RolesAndPermissionsSeeder.php`:
- Administrador (all permissions)
- Usuario

User model uses `HasRoles` trait. Use middleware: `->middleware('role:Administrador')`

### Routes
- `routes/web.php`: Main routes (/, /dashboard, /profile)
- `routes/auth.php`: Authentication routes (Breeze)

### Frontend
- Layouts: `resources/views/layouts/` (app.blade.php, guest.blade.php, navigation-vertical.blade.php)
- Global JS: jQuery as `window.$`, SweetAlert2 as `window.Swal`, Alpine.js as `window.Alpine`
- DataTables with export buttons (Excel, PDF, Print)

### Database
MySQL database "agro" on 127.0.0.1:3306 (no password for local XAMPP).

Core tables: users, roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

### File Uploads
**NEVER use Laravel Storage (storage/ or storage_path()).** All file uploads go directly to `public/uploads/` using `public_path()`. Files are served via `asset('uploads/...')`. No symlinks needed.

- Upload path: `public_path("uploads/{subdirectory}")`
- Public URL: `asset("uploads/{subdirectory}/{filename}")`
- Use `Illuminate\Support\Facades\File` for file operations (not Storage facade)
- Example: Tablero attachments → `public/uploads/tableros/{tablero_id}/{tarjeta_id}/`

## Adding Features

1. Model in `app/Models/`
2. Migration in `database/migrations/`
3. Controller in `app/Http/Controllers/`
4. Service in `app/Services/` (for complex business logic)
5. Routes in `routes/web.php`
6. Views in `resources/views/`
7. Update navigation in `resources/views/layouts/navigation-vertical.blade.php`
