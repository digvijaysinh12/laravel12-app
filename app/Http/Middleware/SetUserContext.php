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

        // dd($request->user()->role);
        $userType = $user
            ? ($user->role === 'admin' ? 'admin' : 'customer')
            : 'guest';
        $context = [
            'request_id' => (string) Str::uuid(),
            'user_id' => $user?->id,
            'user_type' => $userType,
            'ip_address' => $this->resolveIpAddress($request),
        ];

        Context::add($context);
        Log::withContext($context);

        return $next($request);
    }

    private function resolveIpAddress(Request $request): string
    {
        $forwardedFor = $request->header('X-Forwarded-For');

        if (is_string($forwardedFor) && $forwardedFor !== '') {
            $candidate = trim(explode(',', $forwardedFor)[0]);

            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }

        return (string) $request->ip();
    }
}
