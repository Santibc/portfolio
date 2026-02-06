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
use App\Http\Controllers\ListaPreciosController;
use App\Http\Controllers\UbicacionesController;
use App\Http\Controllers\TrasladosController;
use App\Http\Controllers\NovedadesStockController;
use App\Http\Controllers\PortalClienteController;
use App\Http\Controllers\PuntoVentaController;
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
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.metricas');

// Exportaciones de métricas (solo admin)
Route::middleware(['auth', 'role:admin'])->prefix('reportes')->name('reportes.')->group(function () {
    Route::get('/ventas-excel', [HomeController::class, 'exportarVentasExcel'])->name('ventas.excel');
    Route::get('/metricas-pdf', [HomeController::class, 'exportarMetricasPdf'])->name('metricas.pdf');
});

Route::middleware('auth')->group(function () {
    // Perfil - Todos los usuarios autenticados
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX Ciudades - Disponible para todos los usuarios autenticados
    Route::get('ajax/ciudades', [CiudadController::class,'byDepartamento'])
         ->name('ajax.ciudades');
});

// ============================================================
// USUARIOS (Admin e Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios'])->group(function () {
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/importar_usuarios', [UsuariosController::class, 'importar_usuarios'])->name('importar_usuarios');
    Route::get('/usuarios_form/{user?}', [UsuariosController::class, 'form'])->name('usuarios.form');
    Route::post('/usuarios/guardar', [UsuariosController::class, 'guardar'])->name('usuarios.guardar');
});

// ============================================================
// RUTAS SOLO ADMIN
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Categorías (solo admin)
    Route::get('categorias', [CategoriasController::class, 'index'])->name('categorias');
    Route::get('categorias/form/{categoria?}', [CategoriasController::class, 'form'])->name('categorias.form');
    Route::post('categorias/guardar', [CategoriasController::class, 'guardar'])->name('categorias.guardar');
    Route::delete('categorias/{categoria}/eliminar', [CategoriasController::class, 'eliminar'])->name('categorias.eliminar');

    // Listas de Precios (solo admin)
    Route::get('listas-precios', [ListaPreciosController::class, 'index'])->name('listas-precios');
    Route::get('listas-precios/form/{listaPrecio?}', [ListaPreciosController::class, 'form'])->name('listas-precios.form');
    Route::post('listas-precios/guardar', [ListaPreciosController::class, 'guardar'])->name('listas-precios.guardar');
    Route::post('listas-precios/{listaPrecio}/toggle-estado', [ListaPreciosController::class, 'toggleEstado'])->name('listas-precios.toggle-estado');

    // Productos (solo admin)
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductosController::class, 'index'])->name('productos');
        Route::get('/form/{producto?}', [ProductosController::class, 'form'])->name('productos.form');
        Route::post('/guardar', [ProductosController::class, 'guardar'])->name('productos.guardar');
        Route::delete('/{id}/eliminar', [ProductosController::class, 'eliminar'])->name('productos.eliminar');
        Route::get('/{producto}/variantes-ajax', [ProductosController::class, 'variantesAjax'])->name('productos.variantes-ajax');
        Route::get('/{producto}/imagenes-ajax', [ProductosController::class, 'imagenesAjax'])->name('productos.imagenes-ajax');
        Route::get('/{producto}/precios-ajax', [ProductosController::class, 'preciosAjax'])->name('productos.precios-ajax');
        // Exportar productos con imágenes
        Route::get('/exportar-con-imagenes', [ProductosController::class, 'exportarConImagenes'])->name('productos.exportar-con-imagenes');
    });

    // Descargar actualización de precios
    Route::get('actualizaciones/{id}/descargar',
        [ActualizacionPreciosController::class, 'descargarArchivoActualizacion']
    )->name('actualizaciones.descargar');
});

