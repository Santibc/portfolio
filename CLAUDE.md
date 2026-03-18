# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture Overview

This is a Laravel 9 website for **Manzer Agroforestal, S.L.** - a professional forestry and agroforestry services company based in Lleida, Spain. The application provides:

- A premium landing page with cutting-edge visual effects (GSAP, Lenis, tsParticles, Splitting.js)
- Dynamic blog system with categories and tags
- Service pages with individual SEO
- Gallery/portfolio management
- Contact form with email integration
- Full admin panel for content management
- Advanced SEO (sitemap, Schema.org, Open Graph)

Key entities:
- `LandingService` - Forestry services with detail pages
- `BlogPost` / `BlogCategory` / `BlogTag` - Blog system
- `LandingGalleryImage` - Portfolio gallery
- `LandingHomeConfig` / `LandingLayoutConfig` / `LandingAbout` / `LandingContactInfo` - Configurable content
- `Page` / `Seo` - SEO per page system

## Development Commands

**Backend (Laravel/PHP):**
```bash
php artisan serve
php artisan migrate
php artisan db:seed
php artisan test
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan storage:link
```

**Frontend (Vite + TailwindCSS):**
```bash
npm install
npm run dev
npm run build
```

## Database Structure

MySQL with migrations in `database/migrations/`. Key tables:
- `landing_services` - Services with slug, SEO, images
- `blog_posts`, `blog_categories`, `blog_tags`, `blog_post_tag` - Blog system
- `landing_gallery_images` - Portfolio gallery
- `landing_home_configs`, `landing_layout_config`, `landing_about`, `landing_contact_info` - Content config
- `pages`, `seo` - SEO system with Open Graph and Schema.org
- `landing_carousel_images`, `landing_hero_values`, `landing_testimonials` - UI elements

## Key Directories

- `app/Models/` - Eloquent models
- `app/Http/Controllers/` - HomeController, BlogController, SeoController, AdminLandingPageController
- `resources/views/landing_page/` - Public Blade templates (home, servicios, nosotros, contacto, blog/)
- `resources/views/admin/landing/` - Admin panel views
- `routes/web.php` - All routes
- `public/images/` - Images organized by section (gallery, services, blog, home, etc.)

## Frontend Stack

- **CSS**: Custom CSS with CSS variables (--manzer-green, --manzer-forest, etc.)
- **Animations**: GSAP 3.12 + ScrollTrigger (scroll animations, parallax, pinning)
- **Smooth Scroll**: Lenis
- **Text Effects**: Splitting.js (character-by-character reveals)
- **Particles**: tsParticles (falling leaves in hero)
- **3D Hover**: Vanilla-tilt.js (service cards)
- **Carousels**: Swiper 11
- **Gallery**: LightGallery 2.7
- **Counters**: PureCounter
- **Typography**: Montserrat (headings) + Inter (body) via Google Fonts
- **Icons**: Bootstrap Icons
- All libraries loaded via CDN

## URL Structure

- `/` - Homepage
- `/servicios` - Services listing
- `/servicios/{slug}` - Service detail page
- `/nosotros` - About page
- `/contacto` - Contact page
- `/blog` - Blog listing
- `/blog/{slug}` - Blog post
- `/blog/categoria/{slug}` - Blog by category
- `/blog/etiqueta/{slug}` - Blog by tag
- `/sitemap.xml` - XML Sitemap
- `/admin/landing` - Admin panel

## Deployment

Hosted on Hostinger shared hosting:
- Run `npm run build` locally and commit `public/build/`
- Run `php artisan config:cache && route:cache && view:cache` in production
- No Node.js server needed
