<?php

use App\Http\Controllers\DashboardMercadoController;
use App\Http\Controllers\ProductoMercadoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroMercadoController;
use App\Http\Controllers\TipoProductoMercadoController;
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

    Route::get('registro-mercado',                      [RegistroMercadoController::class, 'index'])->name('registro-mercado.index');
    Route::get('registro-mercado/{producto}/registrar', [RegistroMercadoController::class, 'create'])->name('registro-mercado.create');
    Route::post('registro-mercado',                     [RegistroMercadoController::class, 'store'])->name('registro-mercado.store');

    Route::get('mercado/dashboard',                            [DashboardMercadoController::class, 'index'])->name('mercado-dashboard.index');
    Route::get('mercado/dashboard/graficas',                   [DashboardMercadoController::class, 'graficas'])->name('mercado-dashboard.graficas');
    Route::get('mercado/dashboard/registros/{registro}/edit',  [DashboardMercadoController::class, 'edit'])->name('mercado-dashboard.edit');
    Route::patch('mercado/dashboard/registros/{registro}',     [DashboardMercadoController::class, 'update'])->name('mercado-dashboard.update');
    Route::delete('mercado/dashboard/registros/{registro}',    [DashboardMercadoController::class, 'destroy'])->name('mercado-dashboard.destroy');
});

require __DIR__.'/auth.php';
