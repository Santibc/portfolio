<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// App móvil "Mis Negocios": resumen financiero de solo lectura.
Route::middleware(['llave.app-movil', 'throttle:30,1'])
    ->get('/movil/resumen', \App\Http\Controllers\Api\ResumenMovilController::class)
    ->name('api.movil.resumen');
