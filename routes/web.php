<?php

use App\Http\Controllers\Routes\DashboardRoutesController;
use App\Http\Controllers\Routes\ScannedQrController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\PeriodoCampeonatoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FacturaCabeceraController;
use App\Http\Controllers\FacturaListadoController;
use App\Http\Controllers\ProductoJornadaController;
use App\Http\Controllers\FacturaSorteoController;
use App\Http\Controllers\Routes\BoletoRoutesController;

use App\Http\Controllers\MovimientoStockController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\VentasController;

Route::get('', fn() => redirect()->route('auth.login'));

Route::get('autorizar-factura/{claveAcceso}', [FacturaCabeceraController::class, 'verResultado']);
// Route::get('enviar-factura/{claveAcceso}', [FacturaCabeceraController::class, 'enviarRecepcion']);

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::middleware('guest')->get('login', 'login')->name('auth.login');
    Route::middleware('guest')->post('login', 'authenticate')->name('auth.authenticate');
    Route::middleware('guest')->get('reestablecer-contraseña', 'forgotPassword')->name('auth.forgotPassword');
    Route::post('logout', 'logout')->name('auth.logout');
    Route::get('logout', 'logout')->name('auth.logout-get');
});

Route::prefix('qr')->controller(ScannedQrController::class)->group(function () {
    Route::get('/', 'index')->name('qr.scanned');
});

Route::middleware('auth')->controller(DashboardRoutesController::class)->prefix('')->group(function () {
    Route::get('dashboard', 'dashboard')->name('dashboard.index');

    Route::prefix('configuracion')->group(function () {
        Route::get('', 'config')->name('dashboard.config');
        Route::get('form', [ConfigController::class, 'index'])->name('configuracion.index');
    });

    // Route::middleware('permission:view users')->get('usuarios', 'usuarios')->name('users.index');
    Route::get('usuarios', 'usuarios')->name('users.index');

    Route::prefix('productos')->group(function () {
        Route::get('list', 'productsList')->name('products.list');
        Route::get('categoria', 'categories')->name('products.categories');
        Route::get('abonos', 'abonos')->name('products.abonos');
        Route::get('jornadas', 'days')->name('products.days');
    });

    Route::prefix('ventas')->group(function () {
        Route::get('clientes', 'salesClient')->name('sales.client');
        Route::get('factura', 'salesBill')->name('sales.bill');
        Route::get('factura-listado', 'salesListBill')->name('sales.bills');
        Route::get('factura/{id}', [FacturaListadoController::class, 'show'])->name('sales.billDetail');
    });

    Route::prefix('reportes')->group(function () {
        Route::get('Kardex', 'reportCardex')->name('reporte.cardex');
        Route::get('movientoCaja', 'reportMovimientoCaja')->name('reporte.reportMovimientoCaja');
        Route::get('ventasProductos', 'reportVentasProductos')->name('reporte.ventasProductos');
        Route::get('ventasReporte', 'reportVentas')->name('reporte.ventas');
    });

    Route::prefix('sorteos')->group(function () {
        Route::get('cargarDatosFactura', 'sorteoCargarDatosFactura')->name('sorteo.cargarDatosFactura');
        Route::get('sorteos', 'datosBoletos')->name('sorteo.boletos');
        Route::get('periodoCampeonato', 'periodoCampeonato')->name('sorteo.periodoCampeonato');
        Route::get('sorteosRealizados', 'sorteosRealizados')->name('sorteo.sorteosRealizados');
    });

    Route::get('qr', 'qr')->name('qr.home');
});

