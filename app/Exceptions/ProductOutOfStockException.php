<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductOutOfStockException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' =>false,
                'error' => 'Product is out of stock'
            ], 400);
        }

        return back()->with('error', 'Product is out of stock');
    }

    public function report()
    {
        Log::channel('products')->warning('Product out of stock',[
            'message' => $this->getMessage()
        ]);
    }
}
