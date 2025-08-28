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
Route::get('/', function () {
    return view('welcome');
});
Route::get('/ajax/ciudades', [App\Http\Controllers\ClientesController::class, 'ciudadesAjax'])->name('ajax.ciudades');
Route::get('/inicio',[HomeController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');
Route::get('ajax/ciudades', [CiudadController::class,'byDepartamento'])
     ->name('ajax.ciudades');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/importar_usuarios', [UsuariosController::class, 'importar_usuarios'])->name('importar_usuarios');
    Route::get('/usuarios_form/{user?}', [UsuariosController::class, 'form'])->name('usuarios.form');
    Route::post('/usuarios/guardar', [UsuariosController::class, 'guardar'])->name('usuarios.guardar');



//Clientes
    // Listado & AJAX





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
});


// Rutas de Stock
Route::middleware(['auth', 'verificar.empresa'])->group(function () {
    
    // Rutas de Stock
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\StockController::class, 'dashboard'])->name('dashboard');
        Route::post('/entrada', [App\Http\Controllers\StockController::class, 'entrada'])->name('entrada');
        Route::post('/salida', [App\Http\Controllers\StockController::class, 'salida'])->name('salida');
        Route::post('/ajuste', [App\Http\Controllers\StockController::class, 'ajuste'])->name('ajuste');
        Route::post('/configurar', [App\Http\Controllers\StockController::class, 'configurar'])->name('configurar');
        Route::get('/historial', [App\Http\Controllers\StockController::class, 'historial'])->name('historial');
        Route::get('/{id}/obtener', [App\Http\Controllers\StockController::class, 'obtenerStock'])->name('obtener');
        Route::post('/inicializar-todos', [App\Http\Controllers\StockController::class, 'inicializarTodos'])->name('inicializar-todos');
        Route::get('/productos-json', [App\Http\Controllers\StockController::class, 'productosJson'])->name('productos-json');
        Route::get('/reporte-movimiento', [App\Http\Controllers\StockController::class, 'reporteMovimientos'])->name('reporte-movimiento');
        Route::post('/importar', [App\Http\Controllers\StockController::class, 'importar'])->name('importar');
        Route::get('/exportar', [App\Http\Controllers\StockController::class, 'exportar'])->name('exportar');
    });
});

// Agregar ruta AJAX para ver stock desde productos
Route::get('/productos/{producto}/stock-ajax', [App\Http\Controllers\ProductosController::class, 'stockAjax'])->name('productos.stock-ajax');

// Rutas para solicitudes
Route::get('/solicitudes/{solicitud}/pdf', [SolicitudController::class, 'descargarPdf'])->name('solicitudes.pdf');
Route::get('/solicitudes/exportar-excel', [SolicitudController::class, 'exportarExcel'])->name('solicitudes.exportar-excel');
Route::middleware(['auth'])->group(function () {
    // ... otras rutas existentes ...
    
    // Actualización de precios
    Route::post('/productos/actualizar-precios-excel', [ProductosController::class, 'actualizarPreciosExcel'])->name('productos.actualizar-precios-excel');
    Route::get('/productos/historial-precios', [ActualizacionPreciosController::class, 'historial'])->name('productos.historial-precios');
    Route::get('/productos/actualizacion-precios/{id}', [ActualizacionPreciosController::class, 'verDetalle'])->name('productos.actualizacion-precios.detalle');
    
    // Rutas para descargar plantillas
    Route::get('/productos/descargar-plantilla-csv', [ActualizacionPreciosController::class, 'descargarPlantillaCsv'])->name('productos.descargar-plantilla-csv');
    Route::get('/productos/descargar-plantilla-excel', [ActualizacionPreciosController::class, 'descargarPlantillaExcel'])->name('productos.descargar-plantilla-excel');
});
// Agregar estas rutas al archivo routes/web.php dentro del middleware 'auth'

