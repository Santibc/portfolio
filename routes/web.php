<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard (redirige según rol)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==========================================
// RUTAS PÚBLICAS/ESTUDIANTE (autenticadas)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categorías
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categorias.index');
    Route::get('/categorias/{categoria:slug}', [CategoryController::class, 'show'])->name('categorias.show');

    // Cursos
    Route::get('/cursos', [CourseController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/{course:slug}', [CourseController::class, 'show'])
        ->middleware('course.access')
        ->name('cursos.show');
    Route::get('/cursos/{course:slug}/video/{video}', [CourseController::class, 'video'])
        ->middleware('course.access')
        ->name('cursos.video');

    // Progreso
    Route::get('/mi-progreso', [ProgressController::class, 'index'])->name('progreso.index');
    Route::post('/progreso/video/{video}/completar', [ProgressController::class, 'markVideoComplete'])
        ->name('progreso.marcar');
    Route::delete('/progreso/video/{video}/desmarcar', [ProgressController::class, 'unmarkVideoComplete'])
        ->name('progreso.desmarcar');
    Route::get('/progreso/general', [ProgressController::class, 'getOverallProgress'])
        ->name('progreso.general');
    Route::get('/progreso/curso/{course}', [ProgressController::class, 'getCourseProgress'])
        ->name('progreso.curso');

    // Notas
    Route::get('/mis-notas', [NoteController::class, 'misNotas'])->name('notas.mis-notas');
    Route::get('/cursos/{course}/notas', [NoteController::class, 'index'])->name('notas.index');
    Route::post('/cursos/{course}/notas', [NoteController::class, 'store'])->name('notas.store');
    Route::put('/notas/{note}', [NoteController::class, 'update'])->name('notas.update');
    Route::delete('/notas/{note}', [NoteController::class, 'destroy'])->name('notas.destroy');
    Route::get('/notas/buscar', [NoteController::class, 'search'])->name('notas.search');
});

// ==========================================
// RUTAS DE ADMINISTRACIÓN
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestión de Usuarios
    Route::resource('usuarios', AdminUserController::class)->parameters(['usuarios' => 'user']);

    // Gestión de Categorías
    Route::resource('categorias', AdminCategoryController::class)->parameters(['categorias' => 'category']);
    Route::get('/categorias/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categorias.edit.ajax');
    Route::patch('/categorias/{category}/toggle', [AdminCategoryController::class, 'toggleActive'])->name('categorias.toggle');
    Route::post('/categorias/reorder', [AdminCategoryController::class, 'reorder'])->name('categorias.reorder');

    // Gestión de Cursos
    Route::resource('cursos', AdminCourseController::class)->parameters(['cursos' => 'course']);
    Route::get('/cursos/{course}/edit', [AdminCourseController::class, 'edit'])->name('cursos.edit.ajax');
    Route::patch('/cursos/{course}/toggle', [AdminCourseController::class, 'togglePublish'])->name('cursos.toggle');
    Route::post('/cursos/reorder', [AdminCourseController::class, 'reorder'])->name('cursos.reorder');

    // Gestión de Videos (anidado en cursos)
    Route::get('/cursos/{course}/videos', [AdminVideoController::class, 'index'])->name('cursos.videos.index');
    Route::get('/cursos/{course}/videos/create', [AdminVideoController::class, 'create'])->name('cursos.videos.create');
    Route::post('/cursos/{course}/videos', [AdminVideoController::class, 'store'])->name('cursos.videos.store');
    Route::get('/cursos/{course}/videos/{video}/edit', [AdminVideoController::class, 'edit'])->name('cursos.videos.edit');
    Route::put('/cursos/{course}/videos/{video}', [AdminVideoController::class, 'update'])->name('cursos.videos.update');
    Route::delete('/cursos/{course}/videos/{video}', [AdminVideoController::class, 'destroy'])->name('cursos.videos.destroy');
    Route::post('/cursos/{course}/videos/reorder', [AdminVideoController::class, 'reorder'])->name('cursos.videos.reorder');

    // Reportes
    Route::get('/reportes', [AdminReportController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/estudiantes', [AdminReportController::class, 'students'])->name('reportes.estudiantes');
    Route::get('/reportes/estudiantes/{user}', [AdminReportController::class, 'studentDetail'])->name('reportes.estudiante');
    Route::get('/reportes/cursos', [AdminReportController::class, 'courses'])->name('reportes.cursos');
});

require __DIR__.'/auth.php';