// ============================================================
// RUTAS ADMIN, VENDEDOR, INVENTARIOS Y FACTURACIÓN
// ============================================================
Route::middleware(['auth', 'role:admin,vendedor,inventarios,facturacion'])->group(function () {
    // Clientes - Listado (todos pueden ver)
    Route::get('clientes', [ClientesController::class, 'index'])->name('clientes');
    // Documentos - Descargar (todos pueden descargar)
    Route::get('documentos-cliente/{documento}/descargar', [ClientesController::class, 'descargarDocumento'])->name('clientes.documentos.descargar');
});

// Admin, Inventarios y Facturación pueden crear/editar clientes
Route::middleware(['auth', 'role:admin,inventarios,facturacion'])->group(function () {
    // Clientes - Crear/Editar
    Route::get('clientes/form/{cliente?}', [ClientesController::class, 'form'])->name('clientes.form');
    Route::post('clientes/guardar', [ClientesController::class, 'guardar'])->name('clientes.guardar');
    Route::delete('clientes/{cliente}/eliminar', [ClientesController::class, 'eliminar'])->name('clientes.eliminar');

    // Sucursales de clientes
    Route::post('clientes/{cliente}/sucursales', [ClientesController::class, 'guardarSucursal'])->name('clientes.sucursales.guardar');
    Route::delete('sucursales/{sucursal}', [ClientesController::class, 'eliminarSucursal'])->name('clientes.sucursales.eliminar');

    // Documentos de clientes
    Route::post('clientes/{cliente}/documentos', [ClientesController::class, 'subirDocumento'])->name('clientes.documentos.subir');
    Route::delete('documentos-cliente/{documento}', [ClientesController::class, 'eliminarDocumento'])->name('clientes.documentos.eliminar');
});
// ============================================================
// CATÁLOGO Y ENLACES (Admin y Vendedor)
// ============================================================

// Catálogo público con token (sin autenticación)
Route::get('/catalogo/{token}', [App\Http\Controllers\CatalogoController::class, 'mostrarPorToken'])->name('catalogo.token');

// Rutas AJAX del catálogo (pueden ser públicas o autenticadas)
Route::post('/catalogo/productos', [CatalogoController::class, 'obtenerProductos'])->name('catalogo.productos');
Route::get('/catalogo/producto/{producto}', [CatalogoController::class, 'detalleProducto'])->name('catalogo.producto.detalle');
Route::post('/catalogo/solicitud', [CatalogoController::class, 'guardarSolicitud'])->name('catalogo.solicitud.guardar');

// Enlaces y Catálogo autenticado (admin y vendedor)
Route::middleware(['auth', 'role:admin,vendedor'])->group(function () {
    // Enlaces temporales
    Route::get('/enlaces', [App\Http\Controllers\EnlacesController::class, 'index'])->name('enlaces');
    Route::get('/enlaces/crear', [App\Http\Controllers\EnlacesController::class, 'crear'])->name('enlaces.crear');
    Route::post('/enlaces/guardar', [App\Http\Controllers\EnlacesController::class, 'guardar'])->name('enlaces.guardar');
    Route::get('/enlaces/{enlace}/detalle', [App\Http\Controllers\EnlacesController::class, 'detalle'])->name('enlaces.detalle');
    Route::post('/enlaces/{enlace}/cambiar-estado', [App\Http\Controllers\EnlacesController::class, 'cambiarEstado'])->name('enlaces.cambiar-estado');

    // Catálogo autenticado
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');
    Route::post('/catalogo/cliente', [CatalogoController::class, 'mostrarParaCliente'])->name('catalogo.cliente');
});