// Rutas resource para las tablas (CRUD completo)
Route::middleware('auth')->prefix('actions')->group(function () {
    // Productos
    Route::post('productos/{id}/agregar-stock', [ProductoController::class, 'agregarStock'])->name('productos.agregarStock');
    Route::post('productos/{id}/agregar-stock-ticket', [ProductoController::class, 'agregarStockTickets'])->name('productos.agregarStockTicket');
    Route::resource('productos', ProductoController::class);
    Route::get('/productos/{idTicketStock}/jornadas-disponibles', [ProductoController::class, 'obtenerJornadasDisponibles'])->name('productos.jornadas.disponibles');
    Route::get('/productos/{idProducto}/tiene-jornadas', [ProductoController::class, 'tieneJornadas'])->name('productos.tiene.jornadas');
    Route::post('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');

    // Productos - Jornadas
    Route::resource('productos-jornada', ProductoJornadaController::class);
    // Clientes
    Route::get('clientes/consumidor-final', [ClienteController::class, 'consumidorFinal'])->name('clientes.consumidorFinal');
    Route::post('clientes/buscar', [ClienteController::class, 'buscarClientes']);
    Route::resource('clientes', ClienteController::class);
    // Categoria productos
    Route::resource('categoria-productos', CategoriaProductoController::class);
    // Jornadas
    Route::resource('jornadas', JornadaController::class);
    Route::post('jornadas/disponibles', [JornadaController::class, 'getJornadasDisponibles'])->name('jornadas.disponibles');
    // Abonos
    Route::get('abonos/activos', [AbonoController::class, 'getActiveAbonos'])->name('abonos.activos');
    Route::resource('abonos', AbonoController::class);
    // Movimientos de stoxk
    Route::post('/movimientos/filtrar', [MovimientoStockController::class, 'filtrarPorFechas'])
        ->name('movimientos.filtrar');
    //  Coinfiguracion
    Route::prefix('configuracion')->controller(ConfigController::class)->group(function () {
        Route::post('', 'store')->name('action.config.store');
        Route::post('{id}', 'update')->name('action.config.update');
    });
    // Sorteos Cargar Facturas
    Route::post('sorteos/exportarDatos/facturas', [FacturaSorteoController::class, 'importarExcel'])->name('sorteos.importData');
    Route::get('sorteos/obtenerDatosFacturas', [FacturaSorteoController::class, 'obtenerFacturas'])->name('sorteos.getData');
    Route::get('sorteos/obetenerPeriodos', [FacturaSorteoController::class, 'obtenerPeriodosCampeonato'])->name('sorteos.getPeriodos');
    Route::post('sorteos/obtenerJornadas', [FacturaSorteoController::class, 'getJornadasPeriodo'])->name('sorteos.getJornadas');
    Route::get('sorteos/actualizarDatos', [FacturaSorteoController::class, 'actualizarFacturasSorteo'])->name('sorteos.actualizarDatos');
    Route::get('sorteos/generarBoletos', [FacturaSorteoController::class, 'generarBoletosSorteo'])->name('sorteos.generarBoletos');
    Route::post('sorteos/crearSorteo', [FacturaSorteoController::class, 'crearSorteo'])->name('sorteos.crearSorteo');
    Route::post('sorteos/actualizarBoletosSorteo', [FacturaSorteoController::class, 'actualizarBoletos'])->name('sorteos.actualizarBoletos');
    Route::get('sorteos/obtenerSorteos', [FacturaSorteoController::class, 'getSorteos'])->name('sorteos.getSorteos');
    Route::get('sorteos/datosSorteo/{id}', [FacturaSorteoController::class, 'obtenerSorteo'])->name('sorteos.obtenerSorteo');
    // Periodos Campeonato
    Route::resource('periodo-campeonato', PeriodoCampeonatoController::class);
    // Facturas
    Route::prefix('facturas')->controller(FacturaCabeceraController::class)->group(function () {
        Route::get('numeroFactura', 'obtenerNumeroFactura')->name('facturas.numero');
        Route::post('guardarFactura', 'almacenarFactura')->name('facturas.almacenarFactura');
        Route::get('factura-pdf/{facturaId}', 'getInvoicePdf')->name('facturas.facturaPdf');
        Route::get('formasPago', 'formasPago')->name('facturas.formasPago');
        Route::post('movimientosCaja', 'movimientosCaja')->name('facturas.movimientosCaja ');
        Route::post('ventasProductos', 'reporteProductos')->name('facturas.ventasProductos ');
        Route::post('reporteVentas', 'reporteFacturas')->name('facturas.reporteVentas ');
    });

    Route::prefix('facturas')->controller(FacturaListadoController::class)->group(function () {
        Route::get('', 'listadoFacturas')->name('facturas.listado');
        Route::post('anular/{facturaId}', 'anularFactura')->name('facturas.anular');
        Route::post('lote', 'execResendToSriInvoice')->name('facturas.listadoReenvio');
        Route::post('reenviar-sri/{facturaId}', 'resendToSriInvoice')->name('facturas.reenviarSri');
        Route::post('reenviar-email/{facturaId}', 'resendSriEmail')->name('facturas.reenviarSriEmail');
        Route::post('reenviar-qr/{facturaId}', 'resendQrEmail')->name('facturas.reenviarQrEmail');
        Route::get('obtener-pdf-sri/{facturaId}', 'getSriPdf')->name('facturas.obtenerPdfSri');
    });

    // Usuarios
    Route::apiResource('usuarios', UserController::class);

    Route::apiResource('qr', QrController::class);
});

Route::prefix('boletos')->controller(BoletoRoutesController::class)->group(function () {
    Route::get('venta', 'boletos')->name('boletos-venta');
    Route::get('checkout-complete', 'validarCompra')->name('checkout-complete');
});

Route::prefix('ventas')->controller(VentasController::class)->group(function () {
    Route::post('pre-sell', 'preSellItem')->name('ventas.preSellItem');
    Route::get('get-unique-code', 'getUniqueCode')->name('ventas.getUniqueCode');
});
