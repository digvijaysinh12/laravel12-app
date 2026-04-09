<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Services\Customer\CartService;
use App\Services\Customer\CheckoutService;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckoutController extends Controller
{
    public function create(CartService $cartService)
    {
        $cart = $cartService->getCartItems();
        $summary = $cartService->getSummary();
        $shipping = $cartService->getShipping($cart);
        $grandTotal = round($summary['total'] + $shipping['amount'], 2);

        return view('user.checkout.index', compact('cart', 'summary', 'shipping', 'grandTotal'));
    }

    public function store(CheckoutRequest $request, CheckoutService $service)
    {
        try {
            $invoice = $service->process();

            session()->put('last_invoice', $invoice);

            return redirect()
                ->route('user.invoice.show')
                ->with('success', 'Order placed successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function invoice()
    {
        $invoice = session('last_invoice');

        if (! $invoice) {
            return redirect()->route('user.cart.index')
                ->with('error', 'No invoice found');
        }

        return view('user.invoice.index', compact('invoice'));
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
