<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConfirmPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    public function store(ConfirmPasswordRequest $request): RedirectResponse
    {
        $request->session()->put('auth.password_confirmed_at', time());

        $route = $request->user()->role === 'admin'
            ? route('admin.dashboard', absolute: false)
            : route('user.dashboard', absolute: false);

        return redirect()->intended($route);
    }
}
