<?php
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\OrderImportController;
use Illuminate\Support\Facades\Route;

// Route::get('/shipments', [OrderImportController::class, 'index'])->name('imports.orders.index');
// Route::post('/shipments/preview', [OrderImportController::class, 'preview'])->name('imports.orders.preview');
// Route::post('/shipments/import', [OrderImportController::class, 'import'])->name('imports.orders.import');
// Route::post('/shipments', [OrderImportController::class, 'store'])->name('imports.orders.store'); // <-- TAMBAH INI
// Route::get('/shipments/{shipment}/edit', [OrderImportController::class, 'edit'])->name('imports.orders.edit');
// Route::put('/shipments/{shipment}', [OrderImportController::class, 'update'])->name('imports.orders.update');

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
