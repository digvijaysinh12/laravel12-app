<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckoutController extends Controller
{
    public function store(CheckoutService $service)
    {
        try {
            $invoice = $service->process();

            session()->put('last_invoice', $invoice);

            return view('user.invoice.index', compact('invoice'))
                ->with('success', 'Order placed successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function downloadPdf()
    {
        $invoice = session('last_invoice');

        if (! $invoice) {
            return back()->with('error', 'No invoice found');
        }

        $pdf = Pdf::loadView('user.invoice.pdf', compact('invoice'));

        return $pdf->download('invoice_'.$invoice['invoice_no'].'.pdf');
    }
}
