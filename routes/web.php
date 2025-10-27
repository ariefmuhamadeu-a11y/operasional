<?php
use App\Http\Controllers\OrderImportController;

Route::get('/shipments', [OrderImportController::class, 'index'])->name('imports.orders.index');
Route::post('/shipments/preview', [OrderImportController::class, 'preview'])->name('imports.orders.preview');
Route::post('/shipments/import', [OrderImportController::class, 'import'])->name('imports.orders.import');
Route::post('/shipments', [OrderImportController::class, 'store'])->name('imports.orders.store'); // <-- TAMBAH INI
Route::get('/shipments/{shipment}/edit', [OrderImportController::class, 'edit'])->name('imports.orders.edit');
Route::put('/shipments/{shipment}', [OrderImportController::class, 'update'])->name('imports.orders.update');

Route::get('/', function(){
  return view('welcome');
});
