# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 9 base template with authentication and role-based permissions. Cleaned down to a minimal skeleton (login + menu + header + reusable components) ready to build new features on top.

**Tech Stack:**
- Laravel 9.19 (PHP 8.0+), Vite, Alpine.js, Tailwind CSS (via CDN), Bootstrap 5 (via CDN)
- MySQL database `agro`, XAMPP (Windows)
- Laravel Breeze (auth), Spatie Laravel-Permission 6.x (roles), Livewire 2.12
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
php artisan migrate:fresh --seed   # Fresh database + admin@admin.com / 12345678

# Testing
php artisan test                   # Run all tests
./vendor/bin/phpunit --filter=TestName  # Run single test

# Code quality (see section below)
./vendor/bin/pint                  # Format PHP
./vendor/bin/phpstan analyse       # Static analysis

# Cache (run after config changes)
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## Architecture

### Roles & Permissions (Spatie)

Single role configured: **Administrador**. Defined in [database/seeders/RolesAndPermissionsSeeder.php](database/seeders/RolesAndPermissionsSeeder.php).

- User model uses `HasRoles` trait
- Middleware to protect routes: `->middleware('role:Administrador')`
- Seed user: `admin@admin.com` / `12345678`

Role utilities centralized in [app/Services/Auth/RoleService.php](app/Services/Auth/RoleService.php): `assignDefaultRole()`, `changeUserRole()`, `addRoleToUser()`, `hasPermissions()`, `getDashboardRoute()`, `canAccessSection()`, `getAllRoles()`.

### Routes

- [routes/web.php](routes/web.php): `/`, `/dashboard`, `/profile/*`
- [routes/auth.php](routes/auth.php): authentication routes (Breeze)

### Frontend

- Main layout: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) (header + sidebar)
- Guest layout: [resources/views/layouts/guest.blade.php](resources/views/layouts/guest.blade.php)
- Sidebar: [resources/views/layouts/navigation-vertical.blade.php](resources/views/layouts/navigation-vertical.blade.php)

Global JS: jQuery as `window.$`, SweetAlert2 as `window.Swal`, Alpine.js as `window.Alpine`.

### Reusable components

Under [resources/views/components/](resources/views/components/):
- Base (Breeze): `application-logo`, `auth-session-status`, `dropdown`, `dropdown-link`, `input-error`, `input-label`, `modal`, `nav-link`, `primary-button`, `secondary-button`, `danger-button`, `responsive-nav-link`, `text-input`
- Manzer subsystem (`x-manzer.*`): `alert`, `badge`, `button`, `data-table`, `form-group`, `modal`, `page-header`, `progress-bar`, `stat-card`, `table-cell`, `table-row`

### Database

MySQL database `agro` on `127.0.0.1:3306` (no password for local XAMPP).

Only base tables exist currently: `users`, `password_resets`, `failed_jobs`, `personal_access_tokens`, plus Spatie tables (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`).

### File Uploads

**NEVER use Laravel Storage (`storage/` or `storage_path()`).** All file uploads go directly to `public/uploads/` using `public_path()`. Files are served via `asset('uploads/...')`. No symlinks needed.

- Upload path: `public_path("uploads/{subdirectory}")`
- Public URL: `asset("uploads/{subdirectory}/{filename}")`
- Use `Illuminate\Support\Facades\File` for file operations (not Storage facade)

## Adding Features

1. Model in `app/Models/`
2. Migration in `database/migrations/`
3. Controller in `app/Http/Controllers/`
4. Service in `app/Services/` (for complex business logic)
5. Routes in [routes/web.php](routes/web.php)
6. Views in `resources/views/` (reuse components from [resources/views/components/](resources/views/components/))
7. Update [resources/views/layouts/navigation-vertical.blade.php](resources/views/layouts/navigation-vertical.blade.php) to add the menu entry

## Code Quality Tools

### Laravel Pint (formateo)

Instalado en `require-dev`. Usa el preset por defecto de Laravel.

- Formatear todo: `./vendor/bin/pint`
- Solo mostrar qué cambiaría (dry-run): `./vendor/bin/pint --test`
- **Automático**: hay un hook `PostToolUse` en [.claude/settings.json](.claude/settings.json) que corre `pint --quiet` después de cada `Edit`/`Write` de Claude.

### PHPStan + Larastan (análisis estático)

- Versión: PHPStan 1.12 + Larastan 2.11 (compatible con Laravel 9)
- Config: [phpstan.neon](phpstan.neon) — nivel 5, paths analizados: `app/`, `config/`, `database/seeders/`, `database/factories/`, `routes/`
- Comando: `./vendor/bin/phpstan analyse --memory-limit=2G`
- **Automático**: hook `Stop` en [.claude/settings.json](.claude/settings.json) ejecuta PHPStan al final de cada turno y muestra las últimas 30 líneas si hay errores.

Baseline actual: **0 errores**. Si agregas código con errores, PHPStan los reportará al terminar la sesión.

## Claude Agents disponibles

Subagentes especializados en [.claude/agents/](.claude/agents/) — invocables por nombre (ej: *"usa el agente `php-reviewer` para revisar estos cambios"*):

- **[php-reviewer](.claude/agents/php-reviewer.md)** — Revisión de calidad de código PHP: Pint + PHPStan + code smells + convenciones Laravel. Úsalo antes de cerrar un feature.
- **[blade-designer](.claude/agents/blade-designer.md)** — Diseño de vistas Blade con Tailwind + componentes `manzer/*` reutilizables. Úsalo para crear formularios, tablas, modales, layouts.
- **[test-writer](.claude/agents/test-writer.md)** — Generación de tests PHPUnit (Feature tests). Úsalo al cerrar un feature que necesite cobertura.
- **[security-auditor](.claude/agents/security-auditor.md)** — Auditoría de seguridad OWASP aplicada a Laravel. Úsalo antes de deploy o cuando añadas un endpoint mutativo.
- **[db-expert](.claude/agents/db-expert.md)** — Diseño de migraciones, índices, relaciones Eloquent, optimización de queries. Úsalo al añadir nuevas tablas o detectar queries lentas.

Se pueden invocar en paralelo para acelerar revisiones complejas. Ejemplo: *"Revisa el módulo X con `php-reviewer` y `security-auditor` en paralelo"*.