// ============================================================
// COTIZACIONES/SOLICITUDES (Admin, Vendedor y Facturación)
// ============================================================
Route::middleware(['auth', 'role:admin,vendedor,facturacion'])->group(function () {
    // Listado principal
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');

    // Ver detalle
    Route::get('/solicitudes/{solicitud}/detalle', [SolicitudController::class, 'detalle'])->name('solicitudes.detalle');

    // Acciones de estado (métodos antiguos, mantener por compatibilidad)
    Route::post('/solicitudes/{solicitud}/aplicar', [SolicitudController::class, 'aplicar'])->name('solicitudes.aplicar');
    Route::post('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::post('/solicitudes/{solicitud}/descontar-stock', [SolicitudController::class, 'descontarStock'])->name('solicitudes.descontar-stock');

    // CRUD completo (Fase 5) - Editar/Eliminar solo admin y facturación (ver grupo aparte abajo)

    // Acciones adicionales
    Route::post('/solicitudes/{solicitud}/clonar', [SolicitudController::class, 'clonar'])->name('solicitudes.clonar');
    Route::post('/solicitudes/{solicitud}/cambiar-estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.cambiar-estado');

    // Gestión de reservas
    Route::post('/solicitudes/{solicitud}/renovar-reserva', [SolicitudController::class, 'renovarReserva'])->name('solicitudes.renovar-reserva');
    Route::post('/solicitudes/{solicitud}/liberar-reserva', [SolicitudController::class, 'liberarReserva'])->name('solicitudes.liberar-reserva');

    // AJAX helpers para edición
    Route::get('/solicitudes/productos', [SolicitudController::class, 'getProductos'])->name('solicitudes.productos');
    Route::get('/solicitudes/producto/{producto}/variantes', [SolicitudController::class, 'getVariantes'])->name('solicitudes.variantes');
    Route::post('/solicitudes/precio', [SolicitudController::class, 'getPrecio'])->name('solicitudes.precio');

    // Exportar
    Route::get('/solicitudes/{solicitud}/pdf', [SolicitudController::class, 'descargarPdf'])->name('solicitudes.pdf');
    Route::get('/solicitudes/exportar-excel', [SolicitudController::class, 'exportarExcel'])->name('solicitudes.exportar-excel');
});

// ============================================================
// EDICIÓN/ELIMINACIÓN DE COTIZACIONES (Solo Admin y Facturación)
// ============================================================
Route::middleware(['auth', 'role:admin,facturacion'])->group(function () {
    Route::get('/solicitudes/{solicitud}/editar', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
    Route::put('/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('solicitudes.update');
    Route::delete('/solicitudes/{solicitud}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');
});

// ============================================================
// PAGOS Y FACTURACIÓN (Admin, Facturación y Vendedor)
// ============================================================
Route::middleware(['auth', 'role:admin,facturacion,vendedor'])->group(function () {
    // Pagos
    Route::get('/solicitudes/{solicitud}/pago', [App\Http\Controllers\PagosController::class, 'create'])->name('pagos.create');
    Route::post('/solicitudes/{solicitud}/pago', [App\Http\Controllers\PagosController::class, 'store'])->name('pagos.store');
    Route::get('/solicitudes/{solicitud}/pago/detalle', [App\Http\Controllers\PagosController::class, 'show'])->name('pagos.show');
    Route::get('/solicitudes/{solicitud}/comprobante', [App\Http\Controllers\PagosController::class, 'descargarComprobante'])->name('pagos.comprobante');
    Route::get('/solicitudes/{solicitud}/pagos/{pago}/comprobante', [App\Http\Controllers\PagosController::class, 'descargarComprobantePago'])->name('pagos.comprobante-individual');

    // Aprobación/Rechazo de pagos (solo admin y facturación)
    Route::post('/solicitudes/{solicitud}/pagos/{pago}/aprobar', [App\Http\Controllers\PagosController::class, 'aprobar'])->name('pagos.aprobar');
    Route::post('/solicitudes/{solicitud}/pagos/{pago}/rechazar', [App\Http\Controllers\PagosController::class, 'rechazar'])->name('pagos.rechazar');

    // Facturación
    Route::post('/solicitudes/{solicitud}/factura', [SolicitudController::class, 'generarFactura'])->name('solicitudes.generar-factura');
    Route::get('/solicitudes/{solicitud}/factura/pdf', [SolicitudController::class, 'descargarFactura'])->name('solicitudes.factura-pdf');
});

// ============================================================
// STOCK (Solo Admin e Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios'])->prefix('stock')->name('stock.')->group(function () {
    // Vistas principales
    Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index');
    Route::get('/dashboard', [App\Http\Controllers\StockController::class, 'dashboard'])->name('dashboard');

    // Consultas AJAX (lectura)
    Route::get('/productos-json', [App\Http\Controllers\StockController::class, 'productosJson'])->name('productos-json');
    Route::get('/{id}/obtener', [App\Http\Controllers\StockController::class, 'obtenerStock'])->name('obtener');
    Route::get('/historial', [App\Http\Controllers\StockController::class, 'historial'])->name('historial');

    // Reportes (lectura)
    Route::get('/reporte-movimientos', [App\Http\Controllers\StockController::class, 'reporteMovimientos'])->name('reporte-movimientos');
    Route::get('/exportar', [App\Http\Controllers\StockController::class, 'exportar'])->name('exportar');

    // PDFs de notas de movimiento
    Route::get('/movimiento/{id}/pdf', [App\Http\Controllers\StockController::class, 'generarNotaPdf'])->name('movimiento-pdf');
    Route::get('/movimiento/{id}/ver-pdf', [App\Http\Controllers\StockController::class, 'verNotaPdf'])->name('movimiento-ver-pdf');
});

// Operaciones de stock (solo admin e inventarios)
Route::middleware(['auth', 'role:admin,inventarios'])->prefix('stock')->name('stock.')->group(function () {
    Route::post('/entrada', [App\Http\Controllers\StockController::class, 'entrada'])->name('entrada');
    Route::post('/salida', [App\Http\Controllers\StockController::class, 'salida'])->name('salida');
    Route::post('/ajuste', [App\Http\Controllers\StockController::class, 'ajuste'])->name('ajuste');
    Route::post('/configurar', [App\Http\Controllers\StockController::class, 'configurar'])->name('configurar');
    Route::post('/importar', [App\Http\Controllers\StockController::class, 'importar'])->name('importar');
    Route::post('/inicializar-todos', [App\Http\Controllers\StockController::class, 'inicializarTodos'])->name('inicializar-todos');
});

// ============================================================
// UBICACIONES (Solo Admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('ubicaciones', [UbicacionesController::class, 'index'])->name('ubicaciones');
    Route::get('ubicaciones/form/{id?}', [UbicacionesController::class, 'form'])->name('ubicaciones.form');
    Route::post('ubicaciones/guardar', [UbicacionesController::class, 'guardar'])->name('ubicaciones.guardar');
    Route::delete('ubicaciones/{id}/eliminar', [UbicacionesController::class, 'eliminar'])->name('ubicaciones.eliminar');
    Route::post('ubicaciones/{ubicacion}/toggle-estado', [UbicacionesController::class, 'toggleEstado'])->name('ubicaciones.toggle-estado');
    Route::post('ubicaciones/{ubicacion}/marcar-principal', [UbicacionesController::class, 'marcarPrincipal'])->name('ubicaciones.marcar-principal');
});

