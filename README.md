# Sopas y Sopitas

Aplicacion web base construida sobre Laravel 9 con autenticacion, gestion de perfil, layout con sidebar y modo oscuro/claro listos para extender.

## Stack

- Laravel 9 (PHP 8.0+)
- Vite, Alpine.js, Tailwind CSS, Bootstrap 5
- MySQL (XAMPP en Windows)
- Laravel Breeze (auth) + Spatie Laravel-Permission (roles)

## Configuracion inicial

```bash
composer install
npm install
cp .env.example .env   # si aun no existe
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
php artisan serve
```

Usuario por defecto: `admin@admin.com` / `12345678` (rol `admin`).

## Comandos utiles

```bash
/dev-start                          # XAMPP + Laravel + Vite + Chrome
/dev-stop                           # Detener servicios
php artisan migrate:fresh --seed    # BD limpia con admin
php artisan test                    # Correr tests
./vendor/bin/pint                   # Formatear codigo
```

## Documentacion

Convenciones del proyecto y reglas para nuevos modulos (incluyendo soporte obligatorio de modo claro/oscuro) en [CLAUDE.md](CLAUDE.md).
