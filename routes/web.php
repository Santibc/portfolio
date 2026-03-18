<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AdminLandingPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== PAGINAS PUBLICAS ==========
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/nosotros', [HomeController::class, 'nosotros'])->name('nosotros');
Route::get('/servicios', [HomeController::class, 'servicios'])->name('servicios');
Route::get('/servicios/{slug}', [HomeController::class, 'servicioDetalle'])->name('servicios.detalle');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::post('/contact/send', [AdminLandingPageController::class, 'sendContactEmail'])->name('contact.send');

// ========== BLOG ==========
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/categoria/{slug}', [BlogController::class, 'byCategory'])->name('blog.category');
Route::get('/blog/etiqueta/{slug}', [BlogController::class, 'byTag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ========== SEO ==========
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// ========== ADMIN ==========
Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Usuarios
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/usuarios_form/{user?}', [UsuariosController::class, 'form'])->name('usuarios.form');
    Route::post('/usuarios/guardar', [UsuariosController::class, 'guardar'])->name('usuarios.guardar');

    // Admin Landing Page
    Route::prefix('admin/landing')->name('admin.landing.')->group(function () {
        Route::get('/', [AdminLandingPageController::class, 'index'])->name('index');
        Route::post('/config/update', [AdminLandingPageController::class, 'updateConfig'])->name('config.update');

        // Carousel
        Route::post('/carousel/store', [AdminLandingPageController::class, 'storeCarouselImage'])->name('carousel.store');
        Route::delete('/carousel/{id}', [AdminLandingPageController::class, 'deleteCarouselImage'])->name('carousel.delete');

        // Services
        Route::post('/services/store', [AdminLandingPageController::class, 'storeService'])->name('services.store');
        Route::put('/services/{id}', [AdminLandingPageController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{id}', [AdminLandingPageController::class, 'deleteService'])->name('services.delete');

        // Steps
        Route::post('/steps/store', [AdminLandingPageController::class, 'storeStep'])->name('steps.store');
        Route::put('/steps/{id}', [AdminLandingPageController::class, 'updateStep'])->name('steps.update');
        Route::delete('/steps/{id}', [AdminLandingPageController::class, 'deleteStep'])->name('steps.delete');

        // Contact
        Route::post('/contact/update', [AdminLandingPageController::class, 'updateContactInfo'])->name('contact.update');

        // About
        Route::post('/about/update', [AdminLandingPageController::class, 'updateAbout'])->name('about.update');

        // Team
        Route::post('/team/store', [AdminLandingPageController::class, 'storeTeamMember'])->name('team.store');
        Route::put('/team/{id}', [AdminLandingPageController::class, 'updateTeamMember'])->name('team.update');
        Route::delete('/team/{id}', [AdminLandingPageController::class, 'deleteTeamMember'])->name('team.delete');

        // Layout
        Route::post('/layout/update', [AdminLandingPageController::class, 'updateLayoutConfig'])->name('layout.update');

        // Home Config
        Route::post('/home/update', [AdminLandingPageController::class, 'updateHomeConfig'])->name('home.update');

        // SEO
        Route::post('/seo/update', [AdminLandingPageController::class, 'updateSeo'])->name('seo.update');
        Route::get('/seo/{pageId}', [AdminLandingPageController::class, 'getSeoData'])->name('seo.get');
        Route::delete('/seo/{id}', [AdminLandingPageController::class, 'deleteSeo'])->name('seo.delete');

        // Hero Values
        Route::post('/hero-values/store', [AdminLandingPageController::class, 'storeHeroValue'])->name('hero-values.store');
        Route::put('/hero-values/{id}', [AdminLandingPageController::class, 'updateHeroValue'])->name('hero-values.update');
        Route::delete('/hero-values/{id}', [AdminLandingPageController::class, 'deleteHeroValue'])->name('hero-values.delete');

        // Testimonials
        Route::post('/testimonials/store', [AdminLandingPageController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('/testimonials/{id}', [AdminLandingPageController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('/testimonials/{id}', [AdminLandingPageController::class, 'deleteTestimonial'])->name('testimonials.delete');

        // Blog Admin
        Route::post('/blog/posts/store', [AdminLandingPageController::class, 'storeBlogPost'])->name('blog.posts.store');
        Route::put('/blog/posts/{id}', [AdminLandingPageController::class, 'updateBlogPost'])->name('blog.posts.update');
        Route::delete('/blog/posts/{id}', [AdminLandingPageController::class, 'deleteBlogPost'])->name('blog.posts.delete');
        Route::post('/blog/categories/store', [AdminLandingPageController::class, 'storeBlogCategory'])->name('blog.categories.store');
        Route::put('/blog/categories/{id}', [AdminLandingPageController::class, 'updateBlogCategory'])->name('blog.categories.update');
        Route::delete('/blog/categories/{id}', [AdminLandingPageController::class, 'deleteBlogCategory'])->name('blog.categories.delete');

        // Gallery
        Route::post('/gallery/store', [AdminLandingPageController::class, 'storeGalleryImage'])->name('gallery.store');
        Route::put('/gallery/{id}', [AdminLandingPageController::class, 'updateGalleryImage'])->name('gallery.update');
        Route::delete('/gallery/{id}', [AdminLandingPageController::class, 'deleteGalleryImage'])->name('gallery.delete');
    });
});

require __DIR__.'/auth.php';
