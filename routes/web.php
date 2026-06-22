<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ConceptoGastoFijoController;
use App\Http\Controllers\DashboardCajaController;
use App\Http\Controllers\DashboardConsolidadoController;
use App\Http\Controllers\DashboardMercadoController;
use App\Http\Controllers\DashboardNominaController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\GastoFijoController;
use App\Http\Controllers\ListaMercadoController;
use App\Http\Controllers\ListaMercadoItemController;
use App\Http\Controllers\ListaMercadoPlantillaController;
use App\Http\Controllers\MenuDiaController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\PagoAhorroController;
use App\Http\Controllers\PagoAhorroNominaController;
use App\Http\Controllers\PagoNominaController;
use App\Http\Controllers\PrestacionController;
use App\Http\Controllers\ProductoMercadoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroMercadoController;
use App\Http\Controllers\TipoMenuItemController;
use App\Http\Controllers\TipoProductoMercadoController;
use App\Http\Controllers\TrabajadorTurnoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::patch('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');

    Route::view('/components', 'components-showcase')->name('components.showcase');

    Route::resource('productos-mercado/tipos', TipoProductoMercadoController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['tipos' => 'tipo'])
        ->names('productos-mercado.tipos');

    Route::resource('productos-mercado', ProductoMercadoController::class)
        ->except(['show'])
        ->parameters(['productos-mercado' => 'producto']);

    Route::get('registro-mercado', [RegistroMercadoController::class, 'index'])->name('registro-mercado.index');
    Route::get('registro-mercado/{producto}/registrar', [RegistroMercadoController::class, 'create'])->name('registro-mercado.create');
    Route::post('registro-mercado', [RegistroMercadoController::class, 'store'])->name('registro-mercado.store');

    Route::get('mercado/dashboard', [DashboardMercadoController::class, 'index'])->name('mercado-dashboard.index');
    Route::get('mercado/dashboard/graficas', [DashboardMercadoController::class, 'graficas'])->name('mercado-dashboard.graficas');
    Route::get('mercado/dashboard/registros/{registro}/edit', [DashboardMercadoController::class, 'edit'])->name('mercado-dashboard.edit');
    Route::patch('mercado/dashboard/registros/{registro}', [DashboardMercadoController::class, 'update'])->name('mercado-dashboard.update');
    Route::delete('mercado/dashboard/registros/{registro}', [DashboardMercadoController::class, 'destroy'])->name('mercado-dashboard.destroy');

    // === MÓDULO LISTA MERCADO (mercado planificado) ===
    Route::get('lista-mercado', [ListaMercadoController::class, 'index'])->name('lista-mercado.index');
    Route::post('lista-mercado/iniciar', [ListaMercadoController::class, 'iniciar'])->name('lista-mercado.iniciar');
    Route::get('lista-mercado/tipo/{tipo}', [ListaMercadoController::class, 'tipo'])->name('lista-mercado.tipo');
    Route::post('lista-mercado/{mercado}/finalizar', [ListaMercadoController::class, 'finalizar'])->name('lista-mercado.finalizar');
    Route::post('lista-mercado/{mercado}/cancelar', [ListaMercadoController::class, 'cancelar'])->name('lista-mercado.cancelar');
    Route::get('lista-mercado/{mercado}/completado', [ListaMercadoController::class, 'completado'])->name('lista-mercado.completado');

    Route::get('lista-mercado/items/{item}/registrar', [ListaMercadoItemController::class, 'create'])->name('lista-mercado.item.create');
    Route::post('lista-mercado/items/{item}/registrar', [ListaMercadoItemController::class, 'store'])->name('lista-mercado.item.store');
    Route::post('lista-mercado/items/{item}/saltar', [ListaMercadoItemController::class, 'saltar'])->name('lista-mercado.item.saltar');

    Route::get('lista-mercado/plantilla', [ListaMercadoPlantillaController::class, 'index'])->name('lista-mercado.plantilla.index');
    Route::post('lista-mercado/plantilla/items', [ListaMercadoPlantillaController::class, 'storeItem'])->name('lista-mercado.plantilla.items.store');
    Route::patch('lista-mercado/plantilla/items/{item}', [ListaMercadoPlantillaController::class, 'updateItem'])->name('lista-mercado.plantilla.items.update');
    Route::delete('lista-mercado/plantilla/items/{item}', [ListaMercadoPlantillaController::class, 'destroyItem'])->name('lista-mercado.plantilla.items.destroy');

    // === MÓDULO CAJA ===
    Route::resource('menu-items/tipos', TipoMenuItemController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['tipos' => 'tipo'])
        ->names('menu-items.tipos');

    Route::resource('menu-items', MenuItemController::class)
        ->except(['show'])
        ->parameters(['menu-items' => 'menuItem']);

    Route::resource('metodos-pago', MetodoPagoController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['metodos-pago' => 'metodoPago']);

    Route::get('/caja/menu-dia', [MenuDiaController::class, 'index'])->name('menu-dia.index');
    Route::put('/caja/menu-dia/{dia}', [MenuDiaController::class, 'update'])->name('menu-dia.update');

    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('/caja/turno/abrir', [CajaController::class, 'abrir'])->name('caja.turno.abrir');
    Route::post('/caja/turno/{turno}/cerrar', [CajaController::class, 'cerrar'])->name('caja.turno.cerrar');
    Route::post('/caja/venta', [CajaController::class, 'storeVenta'])->name('caja.venta.store');

    Route::get('/caja/dashboard', [DashboardCajaController::class, 'index'])->name('caja-dashboard.index');
    Route::get('/caja/dashboard/turnos/{turno}', [DashboardCajaController::class, 'show'])->name('caja-dashboard.show');

    Route::get('/caja/ventas/{venta}/edit', [VentaController::class, 'edit'])->name('caja.venta.edit');
    Route::patch('/caja/ventas/{venta}', [VentaController::class, 'update'])->name('caja.venta.update');
    Route::delete('/caja/ventas/{venta}', [VentaController::class, 'destroy'])->name('caja.venta.destroy');

    Route::resource('trabajadores-turno', TrabajadorTurnoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['trabajadores-turno' => 'trabajadorTurno']);

    Route::patch('trabajadores-turno/{trabajadorTurno}/activo', [TrabajadorTurnoController::class, 'toggleActivo'])
        ->name('trabajadores-turno.toggle-activo');

    Route::get('trabajadores-turno/{trabajadorTurno}/historial-ahorro', [TrabajadorTurnoController::class, 'historialAhorro'])
        ->name('trabajadores-turno.historial-ahorro');

    Route::get('gastos/pago-masivo', [GastoController::class, 'pagoMasivoTurnos'])->name('gastos.pago-masivo');
    Route::post('gastos/pago-masivo', [GastoController::class, 'pagoMasivoTurnosStore'])->name('gastos.pago-masivo.store');

    Route::resource('gastos', GastoController::class)
        ->except(['show']);

    Route::get('pagos-ahorros', [PagoAhorroController::class, 'index'])->name('pagos-ahorros.index');
    Route::post('pagos-ahorros', [PagoAhorroController::class, 'store'])->name('pagos-ahorros.store');
    Route::delete('pagos-ahorros/{pagoAhorro}', [PagoAhorroController::class, 'destroy'])->name('pagos-ahorros.destroy');
});

