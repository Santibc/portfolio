<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LlamadasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CiudadController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ActualizacionPreciosController;
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

Route::redirect('/', '/login'); // 302 por defecto

Route::get('/dashboard',[HomeController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard de Métricas (solo admin)
Route::get('/dashboard-metricas', [App\Http\Controllers\DashboardMetricasController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard.metricas');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/importar_usuarios', [UsuariosController::class, 'importar_usuarios'])->name('importar_usuarios');
    Route::get('/usuarios_form/{user?}', [UsuariosController::class, 'form'])->name('usuarios.form');
    Route::post('/usuarios/guardar', [UsuariosController::class, 'guardar'])->name('usuarios.guardar');

Route::get('ajax/ciudades', [CiudadController::class,'byDepartamento'])
     ->name('ajax.ciudades');

//Clientes
    // Listado & AJAX
    Route::get('clientes', [ClientesController::class, 'index'])
        ->name('clientes');

    // Formulario (crear / editar)
    Route::get('clientes/form/{cliente?}', [ClientesController::class, 'form'])
        ->name('clientes.form');

    // Guardar
    Route::post('clientes/guardar', [ClientesController::class, 'guardar'])
        ->name('clientes.guardar');

            // Listado & AJAX
    Route::get('categorias', [CategoriasController::class, 'index'])
         ->name('categorias');

    // Formulario (nuevo / editar)
    Route::get('categorias/form/{categoria?}', [CategoriasController::class, 'form'])
         ->name('categorias.form');

    // Guardar (crear / actualizar)
    Route::post('categorias/guardar', [CategoriasController::class, 'guardar'])
         ->name('categorias.guardar');
// Rutas de Productos - versión simplificada
Route::prefix('productos')->middleware('auth')->group(function () {
    Route::get('/', [ProductosController::class, 'index'])->name('productos');
    Route::get('/form/{producto?}', [ProductosController::class, 'form'])->name('productos.form');
    Route::post('/guardar', [ProductosController::class, 'guardar'])->name('productos.guardar');
    Route::get('/{producto}/variantes-ajax', [ProductosController::class, 'variantesAjax'])->name('productos.variantes-ajax');
    Route::get('/{producto}/imagenes-ajax', [ProductosController::class, 'imagenesAjax'])->name('productos.imagenes-ajax');
    Route::get('/{producto}/precios-ajax', [ProductosController::class, 'preciosAjax'])->name('productos.precios-ajax');
});
Route::get('actualizaciones/{id}/descargar', 
    [ActualizacionPreciosController::class, 'descargarArchivoActualizacion']
)->name('actualizaciones.descargar');


});
// Rutas del Catálogo Interactivo
// Flujo A: Acceso público por token
// Agregar estas rutas en routes/web.php

// Módulo de Enlaces de Acceso (autenticado)
Route::middleware(['auth'])->group(function () {
    // Enlaces temporales
    Route::get('/enlaces', [App\Http\Controllers\EnlacesController::class, 'index'])->name('enlaces');
    Route::get('/enlaces/crear', [App\Http\Controllers\EnlacesController::class, 'crear'])->name('enlaces.crear');
    Route::post('/enlaces/guardar', [App\Http\Controllers\EnlacesController::class, 'guardar'])->name('enlaces.guardar');
    Route::get('/enlaces/{enlace}/detalle', [App\Http\Controllers\EnlacesController::class, 'detalle'])->name('enlaces.detalle');
    Route::post('/enlaces/{enlace}/cambiar-estado', [App\Http\Controllers\EnlacesController::class, 'cambiarEstado'])->name('enlaces.cambiar-estado');
});

// Catálogo público con token (sin autenticación)
Route::get('/catalogo/{token}', [App\Http\Controllers\CatalogoController::class, 'mostrarPorToken'])->name('catalogo.token');

// Flujo B: Acceso autenticado (vendedor/admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');
    Route::post('/catalogo/cliente', [CatalogoController::class, 'mostrarParaCliente'])->name('catalogo.cliente');
});

