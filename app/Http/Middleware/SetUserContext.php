<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetUserContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::flush();

        $user = $request->user();
        $userType = $user?->role ?? 'guest';
        $context = [
            'request_id' => $request->header('X-Request-ID', (string) Str::uuid()),
            'user_id' => $user?->id,
            'user_type' => $userType,
            'ip_address' => $request->ip(),
        ];

        Context::add($context);
        Log::withContext($context);

        return $next($request);
    }
}
