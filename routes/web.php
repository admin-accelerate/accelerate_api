<?php

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-pdf', function () {
    $pdf = PDF::loadView('pdf.test');
    return $pdf->download('test.pdf');
});

Route::middleware('admin')->group(function () {
    Route::get('/test-pdf-invoice/{invoice}', function (Invoice $invoice) {
        $pdf = PDF::loadView('pdf.invoice', ['invoice' => $invoice->load(['client', 'lines'])]);
        return $pdf->download('invoice.pdf');
    });
});

Route::get('/test-invoice-view/{invoice}', function (Invoice $invoice) {
    return view('pdf.invoice', ['invoice' => $invoice->load(['client', 'lines'])]);
});
