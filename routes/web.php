<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\CuadrillaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\ParteDiarioController;
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
// RUTAS DE CLIENTES
// ==========================================
Route::middleware(['auth', 'verified', 'permission:ver_clientes'])->group(function () {
    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
});

Route::middleware(['auth', 'verified', 'permission:crear_clientes'])->group(function () {
    Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
});

Route::middleware(['auth', 'verified', 'permission:editar_clientes'])->group(function () {
    Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::patch('clientes/{cliente}', [ClienteController::class, 'update']);

    // Interacciones CRM
    Route::post('clientes/{cliente}/interacciones', [ClienteController::class, 'storeInteraccion'])
        ->name('clientes.interacciones.store');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_clientes'])->group(function () {
    Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});

// ==========================================
// RUTAS DE OBRAS
// ==========================================
Route::middleware(['auth', 'verified', 'permission:crear_obras'])->group(function () {
    Route::get('obras/create', [ObraController::class, 'create'])->name('obras.create');
    Route::post('obras', [ObraController::class, 'store'])->name('obras.store');
});

Route::middleware(['auth', 'verified', 'permission:ver_obras'])->group(function () {
    Route::get('obras', [ObraController::class, 'index'])->name('obras.index');
    Route::get('obras/{obra}', [ObraController::class, 'show'])->name('obras.show');
});

Route::middleware(['auth', 'verified', 'permission:editar_obras'])->group(function () {
    Route::get('obras/{obra}/edit', [ObraController::class, 'edit'])->name('obras.edit');
    Route::put('obras/{obra}', [ObraController::class, 'update'])->name('obras.update');
    Route::patch('obras/{obra}', [ObraController::class, 'update']);

    // Cambio de estado
    Route::post('obras/{obra}/cambiar-estado', [ObraController::class, 'cambiarEstado'])
        ->name('obras.cambiar-estado');

    // Hitos
    Route::post('obras/{obra}/hitos', [ObraController::class, 'storeHito'])
        ->name('obras.hitos.store');
    Route::post('obras/{obra}/hitos/{hito}/completar', [ObraController::class, 'completarHito'])
        ->name('obras.hitos.completar');
    Route::delete('obras/{obra}/hitos/{hito}', [ObraController::class, 'destroyHito'])
        ->name('obras.hitos.destroy');

    // Documentos
    Route::post('obras/{obra}/documentos', [ObraController::class, 'storeDocumento'])
        ->name('obras.documentos.store');
    Route::delete('obras/{obra}/documentos/{documento}', [ObraController::class, 'destroyDocumento'])
        ->name('obras.documentos.destroy');

    // Asignación de trabajadores
    Route::post('obras/{obra}/trabajadores', [ObraController::class, 'addTrabajador'])
        ->name('obras.trabajadores.add');
    Route::delete('obras/{obra}/trabajadores/{trabajador}', [ObraController::class, 'removeTrabajador'])
        ->name('obras.trabajadores.remove');

    // Asignación de cuadrillas
    Route::post('obras/{obra}/cuadrillas', [ObraController::class, 'addCuadrilla'])
        ->name('obras.cuadrillas.add');
    Route::delete('obras/{obra}/cuadrillas/{cuadrilla}', [ObraController::class, 'removeCuadrilla'])
        ->name('obras.cuadrillas.remove');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_obras'])->group(function () {
    Route::delete('obras/{obra}', [ObraController::class, 'destroy'])->name('obras.destroy');
});

// ==========================================
// RUTAS DE FICHAJES
// ==========================================
Route::middleware(['auth', 'verified', 'permission:crear_fichajes'])->group(function () {
    Route::get('fichajes/create', [FichajeController::class, 'create'])->name('fichajes.create');
    Route::post('fichajes', [FichajeController::class, 'store'])->name('fichajes.store');

    // API para check-in/check-out desde móvil
    Route::post('fichajes/check-in', [FichajeController::class, 'checkIn'])->name('fichajes.check-in');
    Route::post('fichajes/check-out', [FichajeController::class, 'checkOut'])->name('fichajes.check-out');
});

