<?php

namespace App\Http\Middleware;

use App\Http\Middleware\SetUserContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return app(SetUserContext::class)->handle($request, $next);
    }
}