// ============================================================
// TRASLADOS DE STOCK (Admin e Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios'])->group(function () {
    Route::get('traslados', [TrasladosController::class, 'index'])->name('traslados');
    Route::get('traslados/form/{id?}', [TrasladosController::class, 'form'])->name('traslados.form');
    Route::post('traslados/guardar', [TrasladosController::class, 'guardar'])->name('traslados.guardar');
    Route::post('traslados/{id}/enviar', [TrasladosController::class, 'enviar'])->name('traslados.enviar');
    Route::post('traslados/{id}/recibir', [TrasladosController::class, 'recibir'])->name('traslados.recibir');
    Route::post('traslados/{id}/cancelar', [TrasladosController::class, 'cancelar'])->name('traslados.cancelar');
    Route::get('traslados/{id}/detalle', [TrasladosController::class, 'detalle'])->name('traslados.detalle');
    Route::get('traslados/variantes/{productoId}', [TrasladosController::class, 'getVariantesPorProducto'])->name('traslados.variantes');
    Route::get('traslados/stock-disponible', [TrasladosController::class, 'getStockDisponible'])->name('traslados.stock-disponible');
    Route::get('traslados/productos-por-ubicacion/{ubicacionId}', [TrasladosController::class, 'getProductosPorUbicacion'])->name('traslados.productos-por-ubicacion');
    Route::get('traslados/variantes-por-ubicacion/{productoId}/{ubicacionId}', [TrasladosController::class, 'getVariantesPorProductoYUbicacion'])->name('traslados.variantes-por-ubicacion');
});

