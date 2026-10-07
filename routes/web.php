<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/orders');

Route::resource('orders', OrderController::class)->only([
    'index',
    'create',
    'show',
    'store',
]);

Route::post('orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
Route::get('orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');

Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index');
Route::get('accounting/accounts/{account}', [AccountingController::class, 'showAccount'])->name('accounting.accounts.show');
