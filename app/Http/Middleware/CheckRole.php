<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // If user role does not match required role
        if ($request->user()->role !== $role) {

            // If admin is trying to access user routes → redirect
            if ($request->user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }

            abort(403);
        }

        return $next($request);
    }
}