// ============================================================
// NOVEDADES DE STOCK (Admin e Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios'])->group(function () {
    Route::get('novedades-stock', [NovedadesStockController::class, 'index'])->name('novedades-stock');
    Route::get('novedades-stock/form/{id?}', [NovedadesStockController::class, 'form'])->name('novedades-stock.form');
    Route::post('novedades-stock/guardar', [NovedadesStockController::class, 'guardar'])->name('novedades-stock.guardar');
    Route::post('novedades-stock/{id}/cerrar', [NovedadesStockController::class, 'cerrar'])->name('novedades-stock.cerrar');
    Route::get('novedades-stock/{id}/detalle', [NovedadesStockController::class, 'detalle'])->name('novedades-stock.detalle');
    Route::get('novedades-stock/variantes/{productoId}', [NovedadesStockController::class, 'getVariantesPorProducto'])->name('novedades-stock.variantes');
    Route::get('novedades-stock/stock-disponible', [NovedadesStockController::class, 'getStockDisponible'])->name('novedades-stock.stock-disponible');
    Route::get('novedades-stock/dashboard', [NovedadesStockController::class, 'dashboard'])->name('novedades-stock.dashboard');
});

// Ver stock desde productos (admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/productos/{producto}/stock-ajax', [App\Http\Controllers\ProductosController::class, 'stockAjax'])->name('productos.stock-ajax');
});

// ============================================================
// IMPORTACIÓN DE PRODUCTOS (Admin e Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios'])->group(function () {
    Route::get('/productos/importacion/descargar-plantilla-csv', [App\Http\Controllers\ImportacionProductosController::class, 'descargarPlantillaCsv'])->name('productos.importacion.descargar-plantilla-csv');
    Route::get('/productos/importacion/descargar-plantilla-excel', [App\Http\Controllers\ImportacionProductosController::class, 'descargarPlantillaExcel'])->name('productos.importacion.descargar-plantilla-excel');
    Route::post('/productos/importar-productos', [App\Http\Controllers\ImportacionProductosController::class, 'importarProductos'])->name('productos.importacion.importar');
    Route::get('/productos/historial-importaciones', [App\Http\Controllers\ImportacionProductosController::class, 'historial'])->name('productos.importacion.historial');
    Route::get('/productos/importacion/{id}', [App\Http\Controllers\ImportacionProductosController::class, 'verDetalle'])->name('productos.importacion.detalle');
});

// ACTUALIZACIÓN DE PRECIOS (Solo Admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/productos/actualizar-precios-excel', [ProductosController::class, 'actualizarPreciosExcel'])->name('productos.actualizar-precios-excel');
    Route::get('/productos/historial-precios', [ActualizacionPreciosController::class, 'historial'])->name('productos.historial-precios');
    Route::get('/productos/actualizacion-precios/{id}', [ActualizacionPreciosController::class, 'verDetalle'])->name('productos.actualizacion-precios.detalle');

    // Plantillas de actualización de precios
    Route::get('/productos/descargar-plantilla-csv', [ActualizacionPreciosController::class, 'descargarPlantillaCsv'])->name('productos.descargar-plantilla-csv');
    Route::get('/productos/descargar-plantilla-excel', [ActualizacionPreciosController::class, 'descargarPlantillaExcel'])->name('productos.descargar-plantilla-excel');
});

