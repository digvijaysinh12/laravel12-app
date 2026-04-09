<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Generate unique request ID
        $requestId = (string) Str::uuid();

        // Detect user
        $user = $request->user();
        
        dd($user);
        // Detect user type
        $userType = 'guest';

        if ($user) {
            $userType = $user->role ?? 'customer'; // adjust if needed
        }

        // Build context
        $context = [
            'request_id' => $requestId,
            'user_id' => $user?->id,
            'user_type' => $userType,
            'ip_address' => $request->ip(),
        ];

        //Attach context to ALL logs automatically
        Log::withContext($context);
        Log::info('Middleware working');

        $request->attributes->set('context', $context);

        return $next($request);
    }
}