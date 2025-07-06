<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LlamadasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard',[HomeController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/importar_usuarios', [UsuariosController::class, 'importar_usuarios'])->name('importar_usuarios');
    Route::get('/usuarios_form/{user?}', [UsuariosController::class, 'form'])->name('usuarios.form');
    Route::post('/usuarios/guardar', [UsuariosController::class, 'guardar'])->name('usuarios.guardar');
    Route::get('/leads', [LeadsController::class, 'index'])->name('leads');
    Route::get('/importar_leads', [LeadsController::class, 'importar_leads'])->name('importar_leads');
    Route::get('/leads_form/{lead?}', [LeadsController::class, 'form'])->name('leads.form');
    Route::get('/llamadas', [LlamadasController::class, 'index'])->name('llamadas');
    Route::get('/leads_form/{llamada?}', [LlamadasController::class, 'form'])->name('llamadas.form');
Route::get('/llamadas/{id}/respuestas-json', [LlamadasController::class, 'respuestasJson'])->name('llamadas.respuestas.json');
Route::get('/leads/{id}/info-json', [LeadsController::class, 'infoJson'])->name('leads.info');
});

require __DIR__.'/auth.php';
