<?php

namespace App\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'External service failed. Please try again later.',
            ], 500);
        }

        return response()->view('errors.external-api', [
            'message' => $this->getMessage(),
        ], 500);
    }
}
