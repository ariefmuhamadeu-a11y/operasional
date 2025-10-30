<?php
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\OrderImportController;
use App\Http\Controllers\Purchasing\PurchaseInvoiceController;
use App\Http\Controllers\Production\ProductionOrderController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderImportController::class)->prefix('shipments')->name('imports.orders.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/preview', 'preview')->name('preview');
    Route::post('/import', 'import')->name('import');
    Route::post('/', 'store')->name('store');
    Route::get('/{shipment}/edit', 'edit')->name('edit');
    Route::put('/{shipment}', 'update')->name('update');
});

// Welcome
Route::get('/', function () {
    return view('welcome');
});

// Route Master Dan Items
Route::resource('master/items', ItemController::class)->names('items');
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('employees', EmployeeController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy',
    ]);
});

// Routes Master Supplier
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('suppliers', \App\Http\Controllers\Master\SupplierController::class);
});

//  Route Purchasing
Route::prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('/', [PurchaseInvoiceController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseInvoiceController::class, 'create'])->name('create');
    Route::post('/', [PurchaseInvoiceController::class, 'store'])->name('store');
    Route::get('{id}', [PurchaseInvoiceController::class, 'show'])
        ->whereNumber('id')
        ->name('purchasing.show');
    Route::post('/{invoice}/payments', [PurchaseInvoiceController::class, 'storePayment'])->name('payments.store');
    Route::post('/{invoice}/status', [PurchaseInvoiceController::class, 'updateStatus'])->name('status.update'); // opsional

});

Route::redirect('/production', '/production/cutting')->name('production.home');

Route::prefix('production')->name('production.')->group(function () {
    Route::get('/{type}', [ProductionOrderController::class, 'index'])
        ->whereIn('type', ['cutting', 'sewing'])
        ->name('orders.index');
    Route::get('/{type}/create', [ProductionOrderController::class, 'create'])
        ->whereIn('type', ['cutting', 'sewing'])
        ->name('orders.create');
    Route::post('/{type}', [ProductionOrderController::class, 'store'])
        ->whereIn('type', ['cutting', 'sewing'])
        ->name('orders.store');
});
