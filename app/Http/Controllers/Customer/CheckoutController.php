<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Services\Customer\CartService;
use App\Services\Customer\CheckoutService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

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

            $url = URL::signedRoute('user.invoice.download', [
                'order' => $invoice['order_id'],
            ]);

            return redirect()
                ->route('user.orders.show', $invoice['order_id'])
                ->with('success', __('Order Confirmed', [
                    'id' => $invoice['invoice_no'],
                    'date' => now()->format('d-m-Y'),
                ]));

        } catch (Exception $e) {
            Log::error('Checkout failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Something went wrong'));
        }
    }

    public function invoice()
    {
        $invoice = session('last_invoice');

        if (! $invoice) {
            return redirect()->route('user.cart.index')
                ->with('error', __('No invoice found'));
        }

        return view('user.invoice.index', compact('invoice'));
    }

    public function downloadPdf()
    {
        $invoice = session('last_invoice');

        if (! $invoice) {
            return back()->with('error', __('No invoice found'));
        }

        $pdf = Pdf::loadView('user.invoice.pdf', compact('invoice'));

        return $pdf->download('invoice_'.$invoice['invoice_no'].'.pdf');
    }
}
