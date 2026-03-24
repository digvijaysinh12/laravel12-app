<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function render($request){
        if($request->expectsJson()){
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ],404);
        }

        return response()->view('errors.404',[],404);
    }
}
