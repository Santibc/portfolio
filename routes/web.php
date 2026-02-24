<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CatalogoItemController;
use App\Http\Controllers\BosquejoMatrizController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\OperarioController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\RoleRedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Pagina principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard - redirige segun rol del usuario
Route::get('/dashboard', [RoleRedirectController::class, 'redirect'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// RUTAS DE PERFIL (autenticadas)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('role:Administrador');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
});

// ==========================================
// RUTAS DE RECEPCION
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador|Recepcion'])
    ->prefix('recepcion')->name('recepcion.')->group(function () {
        Route::get('/panel', function () {
            return view('recepcion.panel');
        })->name('panel');

        // Clientes
        Route::get('/clientes/autocomplete', [ClienteController::class, 'autocomplete'])->name('clientes.autocomplete');
        Route::get('/clientes/export-excel', [ClienteController::class, 'exportExcel'])->name('clientes.export-excel');
        Route::get('/clientes/export-pdf', [ClienteController::class, 'exportPdf'])->name('clientes.export-pdf');
        Route::patch('/clientes/{cliente}/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle-activo')->middleware('role:Administrador');
        Route::resource('clientes', ClienteController::class);

        // Catalogo Items
        Route::get('/items/autocomplete', [CatalogoItemController::class, 'autocomplete'])->name('items.autocomplete');
        Route::get('/items/export-excel', [CatalogoItemController::class, 'exportExcel'])->name('items.export-excel');
        Route::get('/items/export-pdf', [CatalogoItemController::class, 'exportPdf'])->name('items.export-pdf');
        Route::patch('/items/{item}/toggle-activo', [CatalogoItemController::class, 'toggleActivo'])->name('items.toggle-activo');
        Route::resource('items', CatalogoItemController::class)->except(['show', 'destroy'])->parameters(['items' => 'item']);

        // Ordenes - Listado y Exportacion (rutas literales ANTES de {orden})
        Route::get('/ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/export-excel', [OrdenController::class, 'exportExcel'])->name('ordenes.export-excel');
        Route::get('/ordenes/export-pdf', [OrdenController::class, 'exportPdf'])->name('ordenes.export-pdf');

        // Ordenes - Creacion (Wizard)
        Route::get('/ordenes/crear', [OrdenController::class, 'create'])->name('ordenes.crear');
        Route::post('/ordenes/guardar', [OrdenController::class, 'guardar'])->name('ordenes.guardar');
        Route::post('/ordenes/generar', [OrdenController::class, 'generar'])->name('ordenes.generar');
        Route::post('/ordenes/subir-bosquejo', [OrdenController::class, 'subirBosquejo'])->name('ordenes.subir-bosquejo');
        Route::post('/ordenes/crear-cliente-inline', [OrdenController::class, 'crearClienteInline'])->name('ordenes.crear-cliente-inline');
        Route::get('/ordenes/operarios', [OrdenController::class, 'listarOperarios'])->name('ordenes.operarios');
        Route::get('/ordenes/grupos-bosquejos', [OrdenController::class, 'listarGruposBosquejos'])->name('ordenes.grupos-bosquejos');

        // Ordenes - Detalle y Gestion (rutas con parametro {orden})
        Route::get('/ordenes/{orden}', [OrdenController::class, 'show'])->name('ordenes.show');
        Route::get('/ordenes/{orden}/editar', [OrdenController::class, 'edit'])->name('ordenes.edit');
        Route::put('/ordenes/{orden}', [OrdenController::class, 'update'])->name('ordenes.update');
        Route::post('/ordenes/{orden}/copiar', [OrdenController::class, 'copiar'])->name('ordenes.copiar');
        Route::post('/ordenes/{orden}/anular', [OrdenController::class, 'anular'])->name('ordenes.anular');
        Route::post('/ordenes/{orden}/comentarios', [OrdenController::class, 'agregarComentario'])->name('ordenes.comentarios.store');
        Route::post('/ordenes/{orden}/pagos', [OrdenController::class, 'agregarPago'])->name('ordenes.pagos.store');
    });

