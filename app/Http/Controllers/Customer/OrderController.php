<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Customer\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $orders = $this->orderService->paginateForUser($request->user());

        return view('user.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        // Get order with security check
        $order = $this->orderService->getOrderForUser($request->user(), $order);

        // Generate signed URL (10 minutes)
        $signedUrl = URL::temporarySignedRoute(
            'user.invoice.download',
            now()->addMinutes(1),
            ['order' => $order->id]
        );

        return view('user.orders.show', compact('order', 'signedUrl'));
    }

    public function downloadSigned(Request $request, Order $order)
    {
        // Signature validation
        if (!$request->hasValidSignature()) {
            abort(403, 'This link is expired or invalid.');
        }

        // Ownership validation
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Load relations
        $order->load('items.product', 'user');

        // Convert Order → Invoice format
        $invoice = [
            'invoice_no' => $order->order_number,
            'date' => $order->created_at,
            'user' => $order->user,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Product',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ];
            }),
            'grand_total' => $order->total_amount,
        ];

        $pdf = Pdf::loadView('user.invoice.pdf', compact('invoice'));

        return $pdf->download('invoice_'.$order->order_number.'.pdf');
    }
}
