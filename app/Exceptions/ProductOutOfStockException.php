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
                'error' => 'Product is out of stock'
            ], 400);
        }

        return back()->with('error', 'Product is out of stock');
    }

    public function report()
    {
        Log::warning('Out of stock product accessed');
    }
}