// === MÓDULO NÓMINA ===
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('empleados', EmpleadoController::class)->except(['show']);
    Route::patch('empleados/{empleado}/activo', [EmpleadoController::class, 'toggleActivo'])
        ->name('empleados.toggle-activo');

    Route::get('nomina/dashboard', [DashboardNominaController::class, 'index'])->name('nomina-dashboard.index');

    Route::get('nomina', [NominaController::class, 'index'])->name('nomina.index');
    Route::get('nomina/liquidar', [NominaController::class, 'create'])->name('nomina.create');
    Route::post('nomina', [NominaController::class, 'store'])->name('nomina.store');
    Route::get('nomina/{nomina}', [NominaController::class, 'show'])->name('nomina.show');
    Route::get('nomina/{nomina}/edit', [NominaController::class, 'edit'])->name('nomina.edit');
    Route::put('nomina/{nomina}', [NominaController::class, 'update'])->name('nomina.update');
    Route::patch('nomina/{nomina}/aprobar', [NominaController::class, 'aprobar'])->name('nomina.aprobar');
    Route::delete('nomina/{nomina}', [NominaController::class, 'destroy'])->name('nomina.destroy');

    Route::get('nomina-pagos/masivo', [PagoNominaController::class, 'pagoMasivo'])->name('nomina-pagos.masivo');
    Route::post('nomina-pagos/masivo', [PagoNominaController::class, 'pagoMasivoStore'])->name('nomina-pagos.masivo.store');
    Route::get('nomina-pagos/{detalle}/crear', [PagoNominaController::class, 'create'])->name('nomina-pagos.create');
    Route::post('nomina-pagos', [PagoNominaController::class, 'store'])->name('nomina-pagos.store');
    Route::delete('nomina-pagos/{pago}', [PagoNominaController::class, 'destroy'])->name('nomina-pagos.destroy');

    Route::get('prestaciones', [PrestacionController::class, 'index'])->name('prestaciones.index');
    Route::get('prestaciones/liquidar', [PrestacionController::class, 'create'])->name('prestaciones.create');
    Route::post('prestaciones', [PrestacionController::class, 'store'])->name('prestaciones.store');
    Route::patch('prestaciones/{prestacion}/pagar', [PrestacionController::class, 'marcarPagada'])->name('prestaciones.pagar');
    Route::delete('prestaciones/{prestacion}', [PrestacionController::class, 'destroy'])->name('prestaciones.destroy');

    Route::get('nomina-ahorros', [PagoAhorroNominaController::class, 'index'])->name('nomina-ahorros.index');
    Route::post('nomina-ahorros', [PagoAhorroNominaController::class, 'store'])->name('nomina-ahorros.store');
    Route::delete('nomina-ahorros/{pago}', [PagoAhorroNominaController::class, 'destroy'])->name('nomina-ahorros.destroy');

    // === MÓDULO GASTOS FIJOS MENSUALES (arriendo, servicios) ===
    Route::resource('gastos-fijos/conceptos', ConceptoGastoFijoController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['conceptos' => 'concepto'])
        ->names('gastos-fijos.conceptos');

    Route::resource('gastos-fijos', GastoFijoController::class)
        ->except(['show'])
        ->parameters(['gastos-fijos' => 'gastoFijo']);

    // === DASHBOARD CONSOLIDADO (todos los módulos por método de pago) ===
    Route::get('/consolidado', [DashboardConsolidadoController::class, 'index'])->name('consolidado.index');
});

require __DIR__.'/auth.php';