// Rutas AJAX del catálogo (pueden ser públicas o autenticadas)
Route::post('/catalogo/productos', [CatalogoController::class, 'obtenerProductos'])->name('catalogo.productos');
Route::get('/catalogo/producto/{producto}', [CatalogoController::class, 'detalleProducto'])->name('catalogo.producto.detalle');
Route::post('/catalogo/solicitud', [CatalogoController::class, 'guardarSolicitud'])->name('catalogo.solicitud.guardar');

// Rutas de Gestión de Solicitudes
Route::middleware(['auth'])->group(function () {
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');
    Route::get('/solicitudes/{solicitud}/detalle', [SolicitudController::class, 'detalle'])->name('solicitudes.detalle');
    Route::post('/solicitudes/{solicitud}/aplicar', [SolicitudController::class, 'aplicar'])->name('solicitudes.aplicar');
    Route::post('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
});


// Rutas de Stock
Route::prefix('stock')->name('stock.')->group(function () {
    // Vistas principales
    Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index');
    Route::get('/dashboard', [App\Http\Controllers\StockController::class, 'dashboard'])->name('dashboard');
    
    // Operaciones de stock
    Route::post('/entrada', [App\Http\Controllers\StockController::class, 'entrada'])->name('entrada');
    Route::post('/salida', [App\Http\Controllers\StockController::class, 'salida'])->name('salida');
    Route::post('/ajuste', [App\Http\Controllers\StockController::class, 'ajuste'])->name('ajuste');
    Route::post('/configurar', [App\Http\Controllers\StockController::class, 'configurar'])->name('configurar');
    
    // Consultas AJAX
    Route::get('/productos-json', [App\Http\Controllers\StockController::class, 'productosJson'])->name('productos-json');
    Route::get('/{id}/obtener', [App\Http\Controllers\StockController::class, 'obtenerStock'])->name('obtener');
    Route::get('/historial', [App\Http\Controllers\StockController::class, 'historial'])->name('historial');
    
    // Reportes
    Route::get('/reporte-movimientos', [App\Http\Controllers\StockController::class, 'reporteMovimientos'])->name('reporte-movimientos');
    
    // Importación/Exportación
    Route::post('/importar', [App\Http\Controllers\StockController::class, 'importar'])->name('importar');
    Route::get('/exportar', [App\Http\Controllers\StockController::class, 'exportar'])->name('exportar');
    
    // Inicializar stock
    Route::post('/inicializar-todos', [App\Http\Controllers\StockController::class, 'inicializarTodos'])->name('inicializar-todos');
});

// Agregar ruta AJAX para ver stock desde productos
Route::get('/productos/{producto}/stock-ajax', [App\Http\Controllers\ProductosController::class, 'stockAjax'])->name('productos.stock-ajax');

// Rutas para solicitudes
Route::get('/solicitudes/{solicitud}/pdf', [SolicitudController::class, 'descargarPdf'])->name('solicitudes.pdf');
Route::get('/solicitudes/exportar-excel', [SolicitudController::class, 'exportarExcel'])->name('solicitudes.exportar-excel');
Route::middleware(['auth'])->group(function () {
    // ... otras rutas existentes ...

    // Rutas de importación de productos (DEBEN IR ANTES de rutas con parámetros dinámicos)
    Route::get('/productos/importacion/descargar-plantilla-csv', [App\Http\Controllers\ImportacionProductosController::class, 'descargarPlantillaCsv'])->name('productos.importacion.descargar-plantilla-csv');
    Route::get('/productos/importacion/descargar-plantilla-excel', [App\Http\Controllers\ImportacionProductosController::class, 'descargarPlantillaExcel'])->name('productos.importacion.descargar-plantilla-excel');
    Route::post('/productos/importar-productos', [App\Http\Controllers\ImportacionProductosController::class, 'importarProductos'])->name('productos.importacion.importar');
    Route::get('/productos/historial-importaciones', [App\Http\Controllers\ImportacionProductosController::class, 'historial'])->name('productos.importacion.historial');
    Route::get('/productos/importacion/{id}', [App\Http\Controllers\ImportacionProductosController::class, 'verDetalle'])->name('productos.importacion.detalle');

    // Actualización de precios
    Route::post('/productos/actualizar-precios-excel', [ProductosController::class, 'actualizarPreciosExcel'])->name('productos.actualizar-precios-excel');
    Route::get('/productos/historial-precios', [ActualizacionPreciosController::class, 'historial'])->name('productos.historial-precios');
    Route::get('/productos/actualizacion-precios/{id}', [ActualizacionPreciosController::class, 'verDetalle'])->name('productos.actualizacion-precios.detalle');

    // Rutas para descargar plantillas de actualización de precios
    Route::get('/productos/descargar-plantilla-csv', [ActualizacionPreciosController::class, 'descargarPlantillaCsv'])->name('productos.descargar-plantilla-csv');
    Route::get('/productos/descargar-plantilla-excel', [ActualizacionPreciosController::class, 'descargarPlantillaExcel'])->name('productos.descargar-plantilla-excel');

    // ============================================================
    // MÓDULO DE SERVICIO TÉCNICO
    // ============================================================
    Route::prefix('servicio-tecnico')->name('st.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\ServicioTecnico\DashboardSTController::class, 'index'])->name('dashboard');

        // Clientes de Servicio Técnico
        Route::resource('clientes', App\Http\Controllers\ServicioTecnico\STClienteController::class)->parameters(['clientes' => 'cliente']);

        // Técnicos
        Route::resource('tecnicos', App\Http\Controllers\ServicioTecnico\STTecnicoController::class)->parameters(['tecnicos' => 'tecnico']);

        // Equipos
        Route::resource('equipos', App\Http\Controllers\ServicioTecnico\STEquipoController::class)->parameters(['equipos' => 'equipo']);
        Route::get('equipos/cliente/{cliente}', [App\Http\Controllers\ServicioTecnico\STEquipoController::class, 'porCliente'])->name('equipos.por-cliente');

        // Órdenes de Servicio
        Route::resource('ordenes', App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class)->parameters(['ordenes' => 'orden']);
        Route::post('ordenes/{orden}/cambiar-estado', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'cambiarEstado'])->name('ordenes.cambiar-estado');
        Route::get('ordenes/{orden}/pdf', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'generarPdf'])->name('ordenes.pdf');
        Route::get('equipos-cliente/{clienteId}', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'getEquiposByCliente'])->name('equipos-cliente');

        // Diagnósticos
        Route::post('ordenes/{orden}/diagnosticos', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'agregarDiagnostico'])->name('ordenes.diagnostico.store');
        Route::post('diagnosticos', [App\Http\Controllers\ServicioTecnico\STDiagnosticoController::class, 'store'])->name('diagnosticos.store');
        Route::put('diagnosticos/{diagnostico}', [App\Http\Controllers\ServicioTecnico\STDiagnosticoController::class, 'update'])->name('diagnosticos.update');

        // Repuestos
        Route::resource('repuestos', App\Http\Controllers\ServicioTecnico\STRepuestoController::class)->parameters(['repuestos' => 'repuesto']);
        Route::get('repuestos-json', [App\Http\Controllers\ServicioTecnico\STRepuestoController::class, 'json'])->name('repuestos.json');

        // Agregar/Quitar repuestos a órdenes
        Route::post('ordenes/{orden}/repuestos', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'agregarRepuesto'])->name('ordenes.repuesto.store');
        Route::delete('repuestos-usados/{repuestoUsado}', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'eliminarRepuesto'])->name('ordenes.eliminar-repuesto');

        // Imágenes
        Route::post('ordenes/{orden}/imagenes', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'subirImagen'])->name('ordenes.subir-imagen');
        Route::delete('imagenes/{imagen}', [App\Http\Controllers\ServicioTecnico\STOrdenServicioController::class, 'eliminarImagen'])->name('imagenes.eliminar');

        // Reportes
        Route::get('reportes/ordenes', [App\Http\Controllers\ServicioTecnico\ReportesSTController::class, 'ordenes'])->name('reportes.ordenes');
        Route::get('reportes/tecnicos', [App\Http\Controllers\ServicioTecnico\ReportesSTController::class, 'tecnicos'])->name('reportes.tecnicos');
    });
});
require __DIR__.'/auth.php';
