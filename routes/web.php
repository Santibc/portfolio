<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RoleRedirectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectApprovalController;
use App\Http\Controllers\Admin\ProjectRegistrationController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Farmer\DashboardController as FarmerDashboardController;
use App\Http\Controllers\Farmer\ProjectController;
use App\Http\Controllers\Farmer\ProjectDocumentController;
use App\Http\Controllers\Farmer\ProjectImageController;
use App\Http\Controllers\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Sales\DashboardController as SalesDashboardController;
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

// Página principal - En construcción
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

// Dashboard general - Redirige según el rol del usuario
Route::get('/dashboard', [RoleRedirectController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Ruta oculta para pruebas de componentes (solo desarrollo)
Route::get('/component-tests', function () {
    return view('component-tests');
})->middleware(['auth'])->name('component.tests');

// Rutas de perfil (requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Rol
|--------------------------------------------------------------------------
*/

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================
Route::middleware(['auth', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // MÓDULO 3: Registro de Proyectos (Admin crea agricultor + proyecto)
    Route::prefix('proyectos/registro')->name('projects.registration.')->group(function () {
        Route::get('/', [ProjectRegistrationController::class, 'index'])->name('index');
        Route::get('/crear', [ProjectRegistrationController::class, 'create'])->name('create');
        Route::post('/fase-1', [ProjectRegistrationController::class, 'storePhase1'])->name('phase1.store');
        Route::get('/{proyecto}/fase-2', [ProjectRegistrationController::class, 'showPhase2'])->name('phase2');
        Route::post('/{proyecto}/fase-2', [ProjectRegistrationController::class, 'storePhase2'])->name('phase2.store');
        Route::get('/{proyecto}/fase-3', [ProjectRegistrationController::class, 'showPhase3'])->name('phase3');
        Route::post('/{proyecto}/fase-3', [ProjectRegistrationController::class, 'storePhase3'])->name('phase3.store');
        Route::get('/{proyecto}', [ProjectRegistrationController::class, 'show'])->name('show');
        Route::get('/{proyecto}/editar', [ProjectRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{proyecto}', [ProjectRegistrationController::class, 'update'])->name('update');
        Route::post('/{proyecto}/enviar-revision', [ProjectRegistrationController::class, 'submitForReview'])->name('submit-review');
        Route::post('/{proyecto}/reenviar-email', [ProjectRegistrationController::class, 'resendWelcomeEmail'])->name('resend-email');

        // Archivos (documentos e imágenes)
        Route::get('/{proyecto}/archivos', [ProjectRegistrationController::class, 'showFiles'])->name('files');

        // Documentos
        Route::post('/{proyecto}/documentos', [ProjectRegistrationController::class, 'storeDocument'])->name('documents.store');
        Route::delete('/documentos/{documento}', [ProjectRegistrationController::class, 'destroyDocument'])->name('documents.destroy');
        Route::get('/documentos/{documento}/download', [ProjectRegistrationController::class, 'downloadDocument'])->name('documents.download');

        // Imágenes
        Route::post('/{proyecto}/imagenes', [ProjectRegistrationController::class, 'storeImage'])->name('images.store');
        Route::delete('/imagenes/{imagen}', [ProjectRegistrationController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('/imagenes/{imagen}/principal', [ProjectRegistrationController::class, 'setImagePrincipal'])->name('images.set-principal');
    });

    // MÓDULO 3: Aprobación de Proyectos
    Route::get('/proyectos/revision', [ProjectApprovalController::class, 'index'])->name('projects.review.index');
    Route::get('/proyectos/revision/{proyecto}', [ProjectApprovalController::class, 'show'])->name('projects.review.show');
    Route::post('/proyectos/{proyecto}/aprobar', [ProjectApprovalController::class, 'approve'])->name('projects.approve');
    Route::post('/proyectos/{proyecto}/rechazar', [ProjectApprovalController::class, 'reject'])->name('projects.reject');

    // Módulos futuros:
    // - Gestión de usuarios
    // - Aprobación de KYC
    // - Gestión de retiros
    // - Reportes y analytics
    // - Configuración del sistema
});

// ============================================
// RUTAS DE SUPERVISOR
// ============================================
Route::middleware(['auth', 'role:Supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');

    // Módulos futuros:
    // - Revisión de proyectos
    // - Seguimiento de inversiones
    // - Reportes
});

// ============================================
// RUTAS DE AGRICULTOR
// ============================================
Route::middleware(['auth', 'role:Agricultor'])->prefix('agricultor')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');

    // MÓDULO 3: Gestión de Proyectos
    Route::resource('projects', ProjectController::class)->except(['destroy']);
    Route::get('/projects/{project}/archivos', [ProjectController::class, 'files'])->name('projects.files');
    Route::post('/projects/{project}/enviar-revision', [ProjectController::class, 'submitForReview'])->name('projects.submit-review');

    // Rutas para formulario de 3 fases
    Route::get('/projects/{project}/fase-2', [ProjectController::class, 'showPhase2'])->name('projects.phase2');
    Route::post('/projects/{project}/fase-2', [ProjectController::class, 'storePhase2'])->name('projects.phase2.store');
    Route::get('/projects/{project}/fase-3', [ProjectController::class, 'showPhase3'])->name('projects.phase3');
    Route::post('/projects/{project}/fase-3', [ProjectController::class, 'storePhase3'])->name('projects.phase3.store');

    // MÓDULO 4: Documentos e Imágenes de Proyectos
    // Documentos
    Route::post('/projects/{proyecto}/documentos', [ProjectDocumentController::class, 'store'])->name('projects.documents.store');
    Route::delete('/projects/documentos/{documento}', [ProjectDocumentController::class, 'destroy'])->name('projects.documents.destroy');
    Route::get('/projects/documentos/{documento}/download', [ProjectDocumentController::class, 'download'])->name('projects.documents.download');

    // Imágenes
    Route::post('/projects/{proyecto}/imagenes', [ProjectImageController::class, 'store'])->name('projects.images.store');
    Route::delete('/projects/imagenes/{imagen}', [ProjectImageController::class, 'destroy'])->name('projects.images.destroy');
    Route::patch('/projects/imagenes/{imagen}/principal', [ProjectImageController::class, 'setPrincipal'])->name('projects.images.set-principal');
    Route::post('/projects/{proyecto}/imagenes/reorder', [ProjectImageController::class, 'reorder'])->name('projects.images.reorder');

    // Módulos futuros:
    // - Actualizaciones
});

// ============================================
// RUTAS DE INVERSIONISTA
// ============================================
Route::middleware(['auth', 'role:Inversionista'])->prefix('inversionista')->name('inversionista.')->group(function () {
    Route::get('/dashboard', [InvestorDashboardController::class, 'index'])->name('dashboard');

    // Rutas que NO requieren KYC
    Route::get('/kyc', function () {
        return view('kyc.index');
    })->name('kyc.index');

    // Rutas que SÍ requieren KYC aprobado
    Route::middleware('kyc.approved')->group(function () {
        // Módulos futuros:
        // - Catálogo de proyectos
        // - Mis inversiones
        // - Billetera
        // - Depósitos
        // - Retiros
        // - Marketplace (trading)
    });
});

// ============================================
// RUTAS DE VENDEDOR
// ============================================
Route::middleware(['auth', 'role:Vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/dashboard', [SalesDashboardController::class, 'index'])->name('dashboard');

    // Módulos futuros:
    // - CRM (prospectos)
    // - Mis comisiones
    // - Actividades
});

require __DIR__.'/auth.php';