// Rutas de Empresa
Route::prefix('empresa')->name('empresa.')->group(function () {
    Route::get('/', [App\Http\Controllers\EmpresasController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\EmpresasController::class, 'form'])->name('crear');
    Route::get('/editar', [App\Http\Controllers\EmpresasController::class, 'form'])->name('form');
    Route::post('/guardar', [App\Http\Controllers\EmpresasController::class, 'guardar'])->name('guardar');
    Route::post('/cambiar-estado', [App\Http\Controllers\EmpresasController::class, 'cambiarEstado'])->name('cambiar-estado');
    Route::get('/preview', [App\Http\Controllers\EmpresasController::class, 'preview'])->name('preview');
});

// Ruta pública para ver la tienda
/* Route::get('/tienda/{slug}', [App\Http\Controllers\TiendaController::class, 'show'])->name('tienda.empresa'); */
Route::get('/tienda/acceso/{token}', [App\Http\Controllers\TiendaController::class, 'acceso'])->name('tienda.acceso');
Route::middleware(['auth', 'verificar.empresa'])->group(function () {
    
    // Rutas de Categorías
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [App\Http\Controllers\CategoriasController::class, 'index'])->name('index');
        Route::get('/form/{categoria?}', [App\Http\Controllers\CategoriasController::class, 'form'])->name('form');
        Route::post('/guardar', [App\Http\Controllers\CategoriasController::class, 'guardar'])->name('guardar');
     Route::post('/{categoria}/cambiar-estado', [App\Http\Controllers\CategoriasController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::delete('/{categoria}', [App\Http\Controllers\CategoriasController::class, 'eliminar'])->name('eliminar');
    });
    
    // Alias para la ruta index
    Route::get('/categorias', [App\Http\Controllers\CategoriasController::class, 'index'])->name('categorias');
    
    // Rutas de Clientes
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [App\Http\Controllers\ClientesController::class, 'index'])->name('index');
        Route::get('/form/{cliente?}', [App\Http\Controllers\ClientesController::class, 'form'])->name('form');
        Route::post('/guardar', [App\Http\Controllers\ClientesController::class, 'guardar'])->name('guardar');
        Route::post('/{cliente}/cambiar-estado', [App\Http\Controllers\ClientesController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::get('/{cliente}/enlaces-ajax', [App\Http\Controllers\ClientesController::class, 'enlacesAjax'])->name('enlaces-ajax');
    });
    
    // Alias para la ruta index
    Route::get('/clientes', [App\Http\Controllers\ClientesController::class, 'index'])->name('clientes');
});
// Agregar estas rutas al final de web.php, antes del require __DIR__.'/auth.php';

// ========== RUTAS DE GESTIÓN DE COMPRAS (AUTENTICADAS) ==========
Route::middleware(['auth', 'verificar.empresa'])->prefix('compras')->name('compras.')->group(function () {
    Route::get('/', [App\Http\Controllers\ComprasController::class, 'index'])->name('index');
    Route::get('/{compra}', [App\Http\Controllers\ComprasController::class, 'show'])->name('show');
    Route::post('/{compra}/cambiar-estado', [App\Http\Controllers\ComprasController::class, 'cambiarEstado'])->name('cambiar-estado');
    Route::post('/{compra}/actualizar-envio', [App\Http\Controllers\ComprasController::class, 'actualizarEnvio'])->name('actualizar-envio');
    Route::get('/{compra}/timeline', [App\Http\Controllers\ComprasController::class, 'timeline'])->name('timeline');
    Route::get('/exportar/excel', [App\Http\Controllers\ComprasController::class, 'exportar'])->name('exportar');
});

// Alias para la ruta index de compras
Route::get('/compras', [App\Http\Controllers\ComprasController::class, 'index'])
    ->middleware(['auth', 'verificar.empresa'])
    ->name('compras');

// ========== RUTAS DE TIENDA PÚBLICA ==========

// Tienda principal
Route::get('/tienda/{slug}', [App\Http\Controllers\TiendaController::class, 'show'])
    ->name('tienda.empresa');

