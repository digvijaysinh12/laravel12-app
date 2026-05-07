<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // First priority → session
        if (session()->has('locale')) {

            $locale = session('locale');

        }

        // Second priority → user preference
        elseif (auth()->check() && auth()->user()->preferred_locale) {

            $locale = auth()->user()->preferred_locale;

            session(['locale' => $locale]);

        }

        // Default
        else {

            $locale = config('app.locale');

        }

        App::setLocale($locale);

        Carbon::setLocale($locale);

        return $next($request);
    }
}
