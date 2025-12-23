<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\CuadrillaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

// Dashboard principal
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// RUTAS DE PERFIL (autenticadas)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// RUTAS DE TRABAJADORES
// ==========================================
Route::middleware(['auth', 'verified', 'permission:ver_trabajadores'])->group(function () {
    // Ver trabajadores
    Route::get('trabajadores', [TrabajadorController::class, 'index'])->name('trabajadores.index');
    Route::get('trabajadores/{trabajador}', [TrabajadorController::class, 'show'])->name('trabajadores.show');
});

Route::middleware(['auth', 'verified', 'permission:crear_trabajadores'])->group(function () {
    Route::get('trabajadores/create', [TrabajadorController::class, 'create'])->name('trabajadores.create');
    Route::post('trabajadores', [TrabajadorController::class, 'store'])->name('trabajadores.store');
});

Route::middleware(['auth', 'verified', 'permission:editar_trabajadores'])->group(function () {
    Route::get('trabajadores/{trabajador}/edit', [TrabajadorController::class, 'edit'])->name('trabajadores.edit');
    Route::put('trabajadores/{trabajador}', [TrabajadorController::class, 'update'])->name('trabajadores.update');
    Route::patch('trabajadores/{trabajador}', [TrabajadorController::class, 'update']);

    // Documentos del trabajador
    Route::post('trabajadores/{trabajador}/documentos', [TrabajadorController::class, 'storeDocumento'])
        ->name('trabajadores.documentos.store');
    Route::delete('trabajadores/{trabajador}/documentos/{documento}', [TrabajadorController::class, 'destroyDocumento'])
        ->name('trabajadores.documentos.destroy');

    // Formaciones del trabajador
    Route::post('trabajadores/{trabajador}/formaciones', [TrabajadorController::class, 'storeFormacion'])
        ->name('trabajadores.formaciones.store');
    Route::delete('trabajadores/{trabajador}/formaciones/{formacion}', [TrabajadorController::class, 'destroyFormacion'])
        ->name('trabajadores.formaciones.destroy');

    // Historial disciplinario
    Route::post('trabajadores/{trabajador}/historial', [TrabajadorController::class, 'storeHistorial'])
        ->name('trabajadores.historial.store');

    // Dar de baja
    Route::post('trabajadores/{trabajador}/baja', [TrabajadorController::class, 'darBaja'])
        ->name('trabajadores.baja');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_trabajadores'])->group(function () {
    Route::delete('trabajadores/{trabajador}', [TrabajadorController::class, 'destroy'])->name('trabajadores.destroy');
});

// ==========================================
// RUTAS DE CUADRILLAS
// ==========================================
Route::middleware(['auth', 'verified', 'permission:ver_cuadrillas'])->group(function () {
    Route::get('cuadrillas', [CuadrillaController::class, 'index'])->name('cuadrillas.index');
    Route::get('cuadrillas/{cuadrilla}', [CuadrillaController::class, 'show'])->name('cuadrillas.show');
});

Route::middleware(['auth', 'verified', 'permission:crear_cuadrillas'])->group(function () {
    Route::get('cuadrillas/create', [CuadrillaController::class, 'create'])->name('cuadrillas.create');
    Route::post('cuadrillas', [CuadrillaController::class, 'store'])->name('cuadrillas.store');
});

Route::middleware(['auth', 'verified', 'permission:editar_cuadrillas'])->group(function () {
    Route::get('cuadrillas/{cuadrilla}/edit', [CuadrillaController::class, 'edit'])->name('cuadrillas.edit');
    Route::put('cuadrillas/{cuadrilla}', [CuadrillaController::class, 'update'])->name('cuadrillas.update');
    Route::patch('cuadrillas/{cuadrilla}', [CuadrillaController::class, 'update']);

    // Gestión de trabajadores en cuadrilla
    Route::post('cuadrillas/{cuadrilla}/trabajadores', [CuadrillaController::class, 'addTrabajador'])
        ->name('cuadrillas.trabajadores.add');
    Route::delete('cuadrillas/{cuadrilla}/trabajadores/{trabajador}', [CuadrillaController::class, 'removeTrabajador'])
        ->name('cuadrillas.trabajadores.remove');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_cuadrillas'])->group(function () {
    Route::delete('cuadrillas/{cuadrilla}', [CuadrillaController::class, 'destroy'])->name('cuadrillas.destroy');
});

// ==========================================
// RUTAS DE ADMINISTRACIÓN
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Gestión de Usuarios
    Route::resource('usuarios', AdminUserController::class)->parameters(['usuarios' => 'user']);
});

require __DIR__.'/auth.php';