Route::middleware(['auth', 'verified', 'permission:ver_fichajes'])->group(function () {
    Route::get('fichajes', [FichajeController::class, 'index'])->name('fichajes.index');
    Route::get('fichajes/resumen', [FichajeController::class, 'resumen'])->name('fichajes.resumen');
    Route::get('fichajes/{fichaje}', [FichajeController::class, 'show'])->name('fichajes.show');
});

Route::middleware(['auth', 'verified', 'permission:editar_fichajes'])->group(function () {
    Route::get('fichajes/{fichaje}/edit', [FichajeController::class, 'edit'])->name('fichajes.edit');
    Route::put('fichajes/{fichaje}', [FichajeController::class, 'update'])->name('fichajes.update');
    Route::patch('fichajes/{fichaje}', [FichajeController::class, 'update']);
});

Route::middleware(['auth', 'verified', 'permission:validar_fichajes'])->group(function () {
    Route::post('fichajes/{fichaje}/validar', [FichajeController::class, 'validar'])->name('fichajes.validar');
    Route::post('fichajes/validar-multiple', [FichajeController::class, 'validarMultiple'])->name('fichajes.validar-multiple');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_fichajes'])->group(function () {
    Route::delete('fichajes/{fichaje}', [FichajeController::class, 'destroy'])->name('fichajes.destroy');
});

// ==========================================
// RUTAS DE PARTES DIARIOS
// ==========================================
Route::middleware(['auth', 'verified', 'permission:crear_partes'])->group(function () {
    Route::get('partes-diarios/create', [ParteDiarioController::class, 'create'])->name('partes-diarios.create');
    Route::post('partes-diarios', [ParteDiarioController::class, 'store'])->name('partes-diarios.store');
});

Route::middleware(['auth', 'verified', 'permission:ver_partes'])->group(function () {
    Route::get('partes-diarios', [ParteDiarioController::class, 'index'])->name('partes-diarios.index');
    Route::get('partes-diarios/{partes_diario}', [ParteDiarioController::class, 'show'])->name('partes-diarios.show');
});

Route::middleware(['auth', 'verified', 'permission:editar_partes'])->group(function () {
    Route::get('partes-diarios/{partes_diario}/edit', [ParteDiarioController::class, 'edit'])->name('partes-diarios.edit');
    Route::put('partes-diarios/{partes_diario}', [ParteDiarioController::class, 'update'])->name('partes-diarios.update');
    Route::patch('partes-diarios/{partes_diario}', [ParteDiarioController::class, 'update']);

    // Completar parte
    Route::post('partes-diarios/{partes_diario}/completar', [ParteDiarioController::class, 'completar'])
        ->name('partes-diarios.completar');

    // Duplicar parte
    Route::post('partes-diarios/{partes_diario}/duplicar', [ParteDiarioController::class, 'duplicar'])
        ->name('partes-diarios.duplicar');

    // Gestión de trabajadores en parte
    Route::post('partes-diarios/{partes_diario}/trabajadores', [ParteDiarioController::class, 'addTrabajador'])
        ->name('partes-diarios.trabajadores.add');
    Route::delete('partes-diarios/{partes_diario}/trabajadores/{trabajador}', [ParteDiarioController::class, 'removeTrabajador'])
        ->name('partes-diarios.trabajadores.remove');
});

Route::middleware(['auth', 'verified', 'permission:validar_partes'])->group(function () {
    Route::post('partes-diarios/{partes_diario}/validar', [ParteDiarioController::class, 'validar'])
        ->name('partes-diarios.validar');
});

Route::middleware(['auth', 'verified', 'permission:eliminar_partes'])->group(function () {
    Route::delete('partes-diarios/{partes_diario}', [ParteDiarioController::class, 'destroy'])->name('partes-diarios.destroy');
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
