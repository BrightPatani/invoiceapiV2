<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {

    Route::get('/invoices', [InvoiceController::class, 'getAllInvoices']);
    Route::post('/invoices', [InvoiceController::class, 'createInvoice']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'getInvoiceById']);
    Route::put('/invoices/{id}', [InvoiceController::class, 'editInvoice']);
    Route::delete('/invoices/{id}', [InvoiceController::class, 'deleteInvoice']);
});