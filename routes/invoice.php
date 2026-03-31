<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('invoice')->group(function () {

    Route::get('/', function () {
        $invoice = session('last_invoice');

        if (!$invoice) {
            return redirect()->route('cart.index')
                ->with('error', 'No invoice found');
        }

        return view('invoice.index', compact('invoice'));
    })->name('invoice.show');

    Route::get('/pdf', [CheckoutController::class, 'downloadPdf'])
        ->name('invoice.pdf');

});