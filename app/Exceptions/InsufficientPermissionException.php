<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class InsufficientPermissionException extends Exception
{
    public function report()
    {
        Log::channel('authlog')->error('Unauthorized access attempt',[
            'message' => $this->getMessage(),
            'user_id' => auth()->id()
        ]);
    }

    public function render($request)
    {
        if($request->expectsJson()){
            return response()->json([
                'success' => false,
                'error' => 'You do not have permission'
            ],403);
        }

        return response()->view('errors.permission',[],403);
    }
}
