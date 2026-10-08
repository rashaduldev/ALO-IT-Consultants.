<?php

use App\Http\Controllers\AccountingDashboardController;
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

// Enterprise Accounting Dashboard Sub-Routes
Route::prefix('accounting')->name('accounting.')->group(function (): void {
    Route::get('/', [AccountingDashboardController::class, 'index'])->name('index');
    Route::get('/ledger', [AccountingDashboardController::class, 'ledger'])->name('ledger');
    Route::get('/orders', [AccountingDashboardController::class, 'orders'])->name('orders');
    Route::get('/reports', [AccountingDashboardController::class, 'reports'])->name('reports');
    Route::get('/accounts/{account}', [AccountingDashboardController::class, 'showAccount'])->name('accounts.show');
});