// Producto individual
Route::get('/tienda/{slug}/producto/{producto}', [App\Http\Controllers\TiendaController::class, 'producto'])
    ->name('tienda.producto');

// Carrito
Route::get('/tienda/{slug}/carrito', [App\Http\Controllers\TiendaController::class, 'verCarrito'])
    ->name('tienda.carrito');

Route::post('/tienda/{slug}/carrito/agregar', [App\Http\Controllers\TiendaController::class, 'agregarCarrito'])
    ->name('tienda.carrito.agregar');

Route::post('/tienda/{slug}/carrito/actualizar', [App\Http\Controllers\TiendaController::class, 'actualizarCarrito'])
    ->name('tienda.carrito.actualizar');

Route::post('/tienda/{slug}/carrito/quitar', [App\Http\Controllers\TiendaController::class, 'quitarDelCarrito'])
    ->name('tienda.carrito.quitar');

// Checkout y pago
Route::get('/tienda/{slug}/checkout', [App\Http\Controllers\TiendaController::class, 'checkout'])
    ->name('tienda.checkout');

Route::post('/tienda/{slug}/procesar-compra', [App\Http\Controllers\TiendaController::class, 'procesarCompra'])
    ->name('tienda.procesar-compra');

// Confirmación de pago (callback de Wompi)
Route::get('/tienda/{slug}/pago/confirmacion/{referencia}', [App\Http\Controllers\TiendaController::class, 'confirmarPago'])
    ->name('tienda.pago.confirmacion');

// Página de pago pendiente
Route::get('/tienda/{slug}/pago/pendiente/{referencia}', function($slug, $referencia) {
    $empresa = \App\Models\Empresa::where('slug', $slug)->firstOrFail();
    $transaccion = \App\Models\TransaccionPago::where('referencia_transaccion', $referencia)->firstOrFail();
    
    return view('tienda.pago-pendiente', compact('empresa', 'transaccion'));
})->name('tienda.pago.pendiente');

// Webhook de Wompi (sin CSRF)
Route::post('/webhooks/wompi', [App\Http\Controllers\WebhookController::class, 'wompi'])
    ->name('webhooks.wompi')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Agregar estas rutas al final de web.php, antes del require __DIR__.'/auth.php';
// Agregar después de las otras rutas de tienda
Route::get('/{slug}/categorias', [App\Http\Controllers\TiendaController::class, 'categorias'])->name('tienda.categorias');
// Agregar en web.php dentro del middleware 'auth'
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/empresa/{id}', [App\Http\Controllers\DashboardAdminController::class, 'detalleEmpresa'])->name('dashboard.empresa');
    Route::post('/dashboard/pagar', [App\Http\Controllers\DashboardAdminController::class, 'marcarComoPagadas'])->name('dashboard.pagar');
    Route::get('/dashboard/exportar', [App\Http\Controllers\DashboardAdminController::class, 'exportarReporte'])->name('dashboard.exportar');
});

Route::middleware(['auth'])->group(function () {
    // Membresías
    Route::prefix('membresias')->name('membresias.')->group(function () {
        Route::get('/', [App\Http\Controllers\MembresiaController::class, 'index'])->name('index');
        Route::get('/historial', [App\Http\Controllers\MembresiaController::class, 'historial'])->name('historial');
        Route::get('/{slug}', [App\Http\Controllers\MembresiaController::class, 'show'])->name('show');
        Route::post('/{plan}/comprar', [App\Http\Controllers\MembresiaController::class, 'comprar'])->name('comprar');
        Route::post('/cancelar', [App\Http\Controllers\MembresiaController::class, 'cancelar'])->name('cancelar');
    });
});

// Ruta pública para confirmación de pago de membresía
Route::get('/membresias/pago/confirmacion/{referencia}', [App\Http\Controllers\MembresiaController::class, 'confirmarPago'])->name('membresias.pago.confirmacion');

require __DIR__.'/auth.php';