// ============================================================
// PORTAL CLIENTE (Rol: cliente)
// ============================================================
Route::middleware(['auth', 'role:cliente'])->prefix('portal')->name('portal.')->group(function () {
    // Dashboard del cliente
    Route::get('/', [PortalClienteController::class, 'dashboard'])->name('dashboard');

    // Historial de pedidos
    Route::get('/historial', [PortalClienteController::class, 'historial'])->name('historial');

    // Ver detalle de pedido
    Route::get('/pedido/{solicitud}', [PortalClienteController::class, 'detallePedido'])->name('pedido.detalle');

    // Seguimiento de envío
    Route::get('/pedido/{solicitud}/seguimiento', [PortalClienteController::class, 'seguimiento'])->name('pedido.seguimiento');

    // Descargar guía de envío
    Route::get('/pedido/{solicitud}/guia', [PortalClienteController::class, 'descargarGuia'])->name('pedido.guia');

    // Descargar factura
    Route::get('/pedido/{solicitud}/factura', [PortalClienteController::class, 'descargarFactura'])->name('pedido.factura');
});

// ============================================================
// GESTIÓN DE ENVÍOS (Admin, Facturación, Inventarios)
// ============================================================
Route::middleware(['auth', 'role:admin,facturacion,inventarios'])->group(function () {
    // Actualizar estado de envío
    Route::post('/solicitudes/{solicitud}/envio', [SolicitudController::class, 'actualizarEnvio'])->name('solicitudes.envio.update');

    // Subir guía de envío
    Route::post('/solicitudes/{solicitud}/guia', [SolicitudController::class, 'subirGuia'])->name('solicitudes.guia.upload');
});

// ============================================================
// PUNTO DE VENTA (Admin, Inventarios, Punto de Venta)
// ============================================================
Route::middleware(['auth', 'role:admin,inventarios,punto_venta'])->prefix('punto-venta')->name('punto-venta.')->group(function () {
    // Dashboard
    Route::get('/', [PuntoVentaController::class, 'dashboard'])->name('dashboard');

    // Cambiar ubicación
    Route::post('/cambiar-ubicacion', [PuntoVentaController::class, 'cambiarUbicacion'])->name('cambiar-ubicacion');

    // Nueva venta
    Route::get('/nueva', [PuntoVentaController::class, 'nuevaVenta'])->name('nueva-venta');

    // Buscar productos (AJAX)
    Route::get('/buscar-productos', [PuntoVentaController::class, 'buscarProductos'])->name('buscar-productos');

    // Obtener producto (AJAX)
    Route::get('/producto', [PuntoVentaController::class, 'obtenerProducto'])->name('obtener-producto');

    // Verificar stock (AJAX)
    Route::post('/verificar-stock', [PuntoVentaController::class, 'verificarStock'])->name('verificar-stock');

    // Procesar venta (AJAX)
    Route::post('/procesar', [PuntoVentaController::class, 'procesarVenta'])->name('procesar');

    // Historial de ventas
    Route::get('/ventas', [PuntoVentaController::class, 'index'])->name('index');

    // Detalle de venta (AJAX)
    Route::get('/{id}/detalle', [PuntoVentaController::class, 'detalle'])->name('detalle');

    // Anular venta
    Route::post('/{id}/anular', [PuntoVentaController::class, 'anular'])->name('anular');

    // Ticket de venta (PDF)
    Route::get('/{id}/ticket', [PuntoVentaController::class, 'ticket'])->name('ticket');

    // Reporte
    Route::get('/reportes', [PuntoVentaController::class, 'reporte'])->name('reporte');

    // Exportar
    Route::get('/exportar', [PuntoVentaController::class, 'exportar'])->name('exportar');
});

// ============================================================
// MÓDULO DE SERVICIO TÉCNICO (Admin y Técnico)
// ============================================================
Route::middleware(['auth', 'role:admin,tecnico'])->prefix('servicio-tecnico')->name('st.')->group(function () {
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

require __DIR__.'/auth.php';