// ==========================================
// RUTAS DE BOSQUEJOS MATRIZ (todos los roles)
// ==========================================
Route::middleware(['auth', 'verified'])
    ->prefix('recepcion')->name('recepcion.')->group(function () {
        // Lectura: cualquier usuario con permiso ver_bosquejos_matriz
        Route::middleware('permission:ver_bosquejos_matriz')->group(function () {
            Route::get('/bosquejos-matriz', [BosquejoMatrizController::class, 'index'])->name('bosquejos-matriz.index');
            Route::get('/bosquejos-matriz/bosquejos/{bosquejo}/descargar', [BosquejoMatrizController::class, 'downloadBosquejo'])->name('bosquejos-matriz.bosquejos.descargar');
        });
        // Escritura: solo Administrador
        Route::middleware('role:Administrador')->group(function () {
            Route::post('/bosquejos-matriz/grupos', [BosquejoMatrizController::class, 'storeGrupo'])->name('bosquejos-matriz.grupos.store');
            Route::put('/bosquejos-matriz/grupos/{grupo}', [BosquejoMatrizController::class, 'updateGrupo'])->name('bosquejos-matriz.grupos.update');
            Route::delete('/bosquejos-matriz/grupos/{grupo}', [BosquejoMatrizController::class, 'destroyGrupo'])->name('bosquejos-matriz.grupos.destroy');
            Route::post('/bosquejos-matriz/bosquejos', [BosquejoMatrizController::class, 'storeBosquejo'])->name('bosquejos-matriz.bosquejos.store');
            Route::put('/bosquejos-matriz/bosquejos/{bosquejo}', [BosquejoMatrizController::class, 'updateBosquejo'])->name('bosquejos-matriz.bosquejos.update');
            Route::delete('/bosquejos-matriz/bosquejos/{bosquejo}', [BosquejoMatrizController::class, 'destroyBosquejo'])->name('bosquejos-matriz.bosquejos.destroy');
        });
    });

// ==========================================
// RUTAS DE OPERARIO
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador|Operario'])
    ->prefix('operario')->name('operario.')->group(function () {
        // Dashboard
        Route::get('/panel', [OperarioController::class, 'panel'])->name('panel');

        // Ordenes asignadas
        Route::get('/ordenes-asignadas', [OperarioController::class, 'ordenesAsignadas'])->name('ordenes-asignadas');

        // Vista de trabajo
        Route::get('/ordenes/{orden}', [OperarioController::class, 'trabajar'])->name('ordenes.trabajar');

        // Buscar orden
        Route::get('/buscar', [OperarioController::class, 'buscar'])->name('buscar');
        Route::get('/buscar-orden', [OperarioController::class, 'buscarOrden'])->name('buscar-orden');

        // Complementar
        Route::get('/complementar', [OperarioController::class, 'complementar'])->name('complementar');

        // AJAX: Trabajo con piezas
        Route::post('/ordenes/{orden}/actualizar-avances', [OperarioController::class, 'actualizarAvances'])->name('ordenes.actualizar-avances');
        Route::post('/piezas/{pieza}/transferir', [OperarioController::class, 'transferirPieza'])->name('piezas.transferir');
        Route::post('/piezas/{pieza}/dejar-cola', [OperarioController::class, 'dejarEnCola'])->name('piezas.dejar-cola');
        Route::post('/piezas/{pieza}/tomar', [OperarioController::class, 'tomarPieza'])->name('piezas.tomar');
        Route::post('/piezas/{pieza}/foto', [OperarioController::class, 'subirFoto'])->name('piezas.foto');

        // AJAX: Bloqueo
        Route::post('/ordenes/{orden}/bloquear', [OperarioController::class, 'bloquear'])->name('ordenes.bloquear');
        Route::post('/ordenes/{orden}/heartbeat', [OperarioController::class, 'heartbeat'])->name('ordenes.heartbeat');
        Route::post('/ordenes/{orden}/desbloquear', [OperarioController::class, 'desbloquear'])->name('ordenes.desbloquear');
        Route::get('/ordenes/{orden}/estado-bloqueo', [OperarioController::class, 'estadoBloqueo'])->name('ordenes.estado-bloqueo');

        // AJAX: Operarios disponibles
        Route::get('/operarios-disponibles', [OperarioController::class, 'operariosDisponibles'])->name('operarios-disponibles');
    });

// ==========================================
// RUTAS DE CONTABILIDAD
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador|Contabilidad'])
    ->prefix('contabilidad')->name('contabilidad.')->group(function () {
        Route::get('/panel', function () {
            return view('contabilidad.panel');
        })->name('panel');

        // Catalogo Items (solo lectura)
        Route::get('/items', [CatalogoItemController::class, 'index'])->name('items.index');
    });

// ==========================================
// RUTAS DE ADMINISTRACION
// ==========================================
Route::middleware(['auth', 'verified', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    // Gestion de Usuarios
    Route::resource('usuarios', AdminUserController::class)->parameters(['usuarios' => 'user']);

    // Configuracion del Sistema (placeholder)
    Route::get('/configuracion', function () {
        return view('admin.configuracion.index');
    })->name('configuracion');
});

require __DIR__.'/auth.php';
