<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\ImpuestoController;
use App\Http\Controllers\Admin\IncotermController;
use App\Http\Controllers\Admin\MonedaController;
use App\Http\Controllers\Admin\PlantillaFacturaController;
use App\Http\Controllers\Admin\PuertoController;
use App\Http\Controllers\Admin\SiigoController;
use App\Http\Controllers\Admin\TipoDescuentoController;
use App\Http\Controllers\Admin\TipoPagoController;
use App\Http\Controllers\Catalogo\ClienteController;
use App\Http\Controllers\Catalogo\ProductoController;
use App\Http\Controllers\Facturacion\DashboardController as FacturacionDashboardController;
use App\Http\Controllers\Facturacion\FacturaController;
use App\Http\Controllers\Facturacion\FacturaPublicaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
});

Route::middleware(['auth', 'verified', 'role:Administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        Route::get('empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('empresa', [EmpresaController::class, 'update'])->name('empresa.update');

        Route::resource('monedas', MonedaController::class)->except(['show']);
        Route::resource('impuestos', ImpuestoController::class)->except(['show']);
        Route::resource('tipos-descuento', TipoDescuentoController::class)->except(['show']);
        Route::resource('incoterms', IncotermController::class)->except(['show']);
        Route::resource('puertos', PuertoController::class)->except(['show']);
        Route::resource('tipos-pago', TipoPagoController::class)->except(['show']);

        Route::get('siigo', [SiigoController::class, 'edit'])->name('siigo.edit');
        Route::put('siigo', [SiigoController::class, 'update'])->name('siigo.update');
        Route::post('siigo/probar', [SiigoController::class, 'probarConexion'])
            ->middleware('throttle:10,1')
            ->name('siigo.probar');
        Route::post('siigo/sincronizar', [SiigoController::class, 'sincronizarCatalogos'])
            ->middleware('throttle:5,1')
            ->name('siigo.sincronizar');

        Route::resource('plantillas', PlantillaFacturaController::class)
            ->parameters(['plantillas' => 'plantilla'])
            ->except(['show'])
            ->middleware(['store' => 'throttle:30,1', 'update' => 'throttle:30,1']);
        Route::post('plantillas/previsualizar', [PlantillaFacturaController::class, 'previsualizar'])
            ->middleware('throttle:30,1')
            ->name('plantillas.previsualizar');
    });

Route::middleware(['auth', 'verified', 'role:Administrador'])
    ->prefix('catalogos')
    ->name('catalogos.')
    ->group(function () {
        Route::resource('productos', ProductoController::class)
            ->except(['show'])
            ->middleware(['store' => 'throttle:60,1', 'update' => 'throttle:60,1']);

        Route::resource('clientes', ClienteController::class)
            ->except(['show'])
            ->middleware(['store' => 'throttle:60,1', 'update' => 'throttle:60,1']);
    });

Route::middleware(['auth', 'verified', 'role:Administrador'])
    ->prefix('facturacion')
    ->name('facturacion.')
    ->group(function () {
        Route::get('dashboard', [FacturacionDashboardController::class, 'index'])->name('dashboard');
        Route::resource('facturas', FacturaController::class)->except(['show']);
        Route::post('facturas/{factura}/emitir', [FacturaController::class, 'emitir'])->name('facturas.emitir');
        Route::post('facturas/{factura}/emitir-electronica', [FacturaController::class, 'emitirElectronica'])->name('facturas.emitir-electronica');
        Route::patch('facturas/{factura}/datos-envio', [FacturaController::class, 'updateDatosEnvio'])->name('facturas.datos-envio');
        Route::post('facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
        Route::get('facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');
        Route::get('facturas/{factura}/previsualizar', [FacturaController::class, 'previsualizar'])->name('facturas.previsualizar');
    });

// Portal público de descarga por token (sin auth).
Route::get('factura/{token}', [FacturaPublicaController::class, 'descargar'])
    ->middleware('throttle:30,1')
    ->name('factura.publica');

require __DIR__.'/auth.php';
